<?php

class Tender_lumpsum_price extends MY_Model
{

    const DB_TABLE = 'tender_lumpsum_prices';
    const DB_TABLE_PK = 'id';

    public $description;
    public $amount;
    public $created_by;


    public function lumpsum_price_list($tender_component_id,$limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['description','amount'],$order,'description');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = '';
        if($keyword != '') {
            $where_clause .= ' AND (description LIKE "%'.$keyword.'%" OR amount LIKE "%'.$keyword.'%" )';
        }

        $sql = 'SELECT COUNT(id) AS records_total FROM tender_component_lumpsum_prices WHERE tender_component_id = '.$tender_component_id;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        $sql = 'SELECT SQL_CALC_FOUND_ROWS tender_lumpsum_prices.id AS lumpsum_price_no,tender_lumpsum_prices.description,tender_lumpsum_prices.amount
                    FROM tender_lumpsum_prices
                    LEFT JOIN tender_component_lumpsum_prices ON tender_lumpsum_prices.id = tender_component_lumpsum_prices.tender_lumpsum_price_id
                    WHERE tender_component_id = '.$where_clause.$tender_component_id.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach($results as $row){
            $lumpsum_price = new self();
            $lumpsum_price->load($row->lumpsum_price_no);
            $data['lumpsum_price'] = $lumpsum_price;
            $rows[] = [
                $row->description,
                '<span class="pull-right">'.number_format($row->amount).'</span>',
                $this->load->view('tenders/profile/lumpsum_price/tender_lumpsum_price_actions',$data,true)
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

