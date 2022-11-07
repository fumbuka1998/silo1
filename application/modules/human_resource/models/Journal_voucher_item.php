<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:35 PM
 */

class Journal_voucher_item extends MY_Model{
    const DB_TABLE = 'journal_voucher_items';
    const DB_TABLE_PK = 'item_id';

    public $journal_voucher_id;
    public $amount;
    public $debit_account_id;
    public $narration;


}