<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/31/2018
 * Time: 11:41 PM
 */

class Purchase_order_payment_request_approval_invoice_item extends MY_Model{
    const DB_TABLE = 'purchase_order_payment_request_approval_invoice_items';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_approval_id;
    public $purchase_order_payment_request_invoice_item_id;
    public $approved_amount;

    public function purchase_order_payment_request_invoice_item()
    {
        $this->load->model('purchase_order_payment_request_invoice_item');
        $purchase_order_payment_request_invoice_item = new Purchase_order_payment_request_invoice_item();
        $purchase_order_payment_request_invoice_item->load($this->purchase_order_payment_request_invoice_item_id);
        return $purchase_order_payment_request_invoice_item;
    }

    public function purchase_order_payment_request_approval()
    {
        $this->load->model('purchase_order_payment_request_approval');
        $purchase_order_payment_request_approval = new Purchase_order_payment_request_approval();
        $purchase_order_payment_request_approval->load($this->purchase_order_payment_request_approval_id);
        return $purchase_order_payment_request_approval;
    }

    public function paid_invoice_items(){
        $this->load->model('payment_voucher_item_approved_invoice_item');
        $paid_invoice_items = $this->payment_voucher_item_approved_invoice_item->get(0,0,['purchase_order_payment_request_approval_invoice_item_id'=> $this->{$this::DB_TABLE_PK}]);
        return !empty($paid_invoice_items) ? array_shift($paid_invoice_items) : false;
    }

    public function paid_amount(){
        $this->load->model(['payment_voucher_item_approved_invoice_item','payment_voucher_item']);
        $pvi_ainv_items = $this->payment_voucher_item_approved_invoice_item->get(0,0,['purchase_order_payment_request_approval_invoice_item_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_paid_amount = 0;
        if(!empty($pvi_ainv_items)){
            foreach ($pvi_ainv_items as $pvi_ainv_item){
                $pv_item = new Payment_voucher_item();
                $pv_item->load($pvi_ainv_item->payment_voucher_item_id);
                $total_paid_amount += $pv_item->total_amount();
            }
        }

        $sql = 'SELECT COALESCE(SUM(journal_voucher_items.amount),0) AS journal_amount FROM purchase_order_payment_request_approval_invoice_items
                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                LEFT JOIN payment_request_approval_journal_vouchers ON purchase_order_payment_request_approvals.id = payment_request_approval_journal_vouchers.purchase_order_payment_request_approval_id
                LEFT JOIN journal_vouchers ON payment_request_approval_journal_vouchers.journal_voucher_id = journal_vouchers.journal_id
                LEFT JOIN journal_voucher_items ON journal_vouchers.journal_id = journal_voucher_items.journal_voucher_id
                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                WHERE status = "APPROVED" AND is_final = 1 AND purchase_order_payment_request_approval_invoice_items.id = '.$this->{$this::DB_TABLE_PK}.' LIMIT 1';
        $total_paid_amount += $this->db->query($sql)->row()->journal_amount;
        return $total_paid_amount;
    }

    public function payment_voucher_item(){
        $this->load->model(['payment_voucher_item_approved_invoice_item','payment_voucher_item']);
        $where['purchase_order_payment_request_approval_invoice_item_id'] = $this->{$this::DB_TABLE_PK};
        $pvi_ainv_items = $this->payment_voucher_item_approved_invoice_item->get(0,0,$where,'id DESC');
        if(!empty($pvi_ainv_items)){
            foreach($pvi_ainv_items as $pvi_ainv_item){
                $pv_item = new Payment_voucher_item();
                $pv_item->load($pvi_ainv_item->payment_voucher_item_id);
                return $pv_item;
            }
        } else {
            return false;
        }
    }

    public function payment_vouchers(){
        $this->load->model(['payment_voucher']);
        $sql = 'SELECT payment_voucher_items.payment_voucher_id FROM payment_voucher_item_approved_invoice_items
                LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id                
                WHERE purchase_order_payment_request_approval_invoice_item_id = '.$this->{$this::DB_TABLE_PK};
        $results = $this->db->query($sql)->result();
        $payment_vouchers = [];
        foreach($results as $result){
            $pv = new Payment_voucher();
            $pv->load($result->payment_voucher_id);
            $payment_vouchers[] = $pv;
        }
        return $payment_vouchers;
    }

}