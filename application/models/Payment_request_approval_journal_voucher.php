<?php
/**
 * Created by PhpStorm.
 * User: genesis
 * Date: 2019-05-18
 * Time: 10:10
 */

class Payment_request_approval_journal_voucher extends MY_Model
{
    const DB_TABLE = 'payment_request_approval_journal_vouchers';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_approval_id;
    public $journal_voucher_id;
    public $amount;

    public function approved_payments_journal_vouchers($purchase_order_payment_request_approval_id, $status = false)
    {
        $sql = 'SELECT COALESCE (SUM(amount), 0) AS amount FROM payment_request_approval_journal_vouchers WHERE purchase_order_payment_request_approval_id = '.$purchase_order_payment_request_approval_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        if($status){
            return !empty($results) ? true : false;
        } else{
            return !empty($results) ? $results : false;
        }

    }

}