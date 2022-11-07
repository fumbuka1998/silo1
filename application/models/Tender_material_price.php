<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/30/2018
 * Time: 8:40 AM
 */

class Tender_material_price extends MY_Model{

    const DB_TABLE = 'tender_material_prices';
    const DB_TABLE_PK = 'id';

    public $material_item_id;
    public $quantity;
    public $price;
    public $description;
    public $created_by;

    public function material_price_list($tender_component_id,$limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['item_name','quantity','price','description'],$order,'item_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = ' WHERE tender_component_id = '.$tender_component_id;


        $sql = 'SELECT COUNT(id) AS records_total FROM tender_component_material_prices '.$where_clause;

        if($keyword != '') {
            $where_clause .= ' AND (item_name LIKE "%'.$keyword.'%" OR quantity LIKE "%'.$keyword.'%" OR price LIKE "%'.$keyword.'%")';
        }

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        $sql = 'SELECT SQL_CALC_FOUND_ROWS tender_material_prices.id AS material_price_no,tender_material_prices.description,tender_material_prices.quantity,tender_material_prices.price,material_items.item_name
                    FROM tender_material_prices
                    LEFT JOIN material_items ON tender_material_prices.material_item_id = material_items.item_id
                    LEFT JOIN tender_component_material_prices ON tender_material_prices.id = tender_component_material_prices.tender_material_price_id
                    '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach($results as $row){
            $material_price = new self();
            $material_price->load($row->material_price_no);
            $data['material_price'] = $material_price;
            $rows[] = [
                $row->item_name,
                $row->quantity,
                '<span class="pull-right">'.number_format($row->price,2).'</span>',
                $row->description,
                $this->load->view('tenders/profile/material_price/tender_material_price_actions',$data,true)
            ];
        }

        $json = [
            "data" => $rows,
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered

        ];
        return json_encode($json);
    }

    public function material_item()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }
}