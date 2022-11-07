<?php

class Requisition_approval_imprest_voucher extends MY_Model{
    
    const DB_TABLE = 'requisition_approval_imprest_vouchers';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_id;
    public $imprest_voucher_id;


    public function imprest_voucher(){
        $this->load->model('imprest_voucher');
        $imprest_voucher = new Imprest_voucher();
        $imprest_voucher->load($this->imprest_voucher_id);
        return $imprest_voucher;
    }

   

}
