<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/17/2018
 * Time: 5:33 PM
 */

class Department_payment_voucher_item extends MY_Model{
    const DB_TABLE = 'department_payment_voucher_items';
    const DB_TABLE_PK = 'id';

    public $department_id;
    public $payment_voucher_item_id;

    public function department()
    {
        $this->load->model('department');
        $department = new Department();
        $department->load($this->department_id);
        return $department;
    }
}