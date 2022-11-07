<?php

class Payroll_employer_deduction extends MY_Model
{

    const DB_TABLE = 'payroll_employer_deductions';
    const DB_TABLE_PK = 'id';

    public $payroll_id;
    public $employee_id;
    public $deduction_name;
    public $deduction_amount;

}