<?php

class Payment_voucher_item_cash_item extends MY_Model{
    
    const DB_TABLE = 'payment_voucher_item_cash_items';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_cash_item_id;
    public $quantity;
    public $rate;
    public $payment_voucher_item_id;

}