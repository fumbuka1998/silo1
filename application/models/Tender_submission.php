<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 4:34 PM
 */
class Tender_submission extends MY_Model
{

    const DB_TABLE = 'tender_submissions';
    const DB_TABLE_PK = 'id';

    public $date_submitted;
    public $tender_id;
    public $submitted_by;
    public $created_by;

    public function submission_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['submission_no','tender_name','created_at'],$order,'tender_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = '';
        if($keyword != '') {
            $where_clause = 'submission_no LIKE "%'.$keyword.'%" OR tender_name LIKE "%'.$keyword.'%"';
        }

        $sql = 'SELECT COUNT(id) AS records_total FROM tender_submissions';

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        $sql = 'SELECT SQL_CALC_FOUND_ROWS tender_submissions.id AS submission_no,tender_submissions.date_submitted,CONCAT(employees.first_name," ",employees.last_name) AS creater_name,tender_name
                    FROM tender_submissions
                    LEFT JOIN tenders ON tender_submissions.tender_id = tenders.id
                    LEFT JOIN employees ON tender_submissions.created_by = employees.employee_id

                   '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach($results as $row){
            $rows[] = [
                $row->tender_name,
                $row->date_submitted,
                $row->creater_name,
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
