<?php
class Unprocured_delivery_asset_item extends MY_Model{
    const DB_TABLE = 'unprocured_delivery_asset_items';
    const DB_TABLE_PK = 'item_id';

    public $delivery_id;
    public $asset_item_id;
    public $quantity;
    public $price;
    public $remarks;
}
