<?php

class Cost_center_requisition extends MY_Model{
    
    const DB_TABLE = 'cost_center_requisitions';
    const DB_TABLE_PK = 'id';

    public $cost_center_id;
    public $requisition_id;

    public function cost_center()
    {
        $this->load->model('cost_center');
        $cost_center = new Cost_center();
        $cost_center->load($this->cost_center_id);
        return $cost_center;
    }

}

