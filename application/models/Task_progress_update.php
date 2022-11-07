<?php

class Task_progress_update extends MY_Model{
    
    const DB_TABLE = 'task_progress_updates';
    const DB_TABLE_PK = 'update_id';

    public $task_id;
    public $datetime_updated;
    public $percentage;
    public $description;

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

}

