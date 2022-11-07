<?php

class Payment_voucher_item_material_item extends MY_Model{
    
    const DB_TABLE = 'payment_voucher_item_material_items';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_material_item_id;
    public $quantity;
    public $rate;
    public $payment_voucher_item_id;

}