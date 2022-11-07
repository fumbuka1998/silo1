<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/19/2018
 * Time: 5:10 PM
 */

class Imprest_asset_item extends MY_Model{
    const DB_TABLE = 'imprest_asset_items';
    const DB_TABLE_PK = 'id';

    public $grn_asset_sub_location_history_id;
    public $imprest_id;

}