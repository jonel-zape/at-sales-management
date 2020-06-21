<?php

require 'invoice.php';
require 'enums.php';

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

        $filter = cancelIfEmpty($invoiceNumber, ' AND (H.`invoice_number` LIKE \'%'.$invoiceNumber.'%\' OR H.`transaction_id` LIKE \'%'.$invoiceNumber.'%\')');

        $filter .= cancelIfEmpty(
            $status,
            $status == 1 ? ' AND H.`returned_at` IS NULL' : ' AND H.`returned_at` IS NOT NULL'
        );
        $filter .= cancelIfEmpty($dateFrom, ' AND H.`transaction_date` >= \''.$dateFrom.' 00:00:00\'');
        $filter .= cancelIfEmpty($dateTo, ' AND H.`transaction_date` <= \''.$dateTo.' 23:59:59\'');

        $data = getData(
            'SELECT
                H.`id`,
                H.`transaction_id`,
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
            GROUP BY H.`id` ORDER BY H.id DESC'
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

        if (!is_array(post('detail')) || count(post('detail')) < 1) {
            return errorResponse(['Can not save emtpy detail.']);
        }

        $details = [];
        foreach (post('detail') as $key => $value) {
            $detail = [
                'id'                 => $value['detail_id'],
                'purchase_detail_id' => $value['purchase_detail_id'],
                'qty'                => toNumber($value['quantity']),
                'selling_price'      => toNumber($value['selling_price']),
                'remark'             => $value['remark'],
            ];

            if ($returnedAt != null) {
                $detail['qty_damage'] = toNumber($value['qty_damage']);
            }

            $details[] = $detail;
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
            'is_returned'  => true,
            'status'       => SALES_NEW,
            'status_class' => 'new'
        ];

        if (count($data) > 0) {
            $transaction['is_returned'] = $data[0]['returned_at'] != 0;

            $transaction['status']       = SALES_SOLD;
            $transaction['status_class'] = 'sold';

            if ($transaction['is_returned']) {
                $transaction['status']       = SALES_RETURNED_TO_SELLER;
                $transaction['status_class'] = 'rts';
            }
        }

        $details = getData(
            'SELECT
                detail_id,
                short_name,
                stock_no,
                purchase_invoice_number,
                purchase_id,
                transaction_id,
                purchase_detail_id,
                qty_damage,
                quantity,
                max_quantity,
                IF (is_returned = 1, available_quantity + quantity, available_quantity) AS available_quantity,
                selling_price,
                amount,
                remark
            FROM (
                SELECT
                    SD.`id` AS `detail_id`,
                    P.`short_name`,
                    P.`stock_no`,
                    PH.`invoice_number` AS `purchase_invoice_number`,
                    PH.`id` AS purchase_id,
                    SD.`transaction_id`,
                    SD.`purchase_detail_id`,
                    '.roundNumberSql('SD.`qty_damage`', 'qty_damage').',
                    '.roundNumberSql('SD.`qty`', 'quantity').',
                    '.roundNumberSql('(PD.`qty` - SD.`qty`) - SUM(IF(S.`id` IS NULL, 0, COALESCE(SD1.`qty`, 0))) + SD.`qty`', 'max_quantity').',
                    '.roundNumberSql('(PD.`qty` - SD.`qty`) - SUM(IF(S.`id` IS NULL, 0, COALESCE(SD1.`qty`, 0)))', 'available_quantity').',
                    '.roundNumberSql('SD.`selling_price`', 'selling_price').',
                    '.roundNumberSql('SD.qty * SD.`selling_price`', 'amount').',
                    SD.`remark`,
                    IF (S1.`returned_at` IS NOT NULL, 1, 0) AS `is_returned`
                FROM `sales_detail` AS SD
                INNER JOIN `purchase_detail` AS PD ON SD.`purchase_detail_id` = PD.`id`
                INNER JOIN `purchase` AS PH ON PH.`id` = PD.`transaction_id`
                LEFT JOIN `product` AS P ON P.`id` = PD.`product_id`
                LEFT JOIN `sales_detail` AS SD1 ON SD1.`purchase_detail_id` = PD.`id` AND SD1.`transaction_id` <> '.$id.'
                LEFT JOIN `sales` AS S ON S.`id` = SD1.`transaction_id` AND S.`returned_at` IS NULL AND S.`deleted_at` IS NULL
                LEFT JOIN `sales` AS S1 ON S1.id = SD.transaction_id
                WHERE SD.`transaction_id` = '.$id.'
                GROUP BY SD.`id`
            ) AS A'
        );

        return successfulResponse([
            'transaction' => $transaction,
            'details'     => $this->tabulatorCompatible($details)
        ]);
    }
}