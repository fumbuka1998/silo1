<?php
class Asset_depreciation_rate_item extends MY_Model{

    const DB_TABLE = 'asset_depreciation_rate_items';
    const DB_TABLE_PK = 'id';

    public $asset_depreciation_rate_id;
    public $asset_group_id;
    public $rate;

    public function depreciation_rate_items(){
        $rates = $this->get();
        return $rates;
    }
    public function asset_group(){

    	$this->load->model('Asset_group');
        $Asset_group = new Asset_group();
        $Asset_group->load($this->asset_group_id);
        return $Asset_group;

    }


    public function asset_depreciation_rate(){

        $this->load->model('Asset_depreciation_rate');
        $Asset_depreciation_rate = new Asset_depreciation_rate();
        $Asset_depreciation_rate->load($this::DB_TABLE_PK);
        return $Asset_depreciation_rate;

    }


}