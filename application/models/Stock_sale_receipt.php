<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/12/2018
 * Time: 3:41 PM
 *
 */
class Stock_sale_receipt extends MY_Model
{

    const DB_TABLE = 'stock_sale_receipts';
    const DB_TABLE_PK = 'id';

    public $receipt_id;
    public $stock_sale_id;

    public function stock_sale()
    {
        $this->load->model('stock_sale');
        $stock_sale = new Stock_sale();
        $stock_sale->load($this->stock_sale_id);
        return $stock_sale;
    }
}