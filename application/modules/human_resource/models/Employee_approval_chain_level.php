<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 07/04/2018
 * Time: 16:39
 */

class Employee_approval_chain_level extends MY_Model
{

    const DB_TABLE = 'employee_approval_chain_levels';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $approval_chain_level_id;
    public $created_by;

    public function approval_chain_level()
    {
        $this->load->model('approval_chain_level');
        $approval_chain_level = new Approval_chain_level();
        $approval_chain_level->load($this->approval_chain_level_id);
        return $approval_chain_level;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }


}

