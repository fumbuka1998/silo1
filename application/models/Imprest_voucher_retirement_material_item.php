<?php
/**
 * Created by PhpStorm.
 * User: MIRALEARN
 * Date: 9/6/2018
 * Time: 4:39 PM
 */
class Imprest_voucher_retirement_material_item extends MY_Model
{
    const DB_TABLE = 'imprest_voucher_retirement_material_items';
    const DB_TABLE_PK = 'id';

    public $imprest_voucher_retirement_id;
    public $item_id;
    public $quantity;
    public $rate;

    public function material_item(){
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->item_id);
        return $material_item;
    }
}