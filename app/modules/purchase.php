<?php

class Purchase
{
    public function index() {
        view('purchase/list.php');
    }

    public function create() {
        view('purchase/detail.php');
    }

    public function find() {

        $data = getData(
            'SELECT
                `date`,
                `memo`,
                `status`
            FROM `purchase`
            WHERE
                `status` = 1'
        );
        if (count($data) > 0) {
            return successfulResponse($data);
        }

        return errorResponse(['No results found.']);
    }

}