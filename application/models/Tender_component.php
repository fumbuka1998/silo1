<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/27/2018
 * Time: 12:45 PM
 */

class Tender_component extends MY_Model{
    const DB_TABLE = 'tender_components';
    const DB_TABLE_PK = 'id';

    public $tender_id;
    public $lumpsum_price;
    public $component_name;
    public $created_by;

    public function material_prices()
    {
        $this->load->model('tender_material_price');
        return $this->tender_material_price->get(0,0,' id IN(SELECT tender_material_price_id FROM tender_component_material_prices WHERE tender_component_id = '.$this->{$this::DB_TABLE_PK}.')');
    }

    public function lumpsum_prices()
    {
        $this->load->model('tender_lumpsum_price');
        return $this->tender_lumpsum_price->get(0,0,' id IN(SELECT tender_lumpsum_price_id FROM tender_component_lumpsum_prices WHERE tender_component_id = '.$this->{$this::DB_TABLE_PK}.')');

    }



}

