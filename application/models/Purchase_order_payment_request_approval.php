<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/31/2018
 * Time: 11:33 PM
 */

class Purchase_order_payment_request_approval extends MY_Model{
    
    const DB_TABLE = 'purchase_order_payment_request_approvals';
    const DB_TABLE_PK = 'id';

    public $purchase_order_payment_request_id;
    public $approval_date;
    public $approval_chain_level_id;
    public $is_final;
    public $is_printed;
    public $forward_to;
    public $created_by;
    public $comments;

    public function purchase_order_payment_request()
    {
        $this->load->model('purchase_order_payment_request');
        $payment_request = new Purchase_order_payment_request();
        $payment_request->load($this->purchase_order_payment_request_id);
        return $payment_request;
    }

    public function approval_chain_level()
    {
        $this->load->model('approval_chain_level');
        $approval_chain_level = new Approval_chain_level();
        $approval_chain_level->load($this->approval_chain_level_id);
        return $approval_chain_level;
    }

    public function cost_center_name()
    {
        return $this->purchase_order_payment_request()->cost_center_name();
    }

    public function invoice_items($total = false)
    {
        $this->load->model('purchase_order_payment_request_approval_invoice_item');
        $invoice_items = $this->purchase_order_payment_request_approval_invoice_item->get(0,0,['purchase_order_payment_request_approval_id' => $this->{$this::DB_TABLE_PK}]);
        if($total){
            $total = 0;
            foreach ($invoice_items as $item){
                $total += $item->approved_amount;
            }
            return $total;
        } else {
            return $invoice_items;
        }
    }

    public function cash_items($total = false)
    {
        $this->load->model('purchase_order_payment_request_approval_cash_item');
        $cash_items = $this->purchase_order_payment_request_approval_cash_item->get(0,0,['purchase_order_payment_request_approval_id' => $this->{$this::DB_TABLE_PK}]);
        if($total){
            $total = 0;
            foreach ($cash_items as $item){
                $total += $item->approved_amount;
            }
            return $total;
        } else {
            return $cash_items;
        }
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function created_by()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }
    
    public function approval_payment_voucher(){
        $this->load->model('purchase_order_payment_request_approval_payment_voucher');
        $payment_voucher_junctions = $this->purchase_order_payment_request_approval_payment_voucher->get(1,0,['purchase_order_payment_request_approval_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($payment_voucher_junctions) ? array_shift($payment_voucher_junctions) : false;
    }

    public function approved_cash_items(){
        $this->load->model('purchase_order_payment_request_cash_item');
        $where = [
            'purchase_order_payment_request_id' => $this->purchase_order_payment_request_id,
        ];

        $cash_items = $this->purchase_order_payment_request_cash_item->get(0,0,$where);
        return $cash_items;
    }

    public function forwarded_to()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->forward_to);
        return $employee;
    }

    public function invoice_items_approved_amount($base_currency = false){
        $sql = 'SELECT COALESCE(SUM(approved_amount),0) AS approved_amount FROM purchase_order_payment_request_approval_invoice_items
                    WHERE purchase_order_payment_request_approval_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $amount = $query->row()->approved_amount;
        if(!$base_currency){
            return $amount;
        } else {
            $payment_request = $this->purchase_order_payment_request();
            if($payment_request->currency_id == 1){
                return $amount;
            } else {
                $currency = $payment_request->currency();
                return $amount*$currency->rate_to_native($payment_request->request_date);
            }
        }
    }

    public function cash_items_approved_amount($base_currency = false){

        $sql = 'SELECT SUM(approved_amount) AS approved_amount FROM purchase_order_payment_request_approval_cash_items
                  WHERE purchase_order_payment_request_approval_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $amount = $query->row()->approved_amount;
        if(!$base_currency){
            return $amount;
        } else {
            $payment_request = $this->purchase_order_payment_request();
            if($payment_request->currency_id == 1){
                return $amount;
            } else {
                $currency = $payment_request->currency();
                return $amount*$currency->rate_to_native($payment_request->request_date);
            }
        }
    }

