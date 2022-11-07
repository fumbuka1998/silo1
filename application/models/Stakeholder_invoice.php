<?php
class Stakeholder_invoice extends MY_Model{
	const DB_TABLE = 'stakeholder_invoices';
	const DB_TABLE_PK = 'id';

	public $invoice_id;
	public $stakeholder_id;

	public function stakeholder()
	{
		$this->load->model('stakeholder');
		$stakeholder = new Stakeholder();
		$stakeholder->load($this->stakeholder_id);
		return $stakeholder;
	}

	public function invoice()
	{
		$this->load->model('invoice');
		$invoice = new Invoice();
		$invoice->load($this->invoice_id);
		return $invoice;
	}

}
