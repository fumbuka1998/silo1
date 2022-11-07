<?php
class Journal_voucher_item_approved_cash_request_item extends MY_Model{
	const DB_TABLE = 'journal_voucher_item_approved_cash_request_items';
	const DB_TABLE_PK = 'id';

	public $journal_voucher_item_id;
	public $quantity;
	public $rate;
	public $requisition_approval_cash_item_id;
	public $requisition_approval_service_item_id;
	public $requisition_approval_material_item_id;
	public $requisition_approval_asset_item_id;
}
