<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:22 PM
 */

class Journal_voucher extends MY_Model{
    const DB_TABLE = 'journal_vouchers';
    const DB_TABLE_PK = 'journal_id';

    public $transaction_date;
    public $reference;
    public $journal_type;
    public $confidentiality_chain_position;
    public $currency_id;
    public $remarks;
    public $created_by;
    public $created_at;


    public function jv_number(){
        return 'JV/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function journal_transactions($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['transaction_date','journal_type','reference'],$order,'transaction_date');

        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $where = $confidentiality_position ? ' confidentiality_chain_position <=' .$confidentiality_position : '';
        $records_total = $this->count_rows($where);
        if($keyword != ''){
            $where .= ($where != '' ? ' AND ' : '').' (transaction_date LIKE "%'.$keyword.'%" OR journal_type LIKE "%'.$keyword.'%" OR reference LIKE "%'.$keyword.'%") ';
        }
        $records_filtered = $this->count_rows($where);

        $transactions = $this->get($limit,$start,$where,$order_string);
        $this->load->model(['account']);
        $data['account_options'] = $this->account->dropdown_options();
        $data['currency_options'] = currency_dropdown_options();
        $rows = [];
        foreach($transactions as $transaction){
            $journal_voucher = new self();
            $journal_voucher->load($transaction->{$transaction::DB_TABLE_PK});
            $data['jv_transaction'] = $journal_voucher;

                $rows[] = [
                    custom_standard_date($transaction->transaction_date),
                    'JV/'.add_leading_zeros($transaction->{$transaction::DB_TABLE_PK}),
                    $transaction->journal_type,
                    $transaction->reference,
                    '<span class="pull-right">' . number_format($transaction->journal_voucher_amount(), 2) . '</span>',
                    '<span>' . wordwrap($transaction->remarks,80,'<br/>')  . '</span>',
                    $this->load->view('finance/transactions/journals/list_actions',$data,true)
                ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function journal_voucher_amount(){
        $this->load->model('journal_voucher_item');
        $items = $this->journal_voucher_item->get(0,0,['journal_voucher_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_amount = 0;
        foreach($items as $item){
            $total_amount = $total_amount + $item->amount;
        }
        return $total_amount;
    }

    public function jv_transactions($transaction_type){
        $this->load->model(['journal_voucher_credit_account','journal_voucher_item']);
        switch($transaction_type){
            case "CREDIT":
                $modal = 'journal_voucher_credit_account';
                break;

            case "DEBIT":
                $modal = 'journal_voucher_item';
                break;
        }
        $transactions = $this->$modal->get(0,0,['journal_voucher_id'=>$this->{$this::DB_TABLE_PK}]);
        return !empty($transactions) ? $transactions : false;
    }

    public function clear_items($tables,$id){
        foreach($tables as $table){
            $this->db->delete($table,$id);
        }
    }

    public function currency(){
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function created_by(){
        $this->load->model('employee');
        $employee =  new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function largest_count(){
        $this->load->model(['journal_voucher_item','journal_voucher_credit_account']);
        $where = 'journal_voucher_id ='.$this->{$this::DB_TABLE_PK};
        $credit_transactions = $this->journal_voucher_credit_account->get(0,0,$where);
        $credit_trns_count = count($credit_transactions);
        $debit_transactions = $this->journal_voucher_item->get(0,0,$where);
        $debit_trns_count = count($debit_transactions);
        return $credit_trns_count > $debit_trns_count ? $credit_trns_count : $debit_trns_count;
    }

    public function journal($from = null, $to = null){
        $sql = 'SELECT * FROM (

                SELECT credit_transactions.id, transaction_date, credit_transactions.journal_voucher_id, "CREDIT" AS transaction_type, (
                	CASE 
                		WHEN credit_transactions.stakeholder_id IS NULL THEN account_name
                		WHEN credit_transactions.account_id IS NULL THEN stakeholder_name
					END 
                ) AS descriptions, 
                amount AS credited_amount, 0 AS debited_amount
                FROM journal_voucher_credit_accounts AS credit_transactions
                LEFT JOIN journal_vouchers ON credit_transactions.journal_voucher_id = journal_vouchers.journal_id
                LEFT JOIN accounts ON credit_transactions.account_id = accounts.account_id
                LEFT JOIN stakeholders ON credit_transactions.stakeholder_id = stakeholders.stakeholder_id
                WHERE credit_transactions.journal_voucher_id = ' . $this->{$this::DB_TABLE_PK} . '
                AND transaction_date >= "' . $from . '"
                AND transaction_date <= "' . $to . '"
                
                UNION ALL
                
                SELECT debit_transactions.item_id, transaction_date, debit_transactions.journal_voucher_id, "DEBIT" AS transaction_type, (
                	CASE 
                		WHEN debit_transactions.stakeholder_id IS NULL THEN account_name
                		WHEN debit_transactions.debit_account_id IS NULL THEN stakeholder_name
					END 
                ) AS descriptions, 
                0 AS credited_amount, amount AS debited_amount
                FROM journal_voucher_items AS debit_transactions
                LEFT JOIN journal_vouchers ON debit_transactions.journal_voucher_id = journal_vouchers.journal_id
                LEFT JOIN accounts ON debit_transactions.debit_account_id = accounts.account_id
                LEFT JOIN stakeholders ON debit_transactions.stakeholder_id = stakeholders.stakeholder_id 
                WHERE debit_transactions.journal_voucher_id = ' . $this->{$this::DB_TABLE_PK} . '
                AND transaction_date >= "' . $from . '"
                AND transaction_date <= "' . $to . '"
                
                ) AS journal_transactions
                ORDER BY transaction_type DESC, id ASC, descriptions ASC
                ';

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function jv_preview_rows(){
        $this->load->model(['journal_voucher_item','journal_voucher_credit_account']);
        $where = 'journal_voucher_id = '.$this->{$this::DB_TABLE_PK}.'';
        $credit_items = $this->journal_voucher_credit_account->get(0,0,$where,'id ASC');
        $credit_transactions = [];
        foreach($credit_items as $credit_item){
            $credit_transactions[] = [
                'id' => $credit_item->id,
                'account' => $credit_item->account()->account_name,
                'amount' => $credit_item->amount,
                'narration' => $credit_item->narration
            ];
        }

        $debit_items = $this->journal_voucher_item->get(0,0,$where,'item_id ASC');
        $debit_transactions = [];
        foreach($debit_items as $debit_item){
            $debit_transactions[] = [
                'id' => $debit_item->item_id,
                'account' => $debit_item->account()->account_name,
                'amount' => $debit_item->amount,
                'narration' => $debit_item->narration
            ];
        }

        $large_array = [];
        if(sizeof($credit_transactions) > sizeof($debit_transactions)){
            $large_array = $credit_transactions;
        }else{
            $large_array = $debit_transactions;
        }

        //return $credit_transactions;

        return $large_array;
         exit;
      ////// use try and cath
        $count = 0;
        $items_to_preview = [];
        foreach ($large_array as $index => $item){

            $items_to_preview[] = [
                'crdt_trns_id' => $item['id'],
                'cr_account' => $item['account'],
                'cr_amount' => $item['amount'],
                'cr_narration' => $item['narration'],
                'crdt_trns_id' => $debit_transactions[$index] ? $debit_transactions[$index]['id'] : "&bsp;",
                'cr_account' => $debit_transactions[$index] ? $debit_transactions[$index]['account'] : "&bsp;",
                'cr_amount' => $debit_transactions[$index] ? $debit_transactions[$index]['amount'] : "&bsp;",
                'cr_narration' => $debit_transactions[$index] ? $debit_transactions[$index]['narration'] : "&bsp;",

            ];

            $count++;
        }


        return $items_to_preview;

    }

    public function attachments(){
        $this->load->model('journal_voucher_attachment');
        $junctions = $this->journal_voucher_attachment->get(0,0,['journal_voucher_id' => $this->{$this::DB_TABLE_PK}]);
        $attachments = [];
        foreach ($junctions as $junction){
            $attachments[] = $junction->attachment();
        }
        return $attachments;

    }





}
