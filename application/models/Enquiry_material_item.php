<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/17/2018
 * Time: 5:54 PM
 */

class Enquiry_material_item extends MY_Model{
    const DB_TABLE = 'enquiry_material_items';
    const DB_TABLE_PK = 'id';

    public $enquiry_id;
    public $material_item_id;
    public $quantity;
    public $remarks;

    public function material_item(){
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

}