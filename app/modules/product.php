<?php

class Product
{
    public function index()
    {
        view('product/list.php');
    }

    public function create()
    {
        view('product/detail.php');
    }

    public function edit($id)
    {
        view('product/detail.php');
    }

    public function find()
    {
        $request = escapeString([
            'keyword' => get('keyword'),
            'filterBy' => get('filterBy')
        ]);

        $keyword = $request['keyword'];
        $filterBy = $request['filterBy'];

        $filter = ' AND (`name` LIKE \'%'.$keyword.'%\' OR `memo` LIKE \'%'.$keyword.'%\')';
        switch ($filterBy) {
            case 1:
                $filter = ' AND `name` LIKE \'%'.$keyword.'%\'';
                break;
            case 2:
                $filter = ' AND `memo` LIKE \'%'.$keyword.'%\'';
                break;
        }

        $data = getData(
            'SELECT
                `stock_no`,
                `name`,
                `short_name`,
                `cost_price`,
                `selling_price`,
                `wholesale_price`,
                `memo`
            FROM `product`
            WHERE
                `status` = 1
                AND `deleted_at` IS NULL'
                .$filter
        );
        if (count($data) > 0) {
            return successfulResponse($data);
        }

        return errorResponse(['No results found.']);
    }
}
