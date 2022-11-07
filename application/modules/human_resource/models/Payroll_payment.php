<?php

class Payroll_payment extends MY_Model
{

    const DB_TABLE = 'payroll_payments';
    const DB_TABLE_PK = 'id';

    public $payroll_id;
    public $loan_name;
    public $created_by;

    public function chek_if_this_payment_was_made($payroll_id,$payment_name)
    {
        $payment = $this->get(1,0,['payroll_id' => $payroll_id, 'loan_name' => $payment_name]);

        return $payment ? 'true' : 'false';
    }

}