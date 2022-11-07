<?php

class Hse_certificate extends MY_Model {
    const DB_TABLE = 'hse_certificates';
    const DB_TABLE_PK = 'id';
    public $name;
    public $type;
    public $description ;
    public $created_by;

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
    }

    public function dropdown_options($nbsp = false){
        $where = ['type'=>'EMPLOYEE'];
        $certificates = $this->get(0,0,$where);
        $options = [];
        if ($nbsp) {
            $options[''] = '&nbsp;';
        }
        foreach ($certificates as $certificate){
            $options[$certificate->{$certificate::DB_TABLE_PK}] = $certificate->name;
        }
        return $options;

    }

    public function hse_certificates_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['name'],$order,'name');

        $where = '';
        if($keyword != ''){
            $where = ' name LIKE "%'.$keyword.'%" ';
        }

        $certificates = $this->get($limit, $start, $where,$order_string);
        $rows = array();
        foreach ($certificates as $certificate) {
            $data['certificate'] = $certificate;
            $rows[] = array(
                $certificate->name,
                $certificate->type,
                $certificate->description,
                $this->load->view('hse/certificates/certificates_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}
