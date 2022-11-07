<?php
class Registered_certificate extends MY_Model {
    const DB_TABLE = 'registered_certificates';
    const DB_TABLE_PK = 'id';
    public $hse_certificate_id;
    public $company_id;
    public $employee_id;
    public $created_by;

    public function hse_certificate(){
        $this->load->model('hse_certificate');
        $registered_certificate = new Hse_certificate();
        $registered_certificate->load($this->hse_certificate_id);
        return $registered_certificate;
    }

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function certificated_labours($nbsp = false){
        $certicified_labours = $this->get();
        $options = [];
        if ($nbsp) {
            $options[''] = '&nbsp;';
        }
        foreach ($certicified_labours as $certicified_labour){
            $employee = $certicified_labour->employee();
            $options[$employee->{$employee::DB_TABLE_PK}] = $employee->full_name();
        }
        return $options;

    }

    public function employee(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function hse_registered_certificates_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['hse_certificate_id'],$order,'hse_certificate_id');

        $where = '';
        if($keyword != ''){
            $where = ' hse_certificate_id LIKE "%'.$keyword.'%" ';
        }

        $registered_certificates = $this->get($limit, $start, $where,$order_string);
        $this->load->model('hse_certificate');
        $rows = array();
        foreach ($registered_certificates as $registered_certificate) {
            $data['registered_certificate'] = $registered_certificate;
            $data['certificates_options'] = $this->hse_certificate->dropdown_options();
            $data['employees_options'] = employee_options();
            $certificate = $registered_certificate->hse_certificate();
            $rows[] = array(
                $registered_certificate->employee()->full_name(),
                $certificate->name,
                $certificate->type,
                $certificate->description,
               // $certificate->created_by()->full_name(),
                $this->load->view('hse/settings/registered_certificates/registered_certificates_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}