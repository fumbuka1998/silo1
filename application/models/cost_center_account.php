<?php

class cost_center_account extends MY_Model
{

    const DB_TABLE = 'cost_center_accounts';
    const DB_TABLE_PK = 'id';

    public $cost_center_id;
    public $account_id;

    public function cost_center()
    {
        $this->load->model('cost_center');
        $cost_center = new cost_center();
        $cost_center->load($this->cost_center_id);
        return $cost_center;
    }

    public function account()
    {
        $this->load->model('account');
        $account = new account();
        $account->load($this->account_id);
        return $account;
    }
}

