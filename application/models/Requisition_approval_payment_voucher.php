<?php

class Requisition_approval_payment_voucher extends MY_Model{
    
    const DB_TABLE = 'requisition_approval_payment_vouchers';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_id;
    public $payment_voucher_id;

    public function payment_voucher()
    {
        $this->load->model('Payment_voucher');
        $payment_voucher = new Payment_voucher();
        $payment_voucher->load($this->payment_voucher_id);
        return $payment_voucher;
    }

    public function requisition_approval()
    {
        $this->load->model('requisition_approval');
        $requisition_approval = new Requisition_approval();
        $requisition_approval->load($this->requisition_approval_id);
        return $requisition_approval;
    }

}
