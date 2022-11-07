<?php
class Stakeholder extends MY_Model{
	const DB_TABLE = 'stakeholders';
	const DB_TABLE_PK = 'stakeholder_id';

	public $stakeholder_name;
	public $phone;
	public $alternative_phone;
	public $email;
	public $address;
	public $created_by;

	public function dropdown_options($with_us = false)
	{
		$options[''] = $with_us ? get_company_details()->company_name : '&nbsp;';
		$stakeholders = $this->get(0,0,['active' => '1'],'stakeholder_name');
		foreach($stakeholders as $stakeholder){
			$options[$stakeholder->{$this::DB_TABLE_PK}] = $stakeholder->stakeholder_name;
		}
		return $options;
	}

	/**ToDo**/
	public function account(){

	}

	public function account_for(){

	}

	public function stakeholders_list($limit, $start, $keyword, $order){
		//order string
		$order_string = dataTable_order_string(['stakeholder_name','phone','alternative_phone','email','address'],$order,'stakeholder_name');

		$where = '';
		if($keyword != ''){
			$where .= 'stakeholder_name LIKE "%'.$keyword.'%" OR phone LIKE "%'.$keyword.'%" OR alternative_phone LIKE "%'.$keyword.'%"  OR email LIKE "%'.$keyword.'%"  OR address LIKE "%'.$keyword.'%" ';
		}

		$stakeholders = $this->get($limit,$start,$where,$order_string);
		$rows = [];
		foreach($stakeholders as $stakeholder){
			$rows[] = [
				anchor(base_url('stakeholders/stakeholder_profile/'.$stakeholder->{$stakeholder::DB_TABLE_PK}),$stakeholder->stakeholder_name),
				$stakeholder->phone,
				$stakeholder->alternative_phone,
				$stakeholder->email,
				$stakeholder->address
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

	public function supplied_items($from = null, $to = null)
	{
		$sql = 'SELECT goods_received_note_material_stock_items.item_id FROM goods_received_note_material_stock_items
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                WHERE stakeholder_id = '.$this->{$this::DB_TABLE_PK};
		if(!is_null($from)){
			$sql .= ' AND issue_date >= "'.$from.'" ';
		}

		if(!is_null($to)){
			$sql .= ' AND issue_date <= "'.$to.'" ';
		}
		$sql .= ' ORDER BY date_received ASC';
		$query = $this->db->query($sql);

		$supplied_items = [];
		$rows = $query->result();

		$this->load->model('goods_received_note_material_stock_item');
		foreach ($rows as $row){
			$grn_item = new Goods_received_note_material_stock_item();
			$grn_item->load($row->item_id);
			$material_stock = $grn_item->stock_item();
			$material_item = $material_stock->material_item();
			$order_item = $grn_item->order_material_item();
			if(!(isset($last_order_id) && $last_order_id == $order_item->order_id)){
				$purchase_order = $order_item->purchase_order();
				$currency = $purchase_order->currency()->symbol;
			}

			$last_order_id = $order_item->order_id;

			$supplied_items[] = [
				'item_name' => $material_item->item_name,
				'unit' => $material_item->unit()->symbol,
				'quantity' => $material_stock->quantity,
				'price' => $order_item->price,
				'receiving_price' => $grn_item->receiving_price(),
				'order_number' => $purchase_order->order_number(),
				'date_delivered' => custom_standard_date($material_stock->date_received),
				'currency' => $currency,
				'cost_center_name' => $purchase_order->cost_center_name()
			];
		}

		return $supplied_items;
	}

	public function invoices($from = null,$to = null,$status = ['unpaid','approved'])
	{
		$this->load->model('invoice');
		$sql = 'SELECT invoice_id FROM stakeholder_invoices
                LEFT JOIN invoices ON stakeholder_invoices.invoice_id = invoices.id
                WHERE stakeholder_id = '.$this->{$this::DB_TABLE_PK};

		if(in_array("unpaid", $status)){
			$sql .= ' AND 
                (amount - (
                  SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                  LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                  LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                  WHERE stakeholder_invoices.invoice_id = invoice_payment_vouchers.invoice_id
                ) > 0 )
            ';
		}

		if(in_array("approved", $status)){
			$sql .= ' AND invoice_id IN(
                  SELECT invoice_id FROM purchase_order_payment_request_invoice_items
                  LEFT JOIN purchase_order_payment_request_approval_invoice_items ON purchase_order_payment_request_invoice_items.id = purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id
                )
            ';
		}

		if(!is_null($from)){
			$sql .= ' AND invoice_date >= '.$from;
		}

		if(!is_null($to)){
			$sql .= ' AND invoice_date <= '.$to;
		}

		$query = $this->db->query($sql);
		$rows = $query->result();
		$invoices = [];
		foreach ($rows as $row){
			$invoice = new Invoice();
			$invoice->load($row->invoice_id);
			$invoices[] = $invoice;
		}
		return $invoices;
	}

	public function balance_scraped($currency_id,$date = null){
		$date = !is_null($date) ? $date : date('Y-m-d');
		if($currency_id == 1){
			$project_certificate_sql2 =
				'  SELECT COALESCE(SUM(certified_amount),0) FROM sub_contract_certificates
                    LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id =  sub_contracts.id
                    WHERE stakeholder_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND certificate_date <= "'.$date.'"
              ';
		} else {
			$project_certificate_sql2 = 0;
		}

		$sql = 'SELECT (
                     (
                        SELECT COALESCE(SUM(amount),0) FROM journal_voucher_items
                        LEFT JOIN journal_vouchers ON journal_voucher_items.journal_voucher_id = journal_vouchers.journal_id
                        WHERE stakeholder_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND currency_id = "'.$currency_id.'"
                        AND transaction_date <= "'.$date.'"
                     ) + (
						SELECT COALESCE(SUM(
						   (
							   SELECT COALESCE(SUM(quantity * rate),0) FROM outgoing_invoice_items
							   WHERE outgoing_invoice_items.outgoing_invoice_id = outgoing_invoices.id
						   ) * (
							   CASE
								   WHEN outgoing_invoices.vat_inclusive = 1
									   THEN 1.18
								   ELSE 1
								   END
						   )
						),0)
						FROM outgoing_invoices
						LEFT JOIN stakeholders ON outgoing_invoices.invoice_to = stakeholders.stakeholder_id
						WHERE outgoing_invoices.invoice_to = "'.$this->{$this::DB_TABLE_PK}.'"
						AND currency_id = "'.$currency_id.'"
						AND invoice_date <= "'.$date.'"
                    ) + (
						SELECT COALESCE(SUM(withheld_amount),0)
						FROM withholding_taxes
						WHERE withholding_taxes.stakeholder_id = "'.$this->{$this::DB_TABLE_PK}.'"
						AND date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM journal_voucher_credit_accounts
                        LEFT JOIN journal_vouchers ON journal_voucher_credit_accounts.journal_voucher_id = journal_vouchers.journal_id
                        WHERE stakeholder_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND currency_id = "'.$currency_id.'"
                        AND transaction_date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM invoices
                        LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                        WHERE stakeholder_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = "'.$currency_id.'"
                        AND invoice_date <= "'.$date.'"
                    ) - (
                       '.$project_certificate_sql2.'
                    )
              
        ) AS actual_balance ';

		$query = $this->db->query($sql);
		return $query->row()->actual_balance;
	}

    public function balance($currency_id,$date = null){
        $date = !is_null($date) ? $date : date('Y-m-d');
        $account_id = $this->{$this::DB_TABLE_PK};
        $account =  new self();
        $account->load($account_id);

        if($currency_id == 1){
            $project_certificate_sql2 =
                '  SELECT COALESCE(SUM(certified_amount),0) FROM sub_contract_certificates
                    LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id =  sub_contracts.id
                    WHERE stakeholder_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND certificate_date <= "'.$date.'"
              ';
        } else {
            $project_certificate_sql2 = 0;
        }

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
                        WHERE stakeholder_id = '.$account_id.'
                        AND journal_id NOT IN ('.$junctioned_journals.') 
                        AND currency_id = '.$currency_id.'
                        AND transaction_date <= "'.$date.'"
                      
                    ) + (
						SELECT COALESCE(SUM(
						   (
							   SELECT COALESCE(SUM(quantity * rate),0) FROM outgoing_invoice_items
							   WHERE outgoing_invoice_items.outgoing_invoice_id = outgoing_invoices.id
						   ) * (
							   CASE
								   WHEN outgoing_invoices.vat_inclusive = 1
									   THEN 1.18
								   ELSE 1
								   END
						   )
						),0)
						FROM outgoing_invoices
						LEFT JOIN stakeholders ON outgoing_invoices.invoice_to = stakeholders.stakeholder_id
						WHERE outgoing_invoices.invoice_to = "'.$this->{$this::DB_TABLE_PK}.'"
						AND currency_id = "'.$currency_id.'"
						AND invoice_date <= "'.$date.'"
                    ) + (
                        SELECT COALESCE(SUM(amount),0)
                        FROM payment_voucher_items  
                        LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                        WHERE stakeholder_id = '.$account_id.'
                        AND currency_id = '.$currency_id.'
                        AND payment_date <= "'.$date.'"                    
                    ) + (
						SELECT COALESCE(SUM(withheld_amount),0)
						FROM withholding_taxes
						WHERE withholding_taxes.stakeholder_id = "'.$this->{$this::DB_TABLE_PK}.'"
                        AND currency_id = '.$currency_id.'
						AND date <= "'.$date.'"
                    ) - (
                        SELECT COALESCE(SUM(amount),0)
                        FROM journal_voucher_credit_accounts
                        LEFT JOIN journal_vouchers ON journal_voucher_credit_accounts.journal_voucher_id = journal_vouchers.journal_id
                        WHERE stakeholder_id = '.$account_id.'
                        AND journal_id NOT IN ('.$junctioned_journals.') 
                        AND currency_id = '.$currency_id.'
                        AND transaction_date <= "'.$date.'"                      
                    ) - (
                        SELECT COALESCE(SUM(amount),0)
                        FROM payment_voucher_credit_accounts
                        LEFT JOIN payment_vouchers ON payment_voucher_credit_accounts.payment_voucher_id = payment_vouchers.payment_voucher_id
                        WHERE stakeholder_id = '.$account_id.'
                        AND currency_id = '.$currency_id.'
                        AND payment_date <= "'.$date.'"                        
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM receipt_items
                        LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                        WHERE credit_account_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = '.$currency_id.'
                        AND receipt_date <= "'.$date.'"
                    ) - (
                       '.$project_certificate_sql2.'
                    ) - (
                        SELECT COALESCE(SUM(amount),0) FROM invoices
                        LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                        WHERE stakeholder_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND currency_id = "'.$currency_id.'"
                        AND invoice_date <= "'.$date.'"
                    )            
        ) AS actual_balance ';

