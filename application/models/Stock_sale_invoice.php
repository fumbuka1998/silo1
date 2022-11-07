<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/24/2018
 * Time: 10:16 PM
 */

class Stock_sale_invoice extends MY_Model
{

    const DB_TABLE = 'stock_sale_invoices';
    const DB_TABLE_PK = 'id';

    public $stock_sale_id;
    public $outgoing_invoice_id;

    
    public function invoice_number(){
        return date('Ymd',strtotime($this->invoice_date)).'-'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }
}