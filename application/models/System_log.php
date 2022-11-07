<?php

class System_log extends MY_Model{
    
    const DB_TABLE = 'system_logs';
    const DB_TABLE_PK = 'log_id';

    public $datetime_logged;
    public $user_agent;
    public $ip_address;
    public $employee_id;
    public $department_id;
    public $action;
    public $description;
    public $project_id;

    public function department(){
        $this->load->model('department');
        $department = new Department();
        $department->load($this->department_id);
        return $department;
    }

    public function employee(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

}

