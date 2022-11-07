<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 23/10/2017
 * Time: 05:44
 */

class Cost_center_purchase_order extends MY_Model{
    
    const DB_TABLE = 'cost_center_purchase_orders';
    const DB_TABLE_PK = 'id';

    public $cost_center_id;
    public $purchase_order_id;

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Purchase_order();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }

    public function cost_center()
    {
        $this->load->model('cost_center');
        $cost_center = new Cost_center();
        $cost_center->load($this->cost_center_id);
        return $cost_center;
    }

}

