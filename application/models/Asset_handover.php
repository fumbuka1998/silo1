<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/6/2018
 * Time: 1:57 PM
 */
class Asset_handover extends MY_Model
{

    const DB_TABLE = 'asset_handovers';
    const DB_TABLE_PK = 'id';

    public $location_id;
    public $handover_date;
    public $handler_id;
    public $comments;
    public $created_by;

    public function assets_handover_list($location_id,$limit, $start, $keyword, $order){

        $where = '';

        $order_string = dataTable_order_string(['handover_no','handover_date','handler_name','assignor_name'],$order,'handover_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;
        $sql = 'SELECT COUNT(id) AS records_total FROM asset_handovers WHERE location_id = '.$location_id;
        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if($keyword !=''){
            $where .= ' AND (handover_date LIKE "%'.$keyword.'%" OR assignors.first_name LIKE "%'.$keyword.'%"  OR assignors.last_name LIKE "%'.$keyword.'%" OR handlers.first_name LIKE "%'.$keyword.'%"  OR handlers.last_name LIKE "%'.$keyword.'%"  )';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS asset_handovers.id AS handover_no, CONCAT(assignors.first_name," ",assignors.last_name) AS assignor_name,CONCAT(handlers.first_name," ",handlers.last_name) AS handler_name, handover_date,comments FROM asset_handovers
                LEFT JOIN employees AS assignors ON asset_handovers.created_by = assignors.employee_id
                LEFT JOIN employees AS handlers ON asset_handovers.handler_id = handlers.employee_id
              WHERE location_id = '.$location_id.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $this->load->model(['inventory_location','asset']);
        $location = new Inventory_location();
        $location->load($location_id);
        $data['location'] = $location;
        $data['employee_options'] = employee_options();
        $data['asset_stock_options'] = $this->asset->location_asset_options('location',$location_id);
        foreach ($results as $row){
            $handover = new self();
            $handover->load($row->handover_no);
            $data['handover'] = $handover;
            $rows[] = [
                $handover->handover_number(),
                custom_standard_date($row->handover_date),
                $row->handler_name,
                $row->assignor_name,
                $this->load->view('inventory/assets/handovers/assets_handover_actions',$data,true)
            ];
        }

        $data['data'] = $rows;
        $data['recordsFiltered'] = $records_filtered;
        $data['recordsTotal'] = $records_total;
        return json_encode($data);

    }

    public function items()
    {
        $this->load->model('asset_handover_item');
        return $this->asset_handover_item->get(0,0,['asset_handover_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function handover_number(){
        return 'H.O/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function clear_items(){
        $this->db->where('asset_handover_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('asset_handover_items');
    }

    public function handler()
    {
        $this->load->model('employee');
        $handler = new Employee();
        $handler->load($this->handler_id);
        return $handler;
    }

    public function assignor()
    {
        $this->load->model('employee');
        $assignor = new Employee();
        $assignor->load($this->created_by);
        return $assignor;
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }


}