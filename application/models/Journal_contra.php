<?php
class Journal_contra extends MY_Model{
	const DB_TABLE = 'journal_contras';
	const DB_TABLE_PK = 'id';

	public $contra_id;
	public $journal_id;
}
