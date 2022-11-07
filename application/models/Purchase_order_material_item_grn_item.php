<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 18/01/2018
 * Time: 12:53
 */
class Purchase_order_material_item_grn_item extends MY_Model
{

    const DB_TABLE = 'purchase_order_material_item_grn_items';
    const DB_TABLE_PK = 'id';

    public $goods_received_note_item_id;
    public $purchase_order_material_item_id;

}

