<?php
/**
 * Created by PhpStorm.
 * User: MIRALEARN
 * Date: 9/6/2018
 * Time: 4:38 PM
 */

class Imprest_voucher_retirement_asset_item extends MY_Model
{
    const DB_TABLE = 'imprest_voucher_retirement_asset_items';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_retirement_id;
    public $asset_item_id;
    public $book_value;
    public $quantity;


    public function asset_item()
    {
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        $asset_item->load($this->asset_item_id);
        return $asset_item;
    }

}