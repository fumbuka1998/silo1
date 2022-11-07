<?php

class Procurement_attachment extends MY_Model
{
    const DB_TABLE = 'procurement_attachments';
    const DB_TABLE_PK = 'id';

    public $attachment_id;
    public $reffering_id;
    public $reffering_to;


    public function attachment()
    {
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->attachment_id);
        return $attachment;
    }

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Attachment();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }

    public function reffering_object()
    {
        switch ($this->reffering_to) {
            case 'ORDER':
                $model = 'purchase_order';
                break;
            case 'P-INV':
                $model = 'invoice';
                break;
            case 'O-INV':
                $model = 'outgoing_invoice';
                break;
            case 'GRN':
                $model = 'goods_received_note';
                break;
        }
        $this->load->model($model);
        $model_class = ucfirst($model);
        $reffering_object = new $model_class();
        $reffering_object->load($this->reffering_id);
        return $reffering_object;
    }

    public function reffering_object_number()
    {
        $reffering_object =  $this->reffering_object();
        switch ($this->reffering_to) {
            case 'ORDER':
                $object_no = 'order_number';
                $url = 'procurements/preview_purchase_order/' . $reffering_object->{$reffering_object::DB_TABLE_PK};
                break;
            case 'P-INV':
                $object_no = 'reference';
                $url = 'finance/preview_invoice/' . $reffering_object->{$reffering_object::DB_TABLE_PK} . '/purchases';
                break;
            case 'O-INV':
                $object_no = 'outgoing_inv_number';
                $url = 'finance/preview_invoice/' . $reffering_object->{$reffering_object::DB_TABLE_PK} . '/sales';
                break;
            case 'GRN':
                $object_no = 'grn_number';
                $url = 'inventory/preview_grn/' . $reffering_object->{$reffering_object::DB_TABLE_PK};
                break;
        }
        return anchor(base_url($url), $reffering_object->$object_no(), 'target="_blank"');
    }
}
