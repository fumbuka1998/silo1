<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/24/2018
 * Time: 8:17 AM
 */

class Purchase_order_payment_request_cash_item extends MY_Model{
    const DB_TABLE = 'purchase_order_payment_request_cash_items';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_id;
    public $description;
    public $reference;
    public $requested_amount;
    public $claimed_by;

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function approved_item($payment_request_approval_id){
        $this->load->model('purchase_order_payment_request_approval_cash_item');
        $where = [
            'purchase_order_payment_request_approval_id' => $payment_request_approval_id,
            'purchase_order_payment_request_cash_item_id' => $this->{$this::DB_TABLE_PK},
        ];

        $approved_cash_items = $this->purchase_order_payment_request_approval_cash_item->get(1,0,$where);
        return array_shift($approved_cash_items);
    }

    public function purchase_order_payment_request()
    {
        $this->load->model('purchase_order_payment_request');
        $purchase_order_payment_request = new Purchase_order_payment_request();
        $purchase_order_payment_request->load($this->purchase_order_payment_request_id);
        return $purchase_order_payment_request;
    }

}
