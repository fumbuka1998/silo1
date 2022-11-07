<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 02/02/2018
 * Time: 11:27
 */
class Asset_item extends MY_Model
{

    const DB_TABLE = 'asset_items';
    const DB_TABLE_PK = 'id';

    public $asset_name;
    public $asset_group_id;
    public $part_number;
    public $description;
    public $created_by;

    public function asset_group()
    {
        $this->load->model('asset_group');
        $asset_group = new Asset_group();
        $asset_group->load($this->asset_group_id);
        return $asset_group;
    }

    public function name_with_part_number()
    {
        return $this->asset_name.($this->part_number != '' ? ' - '.$this->part_number : '');
    }

    public function asset_items_list($limit, $start, $keyword, $order){

        $asset_group_id = $this->input->post('asset_group_id');

        if($asset_group_id!=''){

            $where = ' asset_group_id ="'.$asset_group_id.'" ';

        }else{ $where = '';}

        $records_total = $this->count_rows($where);

        if($keyword != ''){
            $where .= $where != '' ? ' AND ' : '';
            $where .= ' (asset_name LIKE "%'.$keyword.'%" OR part_number LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" )';
        }

        //order string
        $order_string = dataTable_order_string(['asset_name','asset_group_id','part_number','description'],$order,'asset_name');

        $asset_items = $this->get($limit,$start,$where,$order_string);

        $rows = [];

        $this->load->model('asset_group');
        $data['asset_group_options'] = $this->asset_group->dropdown_options();
        foreach ($asset_items as $asset_item){
            $data['asset_item'] = $asset_item;
            $rows[] = [
                $asset_item->asset_name,
                $asset_item->asset_group()->group_name,
                $asset_item->part_number,
                $asset_item->description,
                $this->load->view('assets/settings/asset_items/asset_item_actions',$data,true)
            ];
        }

        $records_filtered = $this->count_rows($where);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function dropdown_options()
    {
        $options[''] = '&nbsp;';
        $asset_items = $this->get();
        foreach ($asset_items as $asset_item){
            $options[$asset_item->{$asset_item::DB_TABLE_PK}] = $asset_item->asset_name;
        }
        return $options;
    }

    public function excel_dropdown_list(){
        $results = $this->get();
        $list = '';
        foreach ($results as $row){
            $list .= $row->asset_name.',';
        }
        return rtrim($list,',');
    }

    public function sub_location_assets($sub_location_ids,$project_id = null,$asset_group_id = null){
        $sql = 'SELECT DISTINCT asset_item_id FROM assets';
        if(!is_null($asset_group_id)){
            $sql .= ' LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id';
        }
        $sql .= ' LEFT JOIN asset_sub_location_histories AS main_table ON assets.id = main_table.asset_id
                  WHERE asset_id NOT IN(
                    SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                    AND sub_table.id > main_table.id
                  ) AND main_table.id NOT IN (
                      SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                  ) AND sub_location_id IN (' .$sub_location_ids.')
                    AND main_table.id NOT IN (
                         SELECT source_sub_location_history_id FROM external_transfer_asset_items
                       )';

        if($project_id != 'all' && !is_null($project_id)){
            $sql .= ' AND main_table.asset_id NOT IN (
                      SELECT asset_id FROM asset_cost_center_assignment_items
                      LEFT JOIN asset_cost_center_assignments ON asset_cost_center_assignment_items.asset_cost_center_assignment_id = asset_cost_center_assignments.id
                      LEFT JOIN asset_sub_location_histories ON asset_cost_center_assignment_items.asset_sub_location_history_id = asset_sub_location_histories.id
                      WHERE source_project_id = '. $project_id .' AND main_table.sub_location_id = asset_sub_location_histories.sub_location_id
                    )';
        }

        if(is_null($project_id)){
            $sql .= ' AND project_id IS NULL ';
        } else if(!is_null($asset_group_id)){
            $sql .= ' AND asset_items.asset_group_id ='.$asset_group_id;
        } else if($project_id != 'all') {
            $sql .= ' AND project_id = '.$project_id;
        }

        $query = $this->db->query($sql);
        $results = $query->result();
        $asset_items = [];
        foreach ($results as $row) {
            $asset_item = new self();
            $asset_item->load($row->asset_item_id);
            $asset_items[] = $asset_item;
        }
        return $asset_items;
    }

