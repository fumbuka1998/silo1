<?php

class Transferred_transfer_order extends MY_Model{
    
    const DB_TABLE = 'transferred_transfer_orders';
    const DB_TABLE_PK = 'id';
    
    public $transfer_id;
    public $requisition_approval_id;

}

