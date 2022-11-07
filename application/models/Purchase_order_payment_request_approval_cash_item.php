<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/31/2018
 * Time: 11:38 PM
 */

class Purchase_order_payment_request_approval_cash_item extends MY_Model{
    const DB_TABLE = ' purchase_order_payment_request_approval_cash_items';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_approval_id;
    public $purchase_order_payment_request_cash_item_id;
    public $approved_amount;
    public $claimed_by;

    public function purchase_order_payment_request_cash_item()
    {
        $this->load->model('purchase_order_payment_request_cash_item');
        $purchase_order_payment_request_cash_item = new Purchase_order_payment_request_cash_item();
        $purchase_order_payment_request_cash_item->load($this->purchase_order_payment_request_cash_item_id);
        return $purchase_order_payment_request_cash_item;
    }


}