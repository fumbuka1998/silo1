<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 21/06/2019
 * Time: 10:32
 */

class Asset_cost_center_assignment_item extends MY_Model{
    const DB_TABLE = 'asset_cost_center_assignment_items';
    const DB_TABLE_PK = 'id';

    public $asset_sub_location_history_id;
    public $asset_cost_center_assignment_id;


    public function asset_sub_location_history(){
        $this->load->model('asset_sub_location_history');
        $history = new Asset_sub_location_history();
        $history->load($this->asset_sub_location_history_id);
        return $history;
    }

}