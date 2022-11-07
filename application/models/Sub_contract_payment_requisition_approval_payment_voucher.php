<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/26/2018
 * Time: 2:14 PM
 */

class Sub_contract_payment_requisition_approval_payment_voucher extends MY_Model{
    const DB_TABLE = 'sub_contract_payment_requisition_approval_payment_vouchers';
    const DB_TABLE_PK = 'id';

    public $sub_contract_payment_requisition_approval_id;
    public $payment_voucher_id;

    public function payment_voucher()
    {
        $this->load->model('payment_voucher');
        $payment_voucher = new Payment_voucher();
        $payment_voucher->load($this->payment_voucher_id);
        return $payment_voucher;
    }

    public function sub_contract_payment_requisition_approval()
    {
        $this->load->model('sub_contract_payment_requisition_approval');
        $approval = new Sub_contract_payment_requisition_approval();
        $approval->load($this->sub_contract_payment_requisition_approval_id);
        return $approval;
    }
}