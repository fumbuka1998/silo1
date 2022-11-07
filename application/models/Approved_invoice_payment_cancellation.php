<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/28/2018
 * Time: 9:19 AM
 */

class Approved_invoice_payment_cancellation extends MY_Model{

    const DB_TABLE = 'approved_invoice_payment_cancellations';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_approval_id;
    public $date;
    public $cancelled_by;
    public $remarks;

}
