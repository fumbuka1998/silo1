<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 04/04/2018
 * Time: 10:37
 */
class External_transfer_asset_item extends MY_Model
{

    const DB_TABLE = 'external_transfer_asset_items';
    const DB_TABLE_PK = 'id';

    public $transfer_id;
    public $source_sub_location_history_id;
    public $remarks;

    public function asset_sub_location_history()
    {
        $this->load->model('asset_sub_location_history');
        $asset_sub_location_history = new Asset_sub_location_history();
        $asset_sub_location_history->load($this->source_sub_location_history_id);
        return $asset_sub_location_history;
    }

}

