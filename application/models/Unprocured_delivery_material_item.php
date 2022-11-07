<?php
class Unprocured_delivery_material_item extends MY_Model{
    const DB_TABLE = 'unprocured_delivery_material_items';
    const DB_TABLE_PK = 'item_id';

    public $delivery_id;
    public $material_item_id;
    public $quantity;
    public $price;
    public $remarks;
}
