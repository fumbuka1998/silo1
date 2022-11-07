<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 13/05/2018
 * Time: 18:01
 */
class Contractor_account extends MY_Model
{

    const DB_TABLE = 'contractor_accounts';
    const DB_TABLE_PK = 'id';

    public $contractor_id;
    public $account_id;

    public function account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->account_id);
        return $account;
    }

    public function contractor()
    {
        $this->load->model('contractor');
        $contractor = new Contractor();
        $contractor->load($this->contractor_id);
        return $contractor;
    }


}

