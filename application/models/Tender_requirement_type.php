<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 3:11 PM
 */
class Tender_requirement_type extends MY_Model
{
    const DB_TABLE = 'tender_requirement_types';
    const DB_TABLE_PK = 'id';

    public $requirement_name;
    public $description;
    public $created_by;




    public function requirement_type_list($limit, $start, $keyword, $order){
        $where ='';
        if($keyword !=''){  
            $where = 'requirement_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
        }
        $orderstring = dataTable_order_string(['requirement_name ','description'],$order,'requirement_name');
        $sql = 'SELECT COUNT(id) AS records_total FROM tender_requirement_types';
        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;
        $records_filtered = $this->count_rows($where);
        $tender_requirement_types = $this->get($limit,$start,$where,$orderstring);
        $rows = [];
        foreach ($tender_requirement_types as $tender_requirement_type){
            $data['tender_requirement_type'] = $tender_requirement_type;
            $rows[] = [
                $tender_requirement_type->{$tender_requirement_type :: DB_TABLE_PK},
                $tender_requirement_type->requirement_name,
                $tender_requirement_type->description,
                $this->load->view('tenders/settings/requirement_type_actions',$data,true)
            ];

        }
        $data['data'] = $rows;
        $data['recordsFiltered'] = $records_filtered;
        $data['recordsTotal'] = $records_total;
        return json_encode($data);

    }

    public function dropdown_options(){
        $tenders_requirements = $this->get();
        $options = [];
        foreach ($tenders_requirements as $tenders_requirement){
            $options[$tenders_requirement->{$this::DB_TABLE_PK}] = $tenders_requirement->requirement_name;
        }
        return $options;

    }
}