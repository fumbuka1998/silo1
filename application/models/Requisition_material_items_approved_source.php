<?php

class Requisition_material_items_approved_source extends MY_Model{
    
    const DB_TABLE = 'requisition_material_items_approved_sources';
    const DB_TABLE_PK = 'id';

    public $requisition_material_item_id;
    public $currency_id;
    public $source_type;
    public $vendor_id;
    public $location_id;
    public $cashbook_account_id;
    public $approved_quantity;
    public $approved_price;

    public function requisition_material_item()
    {
        $this->load->model('requisition_material_item');
        $requisition_material_item = new Requisition_material_item();
        $requisition_material_item->load($this->requisition_material_item_id);
        return $requisition_material_item;
    }

    public function vendor()
    {
        $this->load->model('vendor');
        $vendor = new Vendor();
        $vendor->load($this->vendor_id);
        return $vendor;
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function cashbook_account()
    {
        $this->load->model('account');
        $cashbook = new Account();
        $cashbook->load($this->cashbook_account_id);
        return $cashbook;
    }

    public function source_name(){
        if($this->source_type == 'cash'){
            $source_name = $this->cashbook_account()->account_name;
        } else if($this->source_type == 'vendor') {
            $source_name = $this->vendor()->vendor_name;
        } else {
            $source_name = $this->location()->location_name;
        }
        return $source_name;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }



}

