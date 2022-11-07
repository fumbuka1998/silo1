<?php
class Payment_voucher_credit_account extends MY_Model{
	const DB_TABLE = 'payment_voucher_credit_accounts';
	const DB_TABLE_PK = 'id';

	public $payment_voucher_id;
	public $account_id;
	public $stakeholder_id;
	public $amount;
	public $narration;


	public function payment_voucher(){
		$this->load->model('payment_voucher');
		$payment_voucher = new Payment_voucher();
		$payment_voucher->load($this->payment_voucher_id);
		return $payment_voucher;
	}

    public function account(){
        $this->load->model('account');
        $account = new Account();
        $account->load($this->account_id);
        return $account;
    }
}

