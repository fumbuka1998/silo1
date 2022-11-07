<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/27/2018
 * Time: 12:49 PM
 */

class Tender_component_material_price extends MY_Model{
    const DB_TABLE = 'tender_component_material_prices';
    const DB_TABLE_PK = 'id';

    public $tender_component_id;
    public $tender_material_price_id;

}