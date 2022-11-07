<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 8/14/2018
 * Time: 3:32 PM
 */

class Imprest_voucher_retired_cash extends MY_Model
{
    const DB_TABLE = 'imprest_voucher_retired_cash';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_retirement_id;
    public $description;
    public $quantity;
    public $rate;


    public function imprest_voucher_retirement(){
        $this->load->model('imprest_voucher_retirement');
        $imprest_voucher_retirement = new Imprest_voucher_retirement();
        $imprest_voucher_retirement->load($this->imprest_voucher_retirement_id);
        return $imprest_voucher_retirement;
    }
}