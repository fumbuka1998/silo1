<?php

class Bank extends MY_Model
{
    const DB_TABLE = 'banks';
    const DB_TABLE_PK = 'id';

    public $bank_name;
    public $description;

    public function bank_options()
    {
       $banks = $this->get();
       $options[] = '&nbsp;';
       foreach ($banks as $bank){
           $options[$bank->{$bank::DB_TABLE_PK}] = $bank->bank_name;
       }
       return $options;
    }

}