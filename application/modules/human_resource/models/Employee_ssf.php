<?php

class Employee_ssf extends MY_Model{

    const DB_TABLE = 'employee_ssfs';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $ssf_id;
    public $ssf_no;
    public $start_date;
    public $created_at;
    public $created_by;

    public function employee_ssf_list($limit, $start, $keyword, $order,$employee_id){
        $records_total = $this->count_rows();

        // $where = '';

        $where = 'employee_id = "'.$employee_id.'"';
        if($keyword != ''){

            $where .= 'start_date LIKE "%'.$keyword.'%" ';
        }

        $order_string = dataTable_order_string(['start_date'],$order,'start_date');

        $employee_ssfs = $this->get($limit,$start,$where,$order_string);
        $rows = [];
     $this->load->model('ssf');
       $data['ssf_options'] = $this->ssf->ssf_options();

        foreach ($employee_ssfs as $employee_ssf){
            $data['employee_ssf'] = $employee_ssf;
            $rows[] = [

                $employee_ssf->ssf()->official_ssf()->ssf_name,
                $employee_ssf->ssf_no,
                custom_standard_date($employee_ssf->start_date),
                $employee_ssf->created_at,
                $employee_ssf->created_by()->full_name(),
                $this->load->view('employees/employee_ssfs/employee_ssf_actions',$data,true)
            ];
        }

        $records_filtered = $this->count_rows($where);

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

    public function ssf(){
        $this->load->model('ssf');
        $ssf_id = new ssf();
        $ssf_id->load($this->ssf_id);
        return $ssf_id;
    }

    public function employee_ssf_details($employee_id, $ssf_id)
    {
        $ssf_details = $this->get(1,0, ['employee_id' => $employee_id, 'ssf_id' => $ssf_id]);
        if ($ssf_details) {
            $found_details = array_shift($ssf_details);
            return $found_details;
        }else{
            return false;
        }

    }


}

