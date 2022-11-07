<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/17/2018
 * Time: 4:51 PM
 */

class Cost_center_payment_voucher_item extends MY_Model{
    const DB_TABLE = 'cost_center_payment_voucher_items';
    const DB_TABLE_PK = 'id';

    public $cost_center_id;
    public $payment_voucher_item_id;

    public function cost_center()
    {
        $this->load->model('cost_center');
        $cost_center = new Cost_center();
        $cost_center->load($this->cost_center_id);
        return $cost_center;
    }

}