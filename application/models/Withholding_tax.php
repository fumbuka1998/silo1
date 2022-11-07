<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/30/2018
 * Time: 6:46 PM
 */

class Withholding_tax extends MY_Model{
    const DB_TABLE = 'withholding_taxes';
    const DB_TABLE_PK = 'id';

    public $date;
    public $stakeholder_id;
    public $debit_account_id;
    public $remarks;
    public $payment_voucher_item_id;
    public $receipt_item_id;
    public $currency_id;
    public $withheld_amount;
    public $status;
    public $created_by;


    public function payment_voucher_item()
    {
        $this->load->model('payment_voucher_item');
        $payment_voucher_item = new Payment_voucher_item();
        $payment_voucher_item->load($this->payment_voucher_item_id);
        return $payment_voucher_item;
    }

    public function receipt_item()
    {
        $this->load->model('receipt_item');
        $receipt_item = new Receipt_item();
        $receipt_item->load($this->receipt_item_id);
        return $receipt_item;
    }

    public function debit_account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->debit_account_id);
        return $account;
    }

    public function credit_account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->credit_account_id);
        return $account;
    }

    public function tra_account(){
        $this->load->model('account');
        $accounts = $this->account->get(0,0,' account_name LIKE "%TRA - Account Payable%" ');
        return !empty($accounts) ? array_shift($accounts) : false;
    }
}
