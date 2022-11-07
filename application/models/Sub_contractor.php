<?php

class Sub_contractor extends MY_Model{
    
    const DB_TABLE = 'sub_contractors';
    const DB_TABLE_PK = 'id';

    public $name;
    public $phone;
    public $alternative_phone;
    public $email;
    public $address;
    public $account_id;
/* ptm
    public function sub_contractors_list($limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['name','phone','alternative_phone','email','address'],$order,'name');

        $where = '';
        if($keyword != ''){
            $where .= 'name LIKE "%'.$keyword.'%" OR phone LIKE "%'.$keyword.'%" OR alternative_phone LIKE "%'.$keyword.'%"  OR email LIKE "%'.$keyword.'%"  OR address LIKE "%'.$keyword.'%" ';
        }

        $sub_contractors = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach($sub_contractors as $sub_contractor){
            $rows[] = [
                anchor(base_url('sub_contractors/profile/'.$sub_contractor->{$sub_contractor::DB_TABLE_PK}),$sub_contractor->name),
                $sub_contractor->phone,
                $sub_contractor->alternative_phone,
                $sub_contractor->email,
                $sub_contractor->address
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows();
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }
*/
    public function sub_contractors_list($limit, $start, $keyword, $order){ //ptm
        //order string
        $order_string = dataTable_order_string(['name','phone','alternative_phone','email','address'],$order,'name');

        $where = '';
        if($keyword != ''){
            $where .= 'name LIKE "%'.$keyword.'%" OR phone LIKE "%'.$keyword.'%" OR alternative_phone LIKE "%'.$keyword.'%"  OR email LIKE "%'.$keyword.'%"  OR address LIKE "%'.$keyword.'%" ';
        }

        $sub_contractors = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach($sub_contractors as $sub_contractor){
            $rows[] = [
                anchor(base_url('sub_contractors/profile/'.$sub_contractor->{$sub_contractor::DB_TABLE_PK}),$sub_contractor->name),
                $sub_contractor->phone,
                $sub_contractor->alternative_phone,
                $sub_contractor->email,
                $sub_contractor->address
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows();
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function sub_contractor_options()
    {
        $options[''] = '&nbsp;';
        $sub_contractors = $this->get(0,0,'','name');
        foreach($sub_contractors as $sub_contractor){
            $options[$sub_contractor->{$sub_contractor::DB_TABLE_PK}] = $sub_contractor->name;
        }
        return $options;
    }
}

