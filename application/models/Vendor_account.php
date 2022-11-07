<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 20/02/2018
 * Time: 15:24
 */
class Vendor_account extends MY_Model
{

    const DB_TABLE = 'vendor_accounts';
    const DB_TABLE_PK = 'id';

    public $vendor_id;
    public $account_id;

    public function vendor()
    {
        $this->load->model('vendor');
        $vendor = new vendor();
        $vendor->load($this->vendor_id);
        return $vendor;
    }

    public function account()
    {
        $this->load->model('account');
        $account = new account();
        $account->load($this->account_id);
        return $account;
    }


}

