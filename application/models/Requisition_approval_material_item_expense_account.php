<?php

class Requisition_approval_material_item_expense_account extends MY_Model{
    
    const DB_TABLE = 'requisition_approval_material_item_expense_accounts';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_id;
    public $expense_account_id;
    public $requisition_material_item_id;

    public function account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->expense_account_id);
        return $account;
    }

    public function expense_account_junction($requisition_approval_id){
        $this->load->model('requisition_approval_material_item_expense_account');
        $junctions = $this->requisition_approval_material_item_expense_account->get(1,0,[
            'requisition_approval_id' => $requisition_approval_id,
            'requisition_material_item_id' => $this->{$this::DB_TABLE_PK}
        ]);
        return !empty($junctions) ? array_shift($junctions) : new Requisition_approval_cash_item_expense_account();
    }

}

