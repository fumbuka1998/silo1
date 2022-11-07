<?php
class Payment_voucher_item_approved_cash_request_item extends MY_Model{
    const DB_TABLE = 'payment_voucher_item_approved_cash_request_items';
    const DB_TABLE_PK = 'id';

    public $payment_voucher_item_id;
    public $quantity;
    public $rate;
    public $requisition_approval_cash_item_id;
    public $requisition_approval_service_item_id;
    public $requisition_approval_material_item_id;
    public $requisition_approval_asset_item_id;

    public function payment_voucher_item(){
        $this->load->model('payment_voucher_item');
        $pv_item = new Payment_voucher_item();
        $pv_item->load($this->payment_voucher_item_id);
        return $pv_item;
    }

    public function requisition_approval_item($item_type){
        $model = 'requisition_approval_'.$item_type.'_item';
        $this->load->model($model);
        $model = ucfirst($model);
        $item_id = 'requisition_approval_'.$item_type.'_item_id';
        $approved_item = new $model;
        $approved_item->load($this->$item_id);
        return $approved_item;
    }

}
?>
