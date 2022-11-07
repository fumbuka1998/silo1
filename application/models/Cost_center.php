<?php

class Cost_center extends MY_Model{
    
    const DB_TABLE = 'cost_centers';
    const DB_TABLE_PK = 'id';

    public $cost_center_name;
    public $description;
    

    public function cost_centers_list($limit, $start, $keyword, $order){

        //order string
        $order_string = dataTable_order_string(['cost_center_name','description'],$order,'cost_center_name');
        
        $where = '';
        if($keyword != ''){
            $where .= 'cost_center_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
        }

        $cost_centers = $this->get($limit,$start,$where,$order_string);
        $rows = [];

        foreach($cost_centers as $cost_center){
            $data['cost_center'] = $cost_center;
            $rows[] = [
                $cost_center->cost_center_name,
                $cost_center->description,
                $this->load->view('finance/settings/cost_centers_list_actions',$data,true)
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows();

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function dropdown_options(){
        $cost_centers = $this->get();
        $options[''] = '&nbsp;';
        foreach ($cost_centers as $cost_center){
            $options[$cost_center->{$cost_center::DB_TABLE_PK}] = $cost_center->cost_center_name;
        }
        return $options;
    }

    public function cost_center_with_no_account_options(){

        $sql = 'SELECT  id,cost_center_name FROM cost_centers WHERE id NOT IN 
                (SELECT id FROM  cost_center_accounts)';

        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = "&nbsp;";
        foreach($results as $result){
            $options[$result->id] = $result->cost_center_name;
        }
        return $options;
    }

    public function cost_center_payments($cost_center_id = "all", $from = null, $to =  null){
        $sql = 'SELECT * FROM (
                SELECT payment_vouchers.payment_voucher_id, payment_date, cost_center_name, payment_vouchers.reference, symbol AS currency_symbol,
                COALESCE(SUM(amount),0) AS paid_amount, exchange_rate
                FROM payment_voucher_items
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN payment_voucher_item_approved_invoice_items ON payment_voucher_items.payment_voucher_item_id = payment_voucher_item_approved_invoice_items.payment_voucher_item_id
                LEFT JOIN purchase_order_payment_request_approval_invoice_items ON payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id = purchase_order_payment_request_approval_invoice_items.id
                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id
                LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
                LEFT JOIN cost_centers ON cost_center_purchase_orders.cost_center_id = cost_centers.id';
                if($cost_center_id != "all" && !is_null($cost_center_id)){
                    $sql .= ' WHERE cost_center_purchase_orders.cost_center_id = "'.$cost_center_id.'" ';
                }
                if(!is_null($from)){
                    $sql .= ''.($cost_center_id != 'all' ? ' AND' : ' WHERE').' payment_date >= "'.$from.'" ';
                }
                if(!is_null($to)){
                    $sql .= ''.($cost_center_id == 'all' && is_null($from)) ? ' WHERE' : ' AND'.' payment_date <= "'.$to.'" ';
                }

        $sql .= '  UNION  
                
                SELECT payment_voucher_id, payment_date,(
                  SELECT DISTINCT cost_center_name FROM cost_center_payment_voucher_items
                  LEFT JOIN cost_centers ON cost_center_payment_voucher_items.cost_center_id = cost_centers.id 
                  LEFT JOIN payment_voucher_items ON cost_center_payment_voucher_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                  WHERE payment_voucher_items.payment_voucher_id = main_table.payment_voucher_id
                ) AS cost_center_name, reference, symbol AS currency_symbol, (
                  SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                  WHERE payment_voucher_items.payment_voucher_id = main_table.payment_voucher_id
                ) AS paid_amount, exchange_rate 
                FROM payment_vouchers AS main_table
                LEFT JOIN currencies ON main_table.currency_id = currencies.currency_id
                WHERE main_table.payment_voucher_id IN (
                  SELECT payment_voucher_id FROM cost_center_payment_voucher_items
                  LEFT JOIN payment_voucher_items ON cost_center_payment_voucher_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id';
        if($cost_center_id != "all" && !is_null($cost_center_id)){
            $sql .= ' WHERE cost_center_id = "'.$cost_center_id.'" ';
        }
        $sql .= ' ) ';
        if(!is_null($from)){
            $sql .= ' AND payment_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND payment_date <= "'.$to.'" ';
        }

        $sql .= ' ) AS all_cost_center_payments ORDER BY payment_date DESC';

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function cost_center_payments_accountwise($cost_center_id = "all", $from = null, $to =  null){
        $sql = 'SELECT * FROM (
                    SELECT "payment" AS nature, account_name AS cost_type, debit_account_id, symbol, COALESCE(SUM(amount),0) AS amount, COALESCE(SUM(amount * exchange_rate),0) AS amount_in_basecurrency
                    FROM payment_voucher_items
                    LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    LEFT JOIN payment_voucher_item_approved_invoice_items ON payment_voucher_items.payment_voucher_item_id = payment_voucher_item_approved_invoice_items.payment_voucher_item_id
                    LEFT JOIN purchase_order_payment_request_approval_invoice_items ON payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id = purchase_order_payment_request_approval_invoice_items.id
                    LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    LEFT JOIN accounts ON payment_voucher_items.debit_account_id = accounts.account_id
                    LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id
                    LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id';
                    if($cost_center_id != "all" && !is_null($cost_center_id)){
                        $sql .= ' WHERE cost_center_id = "'.$cost_center_id.'" ';
                    }
                    if(!is_null($from)){
                        $sql .= ' AND payment_date >= "'.$from.'" ';
                    }
                    if(!is_null($to)){
                        $sql .= ' AND payment_date <= "'.$to.'" ';
                    }
            $sql .= ' GROUP BY account_name 
            
                    UNION
    
                    SELECT "payment" AS nature, account_name AS cost_type, debit_account_id, symbol, COALESCE(SUM(amount),0) AS amount, COALESCE(SUM(amount * exchange_rate),0) AS amount_in_basecurrency
                    FROM cost_center_payment_voucher_items
                    LEFT JOIN payment_voucher_items ON cost_center_payment_voucher_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                    LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    LEFT JOIN accounts ON payment_voucher_items.debit_account_id = accounts.account_id
                    LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id';
                    if($cost_center_id != "all" && !is_null($cost_center_id)){
                        $sql .= ' WHERE cost_center_id = "'.$cost_center_id.'" ';
                    }
                    if(!is_null($from)){
                        $sql .= ' AND payment_date >= "'.$from.'" ';
                    }

                    if(!is_null($to)){
                        $sql .= ' AND payment_date <= "'.$to.'" ';
                    }
        $sql .= ' GROUP BY account_name
        
            ) AS all_cost_center_payments GROUP BY cost_type ORDER BY cost_type ASC';

        $query = $this->db->query($sql);
        return $query->num_rows() > 0 ? $query->result() : false;
    }

}

