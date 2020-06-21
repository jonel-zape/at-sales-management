<?php

class Home
{
    public function index()
    {
        view('home.php');
    }

    public function summary()
    {
        $request = escapeString([
            'month' => get('month'),
            'year'  => get('year')
        ]);

        $response = [];

        $widget = $this->getSummary($request['month'], $request['month'], $request['year'], $request['year']);
        if (isset($widget[0])) {
            $response['widget'] = $widget[0];
        } else {
            $response['widget'] = [
                'month_year' => $request['month'].'-'.$request['year'],
                'cost'       => '0.00',
                'sales'      => '0.00',
                'loss'       => '0.00',
                'net_income' => '0.00'
            ];
        }

        $dateFrom = explode('-', date("Y-m", strtotime($request['year'].'-'.$request['month'].' -4 months')));
        $graph = $this->getSummary($dateFrom[1], $request['month'], $dateFrom[0], $request['year']);

        for ($i = 4; $i > -1; $i--) {
            $yearMonth = explode('-', date("Y-m", strtotime($request['year'].'-'.$request['month'].' -'.$i.' months')));
            $monthYear = ($yearMonth[1] + 0).'-'.$yearMonth[0];
            $data = [
                'month_year' => $monthYear,
                'cost'       => '0.00',
                'sales'      => '0.00',
                'loss'       => '0.00',
                'net_income' => '0.00'
            ];
            foreach($graph as $item) {
                if ($item['month_year'] == $monthYear) {
                    $data = [
                        'month_year' => $monthYear,
                        'cost'       => $item['cost'],
                        'sales'      => $item['sales'],
                        'loss'       => $item['loss'],
                        'net_income' => $item['net_income']
                    ];
                    break;
                }
            }
            $response['graph'][] = $data;
        }

        $notification = $this->getNotificationData($request['month'], $request['year']);
        if (isset($notification[0])) {
            $response['notification'] = [
                'amount_to_pay' => $notification[0]['amount_to_pay'],
                'unreceived_items' => $notification[0]['unreceived_items'],
                'unsold_items' => $notification[0]['unsold_items'],
            ];
        } else {
            $response['notification'] = [
                'amount_to_pay' => 0,
                'unreceived_items' => 0,
                'unsold_items' => 0
            ];
        }

        return successfulResponse($response);
    }

    private function getSummary($monthFrom, $monthTo, $yearFrom, $yearTo)
    {
        return getData(
            'SELECT
                A.`month_year`,
                '.roundNumberSql('SUM(A.cost)', 'cost').',
                '.roundNumberSql('SUM(A.sales)', 'sales').',
                '.roundNumberSql('SUM(A.loss)', 'loss').',
                '.roundNumberSql('(SUM(A.sales) - SUM(A.cost)) - SUM(A.loss)', 'net_income').'
            FROM (
                SELECT
                    CONCAT(MONTH(P.transaction_date), \'-\', YEAR(P.transaction_date)) AS `month_year`,
                    SUM(PD.qty * PD.cost_price) AS cost,
                    0 AS sales,
                    0 AS loss
                FROM purchase AS P
                LEFT JOIN purchase_detail AS PD ON PD.transaction_id = P.id
                WHERE
                (P.transaction_date BETWEEN \''.$yearFrom.'-'.$monthFrom.'-01 00:00:00\' AND \''.$yearTo.'-'.$monthTo.'-31 23:59:59\')
                    AND received_at IS NOT NULL
                GROUP BY CONCAT(MONTH(P.transaction_date), \'-\', YEAR(P.transaction_date))
                UNION ALL
                SELECT
                    CONCAT(MONTH(S.transaction_date), \'-\', YEAR(S.transaction_date)) AS `month_year`,
                    0 AS cost,
                    SUM(IF(S.returned_at IS NULL, SD.qty * SD.selling_price, 0)) AS sales,
                    SUM(IF(S.returned_at IS NOT NULL, SD.qty_damage * SD.selling_price, 0)) AS loss
                FROM sales AS S
                LEFT JOIN sales_detail AS SD ON SD.transaction_id = S.id
                WHERE
                (S.transaction_date BETWEEN \''.$yearFrom.'-'.$monthFrom.'-01 00:00:00\' AND \''.$yearTo.'-'.$monthTo.'-31 23:59:59\')
                GROUP BY CONCAT(MONTH(S.transaction_date), \'-\', YEAR(S.transaction_date))
            ) AS A
            GROUP BY A.`month_year`'
        );
    }

    private function getNotificationData($month, $year)
    {
        return getData(
            'SELECT
                '.roundNumberSql('SUM(amount_to_pay)', 'amount_to_pay').',
                '.roundNumberSql('SUM(unreceived_items)', 'unreceived_items').',
                '.roundNumberSql('SUM(unsold_items)', 'unsold_items').'
            FROM (
                SELECT
                    COALESCE(SUM(amount_to_pay), 0) AS amount_to_pay, 0 AS unreceived_items, 0 AS unsold_items
                FROM (
                    SELECT
                        SUM(PD.qty * PD.cost_price) - P.paid_amount AS amount_to_pay
                    FROM purchase AS P
                    LEFT JOIN purchase_detail AS PD ON PD.transaction_id = P.id
                    WHERE
                        P.deleted_at IS NULL
                        AND P.paid_at IS NULL
                        AND P.received_at IS NOT NULL
                        AND (P.transaction_date BETWEEN \''.$year.'-'.$month.'-01 00:00:00\' AND \''.$year.'-'.$month.'-31 23:59:59\')
                    GROUP BY P.id
                ) AS A
                UNION ALL
                SELECT
                    0 AS amount_to_pay, COALESCE(SUM(unreceived_items), 0) AS unreceived_items, 0 AS unsold_items
                FROM (
                    SELECT
                        SUM(PD.qty) AS unreceived_items
                    FROM purchase AS P
                    LEFT JOIN purchase_detail AS PD ON PD.transaction_id = P.id
                    WHERE
                        P.deleted_at IS NULL
                        AND P.received_at IS NULL
                        AND (P.transaction_date BETWEEN \''.$year.'-'.$month.'-01 00:00:00\' AND \''.$year.'-'.$month.'-31 23:59:59\')
                    GROUP BY P.id
                ) AS B
                UNION ALL
                SELECT
                    0 AS amount_to_pay, 0 AS unreceived_items, COALESCE(SUM(qty)) AS unsold_items
                FROM (
                    SELECT
                        PD.qty - COALESCE(SUM(IF(S.deleted_at IS NULL, IF(S.returned_at IS NULL, SD.qty, SD.qty - SD.qty_damage), 0)), 0) AS qty
                    FROM purchase AS P
                    LEFT JOIN purchase_detail AS PD ON PD.transaction_id = P.id
                    LEFT JOIN sales_detail AS SD ON SD.purchase_detail_id = PD.id
                    LEFT JOIN sales AS S ON S.id = SD.transaction_id
                    WHERE
                        P.deleted_at IS NULL
                        AND P.received_at IS NOT NULL
                        AND (P.transaction_date BETWEEN \''.$year.'-'.$month.'-01 00:00:00\' AND \''.$year.'-'.$month.'-31 23:59:59\')
                    GROUP BY PD.id
                ) AS C
            ) AS Z'
        );
    }
}
