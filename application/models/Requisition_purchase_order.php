<?php

class Requisition_purchase_order extends MY_Model{
    
    const DB_TABLE = 'requisition_purchase_orders';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $purchase_order_id;

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Purchase_order();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }

}

