<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 15/10/2018
 * Time: 14:25
 */

class Payment_voucher_item_approved_invoice_item extends MY_Model{
    const DB_TABLE = 'payment_voucher_item_approved_invoice_items';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_approval_invoice_item_id;
    public $payment_voucher_item_id;


    public function purchase_order_payment_request_approval_invoice_item()
    {
        $this->load->model('purchase_order_payment_request_approval_invoice_item');
        $purchase_order_payment_request_approval_invoice_item = new Purchase_order_payment_request_approval_invoice_item();
        $purchase_order_payment_request_approval_invoice_item->load($this->purchase_order_payment_request_approval_invoice_item_id);
        return $purchase_order_payment_request_approval_invoice_item;
    }

    public function payment_voucher_item()
    {
        $this->load->model('payment_voucher_item');
        $payment_voucher_item = new Payment_voucher_item();
        $payment_voucher_item->load($this->payment_voucher_item_id);
        return $payment_voucher_item;
    }

}