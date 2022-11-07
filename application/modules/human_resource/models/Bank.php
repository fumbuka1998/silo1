<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 30-Jun-17
 * Time: 4:47 PM
 */

class Bank extends MY_Model{

    const DB_TABLE = 'banks';
    const DB_TABLE_PK = 'id';

    public $bank_name;
    public $description;

    public function bank_list($limit, $start, $keyword, $order){
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= 'bank_name LIKE "%'.$keyword.'%" ';
        }


        $order_string = dataTable_order_string(['bank_name'],$order,'bank_name');

        $banks = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach ($banks as $bank){
            $data['banks_data'] = $bank;
            $rows[] = [
                $bank->bank_name,
                $bank->description,
                $this->load->view('settings/banks/banks_list_actions',$data,true)
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


    public function bank_dropdown_options(){
        $bank_dropdowns = $this->get();
        $options[''] = '&nbsp;';
        foreach ($bank_dropdowns as $bank_dropdown){
            $options[$bank_dropdown->{$this::DB_TABLE_PK}] =$bank_dropdown->bank_name;
        }
        return $options;
    }

}