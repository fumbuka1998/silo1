<?php

class Requisition_approval_cash_item_expense_account extends MY_Model{
    
    const DB_TABLE = 'requisition_approval_cash_item_expense_accounts';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_id;
    public $expense_account_id;
    public $requisition_cash_item_id;

    public function account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->expense_account_id);
        return $account;
    }

}

