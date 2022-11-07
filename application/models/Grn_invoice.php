<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 23/02/2018
 * Time: 13:09
 */
class Grn_invoice extends MY_Model
{

    const DB_TABLE = 'grn_invoices';
    const DB_TABLE_PK = 'id';

    public $grn_id;
    public $invoice_id;

    public function invoice()
    {
        $this->load->model('invoice');
        $invoice = new Invoice();
        $invoice->load($this->invoice_id);
        return $invoice;
    }

    public function grn()
    {
        $this->load->model('goods_received_note');
        $good_received_note = new Goods_received_note();
        $good_received_note->load($this->grn_id);
        return $good_received_note;
    }


}

