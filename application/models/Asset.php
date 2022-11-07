<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 12/02/2018
 * Time: 20:24
 */

class Asset extends MY_Model
{

    const DB_TABLE = 'assets';
    const DB_TABLE_PK = 'id';

    public $asset_item_id;
    public $asset_code;
    public $book_value;
    public $useful_life;
    public $salvage_value;
    public $registration_date;
    public $description;
    public $status;
    public $created_by;


    public function asset_code(){
        return !is_null($this->asset_code) && trim($this->asset_code) != '' ? $this->asset_item()->asset_name.'/'.$this->asset_code : $this->asset_item()->asset_name.'/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function barcode(){
        include("./vendor/autoload.php");
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        $asset_code = $this->asset_code();
        $output = '<div style="text-align: center; padding: 0px !important;">
                        <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($asset_code, $generator::TYPE_CODE_128)) . '">
                        <br/>'.$asset_code.'
                </div>';
        return $output;
    }

    public function asset_item()
    {
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        $asset_item->load($this->asset_item_id);
        return $asset_item;
    }

    public function location_assets_datatable($level, $id, $limit, $start, $keyword, $order){
        //Prepare Order String
        $order_string = dataTable_order_string(['asset_name','asset_code','received_date','status'],$order,'received_date');

        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        //Prepare total records
        if($level == 'location') {
            $sql = 'SELECT COUNT(id) AS records_total FROM asset_sub_location_histories AS main_table
                LEFT JOIN sub_locations ON main_table.sub_location_id = sub_locations.sub_location_id
                WHERE asset_id NOT IN(
                  SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                  AND sub_table.id > main_table.id
                ) AND main_table.id NOT IN(
                    SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                ) AND location_id = ' . $id;
        } else {
            //Prepare total records
            $sql = 'SELECT COUNT(id) AS records_total FROM asset_sub_location_histories AS main_table WHERE asset_id NOT IN(
                  SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                  AND sub_table.id > main_table.id
                ) AND main_table.id NOT IN(
                    SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                ) AND sub_location_id = '.$id;
        }

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        $where_clause = ' ';
        if($keyword != ''){
            $where_clause = ' AND (received_date LIKE "%'.$keyword.'%" OR asset_name LIKE "%'.$keyword.'%"  OR status LIKE "%'.$keyword.'%") ';
        }

        //Perform the main query
        if($level == 'location') {
            $sql = 'SELECT  SQL_CALC_FOUND_ROWS received_date,CONCAT(asset_items.asset_name,"/",assets.id) AS asset_code,assets.*,asset_name FROM asset_sub_location_histories AS main_table
                LEFT JOIN sub_locations ON main_table.sub_location_id = sub_locations.sub_location_id
                LEFT JOIN assets ON main_table.asset_id = assets.id
                LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                WHERE asset_id NOT IN(
                  SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                  AND sub_table.id > main_table.id
                ) AND main_table.id NOT IN(
                    SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                ) AND location_id = ' . $id . $where_clause . $order_string;
        } else {
            $sql = 'SELECT  SQL_CALC_FOUND_ROWS received_date,CONCAT(asset_items.asset_name,"/",assets.id) AS asset_code,assets.*,asset_name  FROM asset_sub_location_histories AS main_table
                LEFT JOIN assets ON main_table.asset_id = assets.id
                LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                WHERE asset_id NOT IN(
                  SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                  AND sub_table.id > main_table.id
                ) AND main_table.id NOT IN(
                    SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                ) AND sub_location_id = '.$id.$where_clause.$order_string;
        }

        $query = $this->db->query($sql);
        $results = $query->result();



        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        //Prepare the data array
        $rows = [];
        foreach ($results as $row){
            $asset = new self();
            $asset->load($row->id);
            $data = ['asset' => $asset];
            $data['level'] = $level;
            $data['level_id'] = $id;
            $rows[] = [
                $row->asset_name,
                $asset->asset_code(),
                custom_standard_date($row->received_date),
                $row->status,
                $this->load->view('inventory/assets/assets_datatable_list_actions',$data,true)
            ];

        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);


    }

    public function location_stock($level,$id,$project_id,$asset_group_id = null){

        if(trim($project_id) == '' || is_null($project_id)){
            $project_id = null;
        }

        if($level == 'location') {
            $sql = 'SELECT asset_id FROM asset_sub_location_histories AS main_table';
            if(!is_null($asset_group_id)){
                $sql .= ' LEFT JOIN assets ON main_table.asset_id = assets.id
                          LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id';
            }
            $sql .= ' LEFT JOIN sub_locations ON main_table.sub_location_id = sub_locations.sub_location_id
                      WHERE asset_id NOT IN (
                        SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                        AND sub_table.id > main_table.id
                      ) AND main_table.id NOT IN (
                        SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                      ) AND main_table.id NOT IN (
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

        } else {
            $sql = 'SELECT asset_id FROM asset_sub_location_histories AS main_table';
            if(!is_null($asset_group_id)){
                $sql .= ' LEFT JOIN assets ON main_table.asset_id = assets.id
                          LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id';
            }
            $sql .= ' WHERE asset_id NOT IN (
                      SELECT asset_id FROM asset_sub_location_histories AS sub_table WHERE main_table.asset_id = sub_table.asset_id
                      AND sub_table.id > main_table.id
                    ) AND main_table.id NOT IN (
                        SELECT asset_sub_location_history_id FROM stock_disposal_asset_items
                    ) AND main_table.id NOT IN (
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
        }

        if(!is_null($id)){
            $sql .= ' AND main_table.sub_location_id =' . $id .'';
        }

        if(!is_null($asset_group_id)){
            $sql .= ' AND asset_items.asset_group_id ='.$asset_group_id .'';
        }

        if($project_id != 'all' && !is_null($project_id)){
            $sql .= ' AND project_id = '.$project_id .'';
        }


        if(is_null($project_id)){
            $sql .= ' AND project_id IS NULL';
        }

        $query = $this->db->query($sql);
        $results = $query->result();
        $assets = [];
        foreach ($results as $row){
            $asset = new self();
            $asset->load($row->asset_id);
            $assets[] = $asset;
        }
        return $assets;

    }

    public function location_asset_options($level, $id, $project_id = null)
    {
        $options = ['' => '&nbsp;'];
        $assets = $this->location_stock($level,$id,$project_id);
        foreach ($assets as $asset){
            $options[$asset->{$asset::DB_TABLE_PK}] = $asset->asset_code();
        }
        return $options;
    }

    public function latest_sub_location_history()
    {
        $this->load->model('asset_sub_location_history');
        $histories = $this->asset_sub_location_history->get(1,0,['asset_id' => $this->{$this::DB_TABLE_PK}],' id DESC ');
        return array_shift($histories);
    }


}

