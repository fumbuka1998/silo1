<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 11/05/2019
 * Time: 12:21
 */

Class Payroll_journal_voucher extends MY_Model{
    const DB_TABLE = "payroll_journal_vouchers";
    const DB_TABLE_PK = "id";

    public $payroll_id;
    public $journal_voucher_id;


    public function journal(){
        $this->load->model('journal');
        $journal = new Journal_voucher();
        $journal->load($this->journal_voucher_id);
        return $journal;
    }
}