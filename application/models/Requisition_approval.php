<?php

class Requisition_approval extends MY_Model{

    const DB_TABLE = 'requisition_approvals';
    const DB_TABLE_PK = 'id';

    public $requisition_id;
    public $approval_chain_level_id;
    PUBLIC $returned_chain_level_id;
    public $created_by;
    public $approved_date;
    public $has_sources;
    public $is_final;
    public $is_printed;
    public $freight;
    public $forward_to;
    public $inspection_and_other_charges;
    public $vat_inclusive;
    public $vat_percentage;
    public $approving_comments;
    public $created_at;


    public function approval_chain_level()
    {
        $this->load->model('approval_chain_level');
        $approval_chain_level = new Approval_chain_level();
        $approval_chain_level->load($this->approval_chain_level_id);
        return $approval_chain_level;
    }

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

    public function material_items($source_type = 'all',$requisition_material_item_id = null,$account_id = null){
        $source_type = strtolower($source_type);
        $this->load->model('requisition_approval_material_item');
        $where = ['requisition_approval_id' => $this->{$this::DB_TABLE_PK}];
        if(!is_null($requisition_material_item_id)){
            $where['requisition_material_item_id'] = $requisition_material_item_id;
        }
        if($source_type != 'all'){
            $where['source_type'] = $source_type;
        }

        if(!is_null($account_id)){

            $where['account_id'] = $account_id;
        }

        return $this->requisition_approval_material_item->get(0,0,$where);
    }

    public function asset_items($source_type = 'all',$requisition_asset_item_id = null){
        $source_type = strtolower($source_type);
        $this->load->model('requisition_approval_asset_item');
        $where = ['requisition_approval_id' => $this->{$this::DB_TABLE_PK}];
        if(!is_null($requisition_asset_item_id)){
            $where['requisition_asset_item_id'] = $requisition_asset_item_id;
        }
        if($source_type != 'all'){
            $where['source_type'] = $source_type;
        }

        return $this->requisition_approval_asset_item->get(0,0,$where);
    }

    public function service_items($source_type = 'all',$requisition_service_item_id = null){
        $source_type = strtolower($source_type);
        $this->load->model('requisition_approval_service_item');
        $where = ['requisition_approval_id' => $this->{$this::DB_TABLE_PK}];
        if(!is_null($requisition_service_item_id)){
            $where['requisition_service_item_id'] = $requisition_service_item_id;
        }
        if($source_type != 'all'){
            $where['source_type'] = $source_type;
        }

        return $this->requisition_approval_service_item->get(0,0,$where);
    }

    public function cash_items($requisition_cash_item_id = null,$account_id = null){
        $this->load->model('requisition_approval_cash_item');
        $where = ['requisition_approval_id' => $this->{$this::DB_TABLE_PK}];
        if(!is_null($requisition_cash_item_id)){
            $where['requisition_cash_item_id'] = $requisition_cash_item_id;
        }

        if(!is_null($account_id)){

            $where['account_id'] = $account_id;
        }

        return $this->requisition_approval_cash_item->get(0,0,$where);
    }

