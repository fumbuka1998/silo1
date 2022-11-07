<?php
class Journal_voucher_item_approved_sub_contract_requisition_item extends MY_Model{
	const DB_TABLE = 'journal_voucher_item_approved_sub_contract_requisition_items';
	const DB_TABLE_PK = 'id';

	public $ournal_voucher_item_id;
	public $sub_contract_payment_requisition_approval_item_id;
}
