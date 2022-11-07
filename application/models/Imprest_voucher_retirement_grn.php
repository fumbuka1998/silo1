<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/14/2018
 * Time: 2:44 PM
 */

class Imprest_voucher_retirement_grn extends MY_Model{
    const DB_TABLE = 'imprest_voucher_retirement_grns';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_retirement_id;
    public $grn_id;

    public function goods_received_note(){
        $this->load->model('goods_received_note');
        $goods_received_note = new Goods_received_note();
        $goods_received_note->load($this->grn_id);
        return $goods_received_note;
    }

    public function imprest_voucher_retirement(){
        $this->load->model('imprest_voucher_retirement');
        $imprest_voucher_retirement = new Imprest_voucher_retirement();
        $imprest_voucher_retirement->load($this->imprest_voucher_retirement_id);
        return $imprest_voucher_retirement;
    }

    public function imprest_voucher_id(){
        $imprest_voucher = $this->imprest_voucher_retirement()->imprest_voucher();
        return $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
    }
}