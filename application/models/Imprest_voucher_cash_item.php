<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/13/2018
 * Time: 10:03 AM
 */

class Imprest_voucher_cash_item extends MY_Model{
    const DB_TABLE = 'imprest_voucher_cash_items';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_id;
    public $requisition_approval_cash_item_id;
    public $quantity;
    public $rate;

    public function requisition_approval_cash_item(){
        $this->load->model('requisition_approval_cash_item');
        $requisition_approval_cash_item = new Requisition_approval_cash_item();
        $requisition_approval_cash_item->load($this->requisition_approval_cash_item_id);
        return $requisition_approval_cash_item;
    }

    public function retired_cash_item(){
        $sql = 'SELECT COALESCE(SUM(imprest_voucher_retired_cash.quantity),0) AS retired_cash_quantity FROM imprest_voucher_retired_cash
                LEFT JOIN imprest_voucher_cash_items ON imprest_voucher_retired_cash.imprest_voucher_cash_item_id = imprest_voucher_cash_items.id
                WHERE imprest_voucher_cash_item_id ='.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->retired_cash_quantity;
    }
}