<?php

class Contra_item extends MY_Model{

    const DB_TABLE = 'contra_items';
    const DB_TABLE_PK = 'contra_item_id';

    public $contra_id;
    public $debit_account_id;
    public $stakeholder_id;
    public $amount;
    public $description;

    public function contra()
    {
        $this->load->model('contra');
        $contra = new Contra();
        $contra->load($this->contra_id);
        return $contra;
    }

    public function debit_account()
    {
        $this->load->model('account');
        $debit_account = new Account();
        $debit_account->load($this->debit_account_id);
        return $debit_account;
    }

}

