<?php

require 'invoice.php';

class Sales extends Invoice
{
    protected $table            = 'sales';
    protected $tableDetail      = 'sales_detail';
    protected $detailIdentifier = 'purchase_detail_id';

    public function index()
    {
        view('sales/list.php');
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

        $filter = cancelIfEmpty($invoiceNumber, ' AND H.`invoice_number` LIKE \'%'.$invoiceNumber.'%\'');
        $filter .= cancelIfEmpty(
            $status,
            $status == 1 ? ' AND H.`returned_at` IS NULL' : ' AND H.`returned_at` IS NOT NULL'
        );
        $filter .= cancelIfEmpty($dateFrom, ' AND H.`transaction_date` >= \''.$dateFrom.' 00:00:00\'');
        $filter .= cancelIfEmpty($dateTo, ' AND H.`transaction_date` <= \''.$dateTo.' 23:59:59\'');

        $data = getData(
            'SELECT
                H.`id`,
                H.`invoice_number`,
                DATE(H.`transaction_date`) AS `transaction_date`,
                H.`memo`,
                SUM(D.`qty`) AS `quantity`,
                SUM(D.`qty` * D.`selling_price`) AS `amount`,
                IF (H.`returned_at` IS NULL, \'Sold\', \'RTS\') AS `status`
            FROM `sales` AS H
            LEFT JOIN `sales_detail` AS D ON D.`transaction_id` = H.`id`
            WHERE H.`deleted_at` IS NULL
            '.$filter.'
            GROUP BY H.`id`'
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
            'transaction_id'   => '',
            'transaction_date' => getDateToday(),
            'memo'             => '',
            'returned_at'      => null,
        ];

        view('sales/detail.php', $data);
    }

    public function save()
    {
        $returnedAt = post('returned_at');
        if (trim($returnedAt) == '') {
            $returnedAt = null;
        }

        $head = [
            'id'               => post('id'),
            'invoice_number'   => post('invoice_number'),
            'transaction_id'   => post('transaction_id'),
            'transaction_date' => post('transaction_date'),
            'memo'             => post('memo'),
            'returned_at'      => $returnedAt
        ];

        if (!$this->isValidInvoice($head['invoice_number'], $head['id']) && $head['id'] != 0) {
            return errorResponse(['Invalid invoice number or already exists.']);
        }

        if (!isValidDate($head['transaction_date'])) {
            return errorResponse(['Invalid date input']);
        }

        if (!isValidDate($head['returned_at'], true)) {
            return errorResponse(['Invalid date returned input.']);
        }

        $details = [];
        foreach (post('detail') as $key => $value) {
            $details[] = [
                'id'                 => $value['detail_id'],
                'purchase_detail_id' => $value['purchase_detail_id'],
                'qty'                => toNumber($value['quantity']),
                'selling_price'      => toNumber($value['selling_price']),
                'remark'             => $value['remark'],
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
                `transaction_id`,
                '.dateOnlySql('transaction_date').',
                '.dateOnlySql('returned_at').',
                `memo`
            FROM `sales`
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
            'transaction_id'   => $head[0]['transaction_id'],
            'transaction_date' => $head[0]['transaction_date'],
            'memo'             => $head[0]['memo'],
            'returned_at'      => $head[0]['returned_at'],
        ];

        view('sales/detail.php', $data);
    }

    public function details()
    {
        $id = get('id');
        if (!is_numeric($id)) {
            $id = 0;
        }

        $data = getData(
            'SELECT COALESCE(returned_at, 0) AS returned_at FROM `sales` WHERE id = '.$id
        );

        $transaction = [
            'is_returned' => true
        ];

        if (count($data) > 0) {
            $transaction['is_returned'] = $data[0]['returned_at'] == 0;
        }

        $details = getData(
            'SELECT
                SD.`id` AS `detail_id`,
                SD.`purchase_detail_id`,
                PH.`invoice_number` AS `purchase_invoice_number`,
                P.`stock_no`,
                P.`short_name`,
                '.roundNumberSql('SD.`selling_price`', 'selling_price').',
                '.roundNumberSql('SD.`qty`', 'quantity').',
                '.roundNumberSql('SD.`qty`', 'available_quantity').',
                '.roundNumberSql('SD.qty * SD.`selling_price`', 'amount').',
                SD.`remark`
            FROM `sales_detail` AS SD
            INNER JOIN `purchase_detail` AS PD ON PD.`id` = SD.`purchase_detail_id`
            INNER JOIN `purchase` AS PH ON PH.`id` = PD.`transaction_id`
            INNER JOIN `product` AS P ON P.`id` = PD.`product_id`
            WHERE SD.`transaction_id` = '.$id
        );

        return successfulResponse([
            'transaction' => $transaction,
            'details'     => $this->tabulatorCompatible($details)
        ]);
    }
}