<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/12/2018
 * Time: 3:36 PM
 */

class Receipt_item extends MY_Model
{
    const DB_TABLE = 'receipt_items';
    const DB_TABLE_PK = 'id';

    public $receipt_id;
    public $amount;
    public $remarks;

    public function receipt(){
    	$this->load->model('receipt');
    	$receipt = new Receipt();
    	$receipt->load($this->receipt_id);
    	return $receipt;
	}

    public function total_amount(){
        return $this->amount + $this->withholding_tax_amount();
    }

    public function withholding_tax_amount(){
        $this->load->model('withholding_tax');
        $withholding_taxes = $this->withholding_tax->get(0,0,['receipt_item_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($withholding_taxes)) {
            foreach ($withholding_taxes as $withholding_taxe) {
                return $withholding_taxe->withheld_amount;
            }
        } else {
            return 0;
        }
    }

    public function withholding_tax_account(){
        $sql = 'SELECT account_id FROM accounts
                WHERE account_name LIKE "%Withholding Tax%" LIMIT 1';

        $query = $this->db->query($sql);
        if($query->row()->account_id) {
            $this->load->model('account');
            $account = new Account();
            $account->load($query->row()->account_id);
            return $account;
        } else {

        }
    }

    public function withholding_tax(){
        $this->load->model('withholding_tax');
        $withholding_taxes = $this->withholding_tax->get(0, 0, ['receipt_item_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($withholding_taxes) ? array_shift($withholding_taxes) : false;
    }
}
