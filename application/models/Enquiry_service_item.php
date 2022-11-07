<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/18/2018
 * Time: 7:46 AM
 */

class Enquiry_service_item extends MY_Model{
    const DB_TABLE = 'enquiry_service_items';
    const DB_TABLE_PK = 'id';


    public $enquiry_id;
    public $description;
    public $quantity;
    public $measurement_unit_id;
    public $remarks;

    public function measurement_unit(){
        $this->load->model('measurement_unit');
        $unit = new Measurement_unit();
        $unit->load($this->measurement_unit_id);
        return $unit;
    }
}