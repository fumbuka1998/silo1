<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/6/2018
 * Time: 1:30 PM
 */
class Asset_handover_item extends MY_Model
{

    const DB_TABLE = 'asset_handover_items';
    const DB_TABLE_PK = 'id';

    public $asset_handover_id;
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
