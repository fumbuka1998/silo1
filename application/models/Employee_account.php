<?php

class Employee_account extends MY_Model
{

    const DB_TABLE = 'employee_accounts';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $account_id;
    public $created_by;


}