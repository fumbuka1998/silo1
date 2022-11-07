<?php

class Requisition_cash_item extends MY_Model{

    const DB_TABLE = 'requisition_cash_items';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $description;
    public $expense_account_id;
    public $requested_account_id;
    public $requested_quantity;
    public $measurement_unit_id;
    public $requested_rate;
    public $payee;
    public $requested_currency_id;

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

    public function measurement_unit()
    {
        $this->load->model('measurement_unit');
        $measurement_unit = new Measurement_unit();
        $measurement_unit->load($this->measurement_unit_id);
        return $measurement_unit;
    }

    public function insert_task_junction($task_id){
        $this->load->model('requisition_cash_item_task');
        $junction_item = new Requisition_cash_item_task();
        $junction_item->requisition_item_id = $this->{$this::DB_TABLE_PK};
        $junction_item->task_id = $task_id;
        $junction_item->save();
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

    public function approved_item($requisition_approval_id,$source_id = null){
        $this->load->model('requisition_approval_cash_item');
        $where = [
            'requisition_approval_id' => $requisition_approval_id,
            'requisition_cash_item_id' => $this->{$this::DB_TABLE_PK},
        ];
        if(!is_null($source_id)){
            $where['account_id'] = $source_id;
        }
        $items = $this->requisition_approval_cash_item->get(1,0,$where);
        return array_shift($items);
    }



}