    public function sub_location_available_stock($sub_location_ids,$project_id = null,$count = false,$status = null,$asset_group_id = null){
        $sql = 'SELECT asset_id FROM assets';
    if(!is_null($asset_group_id)){
        $sql .= ' LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id';
    }
        $sql .= ' LEFT JOIN asset_sub_location_histories main_table ON assets.id = main_table.asset_id
                  WHERE asset_item_id = '.$this->{$this::DB_TABLE_PK};

    if(!is_null($status)){
        $sql .= ' AND status="'.$status.'"';
    }
        $sql .= ' AND asset_id NOT IN(
                    SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                    AND sub_table.id > main_table.id
                  ) AND main_table.id NOT IN (
                      SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                  ) AND sub_location_id IN (' .$sub_location_ids.')
                    AND main_table.id NOT IN (
                        SELECT source_sub_location_history_id FROM external_transfer_asset_items
                      )';

        if($project_id != 'all' && !is_null($project_id)){
            $sql .= ' AND main_table.asset_id NOT IN (
                      SELECT asset_id FROM asset_cost_center_assignment_items
                      LEFT JOIN asset_cost_center_assignments ON asset_cost_center_assignment_items.asset_cost_center_assignment_id = asset_cost_center_assignments.id
                      LEFT JOIN asset_sub_location_histories ON asset_cost_center_assignment_items.asset_sub_location_history_id = asset_sub_location_histories.id
                      WHERE source_project_id = '. $project_id .' AND main_table.sub_location_id = asset_sub_location_histories.sub_location_id
                    )';
        }

        if(is_null($project_id)){
            $sql .= ' AND project_id IS NULL ';
        } else if(!is_null($asset_group_id)){
            $sql .= ' AND asset_items.asset_group_id ='.$asset_group_id;
        } else if($project_id != 'all') {
            $sql .= ' AND project_id = '.$project_id;
        }

        $query = $this->db->query($sql);
        if($count){
            return $query->num_rows();
        } else {
            $this->load->model('asset');
            $results = $query->result();
            $assets = [];
            foreach ($results as $row) {
                $asset = new Asset();
                $asset->load($row->asset_id);
                $assets[] = $asset;
            }
            return $assets;
        }
    }

    public function stock_sale_asset_items($stock_sale_id){
        $sql = 'SELECT * FROM stock_sales_asset_items
                LEFT JOIN asset_sub_location_histories ON stock_sales_asset_items.asset_sub_location_history_id = asset_sub_location_histories.id
                LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                WHERE stock_sale_id = '.$stock_sale_id.' AND  asset_item_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->result();

    }

    public function asset(){
        $this->load->model('asset');
        $where['asset_item_id'] = $this->{$this::DB_TABLE_PK};
        $assets = $this->asset->get(0,0,$where);
        foreach($assets as $asset){
            return $asset;
        }
    }

    public function projects_with_this_item(){
        $sql = 'SELECT DISTINCT project_id FROM asset_sub_location_histories AS main_table
                LEFT JOIN assets ON main_table.asset_id = assets.id
                WHERE asset_item_id ='.$this->{$this::DB_TABLE_PK}.'
                ';
        $results = $this->db->query($sql)->result();
        $this->load->model('project');
        $projects = [];
        foreach($results as $result){
            $project = new Project();
            $project->load($result->project_id);
            $projects[$result->project_id] = $project;
        }
        return $projects;
    }


}

