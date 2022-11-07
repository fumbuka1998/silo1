<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 25/10/2017
 * Time: 16:00
 */

class Cancelled_purchase_order extends MY_Model{
    
    const DB_TABLE = 'cancelled_purchase_orders';
    const DB_TABLE_PK = 'id';

    public $purchase_order_id;
    public $reason;
    public $cancellation_date;
    public $created_by;

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new purchase_order();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

}

