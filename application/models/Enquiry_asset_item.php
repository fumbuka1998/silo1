<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/18/2018
 * Time: 7:42 AM
 */

class Enquiry_asset_item extends MY_Model{
    const DB_TABLE = 'enquiry_asset_items';
    const DB_TABLE_PK = 'id';

    public $enquiry_id;
    public $asset_item_id;
    public $quantity;
    public $remarks;

    public function asset_item(){
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        $asset_item->load($this->asset_item_id);
        return $asset_item;
    }


}