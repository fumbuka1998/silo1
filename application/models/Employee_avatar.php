<?php

class Employee_avatar extends MY_Model{
    
    const DB_TABLE = 'employees_avatars';
    const DB_TABLE_PK = 'avatar_id';

    public $avatar_name;
    public $employee_id;
    public $datetime_uploaded;

}

