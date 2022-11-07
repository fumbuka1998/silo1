<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/22/2018
 * Time: 9:16 AM
 */

class Sub_contract_payment_requisition_item extends MY_Model{
    const DB_TABLE = 'sub_contract_payment_requisition_items';
    const DB_TABLE_PK = 'id';

    public $sub_contract_requisition_id;
    public $certificate_id;
    public $requested_amount;


    public function certificate(){
        $this->load->model('sub_contract_certificate');
        $sub_contract_certificate = new Sub_contract_certificate();
        $sub_contract_certificate->load($this->certificate_id);
        return $sub_contract_certificate;
    }

    public function approved_item($last_approval_id){
        $this->load->model('sub_contract_payment_requisition_approval_item');
        $where = [
            'sub_contract_payment_requisition_approval_id' => $last_approval_id,
            'sub_contract_payment_requisition_item_id' => $this->{$this::DB_TABLE_PK},
        ];
        $items = $this->sub_contract_payment_requisition_approval_item->get(1,0,$where);
        return array_shift($items);
    }
}