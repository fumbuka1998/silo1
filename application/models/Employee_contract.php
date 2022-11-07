<?php

class Employee_contract extends MY_Model{
    
    const DB_TABLE = 'employees_contracts';
    const DB_TABLE_PK = 'contract_id';

    public $employee_id;
    public $start_date;
    public $end_date;
    public $salary;
    public $description;
    public $registrar_id;
    public $date_registered;

    public function employee_contracts_list($employee_id){
        $keyword = $this->input->post('search')['value'];
        $limit = $this->input->post('length');
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'start_date';
                break;
            case 1;
                $order_column = 'end_date';
                break;
            case 2;
                $order_column = 'date_registered';
                break;
            case 6;
                $order_column = 'status';
                break;
            default:
                $order_column = 'start_date';
        }

        $order = $order_column.' '.$order_dir;

        $where = 'employee_id = "'.$employee_id.'"';
        if($keyword != ''){
            $where .= 'AND start_date LIKE "%'.$keyword.'%" OR end_date LIKE "%'.$keyword.'%"';
        }

        $contracts = $this->get($limit,$start,$where,$order);
        $rows = [];
        foreach($contracts as $contract){
            $registrar = $contract->registrar();
            $registrar_column = check_permission('Human Resources') || ($contract->registrar_id == $this->session->userdata('employee_id'))  ? anchor(base_url('human_resources/employee_profile/'.$contract->registrar_id),$registrar->full_name()) : $registrar->full_name();
            $rows[] = [
                custom_standard_date($contract->start_date),
                custom_standard_date($contract->end_date),
                '<span class="pull-right">'.number_format($contract->salary).'</span>',
                $registrar_column,
                custom_standard_date($contract->date_registered),
                nl2br($contract->description),
                $contract->status(true),
                $this->load->view('human_resources/employees/contract_action_column',['contract' => $contract],true)
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows(['employee_id' => $employee_id]);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function status($label = false){
        $label_class = 'label ';
        if(strtotime($this->start_date) > time()){
            $status = 'Due';
            $label_class .= ' label-primary';
        } else if(strtotime($this->end_date) < time()){
            $status = 'Expired';
            $label_class .= ' label-warning';
        } else {
            $status = 'Active';
            $label_class .= ' label-success';
        }
        return $label ? '<label class="'.$label_class.'">'.$status.'</label>' : $status;
    }

    public function registrar()
    {
        $this->load->model('employee');
        $registrar = new Employee();
        $registrar->load($this->registrar_id);
        return $registrar;
    }

}

