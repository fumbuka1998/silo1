<?php

class Project_imprest_voucher_item extends MY_Model
{
    const DB_TABLE = 'project_imprest_voucher_items';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $imprest_voucher_service_item_id;
    public $imprest_voucher_cash_item_id;


}