<?php

class Exchange_rate_update extends MY_Model{
    
    const DB_TABLE = 'exchange_rate_updates';
    const DB_TABLE_PK = 'id';

    public $update_date;
    public $currency_id;
    public $exchange_rate;

}

