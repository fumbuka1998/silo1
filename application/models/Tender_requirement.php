<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 2:56 PM
 */
class Tender_requirement extends MY_Model{
    const DB_TABLE = 'tender_requirements';
    const DB_TABLE_PK = 'id';

    public $tender_requirement_type_id;
    public $tender_id;
    public $description;
    public $created_by;

    public function requirement_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['requirement_no','requirement_name','tender_name','description'],$order,'tender_name');//see for addition
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = '';
        if($keyword != '') {
            $where_clause = 'requirement_no LIKE "%'.$keyword.'%" OR tender_name LIKE "%'.$keyword.'%"';
        }

        $sql = 'SELECT COUNT(id) AS records_total FROM tender_requirements';

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        //This querry waits for if ther is any changes from table
        $sql = 'SELECT SQL_CALC_FOUND_ROWS tender_requirements.id AS requirement_no,tender_requirements.description,tender_name,requirement_name
                    FROM tender_requirements
                    LEFT JOIN tender_requirement_types ON tender_requirements.tender_requirement_type_id = tender_requirement_types.id
                    LEFT JOIN tenders ON tender_requirements.tender_id = tenders.id
                   '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach($results as $row){
            $rows[] = [
                add_leading_zeros($row->requirement_no),
                $row->requirement_name,
                $row->tender_name,
                $row->description,
                ''
            ];
        }

        $json = [
            "data" => $rows,
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered

        ];
        return json_encode($json);
    }

}
