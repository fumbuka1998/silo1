<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 3/28/2018
 * Time: 1:55 PM
 */

class Asset_sub_location_history extends MY_Model
{

    const DB_TABLE = 'asset_sub_location_histories';
    const DB_TABLE_PK = 'id';

    public $asset_id;
    public $sub_location_id;
    public $received_date;
    public $book_value;
    public $project_id;
    public $description;
    public $created_by;

    public function asset()
    {
        $this->load->model('asset');
        $asset = new Asset();
        $asset->load($this->asset_id);
        return $asset;
    }

    public function sub_location()
    {
        $this->load->model('sub_location');
        $sub_location = new Sub_location();
        $sub_location->load($this->sub_location_id);
        return $sub_location;
    }

    public function unassigned_assets_options(){
        $this->load->model('asset');
        $sql = 'SELECT asset_id FROM '.$this::DB_TABLE.'
        LEFT JOIN assets ON '.$this::DB_TABLE.'.asset_id = assets.id
        WHERE status = "active" AND  ownership = "OWNED" AND project_id IS NULL';
        $unassigned_assets = $this->db->query($sql)->result();
        $options[] = '&nbsp;';
        foreach($unassigned_assets as $object){
            $asset = new Asset();
            $asset->load($object->asset_id);
            $options[$object->asset_id] = $asset->asset_code();
        }
        return $options;
    }

    
}
