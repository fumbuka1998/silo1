<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 05/04/2018
 * Time: 17:43
 */


class Stock_disposal_asset_item extends MY_Model
{

    const DB_TABLE = 'stock_disposal_asset_items';
    const DB_TABLE_PK = 'id';

    public $disposal_id;
    public $asset_sub_location_history_id;
    public $remarks;

    public function asset_sub_location_history()
    {
        $this->load->model('asset_sub_location_history');
        $asset_sub_location_history = new Asset_sub_location_history();
        $asset_sub_location_history->load($this->asset_sub_location_history_id);
        return $asset_sub_location_history;
    }
}