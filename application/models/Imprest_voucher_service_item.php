<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/13/2018
 * Time: 10:09 AM
 */

class Imprest_voucher_service_item extends MY_Model{
    const DB_TABLE = 'imprest_voucher_service_items';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_id;
    public $requisition_approval_service_item_id;
    public $quantity;
    public $rate;


    public function requisition_approval_service_item(){
        $this->load->model('requisition_approval_service_item');
        $requisition_approval_service_item = new Requisition_approval_service_item();
        $requisition_approval_service_item->load($this->requisition_approval_service_item_id);
        return $requisition_approval_service_item;
    }

    public function retired_service(){
        $sql = 'SELECT COALESCE(SUM(imprest_voucher_retired_services.quantity),0) AS retired_service_quantity FROM imprest_voucher_retired_services
                LEFT JOIN imprest_voucher_service_items ON imprest_voucher_retired_services.imprest_voucher_service_item_id = imprest_voucher_service_items.id
                WHERE imprest_voucher_service_item_id ='.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->retired_service_quantity;
    }
}