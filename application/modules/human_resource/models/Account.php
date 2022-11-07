<?php

class Account extends MY_Model{
    
    const DB_TABLE = 'accounts';
    const DB_TABLE_PK = 'account_id';
    const ACCOUNT_FOR_JUNCTIONS = ['project','contractor'];

    public $account_name;
    public $account_group_id;
    public $opening_balance;
    public $description;
    public $bank_id;

    public function account_group()
    {
        $this->load->model('account_group');
        $account_group = new Account_group();
        $account_group->load($this->account_group_id);
        return $account_group;
    }

    public function account_group_name(){
        return trim($this->account_group()->group_name);
    }

    public function account_for()
    {
        foreach ($this::ACCOUNT_FOR_JUNCTIONS as $junction){
            $model = $junction.'_account';
            $this->load->model($model);
            $junctions = $this->$model->get(1,0,['account_id' => $this->{$this::DB_TABLE_PK}]);
            if(!empty($junctions)){
                return $junction;
            }
        }
    }

    public function project(){
        $this->load->model('project_account');
        $project_accounts = $this->project_account->get(1,0,['account_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($project_accounts) ? array_shift($project_accounts)->project() : false;
    }

    public function bank(){
        $this->load->model('bank');
        $bank = new Bank();
        $bank->load($this->bank_id);
        return $bank;
    }

    public function contractor(){
        $this->load->model('contractor_account');
        $contractor_accounts = $this->contractor_account->get(1,0,['account_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($contractor_accounts) ? array_shift($contractor_accounts)->contractor() : false;
    }

    public function account_nature(){
        $group_name = $this->account_group_name();
        if($group_name == 'ACCOUNT PAYABLE'){
           $nature = 'credit';
        } else {
            $nature = 'debit';
        }
        return $nature;
    }

    public function accounts_list(){
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');
        $limit = $this->input->post('length');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'account_name';
                break;
            case 2;
                $order_column = 'description';
                break;
            default:
                $order_column = 'account_name';
        }

        $order = $order_column.' '.$order_dir;

        $where = '';
        if($keyword != ''){
            $where .= 'account_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
        }

        $accounts = $this->get($limit,$start,$where,$order);
        $this->load->model(['bank', 'bank_account']);
        $data['bank_options'] = $this->bank->bank_options();
        $rows = [];
        foreach($accounts as $account){
            $data['account'] = $account;
            $account_details = $this->bank_account->get(1,0, ['account_id' => $account->account_id]);
            $found_account = array_shift($account_details);
            $data['account_details'] = $found_account;
            $rows[] = [
                $account->account_name,
                $account->account_group()->group_name,
                $account->description,
               $this->load->view('finance/accounts_list_actions',$data,true)
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

    public function contra_debit_account_options($credit_account_id){
        $sql = 'SELECT account_id, account_name FROM accounts
                LEFT JOIN account_groups AS parent ON accounts.account_group_id = parent.account_group_id
                LEFT JOIN account_groups AS nature ON parent.account_group_id = nature.group_nature_id
                WHERE (nature.group_name = "CASH IN HAND" OR nature.group_name = "BANK")
                
                ';
        $sql .= ($credit_account_id != 0 ? ' AND account_id != "'.$credit_account_id.'" ' : '');
        $options[''] = '&nbsp;';
        $query = $this->db->query($sql);
        $results = $query->result();

        foreach ($results as $row){
            $options[$row->account_id] = $row->account_name;
        }

        return $options;
    }

    public function expense_pv_debit_account_options($account_group = 'INDIRECT EXPENSES'){
        $options[''] = '&nbsp;';
        $sql = 'SELECT account_id, account_name FROM accounts
                LEFT JOIN account_groups AS parent ON accounts.account_group_id = parent.account_group_id
                LEFT JOIN account_groups AS nature ON parent.account_group_id = nature.group_nature_id
                 WHERE nature.group_name = "'.$account_group.'"
        ';

        $query = $this->db->query($sql);
        $results = $query->result();

        foreach ($results as $row){
            $options[$row->account_id] = $row->account_name;
        }

        return $options;
    }

    public function budget_expense_account_options($cost_center_level,$cost_center_id){
        $sql = 'SELECT account_id, account_name FROM accounts
                LEFT JOIN account_groups AS parent ON accounts.account_group_id = parent.account_group_id
                LEFT JOIN account_groups AS nature ON parent.account_group_id = nature.group_nature_id
                 WHERE nature.group_name = "DIRECT EXPENSES" AND account_id NOT IN (
                  SELECT expense_account_id FROM miscellaneous_budgets WHERE ';
        if($cost_center_level == 'project'){
                $sql .= ' project_id = "'.$cost_center_id.'" AND task_id IS NULL ';
        } else {
            $sql .= ' task_id = "'.$cost_center_id.'"';
        }
        $sql .= '
                 )
        ';

        $query = $this->db->query($sql);
        $results = $query->result();

        $options = '<option value="">&nbsp;</option>';
        foreach ($results as $row){
            $options .= '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
        }

        return $options;
    }

    public function vendor_pv_debit_account_options(){
        $options[''] = '&nbsp;';
        $sql = 'SELECT account_id, account_name FROM accounts
                LEFT JOIN account_groups ON accounts.account_group_id = account_groups.account_group_id
                WHERE group_name = "ACCOUNT PAYABLE"
        ';
        $query = $this->db->query($sql);
        $results = $query->result();

        foreach ($results as $row){
            $options[$row->account_id] = $row->account_name;
        }

        return $options;
    }

    public function balance($currency_id,$date = null){
        $date = !is_null($date) ? $date : date('Y-m-d');
        if($currency_id == 1){
            $project_certificate_sql =
                '   SELECT COALESCE(SUM(certified_amount),0) FROM project_certificates
                    LEFT JOIN projects ON project_certificates.project_id = projects.project_id
                    LEFT JOIN clients ON projects.client_id = clients.client_id
                    WHERE account_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND certificate_date <= "'.$date.'"
              ';
            $project_certificate_sql2 =
                '  SELECT COALESCE(SUM(certified_amount),0) FROM sub_contract_certificates
                    LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id =  sub_contracts.id
                    LEFT JOIN contractors ON sub_contracts.contractor_id = contractors.id
                    LEFT JOIN contractor_accounts ON contractors.id = contractor_accounts.contractor_id 
                    WHERE account_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND certificate_date <= "'.$date.'"
              ';
        } else {
            $project_certificate_sql = 0;
            $project_certificate_sql2 = 0;
        }

        $sql = 'SELECT (
                    (
                        SELECT COALESCE(SUM(amount),0) FROM contra_items
                        LEFT JOIN contras ON contra_items.contra_id = contras.contra_id
                        WHERE debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND currency_id = "'.$currency_id.'"
                        AND contra_date <= "'.$date.'"
                    ) + (
                        SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                        LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                        WHERE debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND currency_id = "'.$currency_id.'"
                        AND payment_date <= "'.$date.'"
                    ) + (
                        SELECT COALESCE(SUM(
                             (
                               SELECT COALESCE(SUM(requisition_approval_asset_items.approved_quantity * requisition_approval_asset_items.approved_rate),0)
                               FROM requisition_approval_asset_items
                                LEFT JOIN imprest_voucher_asset_items ON requisition_approval_asset_items.id = imprest_voucher_asset_items.requisition_approval_asset_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             ) + (
                               SELECT COALESCE(SUM(requisition_approval_material_items.approved_quantity * requisition_approval_material_items.approved_rate),0)
                               FROM requisition_approval_material_items
                               LEFT JOIN imprest_voucher_material_items ON requisition_approval_material_items.id = imprest_voucher_material_items.requisition_approval_material_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             ) + (
                               SELECT COALESCE(SUM(requisition_approval_cash_items.approved_quantity * requisition_approval_cash_items.approved_rate),0)
                               FROM requisition_approval_cash_items
                               LEFT JOIN imprest_voucher_cash_items ON requisition_approval_cash_items.id = imprest_voucher_cash_items.requisition_approval_cash_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             ) + (
                               SELECT COALESCE(SUM(requisition_approval_service_items.approved_quantity * requisition_approval_service_items.approved_rate),0)
                               FROM requisition_approval_service_items
                               LEFT JOIN imprest_voucher_service_items ON requisition_approval_service_items.id = imprest_voucher_service_items.requisition_approval_service_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             )
                        ),0) 
                        FROM imprest_vouchers
                        WHERE imprest_vouchers.debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND imprest_vouchers.currency_id = "'.$currency_id.'"
                        AND imprest_vouchers.imprest_date <= "'.$date.'"
                    ) + (
                        SELECT COALESCE(SUM(amount),0) FROM receipt_items
                        LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                        WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = "'.$currency_id.'"
                        AND receipt_date <= "'.$date.'"
                    ) +(
                         '.$project_certificate_sql.'
                    ) - (
                          SELECT COALESCE(SUM(amount),0) FROM contra_items
                          LEFT JOIN contras ON contra_items.contra_id = contras.contra_id
                          WHERE credit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                          AND currency_id = "'.$currency_id.'"
                          AND contra_date <= "'.$date.'"
                    ) - (
                          SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                          LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                          WHERE credit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                          AND currency_id = "'.$currency_id.'"
                          AND payment_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(
                             (
                               SELECT COALESCE(SUM(requisition_approval_asset_items.approved_quantity * requisition_approval_asset_items.approved_rate),0)
                               FROM requisition_approval_asset_items
                                LEFT JOIN imprest_voucher_asset_items ON requisition_approval_asset_items.id = imprest_voucher_asset_items.requisition_approval_asset_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             ) + (
                               SELECT COALESCE(SUM(requisition_approval_material_items.approved_quantity * requisition_approval_material_items.approved_rate),0)
                               FROM requisition_approval_material_items
                               LEFT JOIN imprest_voucher_material_items ON requisition_approval_material_items.id = imprest_voucher_material_items.requisition_approval_material_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             ) + (
                               SELECT COALESCE(SUM(requisition_approval_cash_items.approved_quantity * requisition_approval_cash_items.approved_rate),0)
                               FROM requisition_approval_cash_items
                               LEFT JOIN imprest_voucher_cash_items ON requisition_approval_cash_items.id = imprest_voucher_cash_items.requisition_approval_cash_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             ) + (
                               SELECT COALESCE(SUM(requisition_approval_service_items.approved_quantity * requisition_approval_service_items.approved_rate),0)
                               FROM requisition_approval_service_items
                               LEFT JOIN imprest_voucher_service_items ON requisition_approval_service_items.id = imprest_voucher_service_items.requisition_approval_service_item_id
                               WHERE imprest_voucher_id = imprest_vouchers.id
                             )
                        ),0) 
                        FROM imprest_vouchers
                        WHERE imprest_vouchers.credit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND imprest_vouchers.currency_id = "'.$currency_id.'"
                        AND imprest_vouchers.imprest_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM receipt_items
                        LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                        WHERE credit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = "'.$currency_id.'"
                        AND receipt_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM invoices
                       LEFT JOIN vendor_invoices ON invoices.id = vendor_invoices.invoice_id
                       LEFT JOIN vendors ON vendor_invoices.vendor_id = vendors.vendor_id
                          WHERE account_id = '.$this->{$this::DB_TABLE_PK}.'
                          AND currency_id = "'.$currency_id.'"
                          AND invoice_date <= "'.$date.'"
                    ) - (
                       '.$project_certificate_sql2.'
                    ) - (
                        SELECT COALESCE(SUM(
                          (
                              SELECT COALESCE(SUM(assets.book_value), 0)
                              FROM assets
                              LEFT JOIN asset_sub_location_histories ON assets.id = asset_sub_location_histories.asset_id
                              LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                              LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                              LEFT JOIN imprest_voucher_retirement_grns ON goods_received_notes.grn_id = imprest_voucher_retirement_grns.grn_id
                              LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_grns.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                              WHERE imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                              AND imprest_voucher_retirements.id = main_retirement_table.id
                              AND imprest_vouchers.currency_id = "'.$currency_id.'"
                              AND asset_sub_location_histories.received_date <= "'.$date.'"
                        
                          ) + (
                        
                              SELECT COALESCE(SUM(material_stocks.quantity * material_stocks.price), 0)
                              FROM material_stocks
                              LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                              LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                              LEFT JOIN imprest_voucher_retirement_grns ON goods_received_notes.grn_id = imprest_voucher_retirement_grns.grn_id
                              LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_grns.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                              WHERE debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                              AND imprest_voucher_retirements.id = main_retirement_table.id
                              AND imprest_vouchers.currency_id = "'.$currency_id.'"
                              AND imprest_date <= "'.$date.'"
                        
                          ) + (
                        
                              SELECT COALESCE(SUM(imprest_voucher_retired_services.quantity * imprest_voucher_retired_services.rate),0) AS retired_service_quantity
                              FROM imprest_voucher_retired_services
                              WHERE imprest_voucher_retired_services.imprest_voucher_retirement_id = main_retirement_table.id
                              AND imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        
                          ) + (
                        
                              SELECT COALESCE(SUM(imprest_voucher_retired_cash.quantity * imprest_voucher_retired_cash.rate),0) AS retired_cash_quantity
                              FROM imprest_voucher_retired_cash
                              WHERE imprest_voucher_retired_cash.imprest_voucher_retirement_id = main_retirement_table.id
                              AND imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        
                          )
                        ),0)
                            FROM imprest_voucher_retirements AS main_retirement_table
                            LEFT JOIN imprest_vouchers ON main_retirement_table.imprest_voucher_id = imprest_vouchers.id
                            WHERE imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                            AND imprest_vouchers.currency_id = "'.$currency_id.'"
                            AND imprest_date <= "'.$date.'"                  
                    )
              
        ) AS actual_balance ';

        $query = $this->db->query($sql);
        return $this->account_nature() == 'debit' ? $this->opening_balance + $query->row()->actual_balance : -($this->opening_balance + $query->row()->actual_balance);
    }

    public function balance_in_base_currency($as_of = null){
        $this->load->model('currency');
        $currencies = $this->currency->get();
        $total_base_balance = 0;
        foreach ($currencies as $currency){
            $total_base_balance += $this->balance($currency->{$currency::DB_TABLE_PK},$as_of) * $currency->rate_to_native();
        }
        return $total_base_balance;
    }

    public function statement($currency_id,$from,$to){
        $account_id = $this->{$this::DB_TABLE_PK};
        $account =  new self();
        $account->load($account_id);
        $account_nature = $account->account_nature();

        $sql = 'SELECT "CONTRA" AS transaction_type, "CREDIT" AS transaction_action, contra_id AS transaction_id,
                 contra_date AS transaction_date,reference,(
                  SELECT COALESCE(SUM(amount),0) FROM contra_items
                  WHERE contra_id = contras.contra_id
                )  AS credit, 0 AS debit, datetime_posted AS created_at
                FROM contras
                WHERE credit_account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND contra_date >= "'.$from.'"
                AND contra_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "CONTRA" AS transaction_type, "DEBIT" AS transaction_action, contras.contra_id AS transaction_id,
                 contras.contra_date AS transaction_date, reference, 0 AS credit, amount AS debit, datetime_posted AS created_at
                FROM contra_items
                LEFT JOIN contras ON contra_items.contra_id = contras.contra_id
                WHERE debit_account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND contra_date >= "'.$from.'"
                AND contra_date <= "'.$to.'"
                
                UNION ALL

                SELECT "PAYMENT" AS transaction_type, "CREDIT" AS transaction_action, payment_voucher_id AS transaction_id,
                payment_date AS transaction_date,reference,(
                  SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                  WHERE payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                ) AS credit, 0 AS debit, created_at
                FROM payment_vouchers
                WHERE credit_account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND payment_date >= "'.$from.'"
                AND payment_date <= "'.$to.'"
                
                UNION ALL
              
                SELECT "PAYMENT" AS transaction_type, "DEBIT" AS transaction_action, payment_vouchers.payment_voucher_id AS transaction_id, 
                payment_date AS transaction_date,reference, 0 AS credit, amount AS debit, created_at
                FROM payment_voucher_items  
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                WHERE debit_account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND payment_date >= "'.$from.'"
                AND payment_date <= "'.$to.'"
                
                UNION ALL
                              
                SELECT "INVOICE" AS transaction_type,  "CREDIT" AS transaction_action, invoices.id AS transaction_id, invoice_date AS transaction_date,reference,
                amount AS credit,0 AS debit, invoices.created_at
                FROM invoices
                LEFT JOIN vendor_invoices ON invoices.id = vendor_invoices.invoice_id
                LEFT JOIN vendors ON vendor_invoices.vendor_id = vendors.vendor_id
                WHERE account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND invoice_date >= "'.$from.'"
                AND invoice_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "IMPREST" AS transaction_type, "CREDIT" AS transaction_action, imprest_vouchers.id AS transaction_id, imprest_vouchers.imprest_date AS transaction_date, " " AS reference,
                       (
                         (
                           SELECT COALESCE(
                               SUM(requisition_approval_asset_items.approved_quantity * requisition_approval_asset_items.approved_rate),
                               0)
                           FROM requisition_approval_asset_items
                             LEFT JOIN imprest_voucher_asset_items ON requisition_approval_asset_items.id = imprest_voucher_asset_items.requisition_approval_asset_item_id
                           WHERE imprest_voucher_id = imprest_vouchers.id
                         ) + (
                           SELECT COALESCE(
                               SUM(requisition_approval_material_items.approved_quantity * requisition_approval_material_items.approved_rate),
                               0)
                           FROM requisition_approval_material_items
                           LEFT JOIN imprest_voucher_material_items ON requisition_approval_material_items.id = imprest_voucher_material_items.requisition_approval_material_item_id
                           WHERE imprest_voucher_id = imprest_vouchers.id
                         ) + (
                           SELECT COALESCE(
                               SUM(requisition_approval_cash_items.approved_quantity * requisition_approval_cash_items.approved_rate),
                               0)
                           FROM requisition_approval_cash_items
                           LEFT JOIN imprest_voucher_cash_items ON requisition_approval_cash_items.id = imprest_voucher_cash_items.requisition_approval_cash_item_id
                           WHERE imprest_voucher_id = imprest_vouchers.id
                         ) + (
                           SELECT COALESCE(
                               SUM(requisition_approval_service_items.approved_quantity * requisition_approval_service_items.approved_rate),
                               0)
                           FROM requisition_approval_service_items
                           LEFT JOIN imprest_voucher_service_items ON requisition_approval_service_items.id = imprest_voucher_service_items.requisition_approval_service_item_id
                           WHERE imprest_voucher_id = imprest_vouchers.id
                         )
                       ) AS credit,0 AS debit, imprest_vouchers.created_at 
                FROM imprest_vouchers
                WHERE imprest_vouchers.credit_account_id = "'.$account_id.'"
                AND imprest_date >= "'.$from.'"
                AND imprest_date <= "'.$to.'" 
                
                UNION ALL
                               
                SELECT "IMPREST" AS transaction_type, "DEBIT" AS transaction_action, imprest_vouchers.id AS transaction_id, imprest_vouchers.imprest_date AS transaction_date, " " AS reference,0 AS credit,
                   (
                     (
                       SELECT COALESCE(
                           SUM(requisition_approval_asset_items.approved_quantity * requisition_approval_asset_items.approved_rate),
                           0)
                       FROM requisition_approval_asset_items
                         LEFT JOIN imprest_voucher_asset_items ON requisition_approval_asset_items.id = imprest_voucher_asset_items.requisition_approval_asset_item_id
                       WHERE imprest_voucher_id = imprest_vouchers.id
                     ) + (
                       SELECT COALESCE(
                           SUM(requisition_approval_material_items.approved_quantity * requisition_approval_material_items.approved_rate),
                           0)
                       FROM requisition_approval_material_items
                       LEFT JOIN imprest_voucher_material_items ON requisition_approval_material_items.id = imprest_voucher_material_items.requisition_approval_material_item_id
                       WHERE imprest_voucher_id = imprest_vouchers.id
                     ) + (
                       SELECT COALESCE(
                           SUM(requisition_approval_cash_items.approved_quantity * requisition_approval_cash_items.approved_rate),
                           0)
                       FROM requisition_approval_cash_items
                       LEFT JOIN imprest_voucher_cash_items ON requisition_approval_cash_items.id = imprest_voucher_cash_items.requisition_approval_cash_item_id
                       WHERE imprest_voucher_id = imprest_vouchers.id
                     ) + (
                       SELECT COALESCE(
                           SUM(requisition_approval_service_items.approved_quantity * requisition_approval_service_items.approved_rate),
                           0)
                       FROM requisition_approval_service_items
                       LEFT JOIN imprest_voucher_service_items ON requisition_approval_service_items.id = imprest_voucher_service_items.requisition_approval_service_item_id
                       WHERE imprest_voucher_id = imprest_vouchers.id
                     )
                   ) AS debit, imprest_vouchers.created_at 
                FROM imprest_vouchers
                WHERE  imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                AND imprest_date >= "'.$from.'"
                AND imprest_date <= "'.$to.'" 
                 
                UNION ALL
                
                SELECT * FROM (
                  SELECT "RETIREMENT" AS transaction_type, "CREDIT" AS transaction_action, main_retirement_table.id AS transaction_id, retirement_date, " " AS reference,
                    (
                      (
                        SELECT COALESCE(SUM(assets.book_value), 0)
                        FROM assets
                        LEFT JOIN asset_sub_location_histories ON assets.id = asset_sub_location_histories.asset_id
                        LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                        LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                        LEFT JOIN imprest_voucher_retirement_grns ON goods_received_notes.grn_id = imprest_voucher_retirement_grns.grn_id
                        LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_grns.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                        WHERE imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND imprest_voucher_retirements.id = main_retirement_table.id
                        AND imprest_vouchers.currency_id = "'.$currency_id.'"
                        AND asset_sub_location_histories.received_date >= "'.$from.'"
                        AND asset_sub_location_histories.received_date <= "'.$to.'" 
                
                      ) + (
                
                        SELECT COALESCE(SUM(material_stocks.quantity * material_stocks.price), 0)
                        FROM material_stocks
                        LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                        LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                        LEFT JOIN imprest_voucher_retirement_grns ON goods_received_notes.grn_id = imprest_voucher_retirement_grns.grn_id
                        LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_grns.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                        WHERE debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND imprest_voucher_retirements.id = main_retirement_table.id
                        AND imprest_vouchers.currency_id = "'.$currency_id.'"
                        AND imprest_date >= "'.$from.'"
                        AND imprest_date <= "'.$to.'" 
                
                      ) + (
                
                        SELECT COALESCE(SUM(imprest_voucher_retired_services.quantity * imprest_voucher_retired_services.rate),0) AS retired_service_quantity
                        FROM imprest_voucher_retired_services
                        WHERE imprest_voucher_retired_services.imprest_voucher_retirement_id = main_retirement_table.id
                        AND imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                
                      ) + (
                
                        SELECT COALESCE(SUM(imprest_voucher_retired_cash.quantity * imprest_voucher_retired_cash.rate),0) AS retired_cash_quantity
                        FROM imprest_voucher_retired_cash
                        WHERE imprest_voucher_retired_cash.imprest_voucher_retirement_id = main_retirement_table.id
                        AND imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                
                      )
                    ) AS credit, 0 AS debit, main_retirement_table.created_at 
                    FROM imprest_voucher_retirements AS main_retirement_table
                    LEFT JOIN imprest_vouchers ON main_retirement_table.imprest_voucher_id = imprest_vouchers.id
                    WHERE imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                    AND main_retirement_table.is_examined = 1
                    AND imprest_vouchers.currency_id = "'.$currency_id.'"
                    AND imprest_date >= "'.$from.'"
                    AND imprest_date <= "'.$to.'" 
                ) AS artificial_table GROUP BY transaction_id 
                

                 ';

        if($currency_id == 1){
            $sql .= '
                UNION ALL
                
                SELECT "SUB CONTRA CERTIFICATE" AS transaction_type,  "CREDIT" AS transaction_action, sub_contract_certificates.id AS transaction_id, certificate_date AS transaction_date, certificate_number AS reference,
                certified_amount AS credit,0 AS debit, sub_contract_certificates.created_at
                FROM sub_contract_certificates
                LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id =  sub_contracts.id
                LEFT JOIN contractors ON sub_contracts.contractor_id = contractors.id
                LEFT JOIN contractor_accounts ON contractors.id = contractor_accounts.contractor_id 
                WHERE account_id = "'.$account_id.'"
                AND certificate_date >= "'.$from.'"
                AND certificate_date <= "'.$to.'" 
            ';
        }

        $sql .= '
               
                UNION ALL
                              
                SELECT "BILL" AS transaction_type,  "DEBIT" AS transaction_action, outgoing_invoices.id AS transaction_id, invoice_date AS transaction_date,reference,
                0 AS credit,
                (
                    (
                        SELECT COALESCE(SUM(quantity * rate),0) FROM outgoing_invoice_items
                        WHERE outgoing_invoice_items.outgoing_invoice_id = outgoing_invoices.id
                    ) + (
                        0.01 * outgoing_invoices.vat_percentage * (
                        SELECT COALESCE(SUM(quantity * rate),0) FROM outgoing_invoice_items
                        WHERE outgoing_invoice_items.outgoing_invoice_id = outgoing_invoices.id
                        )
                    )
                 )AS debit, outgoing_invoices.created_at
                FROM outgoing_invoices
                LEFT JOIN clients ON outgoing_invoices.invoice_to = clients.client_id
                WHERE account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND invoice_date >= "'.$from.'"
                AND invoice_date <= "'.$to.'"
                 
                UNION ALL
                
                SELECT "RECEIPT" AS transaction_type, "CREDIT" AS transaction_action, id AS transaction_id,
                receipt_date AS transaction_date,reference,(
                SELECT COALESCE(SUM(amount),0) FROM receipt_items
                WHERE receipt_items.receipt_id = receipts.id
                ) AS credit, 0 AS debit, receipts.created_at
                FROM receipts
                WHERE credit_account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND receipt_date >= "'.$from.'"
                AND receipt_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "RECEIPT" AS transaction_type, "DEBIT" AS transaction_action, receipts.id AS transaction_id,
                receipt_date AS transaction_date,reference,0 AS credit,
                (
                SELECT COALESCE(SUM(amount),0) FROM receipt_items
                WHERE receipt_items.receipt_id = receipts.id
                ) AS debit, receipts.created_at
                FROM receipts
                WHERE debit_account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND receipt_date >= "'.$from.'"
                AND receipt_date <= "'.$to.'" 
                ';

        if($account_nature == 'debit'){
            $sql .= '
                UNION ALL
                    
                SELECT "WITHHOLDING TAX" AS transaction_type,  "CREDIT" AS transaction_action, withholding_taxes . id AS transaction_id, date AS transaction_date, sub_contract_certificates . certificate_number AS reference,
                withheld_amount AS credit, 0 AS debit, withholding_taxes . created_at 
                FROM withholding_taxes
                LEFT JOIN payment_voucher_items ON withholding_taxes . payment_voucher_item_id = payment_voucher_items . payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items . payment_voucher_id = payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers . payment_voucher_id = sub_contract_certificate_payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers . sub_contract_certificate_id = sub_contract_certificates . id
                WHERE withholding_taxes.credit_account_id = "'.$account_id.'"
                AND date >= "'.$from.'"
                AND date <= "'.$to.'"';

        } else {
            $sql .= '
                UNION ALL
                    
                SELECT "WITHHOLDING TAX" AS transaction_type,  "DEBIT" AS transaction_action, withholding_taxes . id AS transaction_id, date AS transaction_date, sub_contract_certificates . certificate_number AS reference,
                0 AS credit, withheld_amount AS debit, withholding_taxes . created_at 
                FROM withholding_taxes
                LEFT JOIN payment_voucher_items ON withholding_taxes . payment_voucher_item_id = payment_voucher_items . payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items . payment_voucher_id = payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers . payment_voucher_id = sub_contract_certificate_payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers . sub_contract_certificate_id = sub_contract_certificates . id
                WHERE withholding_taxes.credit_account_id = "'.$account_id.'"
                AND date >= "'.$from.'"
                AND date <= "'.$to.'"';
        }
                
            $sql .= 'UNION ALL
                
                SELECT "WITHHOLDING TAX" AS transaction_type,  "CREDIT" AS transaction_action, withholding_taxes.id AS transaction_id, date AS transaction_date, sub_contract_certificates.certificate_number AS reference,
                withheld_amount AS credit,0 AS debit, withholding_taxes.created_at
                FROM withholding_taxes
                LEFT JOIN payment_voucher_items ON withholding_taxes.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
                WHERE withholding_taxes.debit_account_id = "'.$account_id.'"
                AND date >= "'.$from.'"
                AND date <= "'.$to.'" 
                
                UNION ALL
                
                SELECT "WITHHELD TAX PAYMENT" AS transaction_type,  "DEBIT" AS transaction_action, withholding_taxes_payments.id AS transaction_id, payment_date AS transaction_date, "" AS reference,
                0 AS credit, paid_amount AS debit, paid_at AS created_at
                FROM withholding_taxes_payments
                LEFT JOIN withholding_taxes ON withholding_taxes_payments.withholding_tax_id = withholding_taxes.id
                WHERE withholding_taxes.debit_account_id  = "'.$account_id.'"
                AND status = "PAID"
                AND payment_date >= "'.$from.'"
                AND payment_date <= "'.$to.'"
                
                ORDER BY created_at ASC
                ';

        $query = $this->db->query($sql);
        $results = $query->result();

        $this->load->model([
            'contra',
            'payment_voucher',
            'receipt',
            'invoice',
            'project_certificate',
            'Sub_contract_certificate',
            'imprest_voucher',
            'imprest_voucher_retirement',
            'withholding_tax',
            'withholding_taxes_payment',
            'outgoing_invoice'
        ]);

        $transactions = [];
        foreach ($results as $row){
            if($row->transaction_type == 'CONTRA'){
                $transaction = new Contra();
                $transaction->load($row->transaction_id);
                $reference = $row->reference;
                $remarks = $transaction->remarks;
            } else if($row->transaction_type == 'PAYMENT'){
                $transaction = new Payment_voucher();
                $transaction->load($row->transaction_id);
                $reference = $row->reference;
                $remarks = $transaction->remarks;
            } else if($row->transaction_type == 'IMPREST'){
                $transaction = new Imprest_voucher();
                $transaction->load($row->transaction_id);
                $requisition = $transaction->requisition();
                $reference = anchor(base_url('finance/preview_imprest_voucher/'.$transaction->{$transaction::DB_TABLE_PK}), $requisition->requisition_number().', '.$transaction->imprest_voucher_number(),'target="_blank"');
                $remarks = $transaction->remarks;
            } else if($row->transaction_type == 'RETIREMENT'){
                $transaction = new Imprest_voucher_retirement();
                $transaction->load($row->transaction_id);
                $requisition = $transaction->imprest_voucher()->requisition();
                $reference = $requisition->requisition_number().', '.$transaction->imprest_voucher_retirement_number();
                $remarks = $transaction->remarks;
            } else if($row->transaction_type == 'INVOICE'){
                $transaction = new Invoice();
                $transaction->load($row->transaction_id);
                $reference = $transaction->detailed_reference();
                $remarks = $transaction->description;
            } else if($row->transaction_type == 'BILL'){
                $transaction = new Outgoing_invoice();
                $transaction->load($row->transaction_id);
                $reference = $transaction->detailed_reference();
                $remarks = 'Bill to '.$transaction->invoice_to()->client_name.' for '.$transaction->detailed_reference();
            } else if($row->transaction_type == 'SUB CONTRA CERTIFICATE'){
                $transaction = new Sub_contract_certificate();
                $transaction->load($row->transaction_id);
                $reference = $row->reference;
                $remarks = 'payment for sub contract certificate No. '.$transaction->certificate_number.' of '.$transaction->sub_contract()->contract_name.' - '.$transaction->sub_contract()->sub_contractor()->contractor_name;
            } else if($row->transaction_type == "WITHHOLDING TAX"){
                $transaction = new Withholding_tax();
                $transaction->load($row->transaction_id);
                $reference = $row->reference;
                $remarks = $transaction->remarks;
            } else if($row->transaction_type == "WITHHELD TAX PAYMENT"){
                $transaction = new Withholding_taxes_payment();
                $transaction->load($row->transaction_id);
                $reference = $transaction->withholding_tax()->payment_voucher_item()->payment_voucher()->sub_contract_payment_requisition_approval_payment_voucher()->sub_contract_payment_requisition_approval()->sub_contract_requisition()->sub_contract_requisition_number();
                $remarks = "PAID";
            } else {
                $transaction = new Receipt();
                $transaction->load($row->transaction_id);
                $reference = $row->reference;
                $remarks = $transaction->remarks;
            }

            $transaction->load($row->transaction_id);
            $transactions[] = [
                'transaction_id' => $row->transaction_id,
                'transaction_date' => $row->transaction_date,
                'transaction_type' => $row->transaction_type,
                'remarks'=> $remarks,
                'reference' => $reference,
                'debit' => $row->debit,
                'credit' => $row->credit
            ];
        }

        return $transactions;
    }

    public function expense_pv_cost_center_options(){
        if($this->is_site_petty_cash()){
            $options = $this->host_project()->cost_center_options();
        } else {
            $this->load->model('department');
            $options = $this->department->department_options();
        }
        return $options;
    }

    public function dropdown_options($group_natures = []){
        $sql = 'SELECT account_id, account_name FROM accounts';
        if(!empty($group_natures)){
            $group_names = '';
            foreach ($group_natures as $group_name){
                $group_names .= '"'.$group_name.'", ';
            }
            $group_names = rtrim($group_names,', ');

           $sql .= ' WHERE account_group_id IN (
                     SELECT account_groups.account_group_id FROM account_groups
                     LEFT JOIN account_groups AS nature_table ON account_groups.group_nature_id = nature_table.group_nature_id
                     WHERE nature_table.group_name IN ('.$group_names.')) ';
        }
        $query = $this->db->query($sql);
        $accounts = $query->result();
        $options[''] = '&nbsp;';
        foreach ($accounts as $account){
            $options[$account->{$this::DB_TABLE_PK}] = $account->account_name;
        }
        return $options;
    }

    public function check_loan_account($employee_id)
    {
        $sql = 'SELECT * FROM accounts 
                       LEFT JOIN employee_accounts ON employee_accounts.account_id = accounts.account_id
                       WHERE employee_accounts.employee_id = '.$employee_id.' AND accounts.account_name LIKE "%loan%"';
        $query = $this->db->query($sql);
        $account_found = $query->result();

        return $account_found ? true : false;
    }
}