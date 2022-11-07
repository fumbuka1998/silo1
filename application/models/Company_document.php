<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 6/9/2018
 * Time: 12:37 PM
 */

class Company_document extends MY_Model{

    const DB_TABLE = 'company_documents';
    const DB_TABLE_PK = 'id';

    public $attachment_id;

    public function attachment()
    {
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function company_attachments_list($limit,$start,$keyword,$order){
        $order_string = dataTable_order_string(['created_at','caption'],$order,'created_at');
        $order_string = ' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;
        $records_total = $this->count_rows();

        $where = '';
        if($keyword != ''){
            $where = ' AND (attachment_name LIKE "%'.$keyword.'%" OR created_at LIKE "%'.$keyword.'%" OR caption LIKE "%'.$keyword.'%") ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS attachments.* FROM attachments
                LEFT JOIN company_documents ON attachments.id = company_documents.attachment_id
                WHERE company_documents.id IS NOT NULL 
                '.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model('attachment');

        $rows = [];
        foreach($results as $row){
            $attachment = new Attachment();
            $attachment->load($row->id);
            $rows[] = [
                standard_datetime($row->created_at),
                $row->caption,
                $attachment->action_buttons(),
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }


}


