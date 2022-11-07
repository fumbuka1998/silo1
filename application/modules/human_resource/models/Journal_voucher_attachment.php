<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:27 PM
 */

class Journal_voucher_attachment extends MY_Model{
    const DB_TABLE = 'journal_voucher_attachments';
    const DB_TABLE_PK = 'id';

    public $journal_voucher_id;
    public $attachment_id;

}