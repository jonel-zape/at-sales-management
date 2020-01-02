<?php

abstract class Invoice
{
    protected $table       = '';
    protected $tableDetail = '';

    protected function getNewInvoice()
    {
        $format = $this->getInvoicePrefix().'000000000000000';

        $invoice = getData("SELECT COALESCE(MAX(`id`), '".$format."') AS 'invoice' FROM ".$this->table);
        $invoice = $invoice[0]['invoice'] + 1;

        return $this->getInvoicePrefix().sprintf("%015d", $invoice);
    }

    protected function isValidInvoice($value, $id = 0)
    {
        $value = escapeString(trim($value));

        if ($value == '') {
            return false;
        }

        $withId = '';
        if ($id != 0) {
            $withId = 'AND `id` <> '.$id;
        }

        $data = getData(
            'SELECT
                COUNT(`id`) AS `count`
            FROM '.$this->table.'
            WHERE
                `deleted_at` IS NULL
                '.$withId.'
                AND `invoice_number` =\''.$value.'\''
        );

        return $data[0]['count'] == 0;
    }

    protected function insertOrUpdate($head, $details)
    {
        $escapedHead = escapeString($head);

        if ($escapedHead['id'] == 0) {
            $escapedHead['invoice_number'] = $this->getNewInvoice();
            executeQuery($this->generateInsertQuery($escapedHead));
        } else {
            executeQuery($this->generateUpdateQuery($escapedHead));
        }

        $data = getData(
            'SELECT `id` FROM '.$this->table.' WHERE `invoice_number` = \''.$escapedHead['invoice_number'].'\''
        );

        $transactionId = $data[0]['id'];

        if ($escapedHead['id'] == 0) {
            $inserts = [];
            foreach ($details as $key => $value) {
                if (isset($value['product_id']) && $value['product_id'] == 0) {
                    continue;
                }
                $value['transaction_id'] = $transactionId;
                $inserts[] = $this->generateInsertDetailQuery($value);
            }
            executeQuery($inserts);
        } else {

            $ids = [];
            $inserts = [];
            $updades = [];
            foreach ($details as $key => $value) {
                if (isset($value['product_id']) && $value['product_id'] == 0) {
                    continue;
                }

                $ids[] = $value['id'];

                if ($value['id'] == 0) {
                    $value['transaction_id'] = $transactionId;
                    $inserts[] = $this->generateInsertDetailQuery($value);
                } else {
                    $updades[] = $this->generateUpdateDetailQuery($value);
                }
            }

            // Deletes
            $data = getData(
                'SELECT
                    `id`
                FROM '.$this->tableDetail.'
                WHERE
                    `transaction_id` = '.$transactionId.'
                    AND `id` NOT IN ('.implode($ids, ',').')');

            $deletes = [];
            foreach ($data as $key => $value) {
                $deletes[] = $value['id'];
            }
            if (count($deletes) > 0) {
                executeQuery('DELETE FROM '.$this->tableDetail.' WHERE `id` IN ('.implode($deletes, ',').')');
            }

            // Insert
            if (count($inserts) > 0) {
                executeQuery($inserts);
            }

            // Update
            if (count($updades) > 0) {
                executeQuery($updades);
            }

        }

        return $transactionId;
    }

    protected function tabulatorCompatible($data)
    {
        $result = [];
        $index= 0;
        foreach ($data as $key => $value) {
            $value['id'] = $index;
            $result[] = $value;
            $index++;
        }

        return $result;
    }

    private function generateInsertQuery($data)
    {
        $columns = '';
        $values = '';

        foreach ($data as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $columns .= ','.$key;
            $values .= is_null($value) ? ',null' : ',\''.trim($value).'\'';
        }

        return 'INSERT INTO '.$this->table.'('.substr($columns, 1).') VALUES ('.substr($values, 1).')';
    }

    private function generateUpdateQuery($data)
    {
        $id = $data['id'];
        $columnsAndValues = '';

        foreach ($data as $key => $value) {
            if ($key == 'id') {
                continue;
            }

            if (is_null($value)) {
                $columnsAndValues .= ','.$key.' = null';
            } else {
                $columnsAndValues .= ','.$key.' = \''.trim($value).'\'';
            }
        }

        return 'UPDATE '.$this->table.' SET '.substr($columnsAndValues, 1).' WHERE id = '.$id;
    }

    private function generateInsertDetailQuery($data)
    {
        $columns = '';
        $values = '';

        foreach ($data as $key => $value) {
            if ($key == 'id') {
                continue;
            }

            $columns .= ','.$key;
            $values .= is_null($value) ? ',null' : ',\''.trim($value).'\'';
        }

        return 'INSERT INTO '.$this->tableDetail.'('.substr($columns, 1).') VALUES ('.substr($values, 1).')';
    }

    private function generateUpdateDetailQuery($data)
    {
        $id = $data['id'];
        $columnsAndValues = '';
        foreach ($data as $key => $value) {
            if ($key == 'id') {
                continue;
            }

            if (is_null($value)) {
                $columnsAndValues .= ','.$key.' = null';
            } else {
                $columnsAndValues .= ','.$key.' = \''.trim($value).'\'';
            }
        }

        return 'UPDATE '.$this->tableDetail.' SET '.substr($columnsAndValues, 1).' WHERE id = '.$id;
    }

    private function getInvoicePrefix()
    {
        switch ($this->table) {
            case 'purchase':
                return 'P';
                break;
        }

        return '';
    }
}
