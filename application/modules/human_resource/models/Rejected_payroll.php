<?php

class Rejected_payroll extends MY_Model
{

    const DB_TABLE = 'rejected_payrolls';
    const DB_TABLE_PK = 'id';

    public $payroll_id;
    public $current_level;
    public $reject_coments;
    public $status;
    public $created_by;

}