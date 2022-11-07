<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 04/11/2017
 * Time: 17:14
 */

class Imprest_grn extends MY_Model{
    
    const DB_TABLE = 'imprest_grns';
    const DB_TABLE_PK = 'id';

    public $imprest_id;
    public $grn_id;

    public function imprest()
    {
        $this->load->model('imprest');
        $imprest = new Imprest();
        $imprest->load($this->imprest_id);
        return $imprest;
    }

    public function good_received_note()
    {
        $this->load->model('good_received_note');
        $good_received_note = new Good_received_note();
        $good_received_note->load($this->grn_id);
        return $good_received_note;
    }

}

