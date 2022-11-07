<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/14/2018
 * Time: 2:44 PM
 */

class Imprest_voucher_grn extends MY_Model{
    const DB_TABLE = 'imprest_voucher_grns';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_id;
    public $grn_id;

    public function goods_received_note(){
        $this->load->model('goods_recieved_note');
        $goods_recieved_note = new Goods_recieved_note();
        $goods_recieved_note->load($this->grn_id);
        return $goods_recieved_note;
    }

    public function imprest_voucher(){
        $this->load->model('imprest_voucher');
        $imprest_voucher = new Imprest_voucher();
        $imprest_voucher->load($this->imprest_voucher_id);
        return $imprest_voucher;
    }
}