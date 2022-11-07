<?php

class Payment_voucher_item extends MY_Model{

    const DB_TABLE = 'payment_voucher_items';
    const DB_TABLE_PK = 'payment_voucher_item_id';
    const COST_CENTER_TYPES = ['project','department','cost_center','task'];

    public $payment_voucher_id;
    public $debit_account_id;
    public $stakeholder_id;
    public $amount;
    public $vat_amount;
    public $description;

    public function payment_voucher()
    {
        $this->load->model('payment_voucher');
        $payment_voucher = new Payment_voucher();
        $payment_voucher->load($this->payment_voucher_id);
        return $payment_voucher;
    }

    public function debit_account()
    {
        $this->load->model('account');
        $debit_account = new Account();
        $debit_account->load($this->debit_account_id);
        return $debit_account;
    }

    public function cost_center_junction(){
        foreach ($this::COST_CENTER_TYPES as $type){
            $model = $type.'_payment_voucher_item';
            $this->load->model($model);
            $junctions = $this->$model->get(1,0,['payment_voucher_item_id' => $this->{$this::DB_TABLE_PK}]);
            if(!empty($junctions)){
                return array_shift($junctions);
            }
        }
    }

    public function cost_center_type(){
        $junction = $this->cost_center_junction();
        $class_name = get_class($junction);
        return strtolower(str_replace("_payment_voucher_item","",$class_name));
    }

    public function cost_figure($cost_center_id,$cost_center_type, $from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(amount*exchange_rate),0) AS cost_figure FROM payment_voucher_items
              LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
              ';

        if($cost_center_type == 'project'){
            $sql .= ' LEFT JOIN project_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = project_payment_voucher_items.payment_voucher_item_id
              WHERE project_id =  '.$cost_center_id;
        } else if($cost_center_type == 'task'){
            $sql .= ' LEFT JOIN task_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = task_payment_voucher_items.payment_voucher_item_id
              WHERE task_id = '.$cost_center_id;
        } else if($cost_center_type == 'activity'){
            $sql .= ' LEFT JOIN task_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = task_payment_voucher_items.payment_voucher_item_id
              WHERE task_id IN(
                SELECT task_id FROM tasks WHERE activity_id = '.$cost_center_id.'
              ) ';
        } else if($cost_center_type == 'project_overall'){
            $sql .= ' 
            LEFT JOIN project_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = project_payment_voucher_items.payment_voucher_item_id
            LEFT JOIN task_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = task_payment_voucher_items.payment_voucher_item_id
            WHERE project_id = '.$cost_center_id.' OR task_id IN(
                SELECT task_id FROM tasks
                LEFT JOIN activities ON tasks.activity_id = activities.activity_id
                WHERE project_id = '.$cost_center_id.'
              ) ';
        } else if($cost_center_type == 'cost_center'){
            $sql .= ' LEFT JOIN cost_center_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = cost_center_payment_voucher_items.payment_voucher_item_id
              WHERE cost_center_id = '.$cost_center_id;
        } else if($cost_center_type == 'department'){
            $sql .= ' LEFT JOIN department_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = department_payment_voucher_items.payment_voucher_item_id
              WHERE department_id = '.$cost_center_id;
        }

        if(!is_null($from) && $from != ''){
            $sql .= ' AND payment_date >= "'.$from.'"';
        }
        if(!is_null($to) && $to != ''){
            $sql .=  ' AND payment_date <= "'.$to.'"';
        }
        $query = $this->db->query($sql);
        return $query->row()->cost_figure;
    }

    public function total_amount(){
        return $this->amount + $this->withholding_tax_amount();
    }

    public function withholding_tax_amount(){
        $this->load->model('withholding_tax');
        $withholding_taxes = $this->withholding_tax->get(0,0,['payment_voucher_item_id'=>$this->{$this::DB_TABLE_PK}]);
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
			return false;
        }
    }

    public function vat_returns_account(){
        $sql = 'SELECT account_id FROM accounts
                WHERE account_name LIKE "%VAT Returns%" LIMIT 1';

        $query = $this->db->query($sql);
        if($query->row()->account_id) {
            $this->load->model('account');
            $account = new Account();
            $account->load($query->row()->account_id);
            return $account;
        } else {
			return false;
        }
    }

    public function withholding_tax(){
        $this->load->model('withholding_tax');
        $withholding_taxes = $this->withholding_tax->get(0, 0, ['payment_voucher_item_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($withholding_taxes) ? array_shift($withholding_taxes) : false;
    }

}

