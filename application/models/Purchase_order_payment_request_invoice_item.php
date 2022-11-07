<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/24/2018
 * Time: 8:22 AM
 */

class Purchase_order_payment_request_invoice_item extends MY_Model{
    const DB_TABLE = 'purchase_order_payment_request_invoice_items';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_id;
    public $invoice_id;
    public $description;
    public $requested_amount;
    public $remarks;

    public function invoice(){
        $this->load->model('invoice');
        $invoice = new invoice();
        $invoice->load($this->invoice_id);
        return $invoice;
    }

    public function approved_item($payment_request_approval_id){
        $this->load->model('purchase_order_payment_request_approval_invoice_item');
        $where = [
            'purchase_order_payment_request_approval_id' => $payment_request_approval_id,
            'purchase_order_payment_request_invoice_item_id' => $this->{$this::DB_TABLE_PK},
        ];

        $approved_invoice_items = $this->purchase_order_payment_request_approval_invoice_item->get(1,0,$where);
        return array_shift($approved_invoice_items);
    }

    public function purchase_order_payment_request(){
        $this->load->model('purchase_order_payment_request');
        $purchase_order_payment_request = new Purchase_order_payment_request();
        $purchase_order_payment_request->load($this->purchase_order_payment_request_id);
        return $purchase_order_payment_request;
    }


}