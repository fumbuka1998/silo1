<?php
class Journal_payment_voucher extends MY_Model
{
	const DB_TABLE = 'journal_payment_vouchers';
	const DB_TABLE_PK = 'id';

	public $journal_id;
	public $payment_voucher_id;


	public function journal(){
		$this->load->model('journal_voucher');
		$journal = new Journal_voucher();
		$journal->load($this->journal_id);
		return $journal;
	}

	public function payment_voucher(){
		$this->load->model('payment_voucher');
		$payment_voucher = new Payment_voucher();
		$payment_voucher->load($this->payment_voucher_id);
		return $payment_voucher;
	}
}
