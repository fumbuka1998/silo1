<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/13/2018
 * Time: 10:06 AM
 */

class Imprest_voucher_material_item extends MY_Model{
    const DB_TABLE = ' imprest_voucher_material_items';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_id;
    public $requisition_approval_material_item_id;
    public $quantity;
    public $rate;

    public function requisition_approval_material_item(){
        $this->load->model('requisition_approval_material_item');
        $requisition_approval_material_item = new Requisition_approval_material_item();
        $requisition_approval_material_item->load($this->requisition_approval_material_item_id);
        return $requisition_approval_material_item;
    }

    public function retired_material($imprest_voucher_id,$item_id){
        $sql = 'SELECT COALESCE(SUM(imprest_voucher_retirement_material_items.quantity),0) AS retired_material_quantity FROM imprest_voucher_retirement_material_items
                LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_material_items.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                LEFT JOIN imprest_vouchers ON imprest_voucher_retirements.imprest_voucher_id = imprest_vouchers.id
                LEFT JOIN imprest_voucher_material_items ON imprest_vouchers.id = imprest_voucher_material_items.imprest_voucher_id
                WHERE imprest_voucher_material_items.imprest_voucher_id ='.$imprest_voucher_id.' AND imprest_voucher_material_items.id ='.$this->{$this::DB_TABLE_PK}.'
                AND imprest_voucher_retirement_material_items.item_id ='.$item_id;

        $query = $this->db->query($sql);
        return $query->row()->retired_material_quantity;
    }
}