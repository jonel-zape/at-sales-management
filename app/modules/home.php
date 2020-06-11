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
}
