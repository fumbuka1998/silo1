<?php

class Project_closure extends MY_Model{
    
    const DB_TABLE = 'project_closures';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $closure_date;
    public $remarks;
    public $created_by;

    public function employee_closed()
    {
        $this->load->model('employee');
        $employee_closed = new Employee();
        $employee_closed->load($this->closed_by);
        return $employee_closed;
    }

}

