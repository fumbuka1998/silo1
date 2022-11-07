<?php
/**
* Created by PhpStorm.
* User: Munyaki
* Date: 03-Oct-17
* Time: 1:33 PM
*/

class Material_cost_center_assignment_item extends MY_Model{

    const DB_TABLE = 'material_cost_center_assignment_items';
    const DB_TABLE_PK = 'id';
    
    public $stock_id;
    public $material_cost_center_assignment_id;
    
    
    public function stock(){
    $this->load->model('Material_stock');
    $Material_stock= new Material_stock();
    $Material_stock->load($this->stock_id);
    return $Material_stock;
    }


}