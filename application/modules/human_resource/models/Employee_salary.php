<?php
class Employee_salary extends MY_Model
{

    const DB_TABLE = 'employee_salaries';
    const DB_TABLE_PK = 'id';

    public $employee_contract_id;
    public $payroll_no;
    public $salary;
    public $currency_id;
    public $payment_mode;
    public $tax_details;
    public $ssf_contribution;
    public $start_date;
    public $end_date;
    public $created_by;



     public function employee_contract(){

        $this->load->model('Employee_contract');
        $Employee_contract=new  Employee_contract();
        $Employee_contract->load($this->employee_contract_id);
        return $Employee_contract;


    }

}

