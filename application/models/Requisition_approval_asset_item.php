<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 19/04/2018
 * Time: 16:11
 */
class Requisition_approval_asset_item extends MY_Model
{
    const DB_TABLE = 'requisition_approval_asset_items';
    const DB_TABLE_PK = 'id';

    public $requisition_approval_id;
    public $requisition_asset_item_id;
    public $source_type;
    public $vendor_id;
    public $location_id;
    public $approved_quantity;
    public $approved_rate;
    public $payee;
    public $account_id;
    public $currency_id;

    public function requisition_asset_item()
    {
        $this->load->model('requisition_asset_item');
        $requisition_asset_item = new Requisition_asset_item();
        $requisition_asset_item->load($this->requisition_asset_item_id);
        return $requisition_asset_item;
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function vendor()
    {
        $this->load->model('stakeholder');
        $vendor = new Stakeholder();
        $vendor->load($this->vendor_id);
        return $vendor;
    }

    public function source_name(){
        if($this->source_type == 'cash'){
            $source_name = 'CASH';
        } else if($this->source_type == 'vendor') {
            $source_name = $this->vendor()->stakeholder_name;
        } else {
            $source_name = $this->location()->location_name;
        }
        return $source_name;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function transferred_quantity(){
        $sql = 'SELECT COUNT(external_transfer_asset_items.id) AS transferred_quantity FROM external_transfer_asset_items
                LEFT JOIN asset_sub_location_histories ON external_transfer_asset_items.source_sub_location_history_id = asset_sub_location_histories.id
                LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                LEFT JOIN external_material_transfers ON external_transfer_asset_items.transfer_id = external_material_transfers.transfer_id
                LEFT JOIN transferred_transfer_orders ON external_material_transfers.transfer_id = transferred_transfer_orders.transfer_id
                LEFT JOIN requisition_approvals ON transferred_transfer_orders.requisition_approval_id = requisition_approvals.id
                LEFT JOIN requisition_approval_asset_items ON requisition_approvals.id = requisition_approval_asset_items.requisition_approval_id
                LEFT JOIN requisition_asset_items ON requisition_approval_asset_items.requisition_asset_item_id = requisition_asset_items.id
                WHERE assets.asset_item_id = requisition_asset_items.asset_item_id AND transferred_transfer_orders.requisition_approval_id = requisition_approval_asset_items.requisition_approval_id AND requisition_approval_asset_items.id = '.$this->{$this::DB_TABLE_PK}.'  
                ';
        return $this->db->query($sql)->row()->transferred_quantity;
    }

    public function transfer_order_balance(){
        return $this->approved_quantity - $this->transferred_quantity();
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
                WHERE requisition_approval_asset_item_id = '.$this->{$this::DB_TABLE_PK};
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

