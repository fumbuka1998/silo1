<?php

class Imprest_cash_item extends MY_Model{
    
    const DB_TABLE = 'imprest_cash_items';
    const DB_TABLE_PK = 'id';

    public $imprest_id;
    public $description;
    public $quantity;
    public $rate;
   

}

