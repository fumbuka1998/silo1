<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/9/2018
 * Time: 11:57 AM
 */

class Stock_sales_asset_item extends MY_Model{
    const DB_TABLE = 'stock_sales_asset_items';
    const DB_TABLE_PK = 'id';

    public $stock_sale_id;
    public $asset_sub_location_history_id;
    public $price;
    public $remarks;


    public function invoiced_amount(){
        $this->load->model('outgoing_invoice_item');
        $invoice_sale_items = $this->outgoing_invoice_item->get(0,0,['stock_sale_asset_item_id'=>$this->{$this::DB_TABLE_PK}]);
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
        $invoice_sale_items = $this->outgoing_invoice_item->get(0,0,['stock_sale_asset_item_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_invoiced_qty = 0;
        if(!empty($invoice_sale_items)){
            foreach($invoice_sale_items as $invoice_sale_item){
                $total_invoiced_qty += $invoice_sale_item->quantity;
            }
        }
        return $total_invoiced_qty;
    }

    public function asset_sub_location_history()
    {
        $this->load->model('asset_sub_location_history');
        $asset_sub_location_history = new Asset_sub_location_history();
        $asset_sub_location_history->load($this->asset_sub_location_history_id);
        return $asset_sub_location_history;
    }

    public function source_sub_location()
    {
        return $this->asset_sub_location_history()->sub_location();
    }

    public function asset(){
        return $this->asset_sub_location_history()->asset();
    }

    public function stock_sale(){
        $this->load->model('stock_sale');
        $stock_sale = new Stock_sale();
        $stock_sale->load($this->stock_sale_id);
        return $stock_sale;
    }

    public function asset_item(){
        return $this->asset()->asset_item();
    }

    public function generate_ssa_perticulars($item_id)
    {
        $item = new self();
        if($item->load($item_id)) {
            $ret_val['quantity'] = 1;
            $ret_val['symbol'] = "";
            $ret_val['unit'] = "No.";
            $ret_val['rate'] = number_format($item->price, 2);
            $ret_val['item_type'] = "asset";
            $ret_val['debt_nature'] =  "stock_sale";
            $ret_val['debt_nature_id'] = $item->stock_sale_id;
            echo json_encode($ret_val);
        }
    }


}