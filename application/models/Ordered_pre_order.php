<?php

class Ordered_pre_order extends MY_Model{
    
    const DB_TABLE = 'ordered_pre_orders';
    const DB_TABLE_PK = 'order_id';

    public $purchase_order_id;
    public $currency_id;
}

