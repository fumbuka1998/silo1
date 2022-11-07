<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 27/05/2019
 * Time: 10:59
 */

class Invoice_journal_voucher_item extends MY_Model{
    const DB_TABLE = "invoice_journal_voucher_items";
    const DB_TABLE_PK = "id";

    public $invoice_id;
    public $journal_voucher_item_id;



}
