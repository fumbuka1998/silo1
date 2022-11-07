<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/13/2018
 * Time: 10:00 AM
 */

class Imprest_voucher_asset_item extends MY_Model{
    const DB_TABLE = 'imprest_voucher_asset_items';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_id;
    public $requisition_approval_asset_item_id;
    public $quantity;
    public $rate;

    public function requisition_approval_asset_item(){
        $this->load->model('requisition_approval_asset_item');
        $requisition_approval_asset_item = new Requisition_approval_asset_item();
        $requisition_approval_asset_item->load($this->requisition_approval_asset_item_id);
        return $requisition_approval_asset_item;
    }

    public function retired_asset($imprest_voucher_id,$asset_item_id){
        $sql = 'SELECT COALESCE(SUM(imprest_voucher_retirement_asset_items.quantity))AS retired_asset_quantity FROM imprest_voucher_retirement_asset_items
                LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_asset_items.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                LEFT JOIN imprest_vouchers ON imprest_voucher_retirements.imprest_voucher_id = imprest_vouchers.id
                LEFT JOIN imprest_voucher_asset_items ON imprest_vouchers.id = imprest_voucher_asset_items.imprest_voucher_id
                WHERE imprest_voucher_asset_items.imprest_voucher_id ='.$imprest_voucher_id.' AND imprest_voucher_asset_items.id ='.$this->{$this::DB_TABLE_PK}.'
                AND imprest_voucher_retirement_asset_items.asset_item_id ='.$asset_item_id;

        $query = $this->db->query($sql);
        return $query->row()->retired_asset_quantity;
    }
}