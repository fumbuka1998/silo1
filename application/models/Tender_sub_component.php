<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/27/2018
 * Time: 1:02 PM
 */

class Tender_sub_component extends MY_Model{
    const DB_TABLE = 'tender_sub_components';
    const DB_TABLE_PK = 'id';

    public $tender_component_id;
    public $sub_component_name;
    public $lumpsum_price;
    public $created_by;


    public function tender_sub_components_list($tender_component_id,$limit,$start,$keyword,$order){
        $where = '';
        if($keyword!=''){
            $where .= 'AND (sub_component_name LIKE "%'.$keyword.'%" OR lumpsum_price LIKE "@'.$keyword.'@" OR created_by LIKE "@'.$keyword.'@")';
        }

        $order_string = dataTable_order_string(['sub_component_name','lumpsum_price','created_by'],$order,"sub_component_name");
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $sql = 'SELECT COUNT(id) AS records_total FROM tender_sub_components';

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;


        $sql = 'SELECT SQL_CALC_FOUND_ROWS tender_sub_components.id AS sub_component_no,sub_component_name,lumpsum_price,created_by 
                FROM tender_sub_components
                WHERE tender_component_id='.$tender_component_id.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];

        foreach ($results as $row){
            $tender_sub_component = new self();
            $tender_sub_component->load($row->sub_component_no);
            $this->load->model('tender_component');
            $data['tender_sub_component']= $tender_sub_component;
            $rows[] = [
                $row->sub_component_name,
                '<span class="pull-right">'.number_format($row->lumpsum_price,2).'</span>',
                $this->load->view('tenders/profile/components/tender_sub_component_actions',$data,true)
            ];
        }


        $data = Array(
            'data' => $rows,
            'recordsFiltered' =>$records_filtered,
            'recordsTotal'=>$records_total,

        );

        return json_encode($data);
    }
}