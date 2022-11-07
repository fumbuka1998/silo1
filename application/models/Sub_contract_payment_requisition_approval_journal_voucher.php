<?php
class Sub_contract_payment_requisition_approval_journal_voucher extends MY_Model{
	const DB_TABLE = 'sub_contract_payment_requisition_approval_journal_vouchers';
	const DB_TABLE_PK = 'id';

	public $sub_contract_payment_requisition_approval_id;
	public $journal_voucher_id;
}
