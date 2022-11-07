<?php

class Account extends MY_Model{

    const DB_TABLE = 'accounts';
    const DB_TABLE_PK = 'account_id';
    const ACCOUNT_FOR_JUNCTIONS = ['project','cost_center'];

    public $account_name;
    public $account_group_id;
    public $opening_balance;
    public $description;
    public $account_code;
    public $currency_id;
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

    public function account_for_name(){
		foreach ($this::ACCOUNT_FOR_JUNCTIONS as $junction){
			$model = $junction.'_account';
			$this->load->model($model);
			$junctions = $this->$model->get(1,0,['account_id' => $this->{$this::DB_TABLE_PK}]);
			if(!empty($junctions)){
				$name = $junction.'_name';
				return array_shift($junctions)->$junction()->$name;
			}
		}

	}

    public function project(){
        $this->load->model('project_account');
        $project_accounts = $this->project_account->get(1,0,['account_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($project_accounts) ? array_shift($project_accounts)->project() : false;
    }

    public function cost_center(){
        $this->load->model('cost_center_account');
        $cost_center_accounts = $this->cost_center_account->get(1,0,['account_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($cost_center_accounts) ? array_shift($cost_center_accounts)->cost_center() : false;
    }

    public function bank(){
        $this->load->model('bank');
        $bank = new Bank();
        $bank->load($this->bank_id);
        return $bank;
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

    public function accounts_list($limit,$start,$keyword,$order,$account_group){
		$this->load->model(['bank', 'bank_account','account_group','currency']);
		$currency_id = $this->input->post('currency_id');
		$currency = new Currency();
		$currency->load($currency_id);
		$data['bank_options'] = $this->bank->bank_options();
		$data['account_group'] = $account_group;
		$data['currency_options'] = currency_dropdown_options();
		switch ($account_group) {
			case "BANK":
				$group_name = $account_group;
				$groups = $this->account_group->get(0,0,['group_name'=>$account_group]);
				$group = array_shift($groups);
				$where = ' main_table.account_group_id = '.$group->{$group::DB_TABLE_PK}.' ';
				$order_string = dataTable_order_string(['account_name', 'symbol', 'account_for', 'bank_name'], $order, 'account_name');
				break;
			case "CASH_IN_HAND":
				$group_name = str_replace("_"," ",$account_group);
				$groups = $this->account_group->get(0,0,['group_name'=>(string)$group_name]);
				$group = array_shift($groups);
				$where = ' main_table.account_group_id = '.$group->{$group::DB_TABLE_PK}.' ';
				$order_string = dataTable_order_string(['account_name', 'symbol', 'account_for'], $order, 'account_name');
				break;
			case "LEDGER":
				$primary_groups = "'BANK','CASH IN HAND'";
				$where = ' account_groups.group_name NOT IN ('.$primary_groups.') ';
				$order_string = dataTable_order_string(['account_name', 'symbol', 'account_for'], $order, 'account_name');
				break;
			default:
				$group_name = 'ACCOUNTS '.$account_group;
				$order_string = dataTable_order_string(['account_name'], $order, 'account_name');
				break;
		}
		$order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

		if($account_group == "BANK" || $account_group == "CASH_IN_HAND" || $account_group == "LEDGER") {
			$sql = 'SELECT COUNT(main_table.account_id) AS records_total 
					FROM accounts AS main_table
					LEFT JOIN account_groups ON main_table.account_group_id = account_groups.account_group_id
					LEFT JOIN banks ON main_table.bank_id = banks.id
					LEFT JOIN currencies ON main_table.currency_id = currencies.currency_id
					WHERE ' . $where . '';
			if($account_group == "BANK" || $account_group == "CASH_IN_HAND"){
				$sql .=' AND group_name = "' . $group_name . '"';
			}
			$records_total = $this->db->query($sql)->row()->records_total;
		} else {
			$sql = 'SELECT COUNT(stakeholders.stakeholder_id) AS records_total FROM stakeholders';
			$records_total = $this->db->query($sql)->row()->records_total;
		}

		$where_outer = '';
        if($keyword != ''){
        	switch ($account_group) {
				case "BANK":
					$where_outer = ' WHERE (account_name LIKE "%'.$keyword.'%" OR account_for LIKE "%'.$keyword.'%" OR bank_name LIKE "%'.$keyword.'%" ) ';
					break;
				case "CASH_IN_HAND":
				case "LEDGER":
					$where_outer = ' WHERE (account_name LIKE "%'.$keyword.'%" OR account_for LIKE "%'.$keyword.'%" ) ';
					break;
				default:
					$where_outer = ' WHERE (account_name LIKE "%'.$keyword.'%" ) ';
					break;
			}
        }

		if($account_group == "BANK" || $account_group == "CASH_IN_HAND" || $account_group == "LEDGER") {
			$sql = 'SELECT * FROM (
						SELECT main_table.account_id,account_name, group_name, main_table.currency_id, currency_name, symbol, bank_name, "" AS account_for
						FROM accounts AS main_table
						LEFT JOIN account_groups ON main_table.account_group_id = account_groups.account_group_id
						LEFT JOIN currencies ON main_table.currency_id = currencies.currency_id
						LEFT JOIN banks ON main_table.bank_id = banks.id 
					WHERE ' . $where . '';
			if($account_group == "BANK" || $account_group == "CASH_IN_HAND"){
				$sql .= ' AND group_name = "' . $group_name . '"';
			}
			$sql .= ' ) AS accounts_by_type '.$where_outer.$order_string;
			$query = $this->db->query($sql);
			$records_filtered = $this->db->query($sql)->num_rows();
			$results = $query->result();
		} else {
			$sql = 'SELECT * FROM (
						SELECT stakeholder_id AS account_id, stakeholder_name AS account_name  FROM stakeholders
					) AS stakeholders_list'.$where_outer;
			$query = $this->db->query($sql);
			$records_filtered = $this->db->query($sql)->num_rows();
			$results = $query->result();
		}
        $rows = [];
		$this->load->model('stakeholder');
        foreach($results as $row){
			$data['currency'] = $currency;
			$data['symbol'] = $currency->symbol;
			$data['account_name'] = $row->account_name;
			if($account_group == "BANK"){
				$account = new Account();
				$account->load($row->account_id);
				$data['account'] = $account;
				$account_details = $this->bank_account->get(1,0, ['account_id' => $account->account_id]);
				$found_account = array_shift($account_details);
				$account_type_and_id = $account_group.'_real_'.$row->account_id;
				$data['account_details'] = $found_account;
				$data['running_balance'] = $account->balance($currency_id,date('Y-m-d'));
				$data['account_type_and_id'] = $account_type_and_id;
				$rows[] = [
					$row->account_name,
					$row->bank_name,
					$row->currency_name.'('.$row->symbol.')',
					$account->account_for_name(),
					$this->load->view('finance/accounts_statement_link',$data,true),
					$this->load->view('finance/accounts_list_actions',$data,true)
				];

			} else if($account_group == "CASH_IN_HAND" || $account_group == "LEDGER"){
				$account = new Account();
				$account->load($row->account_id);
				$data['account'] = $account;
				$data['account_details'] = '';
				if($account_group == "CASH_IN_HAND"){
					$account_type_and_id = 'CASH_real_'. $row->account_id;
				} else {
					$account_type_and_id = $account_group . '_real_' . $row->account_id;
				}
				$data['running_balance'] = $account->balance($currency_id,date('Y-m-d'));
				$data['account_type_and_id'] = $account_type_and_id;
				$rows[] = [
					$row->account_name,
					$row->currency_name.'('.$row->symbol.')',
					$account->account_for_name(),
					$this->load->view('finance/accounts_statement_link',$data,true),
					$this->load->view('finance/accounts_list_actions',$data,true)
				];

			} else {
            	$stakeholder = new Stakeholder();
            	$stakeholder->load($row->account_id);
            	$balance = $stakeholder->balance($currency_id,date('Y-m-d'));
            	$data['account'] = $stakeholder;
				$account_type_and_id = $account_group.'_stakeholder_'.$row->account_id;
				$data['running_balance'] = $balance;
				$data['account_type_and_id'] = $account_type_and_id;
            	if($account_group == "PAYABLE"){
            		if($balance < 0 || $balance == 0 ){
						$rows[] = [
							anchor(base_url('stakeholders/stakeholder_profile/'.$row->account_id),$row->account_name,'target="_blank"'),
							$currency->currency_name . '(' . $currency->symbol . ')',
							$this->load->view('finance/accounts_statement_link',$data,true),
						];
					}

				} else if($account_group == "RECEIVABLE"){
					if($balance > 0){
						$rows[] = [
							anchor(base_url('stakeholders/stakeholder_profile/'.$row->account_id),$row->account_name,'target="_blank"'),
							$currency->currency_name . '(' . $currency->symbol . ')',
							$this->load->view('finance/accounts_statement_link',$data,true),
						];
					}
				}
			}
        }

        $json = [
            "recordsTotal" => count($rows),
            "recordsFiltered" => count($rows),
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
//        $sql = 'SELECT account_id, account_name FROM accounts
//                LEFT JOIN account_groups AS parent ON accounts.account_group_id = parent.account_group_id
//                LEFT JOIN account_groups AS nature ON parent.account_group_id = nature.group_nature_id
//                 WHERE nature.group_name = "DIRECT EXPENSES" AND account_id NOT IN (
//                  SELECT expense_account_id FROM miscellaneous_budgets WHERE ';
//        if($cost_center_level == 'project'){
//                $sql .= ' project_id = "'.$cost_center_id.'" AND task_id IS NULL ';
//        } else {
//            $sql .= ' task_id = "'.$cost_center_id.'"';
//        }
//        $sql .= '
//                 )
//        ';
//
        $sql = 'SELECT account_id, account_name FROM accounts
                LEFT JOIN account_groups ON accounts.account_group_id = account_groups.account_group_id
                 WHERE account_groups.group_name = "DIRECT EXPENSES"';

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

    public function balance_scraped($currency_id,$date = null){
        $date = !is_null($date) ? $date : date('Y-m-d');

        $sql = 'SELECT (
                     (
                        SELECT COALESCE(SUM(amount),0) FROM journal_voucher_items
                        LEFT JOIN journal_vouchers ON journal_voucher_items.journal_voucher_id = journal_vouchers.journal_id
                        WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = '.$currency_id.'
                        AND transaction_date <= "'.$date.'"
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
                        AND imprest_vouchers.currency_id = '.$currency_id.'
                        AND imprest_vouchers.imprest_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM journal_voucher_credit_accounts
                        LEFT JOIN journal_vouchers ON journal_voucher_credit_accounts.journal_voucher_id = journal_vouchers.journal_id
                        WHERE account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = '.$currency_id.'
                        AND transaction_date <= "'.$date.'"
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
                        AND imprest_vouchers.currency_id = '.$currency_id.'
                        AND imprest_vouchers.imprest_date <= "'.$date.'"
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
                              WHERE imprest_vouchers.debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                              AND imprest_voucher_retirements.id = main_retirement_table.id
                              AND imprest_vouchers.currency_id = '.$currency_id.'
                              AND asset_sub_location_histories.received_date <= "'.$date.'"
                        
                          ) + (
                        
                              SELECT COALESCE(SUM(material_stocks.quantity * material_stocks.price), 0)
                              FROM material_stocks
                              LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                              LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                              LEFT JOIN imprest_voucher_retirement_grns ON goods_received_notes.grn_id = imprest_voucher_retirement_grns.grn_id
                              LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_grns.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                              WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                              AND imprest_voucher_retirements.id = main_retirement_table.id
                              AND imprest_vouchers.currency_id = '.$currency_id.'
                              AND imprest_date <= "'.$date.'"
                        
                          ) + (
                        
                              SELECT COALESCE(SUM(imprest_voucher_retired_services.quantity * imprest_voucher_retired_services.rate),0) AS retired_service_quantity
                              FROM imprest_voucher_retired_services
                              WHERE imprest_voucher_retired_services.imprest_voucher_retirement_id = main_retirement_table.id
                              AND imprest_vouchers.debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        
                          ) + (
                        
                              SELECT COALESCE(SUM(imprest_voucher_retired_cash.quantity * imprest_voucher_retired_cash.rate),0) AS retired_cash_quantity
                              FROM imprest_voucher_retired_cash
                              WHERE imprest_voucher_retired_cash.imprest_voucher_retirement_id = main_retirement_table.id
                              AND imprest_vouchers.debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        
                          )
                        ),0)
                            FROM imprest_voucher_retirements AS main_retirement_table
                            LEFT JOIN imprest_vouchers ON main_retirement_table.imprest_voucher_id = imprest_vouchers.id
                            WHERE imprest_vouchers.debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                            AND imprest_vouchers.currency_id = '.$currency_id.'
                            AND imprest_date <= "'.$date.'"                  
                    )
              
        ) AS actual_balance ';

        $query = $this->db->query($sql);
        return $this->opening_balance + $query->row()->actual_balance;
    }

    public function balance($currency_id,$date = null){
        $date = !is_null($date) ? $date : date('Y-m-d');
        $account_id = $this->{$this::DB_TABLE_PK};
        $account =  new self();
        $account->load($account_id);

        $junctioned_journals = 'SELECT DISTINCT journal_id FROM (
			SELECT journal_id
			FROM journal_contras
			UNION
			SELECT journal_id
			FROM journal_receipts
			UNION
			SELECT journal_id
			FROM journal_payment_vouchers
		) AS junctioned_journal';

        $sql = 'SELECT (
                    (
                        SELECT COALESCE(SUM(amount),0)
                        FROM journal_voucher_items
                        LEFT JOIN journal_vouchers ON journal_voucher_items.journal_voucher_id = journal_vouchers.journal_id
                        WHERE debit_account_id = '.$account_id.'
                        AND journal_id NOT IN ('.$junctioned_journals.') 
                        AND currency_id = '.$currency_id.'
                        AND transaction_date <= "'.$date.'"
                      
                    ) + (
                        SELECT COALESCE(SUM(amount),0) FROM contra_items
                        LEFT JOIN contras ON contra_items.contra_id = contras.contra_id
                        WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = '.$currency_id.'
                        AND contra_date <= "'.$date.'"                  
                    ) + (
                        SELECT COALESCE(SUM(amount),0)
                        FROM payment_voucher_items  
                        LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                        WHERE debit_account_id = '.$account_id.'
                        AND currency_id = '.$currency_id.'
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
                        AND imprest_vouchers.currency_id = '.$currency_id.'
                        AND imprest_vouchers.imprest_date <= "'.$date.'"
                    ) + (
                        SELECT COALESCE(SUM(
                                (
                                  (
                                  
                                    SELECT COALESCE(SUM(imprest_voucher_retired_services.quantity * imprest_voucher_retired_services.rate),0) AS retired_service_quantity
                                    FROM imprest_voucher_retired_services
                                    WHERE imprest_voucher_retired_services.imprest_voucher_retirement_id = main_retirement_table.id
                                    AND main_retirement_table.retirement_to = '.$this->{$this::DB_TABLE_PK}.'
                            
                                  ) + (
                            
                                    SELECT COALESCE(SUM(imprest_voucher_retired_cash.quantity * imprest_voucher_retired_cash.rate),0) AS retired_cash_quantity
                                    FROM imprest_voucher_retired_cash
                                    WHERE imprest_voucher_retired_cash.imprest_voucher_retirement_id = main_retirement_table.id
                                    AND main_retirement_table.retirement_to = '.$this->{$this::DB_TABLE_PK}.'
                            
                                  )
                                ) * (
                                    CASE 
                                        WHEN main_retirement_table.vat_inclusive = "VAT PRICED" OR main_retirement_table.vat_inclusive IS NULL  
                                           THEN 1 
                                           ELSE 1.18 
                                    END
                                )
                        ),0)
                        FROM imprest_voucher_retirements AS main_retirement_table
                        LEFT JOIN imprest_vouchers ON main_retirement_table.imprest_voucher_id = imprest_vouchers.id
                        WHERE main_retirement_table.retirement_to = '.$this->{$this::DB_TABLE_PK}.'
                        AND main_retirement_table.is_examined = 1
                        AND imprest_vouchers.currency_id = "'.$currency_id.'"
                        AND retirement_date <= "'.$date.'" 
                    
                    ) + (
                        SELECT COALESCE(SUM(amount),0) FROM receipt_items
                        LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                        WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = '.$currency_id.'
                        AND receipt_date <= "'.$date.'"
                    ) + (
                        SELECT COALESCE(SUM(withheld_amount ),0)
                        FROM withholding_taxes
                        LEFT JOIN payment_voucher_items ON withholding_taxes . payment_voucher_item_id = payment_voucher_items . payment_voucher_item_id
                        LEFT JOIN payment_vouchers ON payment_voucher_items . payment_voucher_id = payment_vouchers . payment_voucher_id
                        LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers . payment_voucher_id = sub_contract_certificate_payment_vouchers . payment_voucher_id
                        LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers . sub_contract_certificate_id = sub_contract_certificates . id
                        WHERE withholding_taxes.credit_account_id = '.$account_id.'
                        AND withholding_taxes.currency_id = '.$currency_id.'
                        AND date <= "'.$date.'"
                    ) + (
                        SELECT COALESCE(SUM(paid_amount),0)
                        FROM withholding_taxes_payments
                        LEFT JOIN withholding_taxes ON withholding_taxes_payments.withholding_tax_id = withholding_taxes.id
                        WHERE withholding_taxes.debit_account_id  = '.$account_id.'
                        AND withholding_taxes.currency_id = '.$currency_id.'
                        AND status = "PAID"
                        AND payment_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0)
                        FROM journal_voucher_credit_accounts
                        LEFT JOIN journal_vouchers ON journal_voucher_credit_accounts.journal_voucher_id = journal_vouchers.journal_id
                        WHERE account_id = '.$account_id.'
                        AND journal_id NOT IN ('.$junctioned_journals.') 
                        AND currency_id = '.$currency_id.'
                        AND transaction_date <= "'.$date.'"                      
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM contra_items
                        LEFT JOIN contras ON contra_items.contra_id = contras.contra_id
                        WHERE credit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND currency_id = "'.$currency_id.'"
                        AND contra_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0)
                        FROM payment_voucher_credit_accounts
                        LEFT JOIN payment_vouchers ON payment_voucher_credit_accounts.payment_voucher_id = payment_vouchers.payment_voucher_id
                        WHERE account_id = '.$account_id.'
                        AND currency_id = '.$currency_id.'
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
                        SELECT COALESCE(SUM(
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
                                ) * (
                                    CASE 
                                        WHEN main_retirement_table.vat_inclusive = "VAT PRICED" OR main_retirement_table.vat_inclusive IS NULL  
                                           THEN 1 
                                           ELSE 1.18 
                                    END
                                )
                                                
                        ),0)
                        FROM imprest_voucher_retirements AS main_retirement_table
                        LEFT JOIN imprest_vouchers ON main_retirement_table.imprest_voucher_id = imprest_vouchers.id
                        WHERE imprest_vouchers.debit_account_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND imprest_vouchers.currency_id = "'.$currency_id.'"
                        AND imprest_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM receipt_items
                        LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                        WHERE credit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = '.$currency_id.'
                        AND receipt_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(withheld_amount),0)
                        FROM withholding_taxes
                        LEFT JOIN payment_voucher_items ON withholding_taxes.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                        LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                        LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                        LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
                        WHERE withholding_taxes.debit_account_id = '.$account_id.'
                        AND withholding_taxes.currency_id = '.$currency_id.'
                        AND date <= "'.$date.'" 
                    )            
        ) AS actual_balance ';

        $query = $this->db->query($sql);
        $opening_balance = $this->currency_id == $currency_id ? $this->opening_balance : 0;
        return $opening_balance + $query->row()->actual_balance;
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

		$junctioned_journals = 'SELECT DISTINCT journal_id FROM (
			SELECT journal_id
			FROM journal_contras
			UNION
			SELECT journal_id
			FROM journal_receipts
			UNION
			SELECT journal_id
			FROM journal_payment_vouchers
		) AS junctioned_journal';

		$sql = '
                SELECT "JOURNAL" AS transaction_type, "CREDIT" AS transaction_action, journal_voucher_id AS transaction_id,
                transaction_date, narration AS reference, amount AS credit, 0 AS debit,created_at
                FROM journal_voucher_credit_accounts
                LEFT JOIN journal_vouchers ON journal_voucher_credit_accounts.journal_voucher_id = journal_vouchers.journal_id
                WHERE account_id = '.$account_id.'
                AND journal_id NOT IN ('.$junctioned_journals.') 
                AND currency_id = '.$currency_id.'
                AND transaction_date >= "'.$from.'"
                AND transaction_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "JOURNAL" AS transaction_type, "DEBIT" AS transaction_action, journal_voucher_id AS transaction_id,
                transaction_date, narration AS reference, 0 AS credit, amount AS debit,created_at
                FROM journal_voucher_items
                LEFT JOIN journal_vouchers ON journal_voucher_items.journal_voucher_id = journal_vouchers.journal_id
                WHERE debit_account_id = '.$account_id.'
                AND journal_id NOT IN ('.$junctioned_journals.') 
                AND currency_id = '.$currency_id.'
                AND transaction_date >= "'.$from.'"
                AND transaction_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "CONTRA" AS transaction_type, "CREDIT" AS transaction_action, contra_id AS transaction_id,
                 contra_date AS transaction_date,reference,(
                  SELECT COALESCE(SUM(amount),0) FROM contra_items
                  WHERE contra_id = contras.contra_id
                )  AS credit, 0 AS debit, datetime_posted AS created_at
                FROM contras
                WHERE credit_account_id = '.$account_id.'
                AND currency_id = '.$currency_id.'
                AND contra_date >= "'.$from.'"
                AND contra_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "CONTRA" AS transaction_type, "DEBIT" AS transaction_action, contras.contra_id AS transaction_id,
                 contras.contra_date AS transaction_date, reference, 0 AS credit, amount AS debit, datetime_posted AS created_at
                FROM contra_items
                LEFT JOIN contras ON contra_items.contra_id = contras.contra_id
                WHERE debit_account_id = '.$account_id.'
                AND currency_id = '.$currency_id.'
                AND contra_date >= "'.$from.'"
                AND contra_date <= "'.$to.'"
                
                UNION ALL

                SELECT "PAYMENT" AS transaction_type, "CREDIT" AS transaction_action, payment_vouchers.payment_voucher_id AS transaction_id,
                payment_date AS transaction_date,reference, amount AS credit, 0 AS debit, created_at
                FROM payment_voucher_credit_accounts
                LEFT JOIN payment_vouchers ON payment_voucher_credit_accounts.payment_voucher_id = payment_vouchers.payment_voucher_id
                WHERE account_id = '.$account_id.'
                AND currency_id = '.$currency_id.'
                AND payment_date >= "'.$from.'"
                AND payment_date <= "'.$to.'"
                
                UNION ALL
              
                SELECT "PAYMENT" AS transaction_type, "DEBIT" AS transaction_action, payment_vouchers.payment_voucher_id AS transaction_id, 
                payment_date AS transaction_date,reference, 0 AS credit, amount AS debit, created_at
                FROM payment_voucher_items  
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                WHERE debit_account_id = '.$account_id.'
                AND currency_id = '.$currency_id.'
                AND payment_date >= "'.$from.'"
                AND payment_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "IMPREST" AS transaction_type, "CREDIT" AS transaction_action, imprest_vouchers.id AS transaction_id, imprest_vouchers.imprest_date AS transaction_date, " " AS reference,
                       (
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
                           ) * (
                                CASE 
                                    WHEN imprest_vouchers.vat_inclusive = "VAT PRICED" OR imprest_vouchers.vat_inclusive IS NULL  
                                       THEN 1 
                                       ELSE 1.18 
                                END
                           )
                        ) AS credit,0 AS debit, imprest_vouchers.created_at 
                FROM imprest_vouchers
                WHERE imprest_vouchers.credit_account_id = '.$account_id.'
                AND imprest_vouchers.currency_id = '.$currency_id.'
                AND imprest_date >= "'.$from.'"
                AND imprest_date <= "'.$to.'" 
                
                UNION ALL
                               
                SELECT "IMPREST" AS transaction_type, "DEBIT" AS transaction_action, imprest_vouchers.id AS transaction_id, imprest_vouchers.imprest_date AS transaction_date, " " AS reference,0 AS credit,
                   (
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
                       ) * (
                            CASE 
                                WHEN imprest_vouchers.vat_inclusive = "VAT PRICED" OR imprest_vouchers.vat_inclusive IS NULL  
                                   THEN 1 
                                   ELSE 1.18 
                            END
                       )
                   ) AS debit, imprest_vouchers.created_at 
                FROM imprest_vouchers
                WHERE  imprest_vouchers.debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                AND imprest_vouchers.currency_id = '.$currency_id.'
                AND imprest_date >= "'.$from.'"
                AND imprest_date <= "'.$to.'" 
                 
                UNION ALL
                
                SELECT * FROM (
                  SELECT "RETIREMENT" AS transaction_type, "CREDIT" AS transaction_action, main_retirement_table.id AS transaction_id, retirement_date, " " AS reference,
                    (
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
                        ) * (
                            CASE 
                                WHEN main_retirement_table.vat_inclusive = "VAT PRICED" OR main_retirement_table.vat_inclusive IS NULL  
                                   THEN 1 
                                   ELSE 1.18 
                            END
                        )
                    ) AS credit, 0 AS debit, main_retirement_table.created_at 
                    FROM imprest_voucher_retirements AS main_retirement_table
                    LEFT JOIN imprest_vouchers ON main_retirement_table.imprest_voucher_id = imprest_vouchers.id
                    WHERE imprest_vouchers.debit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND main_retirement_table.is_examined = 1
                    AND imprest_vouchers.currency_id = '.$currency_id.'
                    AND imprest_date >= "'.$from.'"
                    AND imprest_date <= "'.$to.'" 
                ) AS artificial_table GROUP BY transaction_id 
                 
                UNION ALL
                
                SELECT * FROM (
                  SELECT "RETIREMENT" AS transaction_type, "DEBIT" AS transaction_action, main_retirement_table.id AS transaction_id, retirement_date, " " AS reference,0 AS credit,
                    (
                        (
                          (
                          
                            SELECT COALESCE(SUM(imprest_voucher_retired_services.quantity * imprest_voucher_retired_services.rate),0) AS retired_service_quantity
                            FROM imprest_voucher_retired_services
                            WHERE imprest_voucher_retired_services.imprest_voucher_retirement_id = main_retirement_table.id
                            AND main_retirement_table.retirement_to = "'.$this->{$this::DB_TABLE_PK}.'"
                    
                          ) + (
                    
                            SELECT COALESCE(SUM(imprest_voucher_retired_cash.quantity * imprest_voucher_retired_cash.rate),0) AS retired_cash_quantity
                            FROM imprest_voucher_retired_cash
                            WHERE imprest_voucher_retired_cash.imprest_voucher_retirement_id = main_retirement_table.id
                            AND main_retirement_table.retirement_to = "'.$this->{$this::DB_TABLE_PK}.'"
                    
                          )
                        ) * (
                            CASE 
                                WHEN main_retirement_table.vat_inclusive = "VAT PRICED" OR main_retirement_table.vat_inclusive IS NULL  
                                   THEN 1 
                                   ELSE 1.18 
                            END
                        )
                    ) AS debit, main_retirement_table.created_at 
                    FROM imprest_voucher_retirements AS main_retirement_table
                    LEFT JOIN imprest_vouchers ON main_retirement_table.imprest_voucher_id = imprest_vouchers.id
                    WHERE main_retirement_table.retirement_to = '.$this->{$this::DB_TABLE_PK}.'
                    AND main_retirement_table.is_examined = 1
                    AND imprest_vouchers.currency_id = '.$currency_id.'
                    AND retirement_date >= "'.$from.'"
                    AND retirement_date <= "'.$to.'" 
                ) AS artificial_table GROUP BY transaction_id 
                

                UNION ALL
                
                SELECT "RECEIPT" AS transaction_type, "CREDIT" AS transaction_action, id AS transaction_id,
                receipt_date AS transaction_date,reference,(
                SELECT COALESCE(SUM(amount),0) FROM receipt_items
                WHERE receipt_items.receipt_id = receipts.id
                ) AS credit, 0 AS debit, receipts.created_at
                FROM receipts
                WHERE credit_account_id = '.$account_id.'
                AND currency_id = '.$currency_id.'
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
                WHERE debit_account_id = '.$account_id.'
                AND currency_id = '.$currency_id.'
                AND receipt_date >= "'.$from.'"
                AND receipt_date <= "'.$to.'" 
             
                UNION ALL
                    
                SELECT "WITHHOLDING TAX" AS transaction_type,  "DEBIT" AS transaction_action, withholding_taxes . id AS transaction_id, date AS transaction_date, sub_contract_certificates . certificate_number AS reference,
                0 AS credit, withheld_amount AS debit, withholding_taxes . created_at 
                FROM withholding_taxes
                LEFT JOIN payment_voucher_items ON withholding_taxes . payment_voucher_item_id = payment_voucher_items . payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items . payment_voucher_id = payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers . payment_voucher_id = sub_contract_certificate_payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers . sub_contract_certificate_id = sub_contract_certificates . id
                WHERE withholding_taxes.credit_account_id = '.$account_id.'
                AND withholding_taxes.currency_id = '.$currency_id.'
                AND date >= "'.$from.'"
                AND date <= "'.$to.'"
                
                
                UNION ALL
                
                
                SELECT "WITHHOLDING TAX" AS transaction_type,  "CREDIT" AS transaction_action, withholding_taxes.id AS transaction_id, date AS transaction_date, sub_contract_certificates.certificate_number AS reference,
                withheld_amount AS credit,0 AS debit, withholding_taxes.created_at
                FROM withholding_taxes
                LEFT JOIN payment_voucher_items ON withholding_taxes.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
                WHERE withholding_taxes.debit_account_id = '.$account_id.'
                AND withholding_taxes.currency_id = '.$currency_id.'
                AND date >= "'.$from.'"
                AND date <= "'.$to.'" 
                
                UNION ALL
                
                SELECT "WITHHELD TAX PAYMENT" AS transaction_type,  "DEBIT" AS transaction_action, withholding_taxes_payments.id AS transaction_id, payment_date AS transaction_date, "" AS reference,
                0 AS credit, paid_amount AS debit, paid_at AS created_at
                FROM withholding_taxes_payments
                LEFT JOIN withholding_taxes ON withholding_taxes_payments.withholding_tax_id = withholding_taxes.id
                WHERE withholding_taxes.debit_account_id  = '.$account_id.'
                AND withholding_taxes.currency_id = '.$currency_id.'
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
			'outgoing_invoice',
			'journal_voucher'

		]);
		$transactions = [];
		foreach ($results as $row){
			if($row->transaction_type == 'CONTRA'){
				$transaction = new Contra();
				$transaction->load($row->transaction_id);
				$reference = anchor(base_url('finance/preview_contra/'.$row->transaction_id),$transaction->detailed_reference(),'target="_blank"');
				$detailed_reference = $transaction->detailed_reference();
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == 'PAYMENT'){
				$transaction = new Payment_voucher();
				$transaction->load($row->transaction_id);
				$reference = anchor(base_url('finance/preview_payment_voucher/'.$row->transaction_id),$transaction->detailed_reference(),'target="_blank"');
                $detailed_reference = $transaction->detailed_reference();
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == 'IMPREST'){
				$transaction = new Imprest_voucher();
				$transaction->load($row->transaction_id);
				$requisition = $transaction->requisition();
				$reference = anchor(base_url('finance/preview_imprest_voucher/'.$transaction->{$transaction::DB_TABLE_PK}), $transaction->detailed_reference(),'target="_blank"');
                $detailed_reference = $transaction->detailed_reference();
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == 'RETIREMENT'){
				$transaction = new Imprest_voucher_retirement();
				$transaction->load($row->transaction_id);
				$requisition = $transaction->imprest_voucher()->requisition();
				$reference = $requisition->requisition_number().' - '.$transaction->imprest_voucher_retirement_number();
                $detailed_reference = $reference;
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == "WITHHOLDING TAX"){
				$transaction = new Withholding_tax();
				$transaction->load($row->transaction_id);
				$reference = $row->reference;
                $detailed_reference = $reference;
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == "WITHHELD TAX PAYMENT"){
				$transaction = new Withholding_taxes_payment();
				$transaction->load($row->transaction_id);
				$reference = $transaction->withholding_tax()->payment_voucher_item()->payment_voucher()->sub_contract_payment_requisition_approval_payment_voucher()->sub_contract_payment_requisition_approval()->sub_contract_requisition()->sub_contract_requisition_number();
                $detailed_reference = $reference;
				$remarks = "PAID";
			} else if($row->transaction_type == "JOURNAL") {
				$transaction = new Journal_voucher();
				$transaction->load($row->transaction_id);
				$reference = anchor(base_url('finance/preview_journal_voucher/'.$row->transaction_id),$transaction->reference,'target="_blank"');				$remarks = $row->reference;
                $detailed_reference = $row->reference;
                $remarks = $row->reference;

			} else {
				$transaction = new Receipt();
				$transaction->load($row->transaction_id);
				$reference = anchor(base_url('finance/preview_receipt/'.$row->transaction_id),$transaction->detailed_reference(),'target="_blank"');
                $detailed_reference = $transaction->detailed_reference();
				$remarks = $transaction->remarks;
			}

			$transaction->load($row->transaction_id);
			$transactions[] = [
				'transaction_id' => $row->transaction_id,
				'transaction_date' => $row->transaction_date,
				'transaction_type' => $row->transaction_type,
				'created_at' => $row->created_at,
				'remarks'=> $remarks,
				'reference' => $reference,
				'detailed_reference' => $detailed_reference,
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
        $sql = 'SELECT CONCAT("real_",account_id) AS account, account_name FROM accounts';
        if(!empty($group_natures)){
            $group_names = '';
            foreach ($group_natures as $group_name){
                $group_names .= '"'.$group_name.'", ';
            }
            $group_names = rtrim($group_names,', ');

           $sql .= ' WHERE account_group_id IN (
                     SELECT account_groups.account_group_id FROM account_groups
                     WHERE account_groups.group_name IN ('.$group_names.')) ';
        }
        if(empty($group_natures)) {
			$sql .= '
					UNION
					SELECT CONCAT("stakeholder_",stakeholder_id) AS account, stakeholder_name AS account_name FROM stakeholders
					';
		}
        $query = $this->db->query($sql);
        $accounts = $query->result();
        $options[''] = '&nbsp;';
        foreach ($accounts as $account){
            $options[$account->account] = $account->account_name;
        }
        return $options;
    }

    public function expense_account_costs($cost_center_id = null,$from = null, $to = null){
        $sql = 'SELECT payment_date,payment_voucher_items.payment_voucher_id, symbol, COALESCE(SUM(amount),0) AS amount, COALESCE(SUM(amount * exchange_rate),0) AS amount_in_basecurrency
                FROM cost_center_payment_voucher_items
                LEFT JOIN payment_voucher_items ON cost_center_payment_voucher_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id
                WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($cost_center_id)){
           $sql .= ' AND cost_center_id ='.$cost_center_id.'';
        }if(!is_null($from)){
           $sql .= ' AND payment_date >= "'.$from.'" ';
        }if(!is_null($to)){
           $sql .= ' AND payment_date <= "'.$to.'" ';
        }
        $sql .= ' GROUP BY payment_voucher_id ORDER BY payment_date DESC';

        $query = $this->db->query($sql);
        return $query->num_rows()>0 ? $query->result() : false;

    }

    public function cost_center_expenses($cost_center_id = null,$from = null, $to = null){
        $sql = 'SELECT * FROM(
                SELECT payment_date,payment_voucher_items.payment_voucher_id, symbol, COALESCE(SUM(amount),0) AS amount, COALESCE(SUM(amount * exchange_rate),0) AS amount_in_basecurrency
                FROM payment_voucher_items
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN payment_voucher_item_approved_invoice_items ON payment_voucher_items.payment_voucher_item_id = payment_voucher_item_approved_invoice_items.payment_voucher_item_id
                LEFT JOIN purchase_order_payment_request_approval_invoice_items ON payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id = purchase_order_payment_request_approval_invoice_items.id
                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                LEFT JOIN accounts ON payment_voucher_items.debit_account_id = accounts.account_id
                LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id
                LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
                WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'';
                if(!is_null($cost_center_id)){
                    $sql .= ' AND cost_center_id ='.$cost_center_id.'';
                }if(!is_null($from)){
                    $sql .= ' AND payment_date >= "'.$from.'" ';
                }if(!is_null($to)){
                    $sql .= ' AND payment_date <= "'.$to.'" ';
                }
         $sql .= '  GROUP BY payment_voucher_id 
         
         
                   UNION


                SELECT payment_date,payment_voucher_items.payment_voucher_id, symbol, COALESCE(SUM(amount),0) AS amount, COALESCE(SUM(amount * exchange_rate),0) AS amount_in_basecurrency
                FROM cost_center_payment_voucher_items
                LEFT JOIN payment_voucher_items ON cost_center_payment_voucher_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id
                WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'';
                if(!is_null($cost_center_id)){
                    $sql .= ' AND cost_center_id ='.$cost_center_id.'';
                }if(!is_null($from)){
                    $sql .= ' AND payment_date >= "'.$from.'" ';
                }if(!is_null($to)){
                    $sql .= ' AND payment_date <= "'.$to.'" ';
                }
        $sql .= '  GROUP BY payment_voucher_id
                ) AS cost_center_expenses WHERE amount > 0 ORDER BY payment_date DESC';

        $query = $this->db->query($sql);
        return $query->num_rows()>0 ? $query->result() : false;

    }

    public function has_account_details($bank_id = null){
        $sql = 'SELECT id FROM bank_accounts WHERE account_id = '.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($bank_id)){
            $sql .= ' AND bank_id = '.$bank_id;
        }
        $sql .= ' LIMIT 1';
        $query = $this->db->query($sql);
        return $query->num_rows() > 0 ? $query->row()->id : false;
    }

    public function bank_account(){
        $this->load->model('bank_account');
        $bank_accounts = $this->bank_account->get(0,0,['account_id'=>$this->{$this::DB_TABLE_PK}]);
        return !empty($bank_accounts) ? array_shift($bank_accounts) : false;
    }

	public function delete_button(){
		if(check_privilege('Finance Settings') && !$this->has_transactions()) {
			$delete_button = '<button type="button" title="Delete Account" class="btn btn-xs btn-danger delete_account" account_id="'.$this->{$this::DB_TABLE_PK}.'" ><i class="fa fa-trash-o"></i></button>';
		} else {
			$delete_button = '';
		}
		return $delete_button;
	}

	public function edit_button(){
		if(check_privilege('Finance Settings')) {
		    $account_id = $this->{$this::DB_TABLE_PK};
		    $data = [
		        'account' => $this,
                'account_details' => $this->bank_account()
            ];
			$edit_button = '
		                <button data-toggle="modal" data-target="#edit_account_'.$account_id.'" type="button" title="Edit Account" class="btn btn-xs btn-default edit_account" account_id="'.$account_id.'" >
		                    <i class="fa fa-edit"></i>
                        </button>
                        <div id="edit_account_'.$account_id.'" class="modal fade" role="dialog">
                        '.$this->load->view('finance/account_form',
                                $data,
                                true
                            ).'
                        </div>
		                ';

		} else {
			$edit_button = '';
		}
		return $edit_button;
	}

	public function has_transactions(){
        $sql = 'SELECT CONCAT("jv_credit_item_",id) FROM journal_voucher_credit_accounts WHERE account_id = '.$this->{$this::DB_TABLE_PK}.'
                UNION
                SELECT CONCAT("jv_debit_item",item_id) FROM journal_voucher_items WHERE debit_account_id = '.$this->{$this::DB_TABLE_PK}.'';
        $results_count = $this->db->query($sql)->num_rows();
        return $results_count > 0 ? true : false;
    }

}
