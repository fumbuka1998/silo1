<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/18/2018
 * Time: 7:00 PM
 */

class Invoice_payment_voucher extends MY_Model{
    const DB_TABLE = 'invoice_payment_vouchers';
    const DB_TABLE_PK = 'id';

    public $invoice_id ;
    public $payment_voucher_id;

    public function payment_voucher()
    {
        $this->load->model('payment_voucher');
        $payment_voucher = new Payment_voucher();
        $payment_voucher->load($this->payment_voucher_id);
        return $payment_voucher;
    }

    public function invoice()
    {
        $this->load->model('invoice');
        $invoice = new invoice();
        $invoice->load($this->invoice_id);
        return $invoice;
    }
}