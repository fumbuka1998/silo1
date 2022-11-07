<?php

class Maintenance_service_item extends MY_Model
{
    const DB_TABLE = 'maintenance_service_items';
    const DB_TABLE_PK = 'item_id';

    public $service_id;
    public $quantity;
    public $measurement_unit_id;
    public $rate;
    public $description;


    public function amount(){
        return $this->quantity * $this->rate;
    }

    public function invoiced_amount(){
        $this->load->model('outgoing_invoice_item');
        $invoice_service_items = $this->outgoing_invoice_item->get(0,0,['maintenance_service_item_id'=>$this->{$this::DB_TABLE_PK}]);
        $paid_amount = 0;
        if(!empty($invoice_service_items)){
            foreach($invoice_service_items as $invoice_service_item){
                $paid_amount += $invoice_service_item->quantity * $invoice_service_item->rate;
            }
        }
        return $paid_amount;
    }

    public function invoiced_quantity(){
        $this->load->model('outgoing_invoice_item');
        $invoice_sale_items = $this->outgoing_invoice_item->get(0,0,['maintenance_service_item_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_invoiced_qty = 0;
        if(!empty($invoice_sale_items)){
            foreach($invoice_sale_items as $invoice_sale_item){
                $total_invoiced_qty += $invoice_sale_item->quantity;
            }
        }
        return $total_invoiced_qty;
    }

    public function maintenance_service(){
        $this->load->model('maintenance_service');
        $maintenance_service =  new Maintenance_service();
        $maintenance_service->load($this->service_id);
        return $maintenance_service;
    }

    public function measurement_unit(){
        $this->load->model('measurement_unit');
        $unit = new Measurement_unit();
        $unit->load($this->measurement_unit_id);
        return $unit;
    }

    public function dropdown_options($client_id, $currency_id)
    {
        $sql = 'SELECT maintenance_service_items.*, client_id, currency_id FROM maintenance_service_items
                LEFT JOIN maintenance_services on maintenance_service_items.service_id = maintenance_services.service_id 
                WHERE client_id = '.$client_id.' AND currency_id = '.$currency_id;

        $query = $this->db->query($sql);
        $results = $query->result();

        $options[''] = '&nbsp;';
        foreach ($results as $result){
            $options[$result->item_id] = $result->description;
        }
        return $options;
    }

    public function generate_service_perticulars($item_id)
    {
        $item = new self();
        if($item->load($item_id)) {
            $ret_val['quantity'] = $item->quantity;
            $ret_val['symbol'] = $item->measurement_unit()->symbol;
            $ret_val['unit'] = $item->measurement_unit_id;
            $ret_val['rate'] = number_format($item->rate, 2);
            $ret_val['item_type'] = "service";
            $ret_val['debt_nature'] =  "maintenance_service";
            $ret_val['debt_nature_id'] = $item->service_id;
            echo json_encode($ret_val);
        }
    }

    public function service_items_list($service_id){
        $service_items = $this->get(0,0, ['service_id' => $service_id]);
        return $service_items;
    }

}