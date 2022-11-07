<?php
class Journal_voucher_item_approved_invoice_item extends MY_Model{
	const DB_TABLE = 'journal_voucher_item_approved_invoice_items';
	const DB_TABLE_PK = 'id';

	public $journal_voucher_item_id;
	public $purchase_order_payment_request_approval_invoice_item_id;
}
