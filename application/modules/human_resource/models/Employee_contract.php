<?php

class Employee_contract extends MY_Model{

    const DB_TABLE = 'employee_contracts';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $start_date;
    public $end_date;
    public $created_by;
    public $status;

    public function employee_contracts_list($limit, $start, $keyword, $order,$employee_id){

        $this->load->model(['department','job_position','ssf','Bank','Branch','allowance','employee_allowance']);
       // $where = '';

        $where = 'employee_id = "'.$employee_id.'"';
        if($keyword != ''){

            $where .= 'start_date LIKE "%'.$keyword.'%" ';
        }

        $order_string = dataTable_order_string(['start_date'],$order,'start_date');

        $employee_contracts = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach ($employee_contracts as $employee_contract){

            $data['employee_contract'] = $employee_contract;

            $data['employee_salary']=$employee_contract->eariest_contract_salary();
            $data['employee_designation']=$employee_contract->eariest_contract_designation();
            $data['job_position_options'] = $this->job_position->job_position_options();
            $data['department_options'] = $this->department->department_options();
            $data['ssf_options'] = $this->ssf->ssf_options();
            $data['bank_options'] = $this->Bank->bank_dropdown_options();
            $data['branch_options'] = $this->Branch->branch_options();
            $data['allowance_options'] = $this->allowance->allowance_dropdown_options();
            $data['allowances'] = $this->employee_allowance->employee_allowances($employee_id);

             $employee_contract_status = employee_contract_status($employee_contract->id);

             $data['employee_contract_status']=$employee_contract_status;

               $status='';

               if($employee_contract_status=='active_contract'){

                   $status = '<span class="label label-success">Active</span>';

                } else if($employee_contract_status=='incomplete_contract'){

                    $status = '<span class="label label-warning">Incomplete</span>';

                }else if($employee_contract_status=='expired_contract'){

                    $status= '<span class="label label-danger">Expired</span>';

                }else if($employee_contract_status=='closed_contract'){

                    $status = '<span class="label label-danger">Closed</span>';
                }

            $rows[] = [
                custom_standard_date($employee_contract->start_date),
                custom_standard_date($employee_contract->end_date),
                $employee_contract->created_at,
                $employee_contract->created_by()->full_name(),
                $status,
                $this->load->view('employees/employee_contract_actions',$data,true)
            ];
        }

        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows($where);

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

    public function employee_contract_salary_list(){

                $this->load->model('Employee_salary');
                $salaries = $this->Employee_salary->get(0,0,[
                'employee_contract_id' => $this->{ $this::DB_TABLE_PK }
                ]);

             return $salaries;

    }

    public function employee_contract_designation_list(){

                $this->load->model('Employee_designation');
                $designations = $this->Employee_designation->get(0,0,[
                'employee_contract_id' => $this->{ $this::DB_TABLE_PK }
                ]);

             return $designations;

    }

    public function eariest_contract_salary(){

                $this->load->model('Employee_salary');
                $Employee_salary= $this->Employee_salary->get(1,0,[
                'employee_contract_id' => $this->{ $this::DB_TABLE_PK },'id ASC'
                ]);

             return array_shift($Employee_salary);

    }

    public function latest_contract_salary(){

        $this->load->model('Employee_salary');
        $Employee_salary= $this->Employee_salary->get(1,0,[
        'employee_contract_id' => $this->{ $this::DB_TABLE_PK },'id DESC'
        ]);

        return array_shift($Employee_salary);

    }

    public function employee_contract_close(){

        $this->load->model('Employee_contract_close');
        $Employee_contract_close= $this->Employee_contract_close->get(0,0,[
        'employee_contract_id' => $this->{ $this::DB_TABLE_PK }
        ]);
        return !empty($Employee_contract_close) ? array_shift($Employee_contract_close) : false;

    }

    public function eariest_contract_designation(){

                $this->load->model('Employee_designation');
                $Employee_designation= $this->Employee_designation->get(1,0,[
                'employee_contract_id' => $this->{ $this::DB_TABLE_PK },'id ASC'
                ]);

             return array_shift($Employee_designation);

    }

    public function latest_contract_designation(){

        $this->load->model('Employee_designation');
        $Employee_designation= $this->Employee_designation->get(1,0,[
        'employee_contract_id' => $this->{ $this::DB_TABLE_PK },'id DESC'
        ]);

        return array_shift($Employee_designation);

    }

    public function contract_employee_dropdown_options()
    {
        $employees =  $this->get();
        $options[''] = '&nbsp;';
        $this->load->model('employee');
        foreach ($employees as $employee){
            $found_employee = new Employee();
            $found_employee->load($employee->employee_id);
            $options[$employee->employee_id] = $found_employee->full_name();
        }
        return $options;
    }



}

