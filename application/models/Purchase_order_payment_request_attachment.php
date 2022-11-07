<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/6/2018
 * Time: 11:58 AM
 */

class Purchase_order_payment_request_attachment extends MY_Model{
    const DB_TABLE = 'purchase_order_payment_request_attachments';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_id;
    public $attachment_id;


    public function attachment()
    {
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function purchase_order_payment_request(){
        $this->load->model('purchase_order_payment_request');
        $purchase_order_payment_request = new Purchase_order_payment_request();
        $purchase_order_payment_request->load($this->purchase_order_payment_request_id);
        return $purchase_order_payment_request;
    }

}