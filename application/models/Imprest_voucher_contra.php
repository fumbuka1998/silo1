<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 10/6/2018
 * Time: 9:04 AM
 */

class Imprest_voucher_contra extends MY_Model{
    const DB_TABLE = 'imprest_voucher_contras';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_id;
    public $contra_id;


    public function imprest_voucher()
    {
        $this->load->model('imprest_voucher');
        $imprest_voucher = new Imprest_voucher();
        $imprest_voucher->load($this->imprest_voucher_id);
        return $imprest_voucher;
    }

    public function contra()
    {
        $this->load->model('contra');
        $contra = new Contra();
        $contra->load($this->contra_id);
        return $contra;
    }
}