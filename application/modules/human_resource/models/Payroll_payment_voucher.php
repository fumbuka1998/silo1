<?php

class Payroll_payment_voucher extends MY_Model
{

    const DB_TABLE = 'payroll_payment_vouchers';
    const DB_TABLE_PK = 'id';

    public $payroll_id;
    public $payment_voucher_id;
    public $payment_name;
    public $created_by;

}