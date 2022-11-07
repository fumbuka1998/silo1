<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/25/2018
 * Time: 11:10 PM
 */

class Approved_sub_contract_payment_cancellation extends MY_Model{
    const DB_TABLE = 'approved_sub_contract_payment_cancellations';
    const DB_TABLE_PK = 'id';

    public $sub_contract_payment_requisition_approval_id;
    public $date;
    public $remarks;
    public $cancelled_by;


}