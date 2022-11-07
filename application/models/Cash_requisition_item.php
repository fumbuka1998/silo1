<?php

class Cash_requisition_item extends MY_Model{
    
    const DB_TABLE = 'cash_requisition_items';
    const DB_TABLE_PK = 'id';

    public $cash_requisition_id;
    public $description;
    public $measurement_unit_id;
    public $requested_quantity;
    public $requested_rate;
    public $approved_quantity;
    public $approved_rate;
    public $requesting_remarks;
    public $approving_remarks;

    public function measurement_unit()
    {
        $this->load->model('measurement_unit');
        $measurement_unit = new Measurement_unit();
        $measurement_unit->load($this->measurement_unit_id);
        return $measurement_unit;
    }

}

