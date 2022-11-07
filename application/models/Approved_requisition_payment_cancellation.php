<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/26/2018
 * Time: 11:18 AM
 */

class Approved_requisition_payment_cancellation extends MY_Model{

    const DB_TABLE = 'approved_requisition_payment_cancellations';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_id;
    public $date;
    public $cancelled_by;
    public $remarks;

}
