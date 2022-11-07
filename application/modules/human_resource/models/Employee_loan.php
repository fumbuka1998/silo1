<?php

class Employee_loan extends MY_Model
{
    const DB_TABLE = 'employee_loans';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $loan_id;
    public $loan_account_id;
    public $loan_approved_date;
    public $loan_deduction_start_date;
    public $total_loan_amount;
    public $monthly_deduction_amount;
    public $loan_balance_amount;
    public $loan_application_form_path;
    public $description;
    public $status;
    public $created_by;

    public function employee_loans_list($limit, $start, $keyword, $order, $employee_id)
    {
        if($employee_id == 'all'){
            $sql = 'SELECT * FROM employee_loans';
        }else{
            $sql = 'SELECT * FROM employee_loans WHERE employee_id = '.$employee_id;
        }

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if($employee_id == 'all'){
            $where = '';
        }else{
            $where = 'employee_id = '.$employee_id;
        }

        if($keyword != ''){
            $where .= ' AND ( loan_approved_date LIKE "%'.$keyword.'%" OR loan_deduction_start_date LIKE "%'.$keyword.'%" OR total_loan_amount LIKE "%'.$keyword.'%" OR monthly_deduction_amount LIKE "%'.$keyword.'%" OR loan_balance_amount LIKE "%'.$keyword.'%") ';
        }

        $order_string = dataTable_order_string(['loan_approved_date','loan_deduction_start_date','total_loan_amount','monthly_deduction_amount','loan_balance_amount'],$order,'loan_approved_date');

        $employee_loans = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        $count = 1;
        $this->load->model(['loan','employee_loan_repay','employee','account']);
        foreach ($employee_loans as $loan){
            $found_employee_loan = new Employee_loan();
            $found_employee_loan->load($loan->id);
            $data['employee_loan_data'] = $found_employee_loan;
            $data['loan_type_options'] = $this->loan->loan_type_dropdown_options();
            $data['employee_id'] = $employee_id != 'all' ? $employee_id : 'all';
            $loan_type = new Loan();
            $loan_type->load($loan->loan_id);
            $repaid_loan = $this->employee_loan_repay->get(0,0,['employee_loan_id' => $loan->id]);
            $employee = new Employee();
            $employee->load($loan->employee_id);
            $data['account_dropdown_options'] = $this->account->dropdown_options(['CASH','BANK']);



            $rows[] = [
                $employee->full_name(),
                  $loan_type->loan_type,
                  set_date($loan->loan_approved_date),
                  set_date($loan->loan_deduction_start_date),
                  number_format($loan->total_loan_amount,2),
                   number_format($loan->monthly_deduction_amount,2),
                  number_format($loan->loan_balance_amount,2),
                  $loan->loan_application_form_path,
                  $this->load->view('employees/employee_loans/employee_loan_actions',$data,true)

            ];
            $count++;
        }

        $records_filtered = $this->count_rows($where);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

}