<?php

class Payment {
    public function index()
    {
        view('payment/list.php');
    }

    public function find()
    {
        $request = escapeString([
            'invoice_number' => get('invoice_number'),
            'status'         => get('status'),
            'date_from'      => get('date_from'),
            'date_to'        => get('date_to'),
        ]);

        if (!isValidDate($request['date_from'], true) || !isValidDate($request['date_to'], true)) {
            return errorResponse(['Invalid date input.']);
        }

        $invoiceNumber = $request['invoice_number'];
        $status        = $this->setPaymentStatusFilter($request['status']);
        $dateFrom      = $request['date_from'];
        $dateTo        = $request['date_to'];

        $havingFilter = cancelIfEmpty($status, 'HAVING '.$status);
        $filter = cancelIfEmpty($invoiceNumber, ' AND P.`invoice_number` LIKE \'%'.$invoiceNumber.'%\'');
        $filter .= cancelIfEmpty($dateFrom, ' AND P.`transaction_date` >= \''.$dateFrom.' 00:00:00\'');
        $filter .= cancelIfEmpty($dateTo, ' AND P.`transaction_date` <= \''.$dateTo.' 23:59:59\'');

        $data = getData(
            'SELECT
                P.`id`,
                P.`invoice_number`,
                '.formatNumberSql('P.paid_amount', 'paid_amount').',
                SUM(COALESCE(D.`qty`, 0) * COALESCE(D.`cost_price`, 0)) AS `amount_to_pay`,
                SUM(COALESCE(D.`qty`, 0) * COALESCE(D.`cost_price`, 0)) - P.paid_amount AS `balance`,
                COALESCE(DATE_FORMAT(P.paid_at, \'%Y-%m-%d %h:%i:%S %p\'), \'\') AS `date_paid`,
                DATE(P.`transaction_date`) AS `transaction_date`
            FROM `purchase` AS P
            LEFT JOIN `purchase_detail` AS D ON D.`transaction_id` = P.`id`
            WHERE P.`deleted_at` IS NULL AND P.received_at IS NOT NULL
            '.$filter.'
            GROUP BY P.`id`'.$havingFilter.' ORDER BY P.id DESC '
        );

        if (count($data) > 0) {
            return successfulResponse($data);
        }

        return errorResponse(['No results found.']);
    }

    public function pay() {
        $request = escapeString([
            'id'          => post('id'),
            'paid_amount' => post('paid_amount')
        ]);

        $data = getData(
            'SELECT
                P.invoice_number,
                SUM(COALESCE(PD.`qty`, 0) * COALESCE(PD.`cost_price`, 0)) AS `amount_to_pay`,
                P.paid_amount
            FROM purchase AS P
            LEFT JOIN purchase_detail AS PD ON PD.transaction_id = P.id
            WHERE P.deleted_at IS NULL AND P.received_at IS NOT NULL AND P.id = '.$request['id']
        );

        if (count($data) < 1) {
            return errorResponse(['Data not found.']);
        }

        if ((float)$data[0]['amount_to_pay'] == (float)$data[0]['paid_amount'] && (float)$data[0]['paid_amount'] == (float)$request['paid_amount']) {
            return errorResponse(['Purchase '.$data[0]['invoice_number'].' is already paid.']);
        }

        if ((float)$request['paid_amount'] == (float)$data[0]['amount_to_pay']) {
            executeQuery('UPDATE purchase SET paid_at = NOW(), paid_amount = '.$request['paid_amount'].' WHERE id = '.$request['id']);
        } else {
            executeQuery('UPDATE purchase SET paid_at = NULL, paid_amount = '.$request['paid_amount'].' WHERE id = '.$request['id']);
        }

        $data = getData(
            'SELECT
                COALESCE(paid_at, \'\') AS paid_at
            FROM purchase AS P
            WHERE P.deleted_at IS NULL AND P.id = '.$request['id']
        );

        return successfulResponse($data);
    }

    private function setPaymentStatusFilter($status)
    {
        switch($status) {
            case 1:
                // Paid
                return 'date_paid <> \'\'';
            case 2:
                // Unpaid
                return 'date_paid = \'\'';
            case 3:
                // Incomplete Payment
                return 'paid_amount > 0 AND balance > 0';
            case 4:
                // Unpaid/Incomplete Payment
                return 'date_paid = \'\' OR (paid_amount > 0 AND balance > 0)';
            case 5:
                // Excess
                return 'balance < 0';
        }

        return '';
    }
}