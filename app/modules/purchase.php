<?php

require 'invoice.php';
require 'enums.php';

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
                H.`id`,
                H.`invoice_number`,
                H.`transaction_date`,
                H.`memo`,
                SUM(H.`quantity`) AS `quantity`,
                SUM(H.`amount`) AS `amount`,
                H.`status`,
                SUM(H.`quantity`) - (SUM(H.`sold_quantity`) + SUM(H.`damaged_quantity`)) AS `remaining_quantity`
            FROM
            (
                SELECT
                    P.`id`,
                    P.`invoice_number`,
                    DATE(P.`transaction_date`) AS `transaction_date`,
                    P.`memo`,
                    COALESCE(D.`qty`, 0) AS `quantity`,
                    COALESCE(D.`qty`, 0) * COALESCE(D.`cost_price`, 0) AS `amount`,
                    IF (P.`received_at` IS NULL, \'Uncreceived\', \'Received\') AS `status`,
                    SUM(IF(S.`deleted_at` IS NULL AND S.returned_at IS NULL, COALESCE(SD.`qty`, 0), 0)) AS `sold_quantity`,
                    SUM(IF(S.`deleted_at` IS NULL AND S.returned_at IS NOT NULL, COALESCE(SD.`qty_damage`, 0), 0)) AS `damaged_quantity`
                FROM `purchase` AS P 
                LEFT JOIN `purchase_detail` AS D ON D.`transaction_id` = P.`id`
                LEFT JOIN `sales_detail` AS SD ON SD.`purchase_detail_id` = D.`id`
                LEFT JOIN `sales` AS S ON S.`id` = SD.`transaction_id`
                WHERE P.`deleted_at` IS NULL
                '.$filter.'
                GROUP BY D.`id`
            ) AS H
            GROUP BY H.id'
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
        $this->validateSold($id);

        return successfulResponse(['id' => $id]);
    }

    private function validateSold($id)
    {

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

        $filterSellable = get('filterSellable');
        if ($filterSellable != null) {
            $filterSellable = 'HAVING remaining_qty > 0';
        }

        $data = getData(
            'SELECT COALESCE(DATE(received_at), 0) AS received_at, `memo`, `invoice_number` FROM `purchase` WHERE id = '.$id
        );

        $transaction = [
            'is_received'    => false,
            'received_at'    => '',
            'memo'           => '',
            'invoice_number' => '',
            'status'         => PURCHASE_NEW,
            'status_class'   => 'new'
        ];

        if (count($data) > 0) {
            $transaction['is_received']    = $data[0]['received_at'] != 0;
            $transaction['received_at']    = $data[0]['received_at'];
            $transaction['memo']           = $data[0]['memo'];
            $transaction['invoice_number'] = $data[0]['invoice_number'];
            $transaction['status']       = $data[0]['received_at'] == 0 ? PURCHASE_UNRECEIVED : PURCHASE_RECEIVED;
            $transaction['status_class'] = $data[0]['received_at'] == 0 ? 'unreceived' : 'received';
        }

        $details = getData('
            SELECT
                PR.`invoice_number`,
                PR.`detail_id`,
                '.roundNumberSql('PR.`qty`', 'quantity').',
                '.roundNumberSql('PR.`amount`', 'amount').',
                '.roundNumberSql('PR.`sold`', 'sold').',
                '.roundNumberSql('PR.`rts`', 'rts').',
                '.roundNumberSql('PR.`qty_damage`', 'qty_damage').',
                '.roundNumberSql('PR.`qty` - (PR.`sold` + PR.`qty_damage`)', 'remaining_qty').',
                '.roundNumberSql('PR.`sold`', 'min_quantity').',
                '.roundNumberSql('PR.`qty_damage` * PR.`cost_price`', 'lost_amount').',
                '.roundNumberSql('PR.`cost_price`', 'cost_price').',
                '.roundNumberSql('PR.`qty` * PR.`selling_price`', 'selling_amount').',
                1 AS `input_qty`,
                PR.`remark`,
                PR.`product_id`,
                PR.`stock_no`,
                PR.`name`,
                PR.`short_name`,
                PR.`memo`,
                '.roundNumberSql('PR.`selling_price`', 'selling_price').'
            FROM(
                SELECT
                    P.`invoice_number`,
                    PD.`id` AS `detail_id`,
                    PD.`qty`,
                    PD.`qty` * PD.`cost_price` AS `amount`,
                    SUM(COALESCE(IF(S.`returned_at` IS NULL, SD.`qty` , 0), 0)) AS `sold`,
                    SUM(COALESCE(IF(S.`returned_at` IS NOT NULL, SD.`qty` , 0), 0)) AS `rts`,
                    SUM(COALESCE(IF(S.`returned_at` IS NOT NULL, SD.`qty_damage` , 0), 0)) AS `qty_damage`,
                    PD.`cost_price`,
                    PD.`remark`,
                    PD.`product_id`,
                    PRDCT.`stock_no`,
                    PRDCT.`name`,
                    PRDCT.`short_name`,
                    PRDCT.`memo`,
                    PRDCT.`selling_price`
                FROM `purchase_detail` AS PD
                INNER JOIN `purchase` AS P ON P.`id` = PD.`transaction_id`
                LEFT JOIN `sales_detail` AS SD ON SD.`purchase_detail_id` = PD.`id`
                LEFT JOIN `sales` AS S ON S.`id` = SD.`transaction_id` AND S.`deleted_at` IS NULL
                INNER JOIN `product` AS PRDCT ON PRDCT.`id` = PD.`product_id`
                WHERE PD.`transaction_id` = '.$id.'
                GROUP BY PD.`id`
            ) AS PR '.$filterSellable);

        return successfulResponse([
            'transaction' => $transaction,
            'details'     => $this->tabulatorCompatible($details)
        ]);
    }

    public function autonCompleteSearchInvoice()
    {
        $request = escapeString([
            'keyword' => get('keyword'),
        ]);

        $keyword = $request['keyword'];

        $data = getData(
            'SELECT
                H.`id`,
                H.`invoice_number`,
                SUM(H.`quantity`) - (SUM(H.`sold_quantity`) + SUM(H.`damaged_quantity`)) AS `remaining_quantity`
            FROM
            (
                SELECT
                    P.`id`,
                    P.`invoice_number`,
                    COALESCE(D.`qty`, 0) AS `quantity`,
                    SUM(IF(S.`deleted_at` IS NULL AND S.returned_at IS NULL, COALESCE(SD.`qty`, 0), 0)) AS `sold_quantity`,
                    SUM(IF(S.`deleted_at` IS NULL AND S.returned_at IS NOT NULL, COALESCE(SD.`qty_damage`, 0), 0)) AS `damaged_quantity`
                FROM `purchase` AS P 
                LEFT JOIN `purchase_detail` AS D ON D.`transaction_id` = P.`id`
                LEFT JOIN `sales_detail` AS SD ON SD.`purchase_detail_id` = D.`id`
                LEFT JOIN `sales` AS S ON S.`id` = SD.`transaction_id`
                WHERE P.`deleted_at` IS NULL AND P.`received_at` IS NOT NULL
                AND P.`invoice_number` LIKE \'%'.$keyword.'%\'
                GROUP BY D.`id`
            ) AS H
            GROUP BY H.id
            HAVING remaining_quantity > 0
            LIMIT 10'
        );

        if (count($data) > 0) {
            return successfulResponse($data);
        }

        return successfulResponse([['id' => 0, 'invoice_number' => 'No results found for \''.$keyword.'\'']]);
    }
}