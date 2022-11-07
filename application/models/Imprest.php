<?php

class Imprest extends MY_Model{
    
    const DB_TABLE = 'imprests';
    const DB_TABLE_PK = 'id';

    public $issue_date;
    public $payment_voucher_id;
    public $remarks;
    public $created_by;

    public function payment_voucher()
    {
        $this->load->model('payment_voucher');
        $payment_voucher = new Payment_voucher();
        $payment_voucher->load($this->payment_voucher_id);
        return $payment_voucher;
    }

    public function requisition_approval(){
        return $this->payment_voucher()->requisition_approval_payment_voucher()->requisition_approval();
    }

    public function requisition(){
        return $this->requisition_approval()->requisition();
    }

    public function cost_center_name(){
        return $this->requisition()->cost_center_name();
    }

    public function add_grn_junction($grn_id){
        $this->load->model('imprest_grn');
        $imprest_grn = new Imprest_grn();
        $imprest_grn->grn_id = $grn_id;
        $imprest_grn->imprest_id = $this->{$this::DB_TABLE_PK};
        $imprest_grn->save();
    }

    public function imprest_number(){
        return add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function payment_voucher_number()
    {
        return add_leading_zeros($this->payment_voucher_id);
    }

    public function currency(){
        return $this->payment_voucher()->currency();
    }

}

