<?php

class Requisition_material_item_task extends MY_Model{
    
    const DB_TABLE = 'requisition_material_item_tasks';
    const DB_TABLE_PK = 'id';

    public $requisition_item_id;
    public $task_id;

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

}

