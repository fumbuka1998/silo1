<?php

class Contra extends MY_Model{

    const DB_TABLE = 'contras';
    const DB_TABLE_PK = 'contra_id';

    public $credit_account_id;
    public $stakeholder_id;
    public $contra_date;
    public $reference;
    public $remarks;
    public $employee_id;
    public $confidentiality_chain_position;
    public $currency_id;
    public $exchange_rate;

    public function contra_number(){
        return 'CV/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function detailed_reference(){
        $reference = $this->reference != '' ? $this->reference : null;
        return !is_null($reference) ? $this->contra_number().' - '.$reference : $this->contra_number();
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function credit_account()
    {
        $this->load->model('account');
        $credit_account = new Account();
        $credit_account->load($this->credit_account_id);
        return $credit_account;
    }

    public function contra_items(){
        $this->load->model('contra_item');
        return $this->contra_item->get(0,0,['contra_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function delete_items(){
        $this->db->where('contra_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete(['imprest_voucher_contras','contra_items']);
    }

    public function supplementary_accounts($action, $export = false){
        $accounts_string = '';
        if($action == 'CREDIT'){
            $sql = 'SELECT DISTINCT account_name,debit_account_id FROM contra_items
                    LEFT JOIN accounts ON contra_items.debit_account_id = accounts.account_id
                    WHERE contra_id = "'.$this->{$this::DB_TABLE_PK}.'"
                    ';
            $query = $this->db->query($sql);
            $results = $query->result();
            foreach ($results as $row){
                if(check_permission('Finance') && !$export) {
                    $accounts_string .= anchor(base_url('finance/account_profile/' . $row->debit_account_id), $row->account_name) . '<br/> ';
                } else {
                    $accounts_string .= $row->account_name.'<br/>';
                }
            }
        } else {
            $credit_account = $this->credit_account();
            if(check_permission('Finance') && !$export) {
                $accounts_string .= anchor(base_url('finance/account_profile/' . $credit_account->{$credit_account::DB_TABLE_PK}), $credit_account->account_name).'<br/>';
            } else {
                $accounts_string .= $credit_account->account_name.'<br/>';
            }
        }

        return $accounts_string;
    }

    public function contras($limit,$start,$keyword,$order,$imprest_voucher_id = null){
        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $where = $confidentiality_position ? ' confidentiality_chain_position <=' .$confidentiality_position : '';

        $order_string = dataTable_order_string(['contra_date','contra_id','','reference','remarks'],$order,'contra_date');

        if($keyword != ''){
            $where .= ($where != '' ? ' AND ' : ''). ' contra_date LIKE "%'.$keyword.'%" OR contras.contra_id LIKE "%'.$keyword.'%"  OR reference LIKE "%'.$keyword.'%" OR remarks LIKE "%'.$keyword.'%" OR datetime_posted LIKE "%'.$keyword.'%" ';
             $where .= ' OR credit_account_id IN (SELECT account_id FROM accounts WHERE account_name LIKE "%'.$keyword.'%") ';
        }

        if(!is_null($imprest_voucher_id)){
            $where .= ($where != '' ? ' AND ' : ''). ' contra_id IN( SELECT contra_id FROM imprest_voucher_contras WHERE imprest_voucher_id = "'.$imprest_voucher_id.'" )';
        }
        $contras = $this->get($limit,$start,$where,$order_string);

        $records_filtered = $this->count_rows($where);

        $rows = [];

        $data['account_options'] = account_dropdown_options(['CASH IN HAND', 'BANK']);
        $this->load->model('imprest_voucher');
        foreach ($contras as $contra){
            $data['imprest_voucher'] = $contra->imprest_voucher_contra() ? $contra->imprest_voucher_contra()->imprest_voucher() : '';
            $data['contra'] = $contra;
            $data['credit_account'] = $contra->credit_account();
            $data['currency'] = $contra->currency();
            if(is_null($imprest_voucher_id)) {
                $rows[] = [
                    custom_standard_date($contra->contra_date),
                    add_leading_zeros($contra->contra_number()),
                    $data['credit_account']->account_name,
                    $contra->reference,
                    '<span class="pull-right">'.$contra->currency()->symbol.' '.number_format($contra->contra_amount(),2).'</span>',
                    $this->load->view('finance/transactions/contras/contras_list_actions', $data, true)
                ];
            } else {
                $rows[] = [
                    custom_standard_date($contra->contra_date),
                    add_leading_zeros($contra->contra_number()),
                    $contra->reference,
                    $data['credit_account']->account_name,
                    $contra->imprest_contra_debit_account(true, false),
                    '<span class="pull-right">'.$contra->currency()->symbol.' '.number_format($contra->imprest_contra_debit_account(false, true),2).'</span>',
                    $this->load->view('finance/transactions/approved_cash_requests/imprest/imprest_contra_list_actions', $data, true)
                ];
            }
        }
        $json = [
            "recordsTotal" => $this->count_rows($where),
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function imprest_voucher_contra(){
        $this->load->model('imprest_voucher_contra');
        $junctions = $this->imprest_voucher_contra->get(0,0,['contra_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function imprest_contra_debit_account($account_name = false,$amount = false){
        $this->load->model('contra_item');
        $junctions = $this->contra_item->get(0,0,['contra_id' => $this->{$this::DB_TABLE_PK}]);
        foreach ($junctions as $junction){
            if($account_name){
                return $junction->debit_account()->account_name;
            } else if($amount) {
                return $junction->amount;
            } else {
                return $junction->debit_account_id;
            }
        }
    }

    public function contra_amount(){
        $this->load->model('contra_item');
        $contra_items = $this->contra_item->get(0,0,['contra_id' => $this->{$this::DB_TABLE_PK}]);
        $contra_amount = 0;
        foreach($contra_items as $contra_item){
            $contra_amount += $contra_item->amount;
        }
        return $contra_amount;

    }

}

