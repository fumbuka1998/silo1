<?php

class Requisition_material_item extends MY_Model{

    const DB_TABLE = 'requisition_material_items';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $material_item_id;
    public $requested_quantity;
    public $expense_account_id;
    public $requested_rate;
    public $requested_currency_id;
    public $requested_account_id;
    public $requested_vendor_id;
    public $requested_location_id;
    public $payee;
    public $source_type;

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

    public function material_item()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

    public function requested_vendor()
    {
        $this->load->model('stakeholder');
        $requested_vendor = new Stakeholder();
        $requested_vendor->load($this->requested_vendor_id);
        return $requested_vendor;
    }

    public function currency_symbol($approved = false){
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($approved ? $this->approved_currency_id : $this->requested_currency_id);
        return $currency->symbol;
    }

    public function insert_task_junction($task_id){
        $this->load->model('requisition_material_item_task');
        $junction_item = new Requisition_material_item_task();
        $junction_item->requisition_item_id = $this->{$this::DB_TABLE_PK};
        $junction_item->task_id = $task_id;
        $junction_item->save();
    }

    public function approved_item($requisition_approval_id,$source_id = null,$source_type = null){
        $this->load->model('requisition_approval_material_item');
        $where = [
            'requisition_approval_id' => $requisition_approval_id,
            'requisition_material_item_id' => $this->{$this::DB_TABLE_PK},
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
        $items = $this->requisition_approval_material_item->get(1,0,$where);
        return array_shift($items);
    }

    public function expense_account_junction($approval_id = null){
        $this->load->model('requisition_approval_material_item_expense_account');
        $accounts = $this->requisition_approval_material_item_expense_account->get(1,0,[
            'requisition_material_item_id' => $this->{$this::DB_TABLE_PK},
            'requisition_approval_id' => $approval_id
        ]);
        return !empty($accounts) ? array_shift($accounts) : false;
    }

    public function requested_location()
    {
        $this->load->model('inventory_location');
        $requested_location = new Inventory_location();
        $requested_location->load($this->requested_location_id);
        return $requested_location;
    }

    public function requested_account()
    {
        $this->load->model('account');
        $requested_account = new Account();
        $requested_account->load($this->requested_account_id);
        return $requested_account;
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

}

