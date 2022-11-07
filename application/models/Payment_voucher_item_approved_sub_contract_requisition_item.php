<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/26/2018
 * Time: 2:22 PM
 */

class Payment_voucher_item_approved_sub_contract_requisition_item extends MY_Model{
    const DB_TABLE = 'payment_voucher_item_approved_sub_contract_requisition_items';
    const DB_TABLE_PK = 'id';

    public $payment_voucher_item_id;
    public $sub_contract_payment_requisition_approval_item_id;


    public function sub_contract_payment_requisition_approval_item()
    {
        $this->load->model('sub_contract_payment_requisition_approval_item');
        $approved_item = new Sub_contract_payment_requisition_approval_item();
        $approved_item->load($this->sub_contract_payment_requisition_approval_item_id);
        return $approved_item;
    }

    public function payment_voucher_item()
    {
        $this->load->model('payment_voucher_item');
        $payment_voucher_item = new Payment_voucher_item();
        $payment_voucher_item->load($this->payment_voucher_item_id);
        return $payment_voucher_item;
    }
}