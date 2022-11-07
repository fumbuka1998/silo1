<?php
class ssf extends MY_Model{

    const DB_TABLE = 'ssfs';
    const DB_TABLE_PK = 'id';

    public $official_ssf_id;
    public $created_at;
    public $created_by;

       public function ssf_list($limit, $start, $keyword, $order){
            $records_total = $this->count_rows();

            $where = '';
            if($keyword != ''){
                $where .= 'official_ssf_id  LIKE "%'.$keyword.'%" ';
            }
            $order_string = dataTable_order_string(['official_ssf_id'],$order,'official_ssf_id');

            $ssfs = $this->get($limit,$start,$where,$order_string);
            $rows = [];
           $this->load->model('official_ssf');
           $data['official_ssf_option']  = $this->official_ssf->official_ssf_options();
            foreach ($ssfs as $ssf){
                $data['ssf'] = $ssf;
                $rows[] = [
                    $ssf->official_ssf()->ssf_name,
                    $ssf->official_ssf()->employee_deduction_percentage,
                    $ssf->official_ssf()->employer_deduction_percentage,
                    $this->load->view('settings/ssfs/ssfs_list_actions',$data,true)
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
        $this->load->model('Employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function ssf_options(){
        $ssfs = $this->get();
        $options[''] = '&nbsp;';
        foreach ($ssfs as $ssf){
            $options[$ssf->{$this::DB_TABLE_PK}] =$ssf->official_ssf()->ssf_name;
        }
        return $options;
    }

    public function official_ssf(){
        $this->load->model('official_ssf');
        $official_ssf = new official_ssf();
        $official_ssf->load($this->official_ssf_id);
        return $official_ssf;
    }


}