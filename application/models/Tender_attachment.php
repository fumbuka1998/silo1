<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 27/04/2018
 * Time: 13:06
 */
class Tender_attachment extends MY_Model
{

    const DB_TABLE = 'tender_attachments';
    const DB_TABLE_PK = 'id';

    public $tender_id;
    public $attachment_id;

    public function attachment()
    {
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function tender_attachments_list($limit,$start,$keyword,$order){
        $order_string = dataTable_order_string(['created_at','caption'],$order,'created_at');
        $order_string = ' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;
        $where = ' tender_id = '. $this->input->post('tender_id');
        $records_total = $this->count_rows($where);

        if($keyword != ''){
            $where .= ' AND (attachment_name LIKE "%'.$keyword.'%" OR created_at LIKE "%'.$keyword.'%" OR caption LIKE "%'.$keyword.'%") ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS attachments.* FROM attachments
                LEFT JOIN tender_attachments ON attachments.id = tender_attachments.attachment_id
                WHERE '.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model('attachment');
        $rows = [];
        foreach($results as $row){
            $attachment= new Attachment();
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

