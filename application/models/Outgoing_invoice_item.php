<?php

class Outgoing_invoice_item extends MY_Model
{

    const DB_TABLE = 'outgoing_invoice_items';
    const DB_TABLE_PK = 'item_id';

    public $outgoing_invoice_id;
    public $quantity;
    public $measurement_unit_id;
    public $rate;
    public $description;
    public $maintenance_service_item_id;
    public $project_certificate_id;
    public $stock_sale_asset_item_id;
    public $stock_sale_material_item_id;


    public function amount(){
        return $this->quantity * $this->rate;
    }

    public function maintenance_service_item(){
       $this->load->model('maintenance_service_item');
       $maintenance_service_item =  new Maintenance_service_item();
       $maintenance_service_item->load($this->maintenance_service_item_id);
       return $maintenance_service_item;
    }

    public function stock_sale_item_nature(){
        if($this->stock_sale_asset_item_id != null){
            return "asset";
        } else {
            return "material";
        }
    }

    public function stock_sale_asset_item(){
       $this->load->model('stock_sales_asset_item');
       $stock_sale_asset_item =  new Stock_sales_asset_item();
       $stock_sale_asset_item->load($this->stock_sale_asset_item_id);
       return $stock_sale_asset_item;
    }

    public function stock_sale_material_item(){
       $this->load->model('stock_sales_material_item');
       $stock_sale_material_item =  new Stock_sales_material_item();
       $stock_sale_material_item->load($this->stock_sale_material_item_id);
       return $stock_sale_material_item;
    }

    public function project_certificate(){
       $this->load->model('project_certificate');
       $project_certificate =  new Project_certificate();
       $project_certificate->load($this->project_certificate_id);
       return $project_certificate;
    }

    public function measurement_unit(){
        $this->load->model('measurement_unit');
        $measurement_unit = new Measurement_unit();
        $measurement_unit->load($this->measurement_unit_id);
        return $measurement_unit;
    }

    public function outgoing_invoice_items($invoice_number)
    {
       $sql = 'SELECT outgoing_invoice_items.*, measurement_units.symbol as unit_symbol  FROM outgoing_invoice_items
              LEFT JOIN measurement_units ON outgoing_invoice_items.measurement_unit_id = measurement_units.unit_id WHERE outgoing_invoice_id = '.$invoice_number;

        $query = $this->db->query($sql);
        $results = $query->result();
        return $results;
    }

    public function invoice_item_details($item_description)
    {
        $this->load->model('Maintenance_service_item');
        $item = $this->maintanance_service_item->get(1, 0, ['description' => $item_description]);
        return array_shift($item);
    }



}