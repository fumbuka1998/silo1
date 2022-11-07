<?php

class Loan extends MY_Model
{


    const DB_TABLE = 'loans';
    const DB_TABLE_PK = 'id';

    public $loan_type;
    public $description;
    public $created_by;


    public function loan_type_list($limit, $start, $keyword, $order)
    {
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= 'loan_type LIKE "%'.$keyword.'%" ';
        }


        $order_string = dataTable_order_string(['loan_type'],$order,'loan_type');

        $loan_types = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        $count = 1;
        foreach ($loan_types as $type){
            $found_loan_type = new Loan();
            $found_loan_type->load($type->id);
            $data['loan_type_data'] = $found_loan_type;
            $rows[] = [
                $count,
                $type->loan_type,
                $type->description,
                $this->load->view('settings/loans/loan_type_list_actions',$data,true)
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

    public function loan_type_dropdown_options(){
        $loan_type_dropdowns = $this->get();
        $options[''] = '&nbsp;';
        foreach ($loan_type_dropdowns as $item){
            $options[$item->{$this::DB_TABLE_PK}] =$item->loan_type;
        }
        return $options;
    }


}