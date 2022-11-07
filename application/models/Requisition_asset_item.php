<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 31/03/2018
 * Time: 13:28
 */

class Requisition_asset_item extends MY_Model
{

    const DB_TABLE = 'requisition_asset_items';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $asset_item_id;
    public $requested_quantity;
    public $requested_rate;
    public $requested_vendor_id;
    public $requested_account_id;
    public $requested_currency_id;
    public $payee;
    public $source_type;
    public $requested_location_id;

    public function asset_item()
    {
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        $asset_item->load($this->asset_item_id);
        return $asset_item;
    }

    public function currency_symbol(){
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->requested_currency_id);
        return $currency->symbol;
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

    public function requested_source(){
        if($this->source_type == 'vendor'){
            $requested_source = $this->requested_vendor()->stakeholder_name;
        } else if($this->source_type == 'store'){
            $requested_source = $this->requested_location()->location_name;
        } else {
            $requested_source = 'CASH';
        }
        return $requested_source;
    }

    public function approved_item($requisition_approval_id,$source_id = null,$source_type = null){
        $this->load->model('requisition_approval_asset_item');
        $where = [
            'requisition_approval_id' => $requisition_approval_id,
            'requisition_asset_item_id' => $this->{$this::DB_TABLE_PK},
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
        $items = $this->requisition_approval_asset_item->get(1,0,$where);
        return array_shift($items);
    }


}