    public function total_approved_amount($base_currency = false){
        if($base_currency){
            $amount = $this->invoice_items_approved_amount(true) + $this->cash_items_approved_amount(true);
        } else {
            $amount = $this->invoice_items_approved_amount() + $this->cash_items_approved_amount();
        }
        return $amount;
    }

    public function total_paid_amount(){
        $this->load->model(['currency']);
        $approval_items = $this->invoice_items();
        $total_paid_amount = 0;
        foreach($approval_items as $approval_item){
            $total_paid_amount += $approval_item->paid_amount();
        }
        return $total_paid_amount;
    }

    public function cancelled_approved_payment(){
        $this->load->model('approved_invoice_payment_cancellation');
        $where['purchase_order_payment_request_approval_id'] = $this->{$this::DB_TABLE_PK};
        $cancelled_payments = $this->approved_invoice_payment_cancellation->get(0,0,$where);
        $options = [];
        foreach($cancelled_payments as $cancelled_payment){
            $options[] = $cancelled_payment->purchase_order_payment_request_approval_id;
        }
        return $options;
    }

    public function is_cancelled(){
        $purchase_order_payment_request_approval_id = $this->{$this::DB_TABLE_PK};
        return in_array($purchase_order_payment_request_approval_id,$this->cancelled_approved_payment()) ? true : false;
    }

    public function payment_vouchers(){
        $this->load->model(['purchase_order_payment_request_approval_payment_voucher','payment_voucher']);
        $where['purchase_order_payment_request_approval_id'] = $this->{$this::DB_TABLE_PK};
        $approval_payment_vouchers = $this->purchase_order_payment_request_approval_payment_voucher->get(0,0,$where,'id DESC');
        $payment_vouchers = [];
        if(!empty($approval_payment_vouchers)){
            foreach($approval_payment_vouchers as $approval_payment_voucher){
                $payment_voucher = new Payment_voucher();
                $payment_voucher->load($approval_payment_voucher->payment_voucher_id);
                $payment_vouchers[] = $payment_voucher;
            }
        }
        return $payment_vouchers;
    }

    public function payment_voucher(){
        $this->load->model(['purchase_order_payment_request_approval_payment_voucher','payment_voucher']);
        $where['purchase_order_payment_request_approval_id'] = $this->{$this::DB_TABLE_PK};
        $approval_payment_vouchers = $this->purchase_order_payment_request_approval_payment_voucher->get(0,0,$where,'id DESC');
        if(!empty($approval_payment_vouchers)){
            foreach($approval_payment_vouchers as $approval_payment_voucher){
                $payment_voucher = new Payment_voucher();
                $payment_voucher->load($approval_payment_voucher->payment_voucher_id);
                return $payment_voucher;
            }
        } else {
            return false;
        }
    }

    public function journal_vouchers(){
        $this->load->model(['payment_request_approval_journal_voucher']);
        $journal_voucher_entries = $this->payment_request_approval_journal_voucher->get(0,0,['purchase_order_payment_request_approval_id'=>$this->{$this::DB_TABLE_PK}]);
        $journal_vouchers = [];
        if(!empty($journal_voucher_entries)){
            foreach($journal_voucher_entries as $journal_voucher_entry){
                $journal_voucher = new Journal_voucher();
                $journal_voucher->load($journal_voucher_entry->journal_voucher_id);
                $journal_vouchers[] = $journal_voucher;
            }
        }
        return $journal_vouchers;
    }

    public function journal_voucher(){
        $this->load->model(['journal_voucher']);
        $journal_voucher_entries = $this->payment_request_approval_journal_voucher->get(0,0,['purchase_order_payment_request_approval_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($journal_voucher_entries)){
            foreach($journal_voucher_entries as $journal_voucher_entry){
                $journal_voucher = new Journal_voucher();
                $journal_voucher->load($journal_voucher_entry->journal_voucher_id);
                return $journal_voucher;
            }
        } else {
            return false;
        }
    }
}