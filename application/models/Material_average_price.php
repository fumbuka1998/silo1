<?php

class Material_average_price extends MY_Model{
    
    const DB_TABLE = 'material_average_prices';
    const DB_TABLE_PK = 'average_price_id';

    public $datetime_updated;
    public $transaction_date;
    public $sub_location_id;
    public $material_item_id;
    public $project_id;
    public $average_price;
    public $material_stock_id;

    public function material_item()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

}

