<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/13/2018
 * Time: 1:01 AM
 */

class Requisition_service_item extends MY_Model{
    const DB_TABLE = 'requisition_service_items';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $description;
    public $measurement_unit_id;
    public $requested_quantity;
    /*requested currency id is not in the table has to be added*/
    public $requested_rate;
    public $payee;
    public $requested_account_id;
    public $source_type;
    public $requested_vendor_id;

    public function service_items()
    {
        $this->load->model('requisition_service_item');
        $requisition_service_items = new requisition_service_item();
        $requisition_service_items->load($this->requisition_id);
        return $requisition_service_items;
    }

    public function measurement_unit()
    {
        $this->load->model('measurement_unit');
        $measurement_unit = new Measurement_unit();
        $measurement_unit->load($this->measurement_unit_id);
        return $measurement_unit;
    }

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

    public function requested_currency()
    {
        return $this->requisition()->currency();
    }

    public function currency_symbol(){
        return $this->requested_currency()->symbol;
    }

    public function requested_source(){
        if($this->source_type == 'vendor'){
            $requested_source = $this->requested_vendor()->stakeholder_name;
        } else {
            $requested_source = 'CASH';
        }
        return $requested_source;
    }

    public function requested_location()
    {
        $this->load->model('inventory_location');
        $requested_location = new Inventory_location();
        $requested_location->load($this->requested_location_id);
        return $requested_location;
    }

    public function requested_vendor()
    {
        $this->load->model('stakeholder');
        $requested_vendor = new Stakeholder();
        $requested_vendor->load($this->requested_vendor_id);
        return $requested_vendor;
    }

    public function approved_item($requisition_approval_id,$source_id = null,$source_type = null){
        $this->load->model('requisition_approval_service_item');
        $where = [
            'requisition_approval_id' => $requisition_approval_id,
            'requisition_service_item_id' => $this->{$this::DB_TABLE_PK},
        ];
        if(!is_null($source_type)){
            if($source_type == 'store'){
                $where['location_id'] = $source_id;
            } else if($source_type == 'cash'){
                $where['account_id'] = $source_id;
            } else {
                $where['vendor_id'] = $source_id;
            }
        }
        $items = $this->requisition_approval_service_item->get(1,0,$where);
        return array_shift($items);
    }

}
