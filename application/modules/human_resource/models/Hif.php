<?php
class hif extends MY_Model{

    const DB_TABLE = 'hifs';
    const DB_TABLE_PK = 'id';

    public $hif_name;
    public $employer_deduction_percent;
    public $employee_deduction_percent;
    public $created_at;
    public $created_by;

    public function hif_list($limit, $start, $keyword, $order){
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= ' id  LIKE "%'.$keyword.'%" OR hif_name  LIKE "%'.$keyword.'%" ';
        }
        $order_string = dataTable_order_string(['id'],$order,'id');

        $hifs = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach ($hifs as $hif){
            $data['hif'] = $hif;
            $rows[] = [
                $hif->hif_name,
                $hif->employee_deduction_percent,
                $hif->employer_deduction_percent,
                $this->load->view('settings/hifs/hifs_list_actions',$data,true)
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

    public function official_hif(){
        $hif = new self();
        $hif->load($this->{$this::DB_TABLE_PK});
        return $hif;
    }

    public function created_by(){
        $this->load->model('Employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }


}