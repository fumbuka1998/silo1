<?php

class Requisition_equipment_item extends MY_Model{
    
    const DB_TABLE = 'requisition_equipment_items';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $asset_group_id;
    public $requested_quantity;
    public $expense_account_id;
    public $requested_rate;
    public $rate_mode;
    public $duration;
    public $requested_currency_id;
    public $requested_vendor_id;

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

    public function asset_group()
    {
        $this->load->model('Asset_group');
        $Asset_group = new Asset_group();
        $Asset_group->load($this->asset_group_id);
        return $Asset_group;
    }

    public function cost_center()
    {
        $this->load->model('Requisition_equipment_item_task');
        $Requisition_equipment_item_task = new Requisition_equipment_item_task();
        $Requisition_equipment_item_task->load($this->id);
        return $Requisition_equipment_item_task;
    }

    

    public function requested_vendor()
    {
        $this->load->model('vendor');
        $requested_vendor = new Vendor();
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
        $this->load->model('requisition_equipment_item_task');
        $junction_item = new requisition_equipment_item_task();
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

    public function expense_account($approval_id = null){
        if(is_null($approval_id)){
            $this->load->model('account');
            $account = new Account();
            $account->load($this->expense_account_id);
        } else {
            $this->load->model('requisition_approval_material_item_expense_account');
            $accounts = $this->requisition_approval_material_item_expense_account->get(1,0,[
                'requisition_material_item_id' => $this->{$this::DB_TABLE_PK},
                'approval_id' => $approval_id
            ]);
            $account = array_shift($accounts);
        }
        return $account;
    }

}

