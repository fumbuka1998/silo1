<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:22 PM
 */

class Journal_voucher extends MY_Model{
    const DB_TABLE = 'journal_vouchers';
    const DB_TABLE_PK = 'journal_id';

    public $transaction_date;
    public $reference;
    public $journal_type;
    public $currency_id;
    public $remarks;
    public $created_by;


}