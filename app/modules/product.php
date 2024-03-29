<?php

class Product
{
    public function index()
    {
        view('product/list.php');
    }

    public function create()
    {
        $detail = [
            'id'              => 0,
            'stock_no'        => '',
            'name'            => '',
            'short_name'      => '',
            'cost_price'      => '',
            'selling_price'   => '',
            'wholesale_price' => '',
            'memo'            => ''
        ];

        view('product/detail.php', $detail);
    }

    public function edit($id)
    {
        if (!is_numeric($id)) {
            $id = 0;
        }

        $data = getData('
            SELECT
                `id`,
                `stock_no`,
                `name`,
                `short_name`,
                `cost_price`,
                `selling_price`,
                `wholesale_price`,
                `memo`
            FROM `product`
            WHERE `id` = '.$id.'
        ');

        if (count($data) < 1) {
            view('404.php');
            return;
        }

        $detail = [
            'id'              => $data[0]['id'],
            'stock_no'        => $data[0]['stock_no'],
            'name'            => $data[0]['name'],
            'short_name'      => $data[0]['short_name'],
            'cost_price'      => formatNumber($data[0]['cost_price']),
            'selling_price'   => formatNumber($data[0]['selling_price']),
            'wholesale_price' => formatNumber($data[0]['wholesale_price']),
            'memo'            => $data[0]['memo']
        ];

        view('product/detail.php', $detail);
    }

    public function save()
    {
        $request = escapeString([
            'id'              => post('id'),
            'stock_no'        => post('stock_no'),
            'name'            => post('name'),
            'short_name'      => post('short_name'),
            'memo'            => post('memo'),
            'cost_price'      => post('cost_price'),
            'selling_price'   => post('selling_price'),
            'wholesale_price' => post('wholesale_price'),
        ]);

        $id             = trim($request['id']);
        $stockNo        = trim($request['stock_no']);
        $name           = trim($request['name']);
        $shortName      = trim($request['short_name']);
        $memo           = trim($request['memo']);
        $costPrice      = trim($request['cost_price']);
        $sellingPrice   = trim($request['selling_price']);
        $wholesalePrice = trim($request['wholesale_price']);

        $errors = [];

        if ($this->isFieldExists('stock_no', $stockNo, $id)) {
            $errors[] = 'Stock No. already exists.';
        }

        if ($this->isFieldExists('name', $name, $id)) {
            $errors[] = 'Name already exists.';
        }

        if ($this->isFieldExists('short_name', $shortName, $id)) {
            $errors[] = 'Short name already exists.';
        }

        if (strlen($stockNo) < 1) {
            $errors[] = 'Stock No. is required.';
        }

        if (strlen($name) < 1) {
            $errors[] = 'Name is required.';
        }

        if (strlen($shortName) < 1) {
            $errors[] = 'Short name is required.';
        }

        if (!is_numeric($costPrice)) {
            $errors[] = 'Invalid value for cost price.';
        }

        if (!is_numeric($sellingPrice)) {
            $errors[] = 'Invalid value for selling price.';
        }

        if (!is_numeric($wholesalePrice)) {
            $errors[] = 'Invalid value for wholesale price.';
        }

        if (count($errors) > 0) {
            return errorResponse($errors);
        }

        if ($id == 0) {
            executeQuery(
                'INSERT INTO `product` (
                    `stock_no`,
                    `name`,
                    `short_name`,
                    `cost_price`,
                    `selling_price`,
                    `wholesale_price`,
                    `memo`,
                    `status`,
                    `created_by`,
                    `created_at`,
                    `updated_by`,
                    `updated_at`
                ) VALUES (
                    \''.$stockNo.'\',
                    \''.$name.'\',
                    \''.$shortName.'\',
                    '.$costPrice.',
                    '.$sellingPrice.',
                    '.$wholesalePrice.',
                    \''.$memo.'\',
                    1,
                    1,
                    NOW(),
                    1,
                    NOW()
                )
            ');

            $newProduct = getData('SELECT MAX(id) AS id FROM product');
            $id = $newProduct[0]['id'];
        } else {
            executeQuery(
                'UPDATE `product`
                SET
                    `stock_no` = \''.$stockNo.'\',
                    `name` = \''.$name.'\',
                    `short_name` = \''.$shortName.'\',
                    `cost_price` = '.$costPrice.',
                    `selling_price` = '.$sellingPrice.',
                    `wholesale_price` = '.$wholesalePrice.',
                    `memo` = \''.$memo.'\',
                    `status` = 1,
                    `updated_by` = 1,
                    `updated_at` = NOW()
                WHERE `id` = '.$id
            );
        }

        return successfulResponse(['id' => $id]);
    }

    private function isFieldExists($column, $value, $id)
    {
        $value = '\''.$value.'\'';

        $createOrEditFilter = '';
        if ($id != 0) {
            $createOrEditFilter = ' AND `id` <> '.$id;
        }

        $data = getData(
            'SELECT
                `id`
            FROM `product`
            WHERE
                `deleted_at` IS NULL
                AND `'.$column.'` = '.$value.$createOrEditFilter
        );

        return count($data) > 0;
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
                `id`,
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

    public function delete()
    {
        $request = escapeString([
            'id' => get('id'),
        ]);

        $id = $request['id'];

        executeQuery(
            'UPDATE `product`
            SET
                `deleted_at` = NOW(),
                `updated_at` = NOW()
            WHERE `id` = '.$id
        );

        return successfulResponse('Deleted');
    }

    public function autonCompleteSearch()
    {
        $request = escapeString([
            'keyword' => get('keyword'),
            'field' => get('field')
        ]);

        $validFields = ['stock_no', 'name', 'short_name'];

        $keyword = $request['keyword'];
        $field = $request['field'];

        if (!in_array($field, $validFields)) {
            return errorResponse(['No results found.']);
        }

        $filter = $field.' LIKE \'%'.$keyword.'%\'';

        $data = getData(
            'SELECT
                `id` AS `product_id`,
                `stock_no`,
                `name`,
                `short_name`,
                '.roundNumberSql('cost_price').',
                '.roundNumberSql('selling_price').',
                '.roundNumberSql('wholesale_price').',
                `memo`
            FROM `product`
            WHERE
                `status` = 1
                AND `deleted_at` IS NULL
                AND '.$filter.'
            LIMIT 10'
        );

        if (count($data) > 0) {
            return successfulResponse($data);
        }

        return errorResponse(['No results found.']);
    }

    public function receivedAutoCompleteSearch()
    {
        $request = escapeString([
            'keyword' => get('keyword'),
            'field' => get('field')
        ]);

        $validFields = ['stock_no', 'name', 'short_name'];

        $keyword = $request['keyword'];
        $field = $request['field'];

        if (!in_array($field, $validFields)) {
            return errorResponse(['No results found.']);
        }

        $field = 'P.'.$field;
        $filter = $field.' LIKE \'%'.$keyword.'%\'';

        $display = 'CONCAT('.$field.', \' - \', PH.`invoice_number`) AS `display`';

        $data = getData(
            'SELECT 
                P.`id` AS `product_id`,
                P.`stock_no`,
                P.`name`,
                P.`short_name`,
                '.roundNumberSql('P.cost_price', 'cost_price').',
                '.roundNumberSql('P.selling_price', 'selling_price').',
                '.roundNumberSql('P.wholesale_price', 'wholesale_price').',
                P.`memo`,
                PD.`id` AS `purchase_detail_id`,
                '.roundNumberSql('SUM(PD.`qty`)', 'available_quantity').',
                PH.`invoice_number` AS `purchase_invoice_number`,
                '.$display.'
            FROM `product` AS P
            INNER JOIN `purchase_detail` AS PD ON PD.`product_id` = P.`id`
            INNER JOIN `purchase` AS PH ON
                PH.`id` = PD.`transaction_id` 
                AND PH.`received_at` IS NOT NULL
                AND PH.`deleted_at` IS NULL
            LEFT JOIN `sales_detail` AS SD ON SD.`purchase_detail_id` = PD.`id`
            LEFT JOIN `sales` AS SH ON
                SH.`id` = SD.`transaction_id`
                AND SH.`returned_at` IS NOT NULL
                AND SH.`deleted_at` IS NOT NULL
            WHERE
                P.`status` = 1
                AND P.`deleted_at` IS NULL
                AND '.$filter.'
            LIMIT 10'
        );

        if (count($data) > 0) {
            return successfulResponse($data);
        }

        return errorResponse(['No results found.']);
    }
}
