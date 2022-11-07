<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 02/04/2018
 * Time: 14:37
 */
class Internal_transfer_asset_item extends MY_Model
{

    const DB_TABLE = 'internal_transfer_asset_items';
    const DB_TABLE_PK = 'id';

    public $transfer_id;
    public $asset_sub_location_history_id;
    public $source_sub_location_id;
    public $remarks;

    public function asset_sub_location_history()
    {
        $this->load->model('asset_sub_location_history');
        $asset_sub_location_history = new Asset_sub_location_history();
        $asset_sub_location_history->load($this->asset_sub_location_history_id);
        return $asset_sub_location_history;
    }

    public function source_sub_location()
    {
        $this->load->model('sub_location');
        $source_sub_location = new Sub_location();
        $source_sub_location->load($this->source_sub_location_id);
        return $source_sub_location;
    }

    public function destination_sub_location()
    {
        return $this->asset_sub_location_history()->sub_location();
    }


}

