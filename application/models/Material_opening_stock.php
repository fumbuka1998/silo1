<?php

class Material_opening_stock extends MY_Model{
    
    const DB_TABLE = 'material_opening_stocks';
    const DB_TABLE_PK = 'opening_stock_id';

    public $sub_location_id;
    public $project_id;
    public $item_id;
    public $stock_id;

    public function material_stock()
    {
        $this->load->model('material_stock');
        $material_stock = new Material_stock();
        $material_stock->load($this->stock_id);
        return $material_stock;
    }

}

