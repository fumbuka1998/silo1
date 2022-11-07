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

    public function attachment(){
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function journal_voucher(){
        $this->load->model('journal_voucher');
        $journal_voucher = new Journal_voucher();
        $journal_voucher->load($this->journal_voucher_id);
        return $journal_voucher;
    }
}