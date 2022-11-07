<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 27/05/2018
 * Time: 11:35
 */
class Purchase_order_invoice extends MY_Model
{

    const DB_TABLE = 'purchase_order_invoices';
    const DB_TABLE_PK = 'id';

    public $invoice_id;
    public $purchase_order_id;

    public function invoice()
    {
        $this->load->model('invoice');
        $invoice = new Invoice();
        $invoice->load($this->invoice_id);
        return $invoice;
    }

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Purchase_order();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }


}

