<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/13/2018
 * Time: 5:44 PM
 */

class  Withholding_taxes_payment extends MY_Model{
    const DB_TABLE = ' withholding_taxes_payments';
    const DB_TABLE_PK = 'id';


    public $withholding_tax_id;
    public $payment_date;
    public $paid_amount;
    public $remarks;
    public $paid_by;


    public function withholding_tax(){
        $this->load->model('withholding_tax');
        $withholding_tax = new Withholding_tax();
        $withholding_tax->load($this->withholding_tax_id);
        return $withholding_tax;
    }


}