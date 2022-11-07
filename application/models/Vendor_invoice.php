<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 21/02/2018
 * Time: 15:21
 */
class Vendor_invoice extends MY_Model
{

    const DB_TABLE = 'vendor_invoices';
    const DB_TABLE_PK = 'id';

    public $invoice_id;
    public $vendor_id;

    public function vendor()
    {
        $this->load->model('vendor');
        $vendor = new Vendor();
        $vendor->load($this->vendor_id);
        return $vendor;
    }

    public function invoice()
    {
        $this->load->model('invoice');
        $invoice = new Invoice();
        $invoice->load($this->invoice_id);
        return $invoice;
    }


}

