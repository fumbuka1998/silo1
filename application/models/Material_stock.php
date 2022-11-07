<?php

class Material_stock extends MY_Model{
    
    const DB_TABLE = 'material_stocks';
    const DB_TABLE_PK = 'stock_id';

    public $date_received;
    public $receiver_id;
    public $sub_location_id;
    public $item_id;
    public $quantity;
    public $price;
    public $project_id;
    public $description;


    public function sub_location()
    {
        $this->load->model('sub_location');
        $sub_location = new Sub_location();
        $sub_location->load($this->sub_location_id);
        return $sub_location;
    }

    public function material_item()
    {
        $this->load->model('material_item');
        $item = new Material_item();
        $item->load($this->item_id);
        return $item;
    }

    public function update_average_price(){
        $material = $this->material_item();
        $transaction_datetime = $this->date_received.strftime('  %H:%M:%S');
        $material->update_average_price($this->sub_location_id,$this->quantity,$this->price,$this->project_id,$transaction_datetime,$this->{$this::DB_TABLE_PK});
    }

    public function average_prices(){
        $this->load->model('material_average_price');
        return $this->material_average_price->get(0,0,[
            'sub_location_id' => $this->sub_location_id,
            'project_id' => $this->project_id,
            'material_item_id' => $this->item_id
        ],' average_price_id DESC');
    }

    public function material_costs(){
        $this->load->model('material_cost');
        return $this->material_cost->get(0,0,[
            'project_id' => $this->project_id,
            'source_sub_location_id' => $this->sub_location_id,
            'material_item_id' => $this->item_id
        ]);
    }
}

