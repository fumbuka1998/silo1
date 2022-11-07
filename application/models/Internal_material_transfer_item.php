<?php

class Internal_material_transfer_item extends MY_Model{
    
    const DB_TABLE = 'internal_material_transfer_items';
    const DB_TABLE_PK = 'item_id';

    public $transfer_id;
    public $source_sub_location_id;
    public $stock_id;
    public $remarks;

    public function stock()
    {
        $this->load->model('material_stock');
        $stock = new Material_stock();
        $stock->load($this->stock_id);
        return $stock;
    }

    public function source_sub_location()
    {
        $this->load->model('sub_location');
        $source_sub_location = new Sub_location();
        $source_sub_location->load($this->source_sub_location_id);
        return $source_sub_location;
    }

    public function transfer(){
        $this->load->model('internal_material_transfer');
        $transfer = new Internal_material_transfer();
        $transfer->load($this->transfer_id);
        return $transfer;
    }

}

