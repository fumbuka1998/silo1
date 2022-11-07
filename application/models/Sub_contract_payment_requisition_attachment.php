<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/25/2018
 * Time: 2:49 PM
 */

class Sub_contract_payment_requisition_attachment extends MY_Model{
    const DB_TABLE = 'sub_contract_payment_requisition_attachments';
    const DB_TABLE_PK = 'id';

    public $sub_contract_payment_requisition_id;
    public $attachment_id;

    public function attachment()
    {
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function sub_contract_payment_requisition()
    {
        $this->load->model('sub_contract_payment_requisition');
        $requisition = new Sub_contract_payment_requisition();
        $requisition->load($this->sub_contract_payment_requisition_id);
        return $requisition;
    }
}