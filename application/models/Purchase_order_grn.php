<?php

class Purchase_order_grn extends MY_Model{
    
    const DB_TABLE = 'purchase_order_grns';
    const DB_TABLE_PK = 'id';

    public $purchase_order_id;
    public $goods_received_note_id;
    public $freight;
    public $insurance;
    public $other_charges;
    public $import_duty;
    public $vat;
    public $cpf;
    public $rdl;
    public $wharfage;
    public $service_fee;
    public $clearance_charges;
    public $clearance_vat;
    public $clearance_currency_id;
    public $factor;
    public $exchange_rate;

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Purchase_order();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }

    public function grn()
    {
        $this->load->model('goods_received_note');
        $goods_received_note = new Goods_received_note();
        $goods_received_note->load($this->goods_received_note_id);
        return $goods_received_note;
    }

}

