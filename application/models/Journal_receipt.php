<?php
class Journal_receipt extends MY_Model{
	const DB_TABLE = 'journal_receipts';
	const DB_TABLE_PK = 'id';

	public $journal_id;
	public $receipt_id;
}
