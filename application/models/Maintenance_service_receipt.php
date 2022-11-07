<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/1/2019
 * Time: 12:51 PM
 */

class Maintenance_service_receipt extends MY_Model{
    const DB_TABLE = 'maintenance_service_receipts';
    const DB_TABLE_PK = 'id';

    public $receipt_id;
    public $maintenance_service_id;

    public function maintenance_service()
    {
        $this->load->model('maintenance_service');
        $maintenance_service = new Maintenance_service();
        $maintenance_service->load($this->maintenance_service_id);
        return $maintenance_service;
    }

    public function receipt()
    {
        $this->load->model('receipt');
        $receipt = new Receipt();
        $receipt->load($this->receipt_id);
        return $receipt;
    }

}