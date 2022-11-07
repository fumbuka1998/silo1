<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 25/04/2018
 * Time: 08:57
 */
class Purchase_order_asset_item_reject extends MY_Model
{

    const DB_TABLE = 'Purchase_order_asset_item_rejects';
    const DB_TABLE_PK = 'id';

    public $grn_id;
    public $rejected_quantity;
    public $purchase_order_asset_item_id;


}

