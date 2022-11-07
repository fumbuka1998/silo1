<?php

class Tender_component_lumpsum_price extends MY_Model
{

    const DB_TABLE = 'tender_component_lumpsum_prices';
    const DB_TABLE_PK = 'id';

    public $tender_lumpsum_price_id;
    public $tender_component_id;

    public function tender_lumpsum_price()
    {
        $this->load->model('tender_lumpsum_price');
        $tender_lumpsum_price = new Tender_lumpsum_price();
        $tender_lumpsum_price->load($this->tender_lumpsum_price_id);
        return $tender_lumpsum_price;
    }
}

