<?php

class Employee_bank extends MY_Model{

    const DB_TABLE = 'employee_banks';
    const DB_TABLE_PK = 'id';

    public $bank_id;
    public $account_no;
    public $branch;
    public $swift_code;
    public $start_date;
    public $created_at;
    public $created_by;

    public function employee_bank_list($limit, $start, $keyword, $order,$employee_id){
        $records_total = $this->count_rows();

        $where = 'employee_id = "'.$employee_id.'"';

        $records_filtered = $this->count_rows($where);

        if($keyword != ''){

            $where .= 'start_date LIKE "%'.$keyword.'%" ';
        }

        $order_string = dataTable_order_string(['start_date'],$order,'start_date');

        $employee_banks = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        $this->load->model('Bank');
        $data['bank_options'] = $this->Bank->bank_dropdown_options();

        foreach ($employee_banks as $employee_bank){
            $data['employee_bank'] = $employee_bank;
            $rows[] = [

                $employee_bank->bankname()->bank_name,
                $employee_bank->account_no,
                $employee_bank->branch,
                $employee_bank->swift_code,
                custom_standard_date($employee_bank->start_date),
                $employee_bank->created_at,
                $employee_bank->created_by()->full_name(),
                $this->load->view('employees/employee_banks/employee_bank_actions',$data,true)
            ];
        }

        

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function bankname(){
        $this->load->model('Bank');
        $bank_id = new Bank();
        $bank_id->load($this->bank_id);
        return $bank_id;
    }


}

