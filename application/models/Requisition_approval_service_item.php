<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 01/06/2018
 * Time: 15:13
 */

class Requisition_approval_service_item extends MY_Model
{

    const DB_TABLE = 'requisition_approval_service_items';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_id;
    public $requisition_service_item_id;
    public $approved_quantity;
    public $approved_rate;
    public $payee;
    public $account_id;
    public $source_type;
    public $vendor_id;


    public function approved_item($requisition_approval_id,$source_id = null,$source_type = null){
        $this->load->model('requisition_approval_service_item');
        $where = [
            'requisition_approval_id' => $requisition_approval_id,
            'requisition_service_item_id' => $this->{$this::DB_TABLE_PK},
        ];
        if(!is_null($source_type)){
            if($source_type == 'store'){
                $where['location_id'] = $source_id;
            } else if($source_type == 'cash'){
                $where['account_id'] = $source_id;
            } else {
                $where['vendor_id'] = $source_id;
            }
        }
        $items = $this->requisition_approval_service_item->get(1,0,$where);
        return array_shift($items);
    }

    public function requisition_service_item()
    {
        $this->load->model('requisition_service_item');
        $requisition_service_item = new Requisition_service_item();
        $requisition_service_item->load($this->requisition_service_item_id);
        return $requisition_service_item;
    }

    public function vendor()
    {
        $this->load->model('stakeholder');
        $vendor = new Stakeholder();
        $vendor->load($this->vendor_id);
        return $vendor;
    }

    public function source_name(){
        if($this->source_type == 'vendor') {
            $source_name = $this->vendor()->stakeholder_name;
        } else {
            $source_name = 'CASH';
        }
        return $source_name;
    }

    public function paid_amount($item_type){
        $this->load->model(['payment_voucher_item_approved_cash_request_item','payment_voucher_item']);
        $approved_item_id = 'requisition_approval_'.$item_type.'_item_id';
        $pvi_apcr_items = $this->payment_voucher_item_approved_cash_request_item->get(0,0,[$approved_item_id=>$this->{$this::DB_TABLE_PK}]);
        $total_paid_amount = 0;
        if(!empty($pvi_apcr_items)){
            foreach ($pvi_apcr_items as $pvi_apcr_item){
                $pv_item = new Payment_voucher_item();
                $pv_item->load($pvi_apcr_item->payment_voucher_item_id);
                $total_paid_amount += $pv_item->total_amount();
            }
        }
        return $total_paid_amount;
    }

    public function paid_quantity($item_type){
        $imprest_item_model = 'imprest_voucher_'.$item_type.'_item';
        $this->load->model(['payment_voucher_item_approved_cash_request_item',$imprest_item_model,'payment_voucher_item']);
        $approved_item_id = 'requisition_approval_'.$item_type.'_item_id';
        $pvi_apcr_items = $this->payment_voucher_item_approved_cash_request_item->get(0,0,[$approved_item_id=>$this->{$this::DB_TABLE_PK}]);
        $total_paid_quantity = 0;
        if(!empty($pvi_apcr_items)){
            foreach ($pvi_apcr_items as $pvi_apcr_item){
                $total_paid_quantity += $pvi_apcr_item->quantity;
            }
        }

        $imprest_approved_item_id = 'requisition_approval_'.$item_type.'_item_id';
        $impv_matl_items = $this->$imprest_item_model->get(0,0,[$imprest_approved_item_id=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($impv_matl_items)){
            foreach ($impv_matl_items as $impv_matl_item){
                $total_paid_quantity += $impv_matl_item->quantity;
            }
        }
        return $total_paid_quantity;
    }

    public function payment_voucher_item($item_type){
        $this->load->model(['payment_voucher_item_approved_cash_request_item','payment_voucher_item']);
        $approved_item_id = 'requisition_approval_'.$item_type.'_item_id';
        $pvi_apcr_items = $this->payment_voucher_item_approved_cash_request_item->get(0,0,[$approved_item_id=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($pvi_apcr_items)){
            foreach ($pvi_apcr_items as $pvi_apcr_item){
                $pv_item = new Payment_voucher_item();
                $pv_item->load($pvi_apcr_item->payment_voucher_item_id);
                return $pv_item;
            }
        } else {
            return false;
        }
    }

    public function payment_vouchers(){
        $this->load->model(['payment_voucher']);
        $sql = 'SELECT payment_voucher_items.payment_voucher_id 
                FROM payment_voucher_item_approved_cash_request_items
                LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_cash_request_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id                
                WHERE requisition_approval_service_item_id = '.$this->{$this::DB_TABLE_PK};
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

