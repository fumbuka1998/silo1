<?php

class Sub_location extends MY_Model{
    
    const DB_TABLE = 'sub_locations';
    const DB_TABLE_PK = 'sub_location_id';

    public $sub_location_name;
    public $location_id;
    public $equipment_id;
    public $description;
    public $status;

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function equipment()
    {
        $this->load->model('asset');
        $equipment = new Asset();
        $equipment->load($this->equipment_id);
        return $equipment;
    }

    public function material_items($with_balance = false){
        $this->load->model('material_item');
        return $this->material_item->location_material_items($this->location_id, $this->{$this::DB_TABLE_PK}, $with_balance);
    }

    public function material_item_balance_value($material_item,$project_id = 'all',$date = null){
        $sub_location_id = $this->{$this::DB_TABLE_PK};
        return $material_item->sub_location_average_price($sub_location_id, $project_id, $date) * $material_item->sub_location_balance($sub_location_id, $project_id, $date, 'all');
    }

    public function total_material_balance_value($project_id = 'all',$date = null){
        $sub_location_id = $this->{$this::DB_TABLE_PK};
        $this->load->model('material_item');
        $material_items = $this->material_item->location_material_items($this->location_id, $sub_location_id, false,$project_id);
        $total_value = 0;
        foreach ($material_items as $material_item){
            $total_value += $this->material_item_balance_value($material_item,$project_id,$date);
        }
        return $total_value;
    }


    public function material_used_quantity($project_id = 'all', $from = null, $to = null)
    {
        $material_items = $this->material_items();
        if (!empty($material_items)) {
            $material_item = array_shift($material_items);
            return $material_item->sub_location_used_quantity($this->sub_location_id, $project_id, $from, $to);
        } else {
            return 0;
        }
    }

    public function material_average_price($to = null)
    {
        $material_items = $this->material_items();
        if (!empty($material_items)) {
            $material_item = array_shift($material_items);
            return $material_item->location_average_price($this->location_id, $to);
        } else {
            return 0;
        }
    }

}

