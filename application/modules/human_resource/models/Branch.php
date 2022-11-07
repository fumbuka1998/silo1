<?php

class Branch extends MY_Model{

    const DB_TABLE = 'branches';
    const DB_TABLE_PK = 'id';

    public $branch_name;
    public $created_at;
    public $created_by;

    public function branch_list($limit, $start, $keyword, $order){
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= 'branch_name LIKE "%'.$keyword.'%" ';
        }


        $order_string = dataTable_order_string(['branch_name'],$order,'branch_name');

        $branches = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach ($branches as $branch){
            $data['branches_data'] = $branch;
            $rows[] = [
                $branch->branch_name,
                custom_standard_date($branch->created_at),
                $branch->created_by()->full_name(),
                $this->load->view('settings/branches/branches_list_actions',$data,true)
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

    public function branch_options(){
        $branches = $this->get();
        $options[''] = '&nbsp;';
        foreach ($branches as $branch){
            $options[$branch->{$this::DB_TABLE_PK}] =$branch->branch_name;
        }
        return $options;
    }
}