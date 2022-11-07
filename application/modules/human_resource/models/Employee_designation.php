<?php
class Employee_designation extends MY_Model
{

    const DB_TABLE = 'employee_designations';
    const DB_TABLE_PK = 'id';

    public $employee_contract_id;
    public $department_id;
    public $job_position_id;
    public $branch_id;
    public $start_date;
    public $end_date;
    public $created_by;

    public function employee_contract(){

        $this->load->model('Employee_contract');
        $Employee_contract=new  Employee_contract();
        $Employee_contract->load($this->employee_contract_id);
        return $Employee_contract;


    }
    public function employee_department(){

        $this->load->model('Department');
        $Department=new  Department();
        $Department->load($this->department_id);
        return $Department;


    }
    public function employee_job_position(){

        $this->load->model('job_position');
        $job_position=new  job_position();
        $job_position->load($this->job_position_id);
        return $job_position;

    }
    public function employee_branch(){

        $this->load->model('Branch');
        $Branch=new  Branch();
        $Branch->load($this->branch_id);
        return $Branch;

    }




}