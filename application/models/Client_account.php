<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 07/05/2018
 * Time: 14:22
 */
class Client_account extends MY_Model
{

    const DB_TABLE = 'client_accounts';
    const DB_TABLE_PK = 'id';

    public $client_id;
    public $account_id;

    public function client()
    {
        $this->load->model('client');
        $client = new Client();
        $client->load($this->client_id);
        return $client;
    }

    public function account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->account_id);
        return $account;
    }


}