        $query = $this->db->query($sql);
        return $query->row()->actual_balance;
    }

	public function statement($currency_id,$from,$to){
		$account_id = $this->{$this::DB_TABLE_PK};
		$account =  new self();
		$account->load($account_id);
		if($currency_id == 1){
			$project_certificate_sql =
				'  UNION ALL 
				 
  				   SELECT "SUB CONTRACT CERTIFICATE" AS transaction_type,  "CREDIT" AS transaction_action, sub_contract_certificates.id AS transaction_id, certificate_date AS transaction_date, certificate_number AS reference,
				   certified_amount AS credit,0 AS debit, sub_contract_certificates.created_at, "" AS invoice_type
				   FROM sub_contract_certificates
				   LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id =  sub_contracts.id
				   LEFT JOIN stakeholders ON sub_contracts.stakeholder_id = stakeholders.stakeholder_id
				   WHERE sub_contracts.stakeholder_id = "'.$account_id.'"
				   AND certificate_date >= "'.$from.'"
				   AND certificate_date <= "'.$to.'"
              ';
			$withholding_tax_sql = '
					';

		} else {
			$project_certificate_sql = '';
		}

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
                transaction_date, narration AS reference, amount AS credit, 0 AS debit,created_at, "" AS invoice_type
                FROM journal_voucher_credit_accounts
                LEFT JOIN journal_vouchers ON journal_voucher_credit_accounts.journal_voucher_id = journal_vouchers.journal_id
                WHERE stakeholder_id = "'.$account_id.'"
                AND journal_id NOT IN ('.$junctioned_journals.') 
                AND currency_id = "'.$currency_id.'"
                AND transaction_date >= "'.$from.'"
                AND transaction_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "JOURNAL" AS transaction_type, "DEBIT" AS transaction_action, journal_voucher_id AS transaction_id,
                transaction_date, narration AS reference, 0 AS credit, amount AS debit,created_at, "" AS invoice_type
                FROM journal_voucher_items
                LEFT JOIN journal_vouchers ON journal_voucher_items.journal_voucher_id = journal_vouchers.journal_id
                WHERE stakeholder_id = "'.$account_id.'"
                AND journal_id NOT IN ('.$junctioned_journals.') 
                AND currency_id = "'.$currency_id.'"
                AND transaction_date >= "'.$from.'"
                AND transaction_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "CONTRA" AS transaction_type, "CREDIT" AS transaction_action, contra_id AS transaction_id,
                 contra_date AS transaction_date,reference,(
                  SELECT COALESCE(SUM(amount),0) FROM contra_items
                  WHERE contra_id = contras.contra_id
                )  AS credit, 0 AS debit, datetime_posted AS created_at, "" AS invoice_type
                FROM contras
                WHERE contras.stakeholder_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND contra_date >= "'.$from.'"
                AND contra_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "CONTRA" AS transaction_type, "DEBIT" AS transaction_action, contras.contra_id AS transaction_id,
                 contras.contra_date AS transaction_date, reference, 0 AS credit, amount AS debit, datetime_posted AS created_at, "" AS invoice_type
                FROM contra_items
                LEFT JOIN contras ON contra_items.contra_id = contras.contra_id
                WHERE contra_items.stakeholder_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND contra_date >= "'.$from.'"
                AND contra_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "PAYMENT" AS transaction_type, "DEBIT" AS transaction_action, payment_vouchers.payment_voucher_id AS transaction_id, 
                payment_date AS transaction_date,reference, 0 AS credit, amount AS debit, created_at, "" AS invoice_type
                FROM payment_voucher_items  
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                WHERE payment_voucher_items.stakeholder_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND payment_date >= "'.$from.'"
                AND payment_date <= "'.$to.'"
                
                UNION ALL
                              
                SELECT "INVOICE" AS transaction_type,  "CREDIT" AS transaction_action, invoices.id AS transaction_id, invoice_date AS transaction_date,reference,
                amount AS credit,0 AS debit, invoices.created_at, "INCOMING" AS invoice_type
                FROM invoices
                LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                LEFT JOIN stakeholders ON stakeholder_invoices.stakeholder_id = stakeholders.stakeholder_id
                WHERE stakeholder_invoices.stakeholder_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND invoice_date >= "'.$from.'"
                AND invoice_date <= "'.$to.'"
                
                '.$project_certificate_sql.' 

                UNION ALL
                              
                SELECT "INVOICE" AS transaction_type, "DEBIT" AS transaction_action, outgoing_invoices.id AS transaction_id, invoice_date AS transaction_date,reference,
                0 AS credit,
                (
                    (
                        SELECT COALESCE(SUM(quantity * rate),0) FROM outgoing_invoice_items
                        WHERE outgoing_invoice_items.outgoing_invoice_id = outgoing_invoices.id
                    ) * (
                        CASE 
                          WHEN outgoing_invoices.vat_inclusive = 1
                          THEN 1.18
                          ELSE 1
                        END
                    )
                 )AS debit, outgoing_invoices.created_at, "OUTGOING" AS invoice_type
                FROM outgoing_invoices
                LEFT JOIN stakeholders ON outgoing_invoices.invoice_to = stakeholders.stakeholder_id
                WHERE outgoing_invoices.invoice_to = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND invoice_date >= "'.$from.'"
                AND invoice_date <= "'.$to.'"
                 
                UNION ALL
                
                SELECT "RECEIPT" AS transaction_type, "CREDIT" AS transaction_action, id AS transaction_id,
                receipt_date AS transaction_date,reference,(
                SELECT COALESCE(SUM(amount),0) FROM receipt_items
                WHERE receipt_items.receipt_id = receipts.id
                ) AS credit, 0 AS debit, receipts.created_at, "" AS invoice_type
                FROM receipts
                WHERE credit_account_id = "'.$account_id.'"
                AND currency_id = "'.$currency_id.'"
                AND receipt_date >= "'.$from.'"
                AND receipt_date <= "'.$to.'"
                
                UNION ALL
                    
                SELECT "WITHHOLDING TAX" AS transaction_type,  "DEBIT" AS transaction_action, withholding_taxes . id AS transaction_id, date AS transaction_date, sub_contract_certificates . certificate_number AS reference,
                0 AS credit, withheld_amount AS debit, withholding_taxes . created_at, "" AS invoice_type 
                FROM withholding_taxes
                LEFT JOIN payment_voucher_items ON withholding_taxes . payment_voucher_item_id = payment_voucher_items . payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items . payment_voucher_id = payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers . payment_voucher_id = sub_contract_certificate_payment_vouchers . payment_voucher_id
                LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers . sub_contract_certificate_id = sub_contract_certificates . id
                WHERE withholding_taxes.stakeholder_id = "'.$account_id.'"
                AND date >= "'.$from.'"
                AND date <= "'.$to.'"
                
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
				$reference = anchor(base_url('finance/preview_contra/'.$row->transaction_id),$row->reference,'target="_blank"');
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == 'PAYMENT'){
				$transaction = new Payment_voucher();
				$transaction->load($row->transaction_id);
				$reference = anchor(base_url('finance/preview_payment_voucher/'.$row->transaction_id),$transaction->detailed_reference(),'target="_blank"');
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == 'INVOICE'){
					$invoice_type = $row->invoice_type;
					switch ($invoice_type){
						case 'INCOMING':
							$transaction = new Invoice();
							$transaction->load($row->transaction_id);
							$reference = anchor(base_url('finance/preview_invoice/'.$row->transaction_id.'/purchases'),$transaction->detailed_reference(),'target="_blank"');
							$remarks = $transaction->description;
							break;
						case 'OUTGOING':
							$transaction = new Outgoing_invoice();
							$transaction->load($row->transaction_id);
							$reference = anchor(base_url('finance/preview_invoice/'.$row->transaction_id.'/sales'),$transaction->detailed_reference(),'target="_blank"');
							$remarks = 'Bill to '.$transaction->invoice_to()->stakeholder_name.' for '.$transaction->detailed_reference();
							break;
					}
			} else if($row->transaction_type == 'SUB CONTRACT CERTIFICATE'){
				$transaction = new Sub_contract_certificate();
				$transaction->load($row->transaction_id);
				$reference = $row->reference;
				$remarks = 'payment for sub contract certificate No. '.$transaction->certificate_number.' of '.$transaction->sub_contract()->contract_name.' - '.$transaction->sub_contract()->stakeholder()->stakeholder_name;
			} else if($row->transaction_type == "WITHHOLDING TAX"){
				$transaction = new Withholding_tax();
				$transaction->load($row->transaction_id);
				$reference = $row->reference;
				$remarks = $transaction->remarks;
			} else if($row->transaction_type == "JOURNAL") {
				$transaction = new Journal_voucher();
				$transaction->load($row->transaction_id);
				$reference = anchor(base_url('finance/preview_journal_voucher/'.$row->transaction_id),$transaction->reference,'target="_blank"');
				$remarks = $row->reference;
			} else {
				$transaction = new Receipt();
				$transaction->load($row->transaction_id);
				$reference = anchor(base_url('finance/preview_receipt/'.$row->transaction_id),$transaction->detailed_reference(),'target="_blank"');
				$remarks = $transaction->remarks;
			}

			$transaction->load($row->transaction_id);
			$transactions[] = [
				'created_at' => $row->created_at,
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

	public function balance_in_base_currency($as_of){
		return $this->account()->balance_in_base_currency($as_of);
	}

	public function statement_transactions($currency_id = 1,$from = '',$to = ''){
		$sql = 'SELECT "invoice" AS transaction_type,  "debit" AS transaction_action, invoice_date AS transaction_date, reference, amount*exchange_rate AS debit_amount,0 AS credit_amount
                FROM invoices
                LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                LEFT JOIN grn_invoices ON invoices.id = grn_invoices.invoice_id
                LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE currency_id = '.$currency_id.' AND stakeholder_id = '.$this->{$this::DB_TABLE_PK}.' AND invoice_date >= "'.$from.'" AND invoice_date <= "'.$to.'"
                
                UNION ALL
                
                SELECT "payment" AS transaction_type, "credit" AS transaction_action, payment_date AS transaction_date, reference, 0 AS debit_amount,
                  SUM(amount*exchange_rate) AS credit_amount
                  FROM payment_vouchers
                  LEFT JOIN payment_voucher_items ON payment_vouchers.payment_voucher_id = payment_voucher_items.payment_voucher_id
                  WHERE currency_id = '.$currency_id.' AND debit_account_id = '.$this->account_id.' AND payment_date >= "'.$from.'" AND payment_date <= "'.$to.'" GROUP BY payment_voucher_items.payment_voucher_id
                  
                  ORDER BY transaction_date ASC
                  ';

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function supplied_items_in_bulk($from = null, $to = null){
		$sql = 'SELECT * FROM (
                
                  SELECT item_name, symbol AS UOM, (
                    SELECT COALESCE(SUM(quantity),0) FROM purchase_order_material_items
                    LEFT JOIN purchase_orders ON purchase_order_material_items.order_id = purchase_orders.order_id
                    WHERE purchase_order_material_items.material_item_id = material_items.item_id AND status != "CANCELLED" AND purchase_orders.stakeholder_id = '.$this->{$this::DB_TABLE_PK}.'';
		if(!is_null($from)){
			$sql .= ' AND issue_date >= "'.$from.'" ';
		}

		if(!is_null($to)){
			$sql .= ' AND issue_date <= "'.$to.'" ';
		}

		$sql .= '  ) AS quantity
                  FROM material_items
                  LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                
                  UNION
                
                  SELECT asset_name, "No." AS UOM, (
                    SELECT COALESCE(SUM(quantity),0) FROM purchase_order_asset_items
                    LEFT JOIN purchase_orders ON purchase_order_asset_items.order_id = purchase_orders.order_id
                    WHERE purchase_order_asset_items.asset_item_id = asset_items.id AND status != "CANCELLED" AND purchase_orders.stakeholder_id = '.$this->{$this::DB_TABLE_PK}.'';
		if(!is_null($from)){
			$sql .= ' AND issue_date >= "'.$from.'" ';
		}

		if(!is_null($to)){
			$sql .= ' AND issue_date <= "'.$to.'" ';
		}

		$sql .= ' ) AS quantity
                  FROM asset_items
                
                ) AS ordered_items WHERE Quantity > 0 ORDER BY Quantity DESC';
		$results = $this->db->query($sql)->result();
		$table_items = [];
		if(!empty($results)){
			foreach($results as $result){
				$table_items[] = [
					'item_name'=>$result->item_name,
					'unit'=>$result->UOM,
					'quantity'=>$result->quantity
				];
			}
		}
		return $table_items;
	}

	public function stakeholders_commitments($currency_id = null,$commitment_type = null){
		$this->load->model(['purchase_order','maintenance_service','stock_sale','project_certificate']);
		$options[] = '&nbsp;';
		if($commitment_type == 'sales') {
			$sql = 'SELECT * FROM (
                  SELECT CONCAT("Sale_",stock_sales.id,"_asset") AS debted_item, "Stock Sales" AS debt_nature, CONCAT("SALE/",LPAD(stock_sales.id, 4, 0)) AS corresponding_alias 
                  FROM stock_sales
                  WHERE stakeholder_id = ' . $this->{$this::DB_TABLE_PK} . ' 
                  
                  UNION
                  SELECT CONCAT("Service_",maintenance_services.service_id,"_serv") AS debted_item_id, "Maintenance Services" AS debt_nature, CONCAT("SVC/",LPAD(maintenance_services.service_id, 4, 0)) AS corresponding_alias 
                  FROM maintenance_services
                  WHERE client_id = ' . $this->{$this::DB_TABLE_PK} . '
                 
                  UNION
                  SELECT CONCAT("Certificate_",id,"_cert") AS debted_item_id, "Project Certificates" AS debt_nature, certificate_number AS corresponding_alias
                  FROM project_certificates
                  LEFT JOIN projects ON project_certificates.project_id = projects.project_id
                  WHERE stakeholder_id = ' . $this->{$this::DB_TABLE_PK} . '
                  
                ) AS stakeholders_debts';

			$query = $this->db->query($sql);
			$debted_items = $query->result();
			$debt_categories = [
				'Maintenance Services',
				'Stock Sales',
				'Project Certificates'
			];
			foreach($debt_categories as $category){
				foreach($debted_items as $item){
					$exploded_item = explode('_',$item->debted_item);
					if($item->debt_nature == "Maintenance Services"){
						$maintenance_service = new Maintenance_service();
						$maintenance_service->load($exploded_item[1]);
						$outgoing_invoice = $maintenance_service->outgoing_invoice();
						$maintenance_cost = $outgoing_invoice ? $outgoing_invoice->vat_amount() + $maintenance_service->maintenance_cost() : $maintenance_service->maintenance_cost();
						$item_balance = $maintenance_cost - $maintenance_service->maintenance_service_invoice_amount();
					} else if($item->debt_nature == "Stock Sales") {
						$sale = new Stock_sale();
						$sale->load($exploded_item[1]);
						$item_balance = $sale->sale_amount() - $sale->stock_sale_invoice_amount();
					} else {
						$project_certificate = new Project_certificate();
						$project_certificate->load($exploded_item[1]);
						$item_balance = $project_certificate->certified_amount - $project_certificate->invoiced_amount();
					}

					if($item->debt_nature == $category && $item_balance > 0){
						$options[$category][$item->debted_item] = $item->corresponding_alias;
					}
				}
			}
		} else {
			$sql = 'SELECT * FROM purchase_orders
					WHERE currency_id = '.$currency_id.' AND stakeholder_id = '.$this->{$this::DB_TABLE_PK}.' AND status NOT IN ("CANCELLED","CLOSED")';
			$query = $this->db->query($sql);
			$credited_items = $query->result();
			foreach($credited_items as $credited_item){
				$order = new Purchase_order();
				$order->load($credited_item->order_id);
				$options['Purchase Orders']['Purchase_'.$order->{$order::DB_TABLE_PK}.'_order'] = $order->order_number();
			}
		}
		return $options;
	}

	public function sub_contracts($project_id = null){
		$this->load->model('sub_contract');
		if(!is_null($project_id)){
			$where = [
				'project_id'=>$project_id,
				'stakeholder_id'=>$this->{$this::DB_TABLE_PK}
			];
		} else {
			$where = ['stakeholder_id'=>$this->{$this::DB_TABLE_PK}];
		}
		return $this->sub_contract->get(0,0,$where);
	}

	public function sub_contract_options($project_id = null){
		$stakeholders = $this->get(0,0,'','stakeholder_name');
		$options[] = '&nbsp;';
		foreach($stakeholders as $stakeholder){
			if(!is_null($project_id)){
				$sub_contracts = $stakeholder->sub_contracts($project_id);
			} else {
				$sub_contracts = $stakeholder->sub_contracts();
			}
			foreach($sub_contracts as $sub_contract){
				$options[$stakeholder->stakeholder_name][$sub_contract->{$sub_contract::DB_TABLE_PK}] = $sub_contract->contract_name;
			}

		}
		return $options;
	}

	public function stakeholder_with_unpaid_contracts(){
		$sql = 'SELECT stakeholders.stakeholder_id FROM stakeholders 
                LEFT JOIN sub_contracts ON stakeholders.stakeholder_id = sub_contracts.stakeholder_id
                LEFT JOIN sub_contract_certificates ON sub_contracts.id = sub_contract_certificates.sub_contract_id
                WHERE sub_contract_certificates.id NOT IN (
                  SELECT sub_contract_certificate_id FROM sub_contract_certificate_payment_vouchers 
                )
                AND  sub_contract_certificates.id IN (
                  SELECT certificate_id FROM sub_contract_payment_requisition_approval_items
                  LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id 
                )';

		$query = $this->db->query($sql);
		$results = $query->result();

		$options = [];
		foreach($results as $row){
			$stakeholder = new self();
			$stakeholder->load($row->id);
			$options['stakeholder_'.$stakeholder->{$stakeholder::DB_TABLE_PK}] = $stakeholder->stakeholder_name;
		}
		return $options;
	}

	public function vendors_with_orders($from = null, $to = null, $distinctively = false)
	{
		$where = '';
		if(!is_null($from) && $from != ''){
			$where .= ($where != '' ? ' AND ' : ' WHERE ').' issue_date >= "'.$from.'" ';
		}
		if(!is_null($to) && $to != ''){
			$where .= ($where != '' ? ' AND ' : ' WHERE ').' issue_date <= "'.$to.'" ';
		}

		if(!$distinctively) {
			$sql = 'SELECT stakeholders.stakeholder_id,stakeholder_name FROM stakeholders
                LEFT JOIN purchase_orders ON stakeholders.stakeholder_id = purchase_orders.stakeholder_id
                ' . $where;

			$query = $this->db->query($sql);
			return $query->result();
		} else {
			$sql = 'SELECT DISTINCT stakeholders.stakeholder_id FROM stakeholders
                LEFT JOIN purchase_orders ON stakeholders.stakeholder_id = purchase_orders.stakeholder_id
                ' . $where;

			$query = $this->db->query($sql);
			$results = $query->result();
			$vendors_with_orders = [];
			foreach($results as $row){
				$vendor = new self();
				$vendor->load($row->stakeholder_id);
				$vendors_with_orders[] = $vendor;
			}
			return $vendors_with_orders;
		}

	}

	public function vendor_project_purchase_orders($project_id,$currency_id,$from = null, $to = null){
		$this->load->model('purchase_order');
		$sql = 'SELECT order_id FROM project_purchase_orders
                LEFT JOIN purchase_orders ON project_purchase_orders.purchase_order_id = purchase_orders.order_id
                WHERE stakeholder_id ='.$this->{$this::DB_TABLE_PK}.' AND project_id='.$project_id.' AND currency_id =  '.$currency_id;

		if(!is_null($from)){
			$sql .= ' AND issue_date >= "'.$from.'" ';
		}

		if(!is_null($to)){
			$sql .= ' AND issue_date <= "'.$to.'" ';
		}

		$results = $this->db->query($sql)->result();
		$purchase_orders = [];
		foreach ($results as $row){
			$order = new Purchase_order();
			$order->load($row->order_id);
			$purchase_orders[] = $order;
		}
		return $purchase_orders;
	}

	public function balance_per_project($project_id,$currency_id,$from = null, $to = null){
		$orders = $this->vendor_project_purchase_orders($project_id,$currency_id,$from, $to);
		$balance_per_project = 0;
		foreach($orders as $order){
			if($order->status == "CLOSED" || $order->status == "CANCELLED") {
				$balance_per_project += 0;
			} else {
				$balance_per_project += $order->cif() - $order->amount_paid();
			}
		}
		return $balance_per_project;
	}




	/**ToDo**/
	// this function shold be merged with vendors with unpaid invoices;
	public function stakeholder_with_unpaid_claims(){
		$sql = 'SELECT stakeholders.stakeholder_id FROM stakeholders 
                LEFT JOIN sub_contracts ON stakeholders.stakeholder_id = sub_contracts.stakeholder_id
                LEFT JOIN sub_contract_certificates ON sub_contracts.id = sub_contract_certificates.sub_contract_id
                WHERE sub_contract_certificates.id NOT IN (
                  SELECT sub_contract_certificate_id FROM sub_contract_certificate_payment_vouchers 
                )
                AND  sub_contract_certificates.id IN (
                  SELECT certificate_id FROM sub_contract_payment_requisition_approval_items
                  LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id 
                )';

		$query = $this->db->query($sql);
		$results = $query->result();

		$options = [];
		foreach($results as $row){
			$stakeholder = new self();
			$stakeholder->load($row->stakeholder_id);
			$options['stakeholder_'.$stakeholder->{$stakeholder::DB_TABLE_PK}] = $stakeholder->stakeholder_name;
		}
		return $options;
	}

	public function vendors_with_unpaid_order_invoices()
	{
		$sql = 'SELECT DISTINCT vendor_invoices.vendor_id FROM vendor_invoices
            LEFT JOIN invoices ON vendor_invoices.invoice_id = invoices.id
            WHERE (
                      amount - (
                                  SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                                  LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                                  LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                                  WHERE vendor_invoices.invoice_id = invoice_payment_vouchers.invoice_id
                                )
                ) > 0 
                
                AND vendor_invoices.invoice_id IN(
                  SELECT invoice_id FROM purchase_order_payment_request_approval_invoice_items
                  LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                  LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                  WHERE is_final = 1
                 
                )
                ';

		$query = $this->db->query($sql);
		$results = $query->result();
		$vendors_with_orders = [];
		foreach($results as $row){
			$vendor = new self();
			$vendor->load($row->vendor_id);
			$vendors_with_orders['vendor_'.$vendor->{$vendor::DB_TABLE_PK}] = $vendor->vendor_name;
		}
		return $vendors_with_orders;
	}

}
