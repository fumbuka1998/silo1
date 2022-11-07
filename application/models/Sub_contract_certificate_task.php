<?php

class Sub_contract_certificate_task extends MY_Model
{

    const DB_TABLE = 'sub_contract_certificate_tasks';
    const DB_TABLE_PK = 'id';

    public $sub_contract_certificate_id;
    public $task_id;
    public $amount;

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

    public function sub_contract_certificate()
    {
        $this->load->model('sub_contract_certificate');
        $sub_contract_certificate = new Sub_contract_certificate();
        $sub_contract_certificate->load($this->sub_contract_certificate_id);
        return $sub_contract_certificate;
    }
}
