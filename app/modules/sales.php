<?php

require 'invoice.php';

class Sales extends Invoice
{
    protected $table       = 'purchase';
    protected $tableDetail = 'purchase_detail';

    public function index()
    {
        view('sales/list.php');
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
}