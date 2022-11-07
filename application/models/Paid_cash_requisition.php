<?php

class Paid_cash_requisition extends MY_Model{
    
    const DB_TABLE = 'paid_cash_requisitions';
    const DB_TABLE_PK = 'id';

    public $cash_requisition_id;
    public $payment_voucher_id;

}

