<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 08/06/2018
 * Time: 09:11
 */
class Purchase_order_payment_request_approval_payment_voucher extends MY_Model
{

    const DB_TABLE = 'purchase_order_payment_request_approval_payment_vouchers';
    const DB_TABLE_PK = 'id';

    public $payment_voucher_id;
    public $purchase_order_payment_request_approval_id;

    public function payment_voucher()
    {
        $this->load->model('payment_voucher');
        $payment_voucher = new Payment_voucher();
        $payment_voucher->load($this->payment_voucher_id);
        return $payment_voucher;
    }

    public function purchase_order_payment_request_approval()
    {
        $this->load->model('purchase_order_payment_request_approval');
        $purchase_order_payment_request_approval = new Purchase_order_payment_request_approval();
        $purchase_order_payment_request_approval->load($this->purchase_order_payment_request_approval_id);
        return $purchase_order_payment_request_approval;
    }


}

