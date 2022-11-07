<?php

class Payroll_employee_basic_info extends MY_Model
{

    const DB_TABLE = 'payroll_employee_basic_info';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $payroll_id;
    public $title;
    public $location;
    public $basic_salary;
    public $gross_salary;
    public $deducted_nssf;
    public $taxable_amount;
    public $paye;
    public $heslb_loan;
    public $heslb_loan_repay;
    public $heslb_loan_balance;
    public $company_loan;
    public $company_loan_repay;
    public $company_loan_balance;
    public $advance_payment;
    public $net_pay;


}