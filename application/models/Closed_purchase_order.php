<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 29/05/2018
 * Time: 18:03
 */
class Closed_purchase_order extends MY_Model
{

    const DB_TABLE = 'closed_purchase_orders';
    const DB_TABLE_PK = 'id';

    public $purchase_order_id;
    public $closing_date;
    public $closing_remarks;
    public $created_by;

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Purchase_order();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }


}

