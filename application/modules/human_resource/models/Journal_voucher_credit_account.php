<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:29 PM
 */

class Journal_voucher_credit_account extends MY_Model{
    const DB_TABLE = 'journal_voucher_credit_accounts';
    const DB_TABLE_PK = 'id';

    public $account_id;
    public $journal_voucher_id;
    public $amount;
    public $narration;

}