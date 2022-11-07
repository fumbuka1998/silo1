<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/9/2018
 * Time: 12:02 PM
 */

class Stock_sales_material_item extends  MY_Model{
    const DB_TABLE = 'stock_sales_material_items';
    const DB_TABLE_PK = 'id';

    public $stock_sale_id;
    public $material_item_id;
    public $source_sub_location_id;
    public $quantity;
    public $price;
    public $remarks;


    public function amount(){
        return $this->quantity * $this->price;
    }

    public function invoiced_amount(){
        $this->load->model('outgoing_invoice_item');
        $invoice_sale_items = $this->outgoing_invoice_item->get(0,0,['stock_sale_material_item_id'=>$this->{$this::DB_TABLE_PK}]);
        $paid_amount = 0;
        if(!empty($invoice_sale_items)){
            foreach($invoice_sale_items as $invoice_sale_item){
                $paid_amount += $invoice_sale_item->quantity * $invoice_sale_item->rate;
            }
        }
        return $paid_amount;
    }

    public function invoiced_quantity(){
        $this->load->model('outgoing_invoice_item');
        $invoice_sale_items = $this->outgoing_invoice_item->get(0,0,['stock_sale_material_item_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_invoiced_qty = 0;
        if(!empty($invoice_sale_items)){
            foreach($invoice_sale_items as $invoice_sale_item){
                $total_invoiced_qty += $invoice_sale_item->quantity;
            }
        }
        return $total_invoiced_qty;
    }

    public function source_sub_location()
    {
        $this->load->model('sub_location');
        $source_sub_location = new Sub_location();
        $source_sub_location->load($this->source_sub_location_id);
        return $source_sub_location;
    }

    public function material_item()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

    public function stock_sale(){
        $this->load->model('stock_sale');
        $stock_sale = new Stock_sale();
        $stock_sale->load($this->stock_sale_id);
        return $stock_sale;
    }

    public function generate_ssm_perticulars($item_id)
    {
        $item = new self();
        if($item->load($item_id)) {
            $unit = $item->material_item()->unit();
            $ret_val['quantity'] = $item->quantity;
            $ret_val['symbol'] = $unit->symbol;
            $ret_val['unit'] = $unit->{$unit::DB_TABLE_PK};
            $ret_val['rate'] = number_format($item->price, 2);
            $ret_val['item_type'] = "material";
            $ret_val['debt_nature'] =  "stock_sale";
            $ret_val['debt_nature_id'] = $item->stock_sale_id;
            echo json_encode($ret_val);
        }
    }
}