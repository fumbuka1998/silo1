<?php

class External_material_transfer_grn extends MY_Model{
    
    const DB_TABLE = 'external_material_transfer_grns';
    const DB_TABLE_PK = 'id';

    public $grn_id;
    public $transfer_id;

    public function transfer()
    {
        $this->load->model('external_material_transfer');
        $transfer = new External_material_transfer();
        $transfer->load($this->transfer_id);
        return $transfer;
    }

}

