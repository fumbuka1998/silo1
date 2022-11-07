<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 18/06/2019
 * Time: 13:52
 */

class Grn_received_service extends MY_Model{
    const DB_TABLE = 'grn_received_services';
    const DB_TABLE_PK = 'service_reception_id';

    public $grn_id;
    public $purchase_order_service_item_id;
    public $received_quantity;
    public $rejected_quantity;
    public $sub_location_id;
    public $rate;
    public $remarks;
    public $created_by;


    public function grn(){
        $this->load->model('goods_received_note');
        $grn = new Goods_received_note();
        $grn->load($this->grn_id);
        return $grn;
    }

    public function sub_location(){
        $this->load->model('sub_location');
        $sub_location = new Sub_location();
        $sub_location->load($this->sub_location_id);
        return $sub_location;
    }


}