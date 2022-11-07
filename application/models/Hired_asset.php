<?php
/**
 * Created by PhpStorm.
 * User: genesis
 * Date: 2019-05-18
 * Time: 10:10
 */

class Hired_asset extends MY_Model
{
    const DB_TABLE = 'hired_assets';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $sub_location_id;
    public $vendor_id;
    public $client_id;
    public $asset_id;
    public $hired_date;
    public $hiring_cost;
    public $dead_line;
    public $type;
    public $status;


    public function asset(){
        $this->load->model('asset');
        $asset = new Asset();
        $asset->load($this->asset_id);
        return $asset;
    }

    public function hired_assets($limit, $start, $keyword, $order, $type)
    {
        $order_string = dataTable_order_string(['asset_name','asset_code','hired_date','vendor_name', 'dead_line', 'project_name', 'hired_assets.status',],$order,'asset_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = ' WHERE type = "'.strtoupper($type).'"';

        $sql = 'SELECT * FROM hired_assets'.$where_clause;
        $query = $this->db->query($sql);
        $records_total = $query->num_rows();
        if($keyword != ''){
            $where_clause .= ' AND (asset_name LIKE "%'.$keyword.'%" OR asset_code LIKE "%'.$keyword.'%"  OR
                                   hired_date LIKE "%'.$keyword.'%" OR vendor_name LIKE "%'.$keyword.'%" OR 
                                   dead_line LIKE "%'.$keyword.'%" OR project_name LIKE "%'.$keyword.'%" OR
                                   hired_assets.status LIKE "%'.$keyword.'%") ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS hired_assets.id, asset_name, asset_code, hired_date, hiring_cost,(
                            CASE 
                                WHEN type = "'.strtoupper($type).'" AND type = "SUPPLIERS" THEN vendor_name
                                ELSE client_name
                            END
                      ) AS other_end_name, dead_line, project_name, hired_assets.status, asset_items.description,
                      asset_group_id, part_number, (
                            CASE 
                                WHEN type = "'.strtoupper($type).'" AND type = "SUPPLIERS" THEN hired_assets.vendor_id
                                ELSE hired_assets.client_id
                            END
                      ) AS other_end_id, hired_assets.project_id, hired_assets.sub_location_id, (
                            CASE 
                                WHEN type = "'.strtoupper($type).'" AND type = "SUPPLIERS" THEN "procurements/vendor_profile/"
                                ELSE "clients/profile/"
                            END
                      ) AS link
                      FROM hired_assets
                LEFT JOIN assets ON hired_assets.asset_id = assets.id
                LEFT JOIN vendors ON hired_assets.vendor_id = vendors.vendor_id
                LEFT JOIN clients ON hired_assets.client_id = clients.client_id
                LEFT JOIN sub_locations ON hired_assets.sub_location_id = sub_locations.sub_location_id
                LEFT JOIN projects ON hired_assets.project_id = projects.project_id
                LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id 
                '.$where_clause.$order_string;
        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];

        $data['asset_item_options'] = $this->asset_item->dropdown_options();
        $data['project_options'] = $this->project->project_dropdown_options();
        $data['asset_group_options'] = $this->asset_group->dropdown_options();
        $data['vendor_options'] = $this->vendor->vendor_options();
        $data['client_options'] = $this->client->clients_options();
        $data['asset_options'] = $this->asset_sub_location_history->unassigned_assets_options();
        $data['list_type'] = $type;
        foreach ($results as $row){
            $this->load->model(['project',  'asset_group', 'vendor','sub_location']);
            $sub_location = new Sub_location();
            $sub_location->load($row->sub_location_id);
            $hired_asset = new self();
            $hired_asset->load($row->id);
            $data['sub_location_name'] = $sub_location->sub_location_name;
            $data['hired_asset'] = $hired_asset;
            $data['status'] = $row->status;

            if($row->status == "ACTIVE"){
                $status = '<span class="fa fa-success">ACTIVE</span>';
            } else {
                $status = '<span class="fa fa-danger">INACTIVE</span>';
            }

            $rows[] = strtoupper($type) == "CLIENTS" ? [
                $row->asset_name,
                $row->asset_code,
                set_date($row->hired_date),
                anchor(base_url($row->link.$row->other_end_id),$row->other_end_name,'target="_blank"'),
                set_date($row->dead_line),
                number_format($row->hiring_cost),
                $status,
                $this->load->view('assets/hired_assets/list_actions', $data, true)
            ] : [
                $row->asset_name,
                $row->asset_code,
                set_date($row->hired_date),
                anchor(base_url($row->link.$row->other_end_id),$row->other_end_name,'target="_blank"'),
                set_date($row->dead_line),
                number_format($row->hiring_cost),
                anchor(base_url('projects/profile/'.$row->project_id),$row->project_name,'targer="_blank"'),
                $status,
                $this->load->view('assets/hired_assets/list_actions', $data, true)

            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows,
        ];
        return json_encode($json);

    }

    public function dropdown_options($project_id){
        $this->load->model('asset');
        $hired_assets = $this->get(0,0,['project_id'=>$project_id],'id ASC');
        $hired_asset_options = [];
        if(!empty($hired_assets)){
            foreach($hired_assets as $hired_asset){
                $asset = new Asset();
                $asset->load($hired_asset->asset_id);
                $hired_asset_options[$asset->{$asset::DB_TABLE_PK}] = $asset->asset_code();
            }
        }
        return $hired_asset_options;
    }

}