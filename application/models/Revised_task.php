<?php
class Revised_task extends MY_Model
{

    const DB_TABLE = 'revised_tasks';
    const DB_TABLE_PK = 'id';

    public $revision_id;
    public $task_id;
    public $quantity;
    public $rate;
    public $description;

    public function amount(){
        return $this->quantity * $this->rate;
    }

    public function revision(){
        $this->load->model('revision');
        $revision = new Revision();
        $revision->load($this->revision_id);
        return $revision;
    }

    public function task(){
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

    public function revision_cost($revision_id){
        $sql = 'SELECT COALESCE(SUM(revised_tasks.quantity * revised_tasks.rate),0) AS revision_cost FROM revised_tasks
                WHERE revision_id ='.$revision_id;

        $query = $this->db->query($sql);
        return $query->row()->revision_cost;
    }
}