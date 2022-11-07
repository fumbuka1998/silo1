<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/23/2018
 * Time: 8:07 AM
 */

class Sub_contract_payment_requisition_approval_item extends MY_Model{
    const DB_TABLE = 'sub_contract_payment_requisition_approval_items';
    const DB_TABLE_PK = 'id';

    public $sub_contract_payment_requisition_approval_id;
    public $sub_contract_payment_requisition_item_id;
    public $approved_amount;

    public function sub_contract_payment_requisition_item(){
        $this->load->model('sub_contract_payment_requisition_item');
        $requisition_item = new Sub_contract_payment_requisition_item();
        $requisition_item->load($this->sub_contract_payment_requisition_item_id);
        return $requisition_item;

    }

    public function paid_approved_item(){
        $this->load->model('payment_voucher_item_approved_sub_contract_requisition_item');
        $paid_approved_items = $this->payment_voucher_item_approved_sub_contract_requisition_item->get(0,0,[
            'sub_contract_payment_requisition_approval_item_id' => $this->{$this::DB_TABLE_PK}
        ]);
        return !empty($paid_approved_items) ? array_shift($paid_approved_items) : false;
    }

    public function paid_amount(){
        $this->load->model(['payment_voucher_item_approved_sub_contract_requisition_item','payment_voucher_item']);
        $pvi_ascrq_items = $this->payment_voucher_item_approved_sub_contract_requisition_item->get(0,0,['sub_contract_payment_requisition_approval_item_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_paid_amount = 0;
        if(!empty($pvi_ascrq_items)){
            foreach ($pvi_ascrq_items as $pvi_ascrq_item){
                $pv_item = new Payment_voucher_item();
                $pv_item->load($pvi_ascrq_item->payment_voucher_item_id);
                $total_paid_amount += $pv_item->total_amount();
            }
        }
        return $total_paid_amount;
    }

    public function payment_voucher_item(){
        $this->load->model(['payment_voucher_item_approved_sub_contract_requisition_item','payment_voucher_item']);
        $where['sub_contract_payment_requisition_approval_item_id'] = $this->{$this::DB_TABLE_PK};
        $pvi_apscrq_items = $this->payment_voucher_item_approved_sub_contract_requisition_item->get(0,0,$where,'id DESC');
        if(!empty($pvi_apscrq_items)){
            foreach($pvi_apscrq_items as $pvi_apscrq_item){
                $pv_item = new Payment_voucher_item();
                $pv_item->load($pvi_apscrq_items->payment_voucher_item_id);
                return $pv_item;
            }
        } else {
            return false;
        }
    }

    public function payment_vouchers(){
        $this->load->model(['payment_voucher']);
        $sql = 'SELECT payment_voucher_items.payment_voucher_id FROM payment_voucher_item_approved_sub_contract_requisition_items
                LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_sub_contract_requisition_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id                
                WHERE sub_contract_payment_requisition_approval_item_id = '.$this->{$this::DB_TABLE_PK};
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