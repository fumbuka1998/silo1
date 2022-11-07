<?php

class Payroll_employee_allowance extends MY_Model
{

    const DB_TABLE = 'payroll_employee_allowances';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $payroll_id;
    public $allowance_name;
    public $allowance_amount;

}