    public function created_by()
    {
        $this->load->model('employee');
        $created_by = new Employee;
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function forward_to()
    {
        $this->load->model('employee');
        $forward_to = new Employee;
        $forward_to->load($this->forward_to);
        return $forward_to;
    }

    public function material_items_approved_amount($source = 'all',$base_currency = false){
        $sql = 'SELECT COALESCE(SUM(approved_quantity*requisition_approval_material_items.approved_rate),0) AS approved_amount FROM requisition_approval_material_items
                WHERE requisition_approval_id = '.$this->{$this::DB_TABLE_PK};
        if($source != 'all'){
            $sql .= ' AND source_type = "'.$source.'" ';
        }
        $query = $this->db->query($sql);
        $amount = $query->row()->approved_amount;
        if(!$base_currency){
            return $amount;
        } else {
            $requisition = $this->requisition();
            if($requisition->currency_id == 1){
                return $amount;
            } else {
                $currency = $requisition->currency();
                return $amount*$currency->rate_to_native($requisition->request_date);
            }
        }
    }

    public function asset_items_approved_amount($source = 'all',$base_currency = false){
        $sql = 'SELECT COALESCE(SUM(approved_quantity*requisition_approval_asset_items.approved_rate),0) AS approved_amount FROM requisition_approval_asset_items
                WHERE requisition_approval_id = '.$this->{$this::DB_TABLE_PK};
        if($source != 'all'){
            $sql .= ' AND source_type = "'.$source.'" ';
        }
        $query = $this->db->query($sql);
        $amount = $query->row()->approved_amount;
        if(!$base_currency){
            return $amount;
        } else {
            $requisition = $this->requisition();
            if($requisition->currency_id == 1){
                return $amount;
            } else {
                $currency = $requisition->currency();
                return $amount*$currency->rate_to_native($requisition->request_date);
            }
        }
    }

    public function service_items_approved_amount($source = 'all',$base_currency = false){
        $sql = 'SELECT COALESCE(SUM(approved_quantity*requisition_approval_service_items.approved_rate),0) AS approved_amount FROM requisition_approval_service_items
                WHERE requisition_approval_id = '.$this->{$this::DB_TABLE_PK};
        if($source != 'all'){
            $sql .= ' AND source_type = "'.$source.'" ';
        }
        $query = $this->db->query($sql);
        $amount = $query->row()->approved_amount;
        if(!$base_currency){
            return $amount;
        } else {
            $requisition = $this->requisition();
            if($requisition->currency_id == 1){
                return $amount;
            } else {
                $currency = $requisition->currency();
                return $amount*$currency->rate_to_native($requisition->request_date);
            }
        }
    }

    public function cash_items_approved_amount($account_id=null,$base_currency = false){

        $sql = 'SELECT SUM(approved_quantity*approved_rate) AS approved_amount FROM requisition_approval_cash_items
                  WHERE requisition_approval_id = '.$this->{$this::DB_TABLE_PK};
        if($account_id != 'all'){
            $sql .= ' AND account_id '.(is_null($account_id) ? ' IS NULL ' : ' = '.$account_id);
        }

        $query = $this->db->query($sql);
        $amount = $query->row()->approved_amount;

        if(!$base_currency){
            return $amount;
        } else {
            $requisition = $this->requisition();
            if($requisition->currency_id == 1){
                return $amount;
            } else {
                $currency = $requisition->currency();
                return $amount*$currency->rate_to_native($requisition->request_date);
            }
        }
    }

    public function requisition_approval_payment_voucher(){
        $this->load->model('requisition_approval_payment_voucher');
        $requisition_approval_payment_vouchers = $this->requisition_approval_payment_voucher->get(1,0,['requisition_approval_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($requisition_approval_payment_vouchers) ? array_shift($requisition_approval_payment_vouchers) : false;
    }

    public function payment_voucher(){
        $this->load->model(['requisition_approval_payment_voucher','payment_voucher']);
        $requisition_approval_payment_vouchers = $this->requisition_approval_payment_voucher->get(1,0,['requisition_approval_id' => $this->{$this::DB_TABLE_PK}]);
        if(!empty($requisition_approval_payment_vouchers)){
            foreach($requisition_approval_payment_vouchers as $approval_payment_voucher){
                $pv = new Payment_voucher();
                $pv->load($approval_payment_voucher->payment_voucher_id);
                return $pv;
            }
        } else {
            return false;
        }
    }

    public function requisition_approval_imprest_voucher(){
        $this->load->model(['requisition_approval_imprest_voucher','imprest_voucher']);
        $requisition_approval_imprest_vouchers = $this->requisition_approval_imprest_voucher->get(1,0,['requisition_approval_id' => $this->{$this::DB_TABLE_PK}]);
        if(!empty($requisition_approval_imprest_vouchers)){
            foreach($requisition_approval_imprest_vouchers as $approval_imprest_voucher){
                $impv = new Imprest_voucher();
                $impv->load($approval_imprest_voucher->imprest_voucher_id);
                return $impv;
            }
        } else {
            return false;
        }
    }

    public function payment_vouchers(){
        $this->load->model(['requisition_approval_payment_voucher','payment_voucher']);
        $requisition_approval_payment_vouchers = $this->requisition_approval_payment_voucher->get(0,0,['requisition_approval_id' => $this->{$this::DB_TABLE_PK}]);
        $payment_vouchers = [];
        if(!empty($requisition_approval_payment_vouchers)){
            foreach($requisition_approval_payment_vouchers as $approval_payment_voucher){
                $pv = new Payment_voucher();
                $pv->load($approval_payment_voucher->payment_voucher_id);
                $payment_vouchers[] = $pv;
            }
        }
        return $payment_vouchers;
    }

    public function imprest_vouchers(){
        $this->load->model(['requisition_approval_imprest_voucher','imprest_voucher']);
        $requisition_approval_imprest_vouchers = $this->requisition_approval_imprest_voucher->get(0,0,['requisition_approval_id' => $this->{$this::DB_TABLE_PK}]);
        $imprest_vouchers = [];
        if(!empty($requisition_approval_imprest_vouchers)){
            foreach($requisition_approval_imprest_vouchers as $approval_imprest_voucher){
                $pv = new imprest_voucher();
                $pv->load($approval_imprest_voucher->imprest_voucher_id);
                $imprest_vouchers[] = $pv;
            }
        }
        return $imprest_vouchers;
    }

    public function total_paid_amount(){
        $this->load->model(['requisition_approval_payment_voucher','payment_voucher','requisition_approval_imprest_voucher','imprest_voucher']);
        $approval_payment_vouchers = $this->requisition_approval_payment_voucher->get(0,0,['requisition_approval_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_amount = 0;
        if(!empty($approval_payment_vouchers)){
            foreach($approval_payment_vouchers as $approval_pv){
                $approval_payment_voucher = new Requisition_approval_payment_voucher();
                $approval_payment_voucher->load($approval_pv->id);
                $total_amount += $approval_payment_voucher->payment_voucher()->amount();
            }
        }

        $approval_imprest_vouchers = $this->requisition_approval_imprest_voucher->get(0,0,['requisition_approval_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_amount = 0;
        if(!empty($approval_imprest_vouchers)){
            foreach($approval_imprest_vouchers as $approval_pv){
                $approval_imprest_voucher = new Requisition_approval_imprest_voucher();
                $approval_imprest_voucher->load($approval_pv->id);
                $total_amount += $approval_imprest_voucher->imprest_voucher()->total_amount();
            }
        }
        return $total_amount;
    }

    public function approved_cash_requisitions_list($limit, $start, $keyword, $order, $account_id = null){
        $this->load->model([
            'purchase_order_payment_request_approval',
            'account',
            'purchase_order_payment_request_approval_invoice_item',
            'sub_contract_payment_requisition_approval',
            'sub_contract_payment_requisition_approval_item',
            'withholding_tax',
            'payment_voucher_item',
            'payment_request_approval_journal_voucher',
            'invoice'
        ]);
        $status = $this->input->post('status');
        //order string
        $order_string = dataTable_order_string(['requisition_id','approved_date','request_type'],$order,'requisition_id DESC');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;
        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));

        if(!is_null($account_id)) {
            $material_where_clause = ' WHERE requisition_approval_material_items.account_id = "' . $account_id . '" AND is_final = "1" AND status = "APPROVED" '.( $confidentiality_position != '' ? ' AND confidentiality_chain_position <=' .$confidentiality_position : '').'';
            $cash_where_clause = ' WHERE requisition_approval_cash_items.account_id = "' . $account_id . '" AND is_final = "1" AND status = "APPROVED" '.( $confidentiality_position != '' ? ' AND confidentiality_chain_position <=' .$confidentiality_position : '').'';
        } else {
            $material_where_clause = ' WHERE requisition_approval_material_items.source_type = "cash" AND requisition_approval_material_items.account_id IS NULL AND is_final = "1" AND status = "APPROVED" '.( $confidentiality_position != '' ? ' AND confidentiality_chain_position <=' .$confidentiality_position : '').'';
            $cash_where_clause = ' WHERE requisition_approval_cash_items.account_id IS NULL AND is_final = "1" AND status = "APPROVED" '.( $confidentiality_position != '' ? ' AND confidentiality_chain_position <=' .$confidentiality_position : '').'';
        }
        $asset_where_clause = ' WHERE requisition_approval_asset_items.source_type = "cash" AND is_final = "1" AND status = "APPROVED" '.( $confidentiality_position != '' ? ' AND confidentiality_chain_position <=' .$confidentiality_position : '').'';
        $service_where_clause = ' WHERE requisition_approval_service_items.source_type = "cash" AND is_final = "1" AND status = "APPROVED" '.( $confidentiality_position != '' ? ' AND confidentiality_chain_position <=' .$confidentiality_position : '').'';
        $invoice_where_clause = ' WHERE is_final = "1" AND status = "APPROVED" ';
        $sub_contract_where_clause = ' WHERE is_final = "1" AND status = "APPROVED" '.( $confidentiality_position != '' ? ' AND confidentiality_chain_position <=' .$confidentiality_position : '').'';

        $sql = 'SELECT * FROM (
						
						SELECT sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.currency_id, "sub_contract_payment_requisition" AS request_type, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id
						FROM sub_contract_payment_requisition_approval_items
						LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
						LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
						'.$sub_contract_where_clause.'
						
						UNION 
		
						SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_invoice" AS request_type, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id
						FROM purchase_order_payment_request_approval_invoice_items
						LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
						LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
						'.$invoice_where_clause.'
						
						
						UNION
		
						SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
						FROM requisition_approval_service_items
						LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$service_where_clause.'
						
						UNION
					  
		
						SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
						FROM requisition_approval_asset_items
						LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$asset_where_clause.'
						
						UNION
		
						SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
						FROM requisition_approval_material_items
						LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
						LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $material_where_clause . '
		
						UNION
						
						SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
						FROM requisition_approval_cash_items
						LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
						LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $cash_where_clause . '
						
					) AS approved_cash_requisitions';

        if($status == "paid"){
            $sql .= ' WHERE ( 
								   request_type = "requisition" AND requisition_approval_id IN ( SELECT requisition_approval_id FROM requisition_approval_payment_vouchers )
								)
							 OR ( 
								   request_type = "payment_request_invoice" AND requisition_approval_id IN ( 
							  
									   SELECT
										 CASE WHEN purchase_order_payment_request_approval_id IN (
										   SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_invoice_items
											 LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
											 LEFT JOIN invoices ON purchase_order_payment_request_invoice_items.invoice_id = invoices.id
											 LEFT JOIN invoice_payment_vouchers ON invoices.id = invoice_payment_vouchers.invoice_id
										   WHERE purchase_order_payment_request_invoice_items.invoice_id = invoice_payment_vouchers.invoice_id
										 ) THEN purchase_order_payment_request_approval_id
										 ELSE "NULL" END AS paid_invoice_requests
									   FROM purchase_order_payment_request_approval_payment_vouchers
																   
									   )
									   AND approved_invoice_item_id IN ( SELECT purchase_order_payment_request_approval_invoice_item_id FROM payment_voucher_item_approved_invoice_items )
								)
								
							 OR (
								   request_type = "sub_contract_payment_requisition" AND requisition_approval_id IN ( SELECT sub_contract_payment_requisition_approval_id FROM sub_contract_payment_requisition_approval_payment_vouchers )
								   
								 )';

        } else if($status == "pending"){
            $sql .= ' WHERE ( 
								   request_type = "requisition"
								   AND requisition_approval_id NOT IN ( SELECT requisition_approval_id FROM requisition_approval_payment_vouchers ) 
								   AND requisition_approval_id NOT IN ( SELECT requisition_approval_id FROM approved_requisition_payment_cancellations )
								   AND requisition_approval_id NOT IN ( 
									  SELECT
										 CASE
											 WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_material_items 
													LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
											   ) THEN requisition_approval_id
									   
											  WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_asset_items 
													LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
											   ) THEN requisition_approval_id
									   
											  WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_service_items 
													LEFT JOIN requisition_approval_service_items ON imprest_voucher_service_items.requisition_approval_service_item_id = requisition_approval_service_items.id
											   ) THEN requisition_approval_id
											   
											  WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_cash_items 
													LEFT JOIN requisition_approval_cash_items ON imprest_voucher_cash_items.requisition_approval_cash_item_id = requisition_approval_cash_items.id
											   ) THEN requisition_approval_id
										   
											  ELSE "NULL"
											 END
											 AS imprests
											 FROM requisition_approvals
											)
									)';
        } else if($status == "not_paid"){
            $sql .= ' WHERE ( 
								   request_type = "payment_request_invoice" AND requisition_approval_id NOT IN ( 
							  
									   SELECT
										 CASE WHEN purchase_order_payment_request_approval_id IN (
										   SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_invoice_items
											 LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
											 LEFT JOIN invoices ON purchase_order_payment_request_invoice_items.invoice_id = invoices.id
											 LEFT JOIN invoice_payment_vouchers ON invoices.id = invoice_payment_vouchers.invoice_id
										   WHERE purchase_order_payment_request_invoice_items.invoice_id = invoice_payment_vouchers.invoice_id
										 ) THEN purchase_order_payment_request_approval_id
										 ELSE "NULL" END AS paid_invoice_requests
									   FROM purchase_order_payment_request_approval_payment_vouchers
																   
									   )
									   AND approved_invoice_item_id NOT IN ( SELECT purchase_order_payment_request_approval_invoice_item_id FROM payment_voucher_item_approved_invoice_items )
								)
								
							 OR (
								   request_type = "sub_contract_payment_requisition" AND requisition_approval_id NOT IN ( SELECT sub_contract_payment_requisition_approval_id FROM sub_contract_payment_requisition_approval_payment_vouchers )
								   
								 )';
        } else if($status == "imprest"){
            $sql .= ' WHERE request_type = "requisition" AND requisition_approval_id IN ( 
					   SELECT
						  CASE
							  WHEN requisition_approval_id IN (
									SELECT requisition_approval_id FROM imprest_voucher_material_items 
									LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
							   ) THEN requisition_approval_id
					   
							  WHEN requisition_approval_id IN (
									SELECT requisition_approval_id FROM imprest_voucher_asset_items 
									LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
							   ) THEN requisition_approval_id
					   
							  WHEN requisition_approval_id IN (
									SELECT requisition_approval_id FROM imprest_voucher_service_items 
									LEFT JOIN requisition_approval_service_items ON imprest_voucher_service_items.requisition_approval_service_item_id = requisition_approval_service_items.id
							   ) THEN requisition_approval_id
							   
							  WHEN requisition_approval_id IN (
									SELECT requisition_approval_id FROM imprest_voucher_cash_items 
									LEFT JOIN requisition_approval_cash_items ON imprest_voucher_cash_items.requisition_approval_cash_item_id = requisition_approval_cash_items.id
							   ) THEN requisition_approval_id
						  END
						  AS imprests
						FROM requisition_approvals
				)';
        } else if($status == "revoked"){
            $sql .= ' WHERE ( 
								   request_type = "requisition" AND requisition_approval_id IN ( SELECT requisition_approval_id FROM approved_requisition_payment_cancellations )
								)
							 OR ( 
								   request_type = "payment_request_invoice" AND requisition_approval_id IN ( SELECT purchase_order_payment_request_approval_id FROM approved_invoice_payment_cancellations )
								)  
							 OR (
								   request_type = "sub_contract_payment_requisition" AND requisition_approval_id IN ( SELECT sub_contract_payment_requisition_approval_id FROM approved_sub_contract_payment_cancellations )
								   
								 )';

        } else if($status == "all"){
            $sql .= '';
        }

        $sql .= '
					GROUP BY requisition_approval_id, currency_id,request_type,approved_invoice_item_id
					';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $invoice_where_clause .= ' AND ( approval_date LIKE "%' . $keyword . '%" 
				OR purchase_order_payment_requests.id LIKE "%' . $keyword . '%"
				OR first_name LIKE "%' . $keyword . '%"
				OR last_name LIKE "%' . $keyword . '%" 
				OR purchase_order_payment_requests.id IN(
						SELECT purchase_order_payment_requests.id FROM purchase_order_payment_requests
						LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
						LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
						LEFT JOIN cost_centers ON cost_center_purchase_orders.cost_center_id = cost_centers.id
						WHERE cost_center_name LIKE "%' . $keyword . '%"
					)
				OR purchase_order_payment_requests.id IN(
						SELECT purchase_order_payment_requests.id FROM purchase_order_payment_requests
						LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
						LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
						LEFT JOIN projects ON project_purchase_orders.project_id = projects.project_id
						WHERE project_name LIKE "%' . $keyword . '%"
					) 
				OR purchase_order_payment_requests.id IN(
						SELECT purchase_order_payment_requests.id FROM purchase_order_payment_requests
						WHERE purchase_order_id LIKE "%' . $keyword . '%"
					) 
				)';

            $service_where_clause .= ' AND (approved_date LIKE "%' . $keyword . '%" 
				OR requisition_approvals.requisition_id  LIKE "%' . $keyword . '%"
				OR first_name LIKE "%' . $keyword . '%"
				OR last_name LIKE "%' . $keyword . '%"
				OR requisition_approvals.requisition_id IN(
					SELECT requisition_id FROM project_requisitions
					LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
					WHERE project_name LIKE "%' . $keyword . '%"
					)
				OR requisition_approvals.requisition_id IN (
					SELECT requisition_id FROM cost_center_requisitions
					LEFT JOIN cost_centers ON cost_center_requisitions.cost_center_id = cost_centers.id
					WHERE cost_center_name LIKE "%' . $keyword . '%"
					)
				)';

            $material_where_clause .= ' AND (approved_date LIKE "%' . $keyword . '%" 
				OR requisition_approvals.requisition_id  LIKE "%' . $keyword . '%"
				OR first_name LIKE "%' . $keyword . '%"
				OR last_name LIKE "%' . $keyword . '%"
				OR requisition_approvals.requisition_id IN(
					SELECT requisition_id FROM project_requisitions
					LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
					WHERE project_name LIKE "%' . $keyword . '%"
					)
				OR requisition_approvals.requisition_id IN (
					SELECT requisition_id FROM cost_center_requisitions
					LEFT JOIN cost_centers ON cost_center_requisitions.cost_center_id = cost_centers.id
					WHERE cost_center_name LIKE "%' . $keyword . '%"
					)
				)';

            $asset_where_clause .= ' AND (approved_date LIKE "%' . $keyword . '%"
				OR requisition_approvals.requisition_id  LIKE "%' . $keyword . '%"
				OR first_name LIKE "%' . $keyword . '%"
				OR last_name LIKE "%' . $keyword . '%"
				OR requisition_approvals.requisition_id IN(
					SELECT requisition_id FROM project_requisitions
					LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
					WHERE project_name LIKE "%' . $keyword . '%"
					)
				OR requisition_approvals.requisition_id IN (
					SELECT requisition_id FROM cost_center_requisitions
					LEFT JOIN cost_centers ON cost_center_requisitions.cost_center_id = cost_centers.id
					WHERE cost_center_name LIKE "%' . $keyword . '%"
					)
				)';

            $cash_where_clause .= ' AND (approved_date LIKE "%' . $keyword . '%"
				OR requisition_approvals.requisition_id  LIKE "%' . $keyword . '%"
				OR first_name LIKE "%' . $keyword . '%"
				OR last_name LIKE "%' . $keyword . '%"
				OR requisition_approvals.requisition_id IN(
					SELECT requisition_id FROM project_requisitions
					LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
					WHERE project_name LIKE "%' . $keyword . '%"
					)
				OR requisition_approvals.requisition_id IN(
					SELECT requisition_id FROM cost_center_requisitions
					LEFT JOIN cost_centers ON cost_center_requisitions.cost_center_id = cost_centers.id
					WHERE cost_center_name LIKE "%' . $keyword . '%"
					)
				)';

            $sub_contract_where_clause .= ' AND (approval_date LIKE "%' . $keyword . '%"
				OR sub_contract_payment_requisition_approvals.sub_contract_requisition_id  LIKE "%' . $keyword . '%"
				OR first_name LIKE "%' . $keyword . '%"
				OR last_name LIKE "%' . $keyword . '%"
				OR sub_contract_payment_requisition_approvals.sub_contract_requisition_id IN (
					SELECT sub_contract_requisition_id FROM sub_contract_payment_requisition_items
					LEFT JOIN sub_contract_certificates ON sub_contract_payment_requisition_items.certificate_id = sub_contract_certificates.id
					LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
					LEFT JOIN projects ON sub_contracts.project_id = projects.project_id
					WHERE project_name LIKE "%' . $keyword . '%"
					)
				)';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS * FROM (
						
						SELECT "sub_contract" AS item_type, "sub_contract_payment_requisition" AS request_type, "" AS approved_requisition_item_id, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id, sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.sub_contract_requisition_id AS requisition_id, sub_contract_payment_requisitions.currency_id, approval_date AS approved_date, CONCAT(first_name," ",last_name) as approver_name
						FROM sub_contract_payment_requisition_approval_items
						LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
						LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
						LEFT JOIN employees ON sub_contract_payment_requisition_approvals.created_by = employees.employee_id
						'.$sub_contract_where_clause.'
						
						UNION 
		
						SELECT "invoice" AS item_type, "payment_request_invoice" AS request_type, "" AS approved_requisition_item_id, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id, purchase_order_payment_request_approvals.id AS requisition_approval_id, purchase_order_payment_requests.id AS requisition_id, purchase_order_payment_requests.currency_id, approval_date AS approved_date, CONCAT(first_name," ",last_name) as approver_name
						FROM purchase_order_payment_request_approval_invoice_items
						LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
						LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
						LEFT JOIN employees ON purchase_order_payment_request_approvals.created_by = employees.employee_id
						'.$invoice_where_clause.'
						
						UNION
		
						SELECT "service" AS item_type, "requisition" AS request_type, requisition_approval_service_items.id AS approved_requisition_item_id, "" AS approved_invoice_item_id, requisition_approvals.id AS requisition_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id ,approved_date,
						CONCAT(first_name," ",last_name) as approver_name
						FROM requisition_approval_service_items
						LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$service_where_clause.'
						
						UNION
						
						SELECT "asset" AS item_type, "requisition" AS request_type, requisition_approval_asset_items.id AS approved_requisition_item_id, "" AS approved_invoice_item_id, requisition_approvals.id AS requisition_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id ,approved_date,
						CONCAT(first_name," ",last_name) as approver_name
						FROM requisition_approval_asset_items
						LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$asset_where_clause.'
						
						UNION
		
						SELECT "material" AS item_type, "requisition" AS request_type, requisition_approval_material_items.id AS approved_requisition_item_id, "" AS approved_invoice_item_id, requisition_approvals.id AS requisition_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id, approved_date,
						CONCAT(first_name," ",last_name) as approver_name
						FROM requisition_approval_material_items
						LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
						LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $material_where_clause . '
		
						UNION
						
						SELECT "cash" AS item_type, "requisition" AS request_type, requisition_approval_cash_items.id AS approved_requisition_item_id, "" AS approved_invoice_item_id, requisition_approvals.id AS requisition_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id ,approved_date,
						CONCAT(first_name," ",last_name) as approver_name
						FROM requisition_approval_cash_items
						LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
						LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
						LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
						LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $cash_where_clause . '
						
						
					) AS approved_cash_requisitions';

        if($status == "paid"){
            $sql .= ' WHERE ( 
									   request_type = "requisition" AND requisition_approval_id IN ( SELECT requisition_approval_id FROM requisition_approval_payment_vouchers )
									)
								 OR ( 
									   request_type = "payment_request_invoice" AND requisition_approval_id IN ( 
								  
										   SELECT
											 CASE WHEN purchase_order_payment_request_approval_id IN (
											   SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_invoice_items
												 LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
												 LEFT JOIN invoices ON purchase_order_payment_request_invoice_items.invoice_id = invoices.id
												 LEFT JOIN invoice_payment_vouchers ON invoices.id = invoice_payment_vouchers.invoice_id
											   WHERE purchase_order_payment_request_invoice_items.invoice_id = invoice_payment_vouchers.invoice_id
											 ) THEN purchase_order_payment_request_approval_id
											 ELSE "NULL" END AS paid_invoice_requests
										   FROM purchase_order_payment_request_approval_payment_vouchers
																	   
										   )
										   AND approved_invoice_item_id IN ( SELECT purchase_order_payment_request_approval_invoice_item_id FROM payment_voucher_item_approved_invoice_items )
									)
									
								 OR (
									   request_type = "sub_contract_payment_requisition" AND requisition_approval_id IN ( SELECT sub_contract_payment_requisition_approval_id FROM sub_contract_payment_requisition_approval_payment_vouchers )
									   
									 )';

        } else if($status == "pending"){
            $sql .= ' WHERE ( 
								   request_type = "requisition"
								   AND requisition_approval_id NOT IN ( SELECT requisition_approval_id FROM requisition_approval_payment_vouchers ) 
								   AND requisition_approval_id NOT IN ( SELECT requisition_approval_id FROM approved_requisition_payment_cancellations )
								   AND requisition_approval_id NOT IN ( 
									  SELECT
										 CASE
											 WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_material_items 
													LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
											   ) THEN requisition_approval_id
									   
											  WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_asset_items 
													LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
											   ) THEN requisition_approval_id
									   
											  WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_service_items 
													LEFT JOIN requisition_approval_service_items ON imprest_voucher_service_items.requisition_approval_service_item_id = requisition_approval_service_items.id
											   ) THEN requisition_approval_id
											   
											  WHEN requisition_approval_id IN (
													SELECT requisition_approval_id FROM imprest_voucher_cash_items 
													LEFT JOIN requisition_approval_cash_items ON imprest_voucher_cash_items.requisition_approval_cash_item_id = requisition_approval_cash_items.id
											   ) THEN requisition_approval_id
										   
											  ELSE "NULL"
											 END
											 AS imprests
											 FROM requisition_approvals
											)
									)';
        } else if($status == "not_paid"){
            $sql .= ' WHERE ( 
								   request_type = "payment_request_invoice" AND requisition_approval_id NOT IN ( 
							  
									   SELECT
										 CASE WHEN purchase_order_payment_request_approval_id IN (
										   SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_invoice_items
											 LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
											 LEFT JOIN invoices ON purchase_order_payment_request_invoice_items.invoice_id = invoices.id
											 LEFT JOIN invoice_payment_vouchers ON invoices.id = invoice_payment_vouchers.invoice_id
										   WHERE purchase_order_payment_request_invoice_items.invoice_id = invoice_payment_vouchers.invoice_id
										 ) THEN purchase_order_payment_request_approval_id
										 ELSE "NULL" END AS paid_invoice_requests
									   FROM purchase_order_payment_request_approval_payment_vouchers
																   
									   )
									   AND approved_invoice_item_id NOT IN ( SELECT purchase_order_payment_request_approval_invoice_item_id FROM payment_voucher_item_approved_invoice_items )
								)
								
							 OR (
								   request_type = "sub_contract_payment_requisition" AND requisition_approval_id NOT IN ( SELECT sub_contract_payment_requisition_approval_id FROM sub_contract_payment_requisition_approval_payment_vouchers )
								   
								 )';
        } else if($status == "imprest"){
            $sql .= ' WHERE request_type = "requisition" AND requisition_approval_id IN ( 
						   SELECT
							  CASE
								  WHEN requisition_approval_id IN (
										SELECT requisition_approval_id FROM imprest_voucher_material_items 
										LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
								   ) THEN requisition_approval_id
						   
								  WHEN requisition_approval_id IN (
										SELECT requisition_approval_id FROM imprest_voucher_asset_items 
										LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
								   ) THEN requisition_approval_id
						   
								  WHEN requisition_approval_id IN (
										SELECT requisition_approval_id FROM imprest_voucher_service_items 
										LEFT JOIN requisition_approval_service_items ON imprest_voucher_service_items.requisition_approval_service_item_id = requisition_approval_service_items.id
								   ) THEN requisition_approval_id
								   
								  WHEN requisition_approval_id IN (
										SELECT requisition_approval_id FROM imprest_voucher_cash_items 
										LEFT JOIN requisition_approval_cash_items ON imprest_voucher_cash_items.requisition_approval_cash_item_id = requisition_approval_cash_items.id
								   ) THEN requisition_approval_id
							  END
							  AS imprests
							FROM requisition_approvals
					)';
        } else if($status == "revoked"){
            $sql .= ' WHERE ( 
									   request_type = "requisition" AND requisition_approval_id IN ( SELECT requisition_approval_id FROM approved_requisition_payment_cancellations )
									)
								 OR ( 
									   request_type = "payment_request_invoice" AND requisition_approval_id IN ( SELECT purchase_order_payment_request_approval_id FROM approved_invoice_payment_cancellations )
									)  
								 OR (
									   request_type = "sub_contract_payment_requisition" AND requisition_approval_id IN ( SELECT sub_contract_payment_requisition_approval_id FROM approved_sub_contract_payment_cancellations )
									   
									 )';

        } else if($status == "all"){
            $sql .= '';
        }

        $sql .= '
					GROUP BY requisition_approval_id, currency_id,request_type,approved_invoice_item_id
					'.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        if(is_null($account_id)){
            $data['credit_account_options'] = account_dropdown_options(['CASH IN HAND','BANK']);
        } else {
            $account = new Account();
            $account->load($account_id);
            $data['account'] = $account;
        }
        $data['account_id'] = $account_id;
        $data['expense_pv_debit_account_options'] = $data['debit_account_options'] = account_dropdown_options(['EXPENSES','REVENUE','CASH IN HAND','OTHER REVENUES','DIRECT EXPENSES','INDIRECT EXPENSES','COGS','CURRENT LIABILITIES','NON CURRENT LIABILITIES','LIABILITIES']);
        $data['currency_options'] = currency_dropdown_options();
        $data['employee_options'] = employee_options(true);
        $data['payment_voucher_print_out_link'] = 'Finance/preview_payment_voucher/';
        $data['journal_voucher_print_out_link'] = 'Finance/preview_journal_voucher/';
        $data['imprest_voucher_print_out_link'] = 'Finance/preview_imprest_voucher/';

        $rows = [];
        foreach ($results as $row){
            $data['requisition_approval_id']= $row->requisition_approval_id;
            $data['request_type']= $row->request_type;
            if($row->request_type == 'payment_request_invoice'){

                $approved_item = new Purchase_order_payment_request_approval_invoice_item();
                $approved_item->load($row->approved_invoice_item_id);
                $requisition_approval = $approved_item->purchase_order_payment_request_approval();
                $payment_voucher = $requisition_approval->payment_voucher();
                $journal_voucher = $requisition_approval->journal_voucher();
                $payment_voucher_item = $approved_item->payment_voucher_item();
                $payment_request = $requisition_approval->purchase_order_payment_request();
                $payment_request_invoice_item = $approved_item->purchase_order_payment_request_invoice_item();
                $invoice = $payment_request_invoice_item->invoice();
                $paid_invoice_item = $approved_item->paid_invoice_items();

                $paid_amount = $approved_item->paid_amount();
                $amount_to_be_paid = $approved_item->approved_amount - $paid_amount;

                $data['payment_voucher'] = $payment_voucher;
                $data['imprest_voucher'] = false;
                $data['payment_voucher_item'] = $payment_voucher_item;
                $data['approved_item']  = $approved_item;
                $data['paid_invoice_item'] = $paid_invoice_item;
                $data['requisition_approval']  = $requisition_approval;
                $data['currency'] = $payment_request->currency();
                $data['is_cancelled'] = $requisition_approval->is_cancelled();
                $data['account_options'] = $this->account->dropdown_options();
                $data['has_stock_items'] = false;
                $data['amount_to_be_paid'] = $amount_to_be_paid;
                $data['stakeholder'] = $invoice->stakeholder();
                $data['invoice'] = $invoice;
                $data['requisition_number'] = $requisition_approval->purchase_order_payment_request()->request_number();
                $data['paid_items'] = $approved_item->payment_vouchers();
                $data['journal_voucher'] = $journal_voucher;
                $data['imprest_voucher_id'] = false;
                $data['approved_print_out_link'] = 'procurements/preview_approved_purchase_order_payments/';

                if(($journal_voucher || $payment_voucher) && round($amount_to_be_paid,2) <= 0){
                    $status = '<span style="font-size: 10px" class="label label-success">Paid</span>';
                } else if(($journal_voucher || $payment_voucher) && round($amount_to_be_paid,2) > 0) {
                    $status = '<span class="label" style="background-color: #00e765; font-size: 12px;">Partial Payment(s) '.$amount_to_be_paid.'</span>';
                } else if(in_array($row->requisition_approval_id,$requisition_approval->cancelled_approved_payment())){
                    $status = '<span style="font-size: 10px" class="label label-danger">Revoked</span>';
                } else {
                    $status = '<span style="font-size: 10px" class="label label-info">Pending</span>';
                }

                if($approved_item->approved_amount > 0) {
                    $data['request_number'] = $payment_request->request_number();
                    $rows[] = [
                        custom_standard_date($row->approved_date),
                        'P.O Payment (Invoice)',
                        $payment_request->request_number(),
                        $requisition_approval->cost_center_name(),
                        $row->approver_name,
                        $data['currency']->symbol . '<span class="pull-right">' . number_format($approved_item->approved_amount) . '</span>',
                        $status,
                        $this->load->view('finance/transactions/approved_cash_requests/approved_cash_list_actions', $data, true)
                    ];
                }

            } else if($row->request_type == 'sub_contract_payment_requisition'){
                $requisition_approval = new Sub_contract_payment_requisition_approval();
                $requisition_approval->load($row->requisition_approval_id);
                $cost_center_name = $requisition_approval->sub_contract_requisition()->cost_center_name();
                $journal_voucher = $requisition_approval->journal_voucher();

                $sub_contract_requisition = $requisition_approval->sub_contract_requisition();
                $approved_item = new Sub_contract_payment_requisition_approval_item();
                $approved_item->load($row->approved_invoice_item_id);
                $approved_amount = $requisition_approval->vat_inclusive == 1 ? $approved_item->approved_amount*1.18 : $approved_item->approved_amount;
                $amount_to_be_paid = $approved_amount - $requisition_approval->total_paid_amount();

                $data['currency'] = $requisition_approval->currency();
                $data['is_cancelled'] = $requisition_approval->is_cancelled();
                $data['requisition_approval'] = $requisition_approval;
                $data['approved_item'] = $approved_item;
                $data['contractor_account_options'] = [];
                $data['has_stock_items'] = false;
                $data['amount_to_be_paid'] = $amount_to_be_paid;
                $data['stakeholder'] = $approved_item->sub_contract_payment_requisition_item()->certificate()->sub_contract()->stakeholder();
                $data['payment_voucher'] = $payment_voucher = $requisition_approval->payment_voucher();
                $data['imprest_voucher'] = false;
                $data['paid_certificate'] = $paid_certificate = $approved_item->sub_contract_payment_requisition_item()->certificate()->paid_certificate();
                $data['paid_approved_item'] = $paid_approved_item = $approved_item->paid_approved_item();
                $data['requisition_number'] = $requisition_approval->sub_contract_requisition()->sub_contract_requisition_number();
                $data['paid_items'] = $approved_item->payment_vouchers();
                $data['journal_voucher'] = $journal_voucher;
                $data['imprest_voucher_id'] = false;
                $data['approved_print_out_link'] = 'requisitions/preview_approved_sub_contract_payment_requsition/';

                $status = '<span style="font-size: 10px" class="label label-info">Pending</span>';
                if(($journal_voucher || $payment_voucher) && $paid_certificate && $paid_approved_item && $amount_to_be_paid <= 0){
                    $status = '<span style="font-size: 10px" class="label label-success">Paid</span>';
                    $payment_voucher_item = new  Payment_voucher_item();
                    $payment_voucher_item->load($paid_approved_item->payment_voucher_item_id);
                    $data['withholding_tax'] = $payment_voucher_item->withholding_tax();
                } else if(!($journal_voucher && $payment_voucher) && in_array($row->requisition_approval_id,$requisition_approval->cancelled_approved_payment())){
                    $status = '<span style="font-size: 10px" class="label label-danger">Revoked</span>';
                } else if(($journal_voucher || $payment_voucher) && $paid_approved_item && $amount_to_be_paid > 0) {
                    $status = '<span class="label" style="background-color: #00e765; font-size: 12px;">Partial Payment(s)</span>';
                }

                if($approved_amount > 0) {
                    $rows[] = [
                        custom_standard_date($row->approved_date),
                        'Requisition(Sub Contract Payment)',
                        $sub_contract_requisition->sub_contract_requisition_number(),
                        $cost_center_name,
                        $row->approver_name,
                        $data['currency']->symbol . ' <span class="pull-right">' . number_format($approved_amount) . '</span>',
                        $status,
                        $this->load->view('finance/transactions/approved_cash_requests/approved_cash_list_actions',$data,true)
                    ];
                }

            } else {

                $requisition_approval = new self();
                $requisition_approval->load($row->requisition_approval_id);

                $approved_amount = $requisition_approval->material_items_approved_amount('cash') + $requisition_approval->cash_items_approved_amount($account_id);
                $approved_amount += $requisition_approval->asset_items_approved_amount('cash') + $requisition_approval->service_items_approved_amount('cash');
                $cost_center_name = $requisition_approval->requisition()->cost_center_name();

                $data['payment_voucher'] = $payment_voucher = $requisition_approval->payment_voucher();
                $data['imprest_voucher'] = $imprest_voucher = $requisition_approval->requisition_approval_imprest_voucher();
                $data['amount_to_be_paid'] = $requisition_approval->quantitiy_to_pay();
                $data['requisition_approval'] = $requisition_approval;
                $data['requisition'] = $requisition_approval->requisition();
                $data['requisition_approval_payment_voucher'] = $requisition_approval->requisition_approval_payment_voucher();
                $data['has_stock_items'] = (count($requisition_approval->material_items('cash')) + count($requisition_approval->asset_items('cash'))) > 0;
                $data['is_cancelled'] = $requisition_approval->is_cancelled();
                $data['currency'] = $requisition_approval->requisition()->currency();
                $data['requisition_number'] = $requisition_approval->requisition()->requisition_number();
                $data['paid_items'] = $requisition_approval->payment_vouchers();
                $data['imprest_items'] = $requisition_approval->imprest_vouchers();
                $data['journal_voucher'] = false;
                $data['imprest_voucher_id'] = $imprest_voucher_id = $requisition_approval->imprest_voucher($row->requisition_approval_id);
                $data['approved_print_out_link'] = 'requisitions/preview_approved_cash_requisition/';

                $status = '<span style="font-size: 10px" class="label label-info">Pending</span>';
                if($imprest_voucher_id) {
                    $this->load->model('imprest_voucher');
                    $imprest_voucher = new Imprest_voucher();
                    $imprest_voucher->load($imprest_voucher_id);
                    $retirements = $imprest_voucher->retirements();
                    $data['imprest_voucher'] = $imprest_voucher;
                    if ($retirements) {
                        $data['retirements'] = $retirements;
                    }
                    $status = $imprest_voucher->status();

                } else if(!$imprest_voucher_id && $payment_voucher && $requisition_approval->quantitiy_to_pay() > 0){
                    $status = '<span class="label" style="background-color: #00e765; font-size: 12px;">Partial Payment(s)</span>';
                } else if(!$imprest_voucher_id && $payment_voucher &&  $requisition_approval->quantitiy_to_pay()  <= 0){
                    $status = '<span style="font-size: 10px" class="label label-success">Paid</span>';
                } else if(in_array($row->requisition_approval_id,$requisition_approval->cancelled_approved_payment())){
                    $status = '<span style="font-size: 10px" class="label label-danger">Revoked</span>';
                }

                if($approved_amount > 0) {
                    $req_approved_amount = $requisition_approval->vat_inclusive == "VAT COMPONENT" ? 1.18 * $approved_amount : $approved_amount;
                    $rows[] = [
                        custom_standard_date($row->approved_date),
                        'Requisition',
                        'RQ/' . add_leading_zeros($row->requisition_id),
                        $cost_center_name,
                        $row->approver_name,
                        $data['currency']->symbol . ' <span class="pull-right">' . number_format($req_approved_amount) . '</span>',
                        $status,
                        $this->load->view('finance/transactions/approved_cash_requests/approved_cash_list_actions', $data, true)
                    ];
                }
            }
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function imprest_voucher_object(){
        $this->load->model('imprest_voucher');
        $imp_v = new Imprest_voucher();
        $imp_v->load($this->imprest_voucher($this->{$this::DB_TABLE_PK}));
        return $imp_v;
    }

    public function imprest_voucher($requisition_approval_id,$item_types = ['material','asset','cash','service']){
        $this->load->model([
            'imprest_voucher_material_item',
            'imprest_voucher_asset_item',
            'imprest_voucher_cash_item',
            'imprest_voucher_service_item',
            'requisition_approval_material_item',
            'requisition_approval_asset_item',
            'requisition_approval_cash_item',
            'requisition_approval_service_item',
        ]);

        foreach ($item_types as $item_type){
            $model = 'requisition_approval_'.$item_type.'_item';
            $found_items = $this->$model->get(0,0,['requisition_approval_id'=> $requisition_approval_id]);
            if(!empty($found_items)){
                foreach($found_items as $found_item) {
                    $iv_model = 'imprest_voucher_' . $item_type . '_item';
                    $imprest_voucher_items = $this->$iv_model->get(0,0,['requisition_approval_'.$item_type.'_item_id' => $found_item->{$found_item::DB_TABLE_PK}]);
                    if(!empty($imprest_voucher_items)){
                        foreach ($imprest_voucher_items as $imprest_voucher_item){
                            return $imprest_voucher_item->imprest_voucher_id;
                        }
                    }
                }
            }
        }
    }

    public function total_approved_amount($base_currency = false, $source = 'all'){
        if($base_currency){
            $amount = $this->cash_items_approved_amount(null,true)+$this->material_items_approved_amount($source,true)+$this->asset_items_approved_amount($source,true)+$this->service_items_approved_amount($source,true)+$this->freight+$this->inspection_and_other_charges;
        } else {
            $amount = $this->cash_items_approved_amount()+$this->material_items_approved_amount($source)+$this->asset_items_approved_amount($source)+$this->service_items_approved_amount($source)+$this->freight+$this->inspection_and_other_charges;
        }
        $req_approved_amount = $this->vat_inclusive == "VAT COMPONENT" ? 1.18 * $amount : $amount;
        return $req_approved_amount;
    }

    public function cancelled_approved_payment(){
        $this->load->model('approved_requisition_payment_cancellation');
        $where['requisition_approval_id'] = $this->{$this::DB_TABLE_PK};
        $cancelled_payments = $this->approved_requisition_payment_cancellation->get(0,0,$where);
        $options = [];
        foreach($cancelled_payments as $cancelled_payment){
            $options[] = $cancelled_payment->requisition_approval_id;
        }
        return $options;
    }

    public function is_cancelled(){
        $requisition_approval_id = $this->{$this::DB_TABLE_PK};
        return in_array($requisition_approval_id,$this->cancelled_approved_payment()) ? true : false;
    }

    public function vat_enum_values($field = false)
    {
        $options['NULL'] = 'NONE';
        if ($field == false) {
            $options[0] = 'No';
            $options[1] = 'Yes';
        }else{
            $type = $this->db->query( "SHOW COLUMNS FROM requisition_approvals WHERE Field = '".$field."'" )->row( 0 )->Type;
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $enum = explode("','", $matches[1]);
            $count = 0;
            foreach ($enum AS $item){
                $options[$item] = $item == 'VAT PRICED' ? $item : 'CALCULATE VAT';
            }
        }
        return $options;
    }

    public function approved_cash_requisitions_list_on_dashboard(){

        if($this->session->userdata('payments_data')){
            return json_encode($this->session->userdata('payments_data'));
        }else{

            $this->load->model([
                'purchase_order_payment_request_approval',
                'account',
                'purchase_order_payment_request_approval_invoice_item',
                'sub_contract_payment_requisition_approval',
                'sub_contract_payment_requisition_approval_item',
                'withholding_tax',
                'payment_voucher_item'
            ]);

            $material_where_clause = ' WHERE requisition_approval_material_items.source_type = "cash" AND requisition_approval_material_items.account_id IS NULL AND is_final = "1" AND status = "APPROVED"  ';
            $cash_where_clause = ' WHERE requisition_approval_cash_items.account_id IS NULL AND is_final = "1" AND status = "APPROVED"  ';
            $asset_where_clause = ' WHERE requisition_approval_asset_items.source_type = "cash" AND is_final = "1" AND status = "APPROVED"  ';
            $service_where_clause = ' WHERE requisition_approval_service_items.source_type = "cash" AND is_final = "1" AND status = "APPROVED"  ';
            $invoice_where_clause = ' WHERE is_final = "1" AND status = "APPROVED"  ';
            $non_invoice_where_clause = ' WHERE is_final = "1" AND status = "APPROVED"  ';
            $sub_contract_where_clause = ' WHERE is_final = "1" AND status = "APPROVED"  ';

            $sql = 'SELECT ( 
                             SELECT COUNT(requisition_approval_id) FROM (
                
                                SELECT sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.currency_id, "sub_contract_payment_requisition" AS request_type, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id
                                FROM sub_contract_payment_requisition_approval_items
                                LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                                LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                                '.$sub_contract_where_clause.'
                                
                                UNION 
            
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_invoice" AS request_type, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_invoice_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$invoice_where_clause.'
                                
                                
                                UNION
                                
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_non_invoice" AS request_type, "" AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_cash_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$non_invoice_where_clause.'
                                
                                
                                UNION
            
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_service_items
                                LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$service_where_clause.'
                                
                                UNION
                              
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_asset_items
                                LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$asset_where_clause.'
                                
                                UNION
             
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_material_items
                                LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $material_where_clause . '
            
                                UNION
                                
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_cash_items
                                LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                                LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $cash_where_clause . '
                                
                            ) AS approved_cash_requisitions
                            WHERE ( 
                                   request_type = "requisition" AND requisition_approval_id IN ( SELECT requisition_approval_id FROM requisition_approval_payment_vouchers )
                                )
                             OR ( 
                                   request_type = "payment_request_invoice" AND requisition_approval_id IN ( 
                              
                                       SELECT
                                         CASE WHEN purchase_order_payment_request_approval_id IN (
                                           SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_invoice_items
                                             LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                                             LEFT JOIN invoices ON purchase_order_payment_request_invoice_items.invoice_id = invoices.id
                                             LEFT JOIN invoice_payment_vouchers ON invoices.id = invoice_payment_vouchers.invoice_id
                                           WHERE purchase_order_payment_request_invoice_items.invoice_id = invoice_payment_vouchers.invoice_id
                                         ) THEN purchase_order_payment_request_approval_id
                                         ELSE "NULL" END AS paid_invoice_requests
                                       FROM purchase_order_payment_request_approval_payment_vouchers
                                                                   
                                       )
                                       AND approved_invoice_item_id IN ( SELECT purchase_order_payment_request_approval_invoice_item_id FROM payment_voucher_item_approved_invoice_items )
                                )
                                
                             OR ( 
                                   request_type = "payment_request_non_invoice" AND requisition_approval_id IN (
                                       SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_payment_vouchers
                                   )
                                )
                                    
                             OR (
                                   request_type = "sub_contract_payment_requisition" AND requisition_approval_id IN ( SELECT sub_contract_payment_requisition_approval_id FROM sub_contract_payment_requisition_approval_payment_vouchers )
                                   
                                 )
                                 
                          ) AS PAID, (
                            
                             SELECT COUNT(requisition_approval_id) FROM (
                
                                SELECT sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.currency_id, "sub_contract_payment_requisition" AS request_type, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id
                                FROM sub_contract_payment_requisition_approval_items
                                LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                                LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                                '.$sub_contract_where_clause.'
                                
                                UNION 
            
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_invoice" AS request_type, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_invoice_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$invoice_where_clause.'
                                
                                
                                UNION
                                
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_non_invoice" AS request_type, "" AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_cash_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$non_invoice_where_clause.'
                                
                                
                                UNION
            
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_service_items
                                LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$service_where_clause.'
                                
                                UNION
                              
            
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_asset_items
                                LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$asset_where_clause.'
                                
                                UNION
             
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_material_items
                                LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $material_where_clause . '
            
                                UNION
                                
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_cash_items
                                LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                                LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $cash_where_clause . '
                                
                            ) AS approved_cash_requisitions
                            WHERE ( 
                            request_type = "requisition"
                            AND requisition_approval_id NOT IN ( SELECT requisition_approval_id FROM requisition_approval_payment_vouchers ) 
                            AND requisition_approval_id NOT IN ( SELECT requisition_approval_id FROM approved_requisition_payment_cancellations )
                            AND requisition_approval_id NOT IN ( 
                               SELECT
                                  CASE
                                      WHEN requisition_approval_id IN (
                                             SELECT requisition_approval_id FROM imprest_voucher_material_items 
                                             LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
                                        ) THEN requisition_approval_id
                                
                                       WHEN requisition_approval_id IN (
                                             SELECT requisition_approval_id FROM imprest_voucher_asset_items 
                                             LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
                                        ) THEN requisition_approval_id
                                
                                       WHEN requisition_approval_id IN (
                                             SELECT requisition_approval_id FROM imprest_voucher_service_items 
                                             LEFT JOIN requisition_approval_service_items ON imprest_voucher_service_items.requisition_approval_service_item_id = requisition_approval_service_items.id
                                        ) THEN requisition_approval_id
                                        
                                       WHEN requisition_approval_id IN (
                                             SELECT requisition_approval_id FROM imprest_voucher_cash_items 
                                             LEFT JOIN requisition_approval_cash_items ON imprest_voucher_cash_items.requisition_approval_cash_item_id = requisition_approval_cash_items.id
                                        ) THEN requisition_approval_id
                                    
                                       ELSE "NULL"
                                      END
                                      AS imprests
                                      FROM requisition_approvals
                                     )
                             )
       
                          ) AS PENDING, (
                          
                              SELECT COUNT(requisition_approval_id) FROM (
                
                                SELECT sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.currency_id, "sub_contract_payment_requisition" AS request_type, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id
                                FROM sub_contract_payment_requisition_approval_items
                                LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                                LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                                '.$sub_contract_where_clause.'
                                
                                UNION 
            
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_invoice" AS request_type, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_invoice_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$invoice_where_clause.'
                                
                                
                                UNION
                                
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_non_invoice" AS request_type, "" AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_cash_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$non_invoice_where_clause.'
                                
                                
                                UNION
            
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_service_items
                                LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$service_where_clause.'
                                
                                UNION
                              
            
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_asset_items
                                LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$asset_where_clause.'
                                
                                UNION
             
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_material_items
                                LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $material_where_clause . '
            
                                UNION
                                
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_cash_items
                                LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                                LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $cash_where_clause . '
                                
                            ) AS approved_cash_requisitions
                            WHERE ( 
                            request_type = "payment_request_invoice" AND requisition_approval_id NOT IN ( 
                       
                                SELECT
                                  CASE WHEN purchase_order_payment_request_approval_id IN (
                                    SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_invoice_items
                                      LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                                      LEFT JOIN invoices ON purchase_order_payment_request_invoice_items.invoice_id = invoices.id
                                      LEFT JOIN invoice_payment_vouchers ON invoices.id = invoice_payment_vouchers.invoice_id
                                    WHERE purchase_order_payment_request_invoice_items.invoice_id = invoice_payment_vouchers.invoice_id
                                  ) THEN purchase_order_payment_request_approval_id
                                  ELSE "NULL" END AS paid_invoice_requests
                                FROM purchase_order_payment_request_approval_payment_vouchers
                                                            
                                )
                                AND approved_invoice_item_id NOT IN ( SELECT purchase_order_payment_request_approval_invoice_item_id FROM payment_voucher_item_approved_invoice_items )
                             )
                             OR ( 
                                   request_type = "payment_request_non_invoice" AND requisition_approval_id NOT IN (
                                       SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_payment_vouchers
                                   )
                             )
                             OR (
                                   request_type = "sub_contract_payment_requisition" AND requisition_approval_id NOT IN ( SELECT sub_contract_payment_requisition_approval_id FROM sub_contract_payment_requisition_approval_payment_vouchers )
                                   
                                 )
                          
                          ) AS NOT_PAID, (
                          
                              SELECT COUNT(requisition_approval_id) FROM (
                
                                SELECT sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.currency_id, "sub_contract_payment_requisition" AS request_type, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id
                                FROM sub_contract_payment_requisition_approval_items
                                LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                                LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                                '.$sub_contract_where_clause.'
                                
                                UNION 
            
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_invoice" AS request_type, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_invoice_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$invoice_where_clause.'
                                
                                
                                UNION
                                
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_non_invoice" AS request_type, "" AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_cash_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$non_invoice_where_clause.'
                                
                                
                                UNION
            
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_service_items
                                LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$service_where_clause.'
                                
                                UNION
                              
            
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_asset_items
                                LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$asset_where_clause.'
                                
                                UNION
             
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_material_items
                                LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $material_where_clause . '
            
                                UNION
                                
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_cash_items
                                LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                                LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $cash_where_clause . '
                                
                            ) AS approved_cash_requisitions
                             WHERE request_type = "requisition" AND requisition_approval_id IN ( 
                               SELECT
                                  CASE
                                      WHEN requisition_approval_id IN (
                                            SELECT requisition_approval_id FROM imprest_voucher_material_items 
                                            LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
                                       ) THEN requisition_approval_id
                               
                                      WHEN requisition_approval_id IN (
                                            SELECT requisition_approval_id FROM imprest_voucher_asset_items 
                                            LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
                                       ) THEN requisition_approval_id
                               
                                      WHEN requisition_approval_id IN (
                                            SELECT requisition_approval_id FROM imprest_voucher_service_items 
                                            LEFT JOIN requisition_approval_service_items ON imprest_voucher_service_items.requisition_approval_service_item_id = requisition_approval_service_items.id
                                       ) THEN requisition_approval_id
                                       
                                      WHEN requisition_approval_id IN (
                                            SELECT requisition_approval_id FROM imprest_voucher_cash_items 
                                            LEFT JOIN requisition_approval_cash_items ON imprest_voucher_cash_items.requisition_approval_cash_item_id = requisition_approval_cash_items.id
                                       ) THEN requisition_approval_id
                                  END
                                  AS imprests
                                FROM requisition_approvals
                             )
                          
                          ) AS IMPREST, (
                          
                              SELECT COUNT(requisition_approval_id) FROM (
                
                                SELECT sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.currency_id, "sub_contract_payment_requisition" AS request_type, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id
                                FROM sub_contract_payment_requisition_approval_items
                                LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                                LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                                '.$sub_contract_where_clause.'
                                
                                UNION 
            
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_invoice" AS request_type, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_invoice_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$invoice_where_clause.'
                                
                                
                                UNION
                                
                                SELECT purchase_order_payment_request_approvals.id AS requisition_approval_id, currency_id,"payment_request_non_invoice" AS request_type, "" AS approved_invoice_item_id
                                FROM purchase_order_payment_request_approval_cash_items
                                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                                '.$non_invoice_where_clause.'
                                
                                
                                UNION
            
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_service_items
                                LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$service_where_clause.'
                                
                                UNION
                              
            
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_asset_items
                                LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id '.$asset_where_clause.'
                                
                                UNION
             
                                SELECT requisition_approvals.id AS requisition_approval_id, requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_material_items
                                LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $material_where_clause . '
            
                                UNION
                                
                                SELECT requisition_approvals.id AS requisition_approval_id,  requisitions.currency_id AS currency_id, "requisition" AS request_type, "" AS approved_invoice_item_id
                                FROM requisition_approval_cash_items
                                LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                                LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                                LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
                                LEFT JOIN employees ON requisitions.finalizer_id = employees.employee_id ' . $cash_where_clause . '
                                
                            ) AS approved_cash_requisitions
                            WHERE ( 
                                   request_type = "requisition" AND requisition_approval_id IN ( SELECT requisition_approval_id FROM approved_requisition_payment_cancellations )
                                )
                             OR ( 
                                   request_type = "payment_request_invoice" AND requisition_approval_id IN ( SELECT purchase_order_payment_request_approval_id FROM approved_invoice_payment_cancellations )
                                )  
                             OR (
                                   request_type = "sub_contract_payment_requisition" AND requisition_approval_id IN ( SELECT sub_contract_payment_requisition_approval_id FROM approved_sub_contract_payment_cancellations )
                                   
                                 )
                                  
                          ) AS REVOKED ';

            $query = $this->db->query($sql);

            $values['payments'][] = [
                'name' => 'Pending',
                'data' => [intval($query->row()->PENDING)]
            ];
            $values['payments'][] = [
                'name' => 'Imprest',
                'data' => [intval($query->row()->IMPREST)]
            ];
            $values['payments'][] = [
                'name' => 'Paid',
                'data' => [intval($query->row()->PAID)]
            ];
            $values['payments'][] = [
                'name' => 'Not Paid',
                'data' => [intval($query->row()->NOT_PAID)]
            ];
            $values['payments'][] = [
                'name' => 'Revoked',
                'data' => [intval($query->row()->REVOKED)]
            ];

            $payment_data = [
                'payments_data' => $values
            ];

            $this->session->set_userdata($payment_data);
            return json_encode($values);

        }

    }

    public function quantitiy_to_pay(){
        $item_types = ['material','asset','cash','service'];
        $where = ['requisition_approval_id'=>$this->{$this::DB_TABLE_PK}];
        $total_quantity = 0;
        foreach($item_types as $item_type){
            $model = 'requisition_approval_'.$item_type.'_item';
            $this->load->model($model);
            $items = $this->$model->get(0,0,$where);
            foreach($items as $item){
                $total_quantity += $item->approved_quantity - $item->paid_quantity($item_type);
            }
        }
        return $total_quantity;
    }

}

