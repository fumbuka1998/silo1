<?php
class Unprocured_delivery_grn extends MY_Model{
    const DB_TABLE = 'unprocured_delivery_grns';
    const DB_TABLE_PK = 'id';

    public $delivery_id;
    public $grn_id;

    public function delivery(){
        $this->load->model('unprocured_delivery');
        $delivery = new Unprocured_delivery();
        $delivery->load($this->delivery_id);
        return $delivery;
    }
}