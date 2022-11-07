<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/17/2018
 * Time: 5:28 PM
 */

class Task_payment_voucher_item extends MY_Model{
    const DB_TABLE = 'task_payment_voucher_items';
    const DB_TABLE_PK = 'id';

    public $task_id;
    public $payment_voucher_item_id;

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }
}