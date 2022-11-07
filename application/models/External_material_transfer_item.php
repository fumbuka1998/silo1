<?php

class External_material_transfer_item extends MY_Model{
    
    const DB_TABLE = 'external_material_transfer_items';
    const DB_TABLE_PK = 'item_id';

    public $transfer_id;
    public $source_sub_location_id;
    public $material_item_id;
    public $project_id;
    public $quantity;
    public $price;
    public $remarks;

    public function source_sub_location()
    {
        $this->load->model('sub_location');
        $source_sub_location = new Sub_location();
        $source_sub_location->load($this->source_sub_location_id);
        return $source_sub_location;
    }

    public function material_item()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new project();
        $project->load($this->project_id);
        return $project;
    }

    public function quantity_received(){
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS quantity_received FROM material_stocks WHERE stock_id IN (
                  SELECT stock_id FROM goods_received_note_material_stock_items WHERE grn_id IN (
                    SELECT grn_id FROM external_material_transfer_grns WHERE transfer_id = '.$this->transfer_id.'
                  )
              ) AND item_id = '.$this->material_item_id;
        $query = $this->db->query($sql);
        return $query->row()->quantity_received;
    }

    public function transfer()
    {
        $this->load->model('external_material_transfer');
        $transfer = new External_material_transfer();
        $transfer->load($this->transfer_id);
        return $transfer;
    }

}

