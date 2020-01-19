<?php

require 'invoice.php';

class Purchase extends Invoice
{
    protected $table            = 'purchase';
    protected $tableDetail      = 'purchase_detail';
    protected $detailIdentifier = 'product_id';

    public function index()
    {
        view('purchase/list.php');
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
        $status        = $request['status'] == 0 ? '' : $request['status'];
        $dateFrom      = $request['date_from'];
        $dateTo        = $request['date_to'];

        $filter = cancelIfEmpty($invoiceNumber, ' AND P.`invoice_number` LIKE \'%'.$invoiceNumber.'%\'');
        $filter .= cancelIfEmpty(
            $status,
            $status == 1 ? ' AND P.`received_at` IS NOT NULL' : ' AND P.`received_at` IS NULL'
        );
        $filter .= cancelIfEmpty($dateFrom, ' AND P.`transaction_date` >= \''.$dateFrom.' 00:00:00\'');
        $filter .= cancelIfEmpty($dateTo, ' AND P.`transaction_date` <= \''.$dateTo.' 23:59:59\'');

        $data = getData(
            'SELECT
                P.`id`,
                P.`invoice_number`,
                DATE(P.`transaction_date`) AS `transaction_date`,
                P.`memo`,
                SUM(COALESCE(D.`qty`, 0)) AS `quantity`,
                0 AS `remaining_quantity`,
                SUM(COALESCE(D.`qty`, 0) * COALESCE(D.`cost_price`, 0)) AS `amount`,
                IF (P.`received_at` IS NULL, \'Uncreceived\', \'Received\') AS `status`
            FROM `purchase` AS P 
            LEFT JOIN `purchase_detail` AS D ON D.`transaction_id` = P.`id`
            WHERE P.`deleted_at` IS NULL
            '.$filter.'
            GROUP BY P.`id`'
        );

        if (count($data) > 0) {
            return successfulResponse($data);
        }

        return errorResponse(['No results found.']);
    }

    public function create()
    {
        $data = [
            'id'               => 0,
            'invoice_number'   => '',
            'transaction_date' => getDateToday(),
            'memo'             => '',
            'received_at'      => null,
        ];

        view('purchase/detail.php', $data);
    }

    public function save()
    {
        $receivedAt = post('received_at');
        if (trim($receivedAt) == '') {
            $receivedAt = null;
        }

        $head = [
            'id'               => post('id'),
            'invoice_number'   => post('invoice_number'),
            'transaction_date' => post('transaction_date'),
            'memo'             => post('memo'),
            'received_at'      => $receivedAt
        ];

        if (!$this->isValidInvoice($head['invoice_number'], $head['id']) && $head['id'] != 0) {
            return errorResponse(['Invalid invoice number or already exists.']);
        }

        if (!isValidDate($head['transaction_date'])) {
            return errorResponse(['Invalid date input']);
        }

        if (!isValidDate($head['received_at'], true)) {
            return errorResponse(
                ['Invalid date received input. Tip: You can leave it blank or unchecked to unreceived purchase']
            );
        }

        $details = [];
        foreach (post('detail') as $key => $value) {
            $details[] = [
                'id'            => $value['detail_id'],
                'product_id'    => $value['product_id'],
                'qty'           => toNumber($value['quantity']),
                'cost_price'    => toNumber($value['cost_price']),
                'remark'        => $value['remark'],
            ];
        }

        $id = $this->insertOrUpdate($head, $details);

        return successfulResponse(['id' => $id]);
    }

    public function edit($id)
    {
        if (!is_numeric($id)) {
            $id = 0;
        }

        $head = getData(
            'SELECT
                `invoice_number`,
                '.dateOnlySql('transaction_date').',
                '.dateOnlySql('received_at').',
                `memo`
            FROM `purchase`
            WHERE
                `deleted_at` IS NULL
                AND `id` = '.$id
        );

        if (count($head) < 1) {
            view('404.php');
            return;
        }

        $data = [
            'id'               => $id,
            'invoice_number'   => $head[0]['invoice_number'],
            'transaction_date' => $head[0]['transaction_date'],
            'memo'             => $head[0]['memo'],
            'received_at'      => $head[0]['received_at'],
        ];

        view('purchase/detail.php', $data);
    }

    public function delete()
    {
        $request = escapeString([
            'id' => post('id'),
        ]);

        $data = getData(
            'SELECT COUNT(`id`) AS `count` FROM `purchase` WHERE `received_at` IS NULL AND `id` ='.$request['id']
        );

        if ($data[0]['count'] == 0) {
            return errorResponse(['Cannot delete purchase that is already received.']);
        }

        executeQuery('UPDATE `purchase` SET `deleted_at` = NOW() WHERE `id` = '.$request['id']);

        return successfulResponse(['Deleted']);
    }

    public function details()
    {
        $id = get('id');
        if (!is_numeric($id)) {
            $id = 0;
        }

        $data = getData(
            'SELECT COALESCE(received_at, 0) AS received_at FROM `purchase` WHERE id = '.$id
        );

        $transaction = [
            'is_received' => true
        ];

        if (count($data) > 0) {
            $transaction['is_received'] = $data[0]['received_at'] == 0;
        }

        $details = getData(
            'SELECT
                D.`id` AS `detail_id`,
                D.`product_id`,
                P.`stock_no`,
                P.`name`,
                P.`short_name`,
                P.`memo`,
                '.roundNumberSql('D.`cost_price`', 'cost_price').',
                '.roundNumberSql('D.`qty`', 'quantity').',
                '.roundNumberSql('D.`cost_price` * D.`qty`', 'amount').',
                '.roundNumberSql('D.`qty` - SUM(COALESCE(IF(SH.id IS NOT NULL, 0, S.`qty`), 0))', 'remaining_qty').',
                '.roundNumberSql('D.`qty` - (D.`qty` - SUM(COALESCE(IF(SH.id IS NOT NULL, 0, S.`qty`), 0)))', 'min_quantity').',
                D.`remark`
            FROM `purchase_detail` AS D
            LEFT JOIN `sales_detail` AS S ON S.`purchase_detail_id` = D.`id`
            LEFT JOIN `sales` AS SH ON SH.`id` = S.`transaction_id` AND SH.`deleted_at` IS NULL
            INNER JOIN `product` AS P ON P.`id` = D.`product_id`
            WHERE D.`transaction_id` = '.$id.'
            GROUP BY D.`id`'
        );

        return successfulResponse([
            'transaction' => $transaction,
            'details'     => $this->tabulatorCompatible($details)
        ]);
    }
}