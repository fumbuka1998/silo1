<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 28/03/2019
 * Time: 08:54
 */

class Allowance extends MY_Model
{

    const DB_TABLE = 'allowances';
    const DB_TABLE_PK = 'id';

    public $allowance_name;
    public $description;
    public $created_by;

    public function allowances_list($limit, $start, $keyword, $order)
    {
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where .= 'allowance_name LIKE "%'.$keyword.'%" ';
        }


        $order_string = dataTable_order_string(['allowance_name'],$order,'allowance_name');

        $allowances = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        $count = 1;
        foreach ($allowances as $allowance){
            $data['allowance_data'] = $allowance;
            $rows[] = [
                $count,
                $allowance->allowance_name,
                $allowance->description,
                $this->load->view('settings/allowances/allowance_list_actions',$data,true)
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

    public function allowance_dropdown_options(){
        $allowance_dropdowns = $this->get();
        $options[''] = '&nbsp;';
        foreach ($allowance_dropdowns as $allowance_dropdown){
            $options[$allowance_dropdown->{$this::DB_TABLE_PK}] =$allowance_dropdown->allowance_name;
        }
        return $options;
    }

}
