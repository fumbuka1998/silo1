<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/13/2018
 * Time: 6:32 AM
 */

class Purchase_order_service_item extends MY_Model{
    const DB_TABLE = 'purchase_order_service_items';
    const DB_TABLE_PK = 'id';

    public $order_id;
    public $description;
    public $measurement_unit_id;
    public $quantity;
    public $price;
    public $remarks;

    public function service_items()
    {
        $this->load->model('purchase_order_service_item');
        $purchase_order_service_items = new Purchase_order_service_item();
        $purchase_order_service_items->load($this->order_id);
        return $purchase_order_service_items;
    }

    public function measurement_unit()
    {
        $this->load->model('measurement_unit');
        $measurement_unit = new Measurement_unit();
        $measurement_unit->load($this->measurement_unit_id);
        return $measurement_unit;
    }

    public function received_quantity(){
        $sql = 'SELECT COALESCE(SUM(received_quantity),0) AS received_quantity FROM grn_received_services
                LEFT JOIN goods_received_notes ON grn_received_services.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE purchase_order_service_item_id = '.$this->{$this::DB_TABLE_PK}.' AND purchase_order_id = '.$this->order_id.' 
                ';

        $query = $this->db->query($sql);
        return $query->row()->received_quantity;

    }

    public function unreceived_quantity(){
        $balance = $this->quantity - $this->received_quantity();
        return $balance > 0 ? $balance : 0;
    }

}