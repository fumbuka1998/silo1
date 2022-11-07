<?php

class Project extends MY_Model{

    const DB_TABLE = 'projects';
    const DB_TABLE_PK = 'project_id';
    const COST_TYPES = ['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract'];

    public $category_id;
    public $project_name;
    public $description;
    public $stakeholder_id;
    public $reference_number;
    public $currency_id;
    public $site_location;
    public $start_date;
    public $end_date;
    public $created_by;

    /*************************************************************************
     * GENERAL METHODS
     *************************************************************************/
    public function generated_project_id(){
        $start_year = explode('-',$this->start_date);
        $project_catergory = substr($this->category()->category_name,0,4);
        $project_name = substr($this->project_name,0,20);
        return add_leading_zeros($this->{$this::DB_TABLE_PK}).'/'.$start_year[0].'/'.$project_catergory.'/'.substr(getCapitalLetters($project_name),0,7);
    }

    public function projects_list($limit, $start, $keyword, $order,$category_id = null, $stakeholder_id = null){
    	$sql = 'SELECT COUNT(projects.project_id) AS records_total
            	FROM projects
            	LEFT JOIN project_categories ON projects.category_id = project_categories.category_id';

        $where = '';
        if(!check_permission('Administrative Actions') && !check_privilege('All Projects')){
            $where .= ' 
                    LEFT JOIN project_team_members ON projects.project_id = project_team_members.project_id
                    WHERE (project_team_members.employee_id = "'.$this->session->userdata('employee_id').'" OR (projects.created_by = '. $this->session->userdata('employee_id').' AND project_team_members.employee_id IS NULL))';
        }
        if(!is_null($stakeholder_id)){
            $where .= ($where == '' ? ' WHERE ' : ' AND ').' projects.stakeholder_id = "'.$stakeholder_id.'" ';
        }
        if(!is_null($category_id)){
            $where .= ($where == '' ? ' WHERE ' : ' AND ').' projects.category_id = "'.$category_id.'" ';
        }

        $query = $this->db->query($sql.$where);
        $records_total = $query->row()->records_total;

        $sql = 'SELECT SQL_CALC_FOUND_ROWS projects.*, stakeholder_name, category_name
                FROM projects
                LEFT JOIN stakeholders ON projects.stakeholder_id = stakeholders.stakeholder_id
                LEFT JOIN project_categories ON projects.category_id = project_categories.category_id
                '.$where;

        if($keyword != ''){
            $sql .= ($where == '' ? ' WHERE ' : ' AND ').' (stakeholder_name LIKE "%'.$keyword.'%" OR project_name LIKE "%'.$keyword.'%" OR category_name LIKE "%'.$keyword.'%" OR site_location LIKE "%'.$keyword.'%"  OR start_date LIKE "%'.$keyword.'%"  OR end_date LIKE "%'.$keyword.'%"  OR reference_number LIKE "%'.$keyword.'%") ';
        }
        $order_string = dataTable_order_string(['project_name','category_name','reference_number','stakeholder_name','start_date','end_date','site_location'],$order,'project_name');
        $sql .= " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;
        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach($results as $row){
            $project = new self();
            $project->load($row->project_id);
            if($row->stakeholder_name != null){
                $stakeholder_column_string = check_permission('Clients') ? anchor(base_url('stakeholders/stakeholder_profile/'.$row->stakeholder_id),$row->stakeholder_name) : $row->stakeholder_name;
            } else {
				$stakeholder_column_string = 'N/A';
            }

            if(!is_null($stakeholder_id))
            $rows[] = [
                anchor(base_url('projects/profile/' . $row->project_id), $row->project_name),
                $row->category_name,
                $row->reference_number,
                $row->start_date != '' ? custom_standard_date($row->start_date) : 'N/A',
                $project->completion_date() != '' ? custom_standard_date($project->completion_date()) : 'N/A',
                $project->site_location,
                $project->status(true)
            ];
            else
			$rows[] = [
				anchor(base_url('projects/profile/' . $row->project_id), $row->project_name),
				$row->category_name,
				$row->reference_number,
				$stakeholder_column_string,
				$row->start_date != '' ? custom_standard_date($row->start_date) : 'N/A',
				$project->completion_date() != '' ? custom_standard_date($project->completion_date()) : 'N/A',
				$project->site_location,
				$project->status(true)
			];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function category()
    {
        $this->load->model('project_category');
        $category = new Project_category();
        $category->load($this->category_id);
        return $category;
    }

    public function stakeholder()
    {
        $this->load->model('stakeholder');
        $stakeholder = new Stakeholder();
        $has_stakeholder = $stakeholder->load($this->stakeholder_id);
        return $has_stakeholder ? $stakeholder : false;
    }

    public function status($label = false){
        $this->load->model('project_closure');
        $where['project_id'] = $this->{$this::DB_TABLE_PK};
        $today = date('Y-m-d');
        if($this->project_closure->count_rows($where) > 0){
            $status = 'closed';
            $status = $label ? '<span class="label label-info">'.$status.'</span>' : $status;
        } else if($this->start_date > $today){
            $status = 'due';
            $status = $label ? '<span class="label label-primary">'.$status.'</span>' : $status;
        } else if($this->completion_date() < $today){
            $status = 'overdue';
            $status = $label ? '<span class="label label-warning">'.$status.'</span>' : $status;
        } else {
            $status = 'active';
            $status = $label ? '<span class="label label-success">'.$status.'</span>' : $status;
        }
        return $status;
    }

    public function projects_without_stores(){
        $sql = 'SELECT projects.project_id, project_name
                FROM projects
                LEFT JOIN inventory_locations ON projects.project_id = inventory_locations.project_id
                WHERE inventory_locations.location_id IS NULL
        ';
        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = "&nbsp;";
        foreach($results as $result){
            $options[$result->project_id] = $result->project_name;
        }
        return $options;
    }

    public function related_material_items(){

        $sql = 'SELECT DISTINCT item_id AS material_item_id FROM material_stocks
                WHERE project_id = '.$this->{$this::DB_TABLE_PK}.'
                
                UNION
                
                SELECT DISTINCT material_item_id FROM requisition_material_items
                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                WHERE project_id = '.$this->{$this::DB_TABLE_PK}.'
                
                UNION
                
                SELECT DISTINCT material_item_id FROM material_budgets
                WHERE project_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $results = $query->result();
        $material_items = [];
        if(!empty($results)){
            $this->load->model('material_item');
        }

        foreach ($results as $row){
            $material_item = new Material_item();
            $material_item->load($row->material_item_id);
            $material_items[] = $material_item;
        }
        return $material_items;
    }

    public function activities($keyword = '',$total = false){
        $where = ' project_id = '.$this->{$this::DB_TABLE_PK};
        if($keyword != ''){
            $where .= ' AND activity_name LIKE "%'.$keyword.'%"';
        }
        if(!$total) {
            $this->load->model('activity');
            $sql = 'SELECT activities.activity_id, MIN(tasks.start_date) AS start_date FROM activities
                     LEFT JOIN tasks ON activities.activity_id = tasks.activity_id
                     WHERE '.$where.' GROUP BY activity_id ORDER BY start_date';
            $activities = [];
            $query = $this->db->query($sql);
            $results = $query->result();
            foreach ($results as $row){
                $activity = new Activity();
                $activity->load($row->activity_id);
                $activities[] = $activity;
            }
            return $activities;
        } else {
            return $this->db->where($where)->from('activities')->count_all_results();
        }
    }

    public function location(){
        $this->load->model('inventory_location');
        $where['project_id'] = $this->{$this::DB_TABLE_PK};
        $locations = $this->inventory_location->get(1,0,$where);
        return !empty($locations) ? array_shift($locations) : false;
    }

    public function project_dropdown_options($not_grouped = false,$with_closed = false, $string = false){
        !$string ? $options[''] = '&nbsp;' : $options = '<option value="">&nbsp;</option>';
        if($not_grouped){
            $where = '';
            if($with_closed == false) {
                $where .= ' project_id NOT IN( SELECT project_id FROM project_closures )';
            }
            $projects = $this->get(0, 0, $where, 'project_name');
            foreach ($projects as $project) {
                $project_id = $project->{$project::DB_TABLE_PK};
                !$string ? $options[$project_id] = $project->project_name : $options .= '<option value="'.$project_id.'">'.$project->project_name.'</option>';
            }
        } else {
            $this->load->model(['project_category']);
            $categories = $this->project_category->get(0,0,'','category_name');
            foreach($categories as $category){
                if($with_closed) {
                    $projects = $category->projects();
                } else {
                    $projects = $category->projects(true);
                }
                if(!empty($projects)){
                    if($string){
                        $options .= '<optgroup label="'.$category->category_name.'">';
                    }
                    foreach($projects as $project) {
                        $project_id = $project->{$project::DB_TABLE_PK};
                        !$string ? $options[$category->category_name][$project_id] = $project->project_name : $options .= '<option value="'.$project_id.'">'.$project->project_name.'</option>';
                    }
                    if($string){
                        $options .= '</optgroup>';
                    }
                }
            }
        }
        return $options;
    }

    public function on_going_projects_dropdown(){
        $this->load->model('project');
        $where = ' project_id NOT IN (SELECT project_id FROM project_closures)';
        $on_going_projects = $this->project->get(0,0,$where);
        $options[''] = '&nbsp;';
        foreach ($on_going_projects as $project){
            $category = $project->category();
            $options[$category->category_name][$project->{$project::DB_TABLE_PK}] = $project->project_name;
        }
        return $options;

    }

    public function project_with_no_account_options(){

        $sql = 'SELECT  project_id,project_name FROM projects WHERE project_id NOT IN 
                (SELECT project_id FROM  project_accounts)';

        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = "&nbsp;";
        foreach($results as $result){
            $options[$result->project_id] = $result->project_name;
        }
        return $options;
    }

    /**************************************************
     * REQUISITIONS
     *************************************************/

    public function requisitions($from = null,$to = null, $only_valid = true){
        $this->load->model('requisition');
        $project_id = $this->{$this::DB_TABLE_PK};
        $sql = 'SELECT project_requisitions.requisition_id FROM project_requisitions
              LEFT JOIN requisitions ON project_requisitions.requisition_id = requisitions.requisition_id
              WHERE project_id = '.$project_id;

        if($only_valid){
            $sql .= ' AND status != "DECLINED" AND status != "INCOMPLETE" ';
        }
        if(!is_null($from)){
            $sql .= ' AND request_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND request_date <= "'.$to.'" ';
        }

        $results = $this->db->query($sql)->result();
        $requisitions = [];

        foreach ($results as $row){
            $requisition = new Requisition();
            $requisition->load($row->requisition_id);
            $requisitions[] = $requisition;
        }
        return $requisitions;
    }

    public function total_requested_amount($from = null,$to = null, $only_valid = true){
        $requisitions = $this->requisitions($from,$to,$only_valid);
        $amount = 0;
        foreach ($requisitions as $requisition){
            $amount += $requisition->total_amount_in_base_currency();
        }
        return $amount;
    }

    /**
     * PURCHASE ORDERS
     */

    public function purchase_orders($from = null, $to = null){
        $this->load->model('purchase_order');
        $sql = 'SELECT purchase_orders.order_id FROM purchase_orders
                LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                WHERE project_id = '.$this->{$this::DB_TABLE_PK};
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

    public function approved_cash_commitments($currency_id,$from,$to,$summation_of = false, $print = false)
    {

        if($summation_of){
            $sql = 'SELECT COALESCE(SUM(approved_amount)) AS unpaid_cash_amount FROM (';
        } else {
            $sql = 'SELECT * FROM (';
        }
        $sql .= ' SELECT requisition_approvals.id AS requisition_approval_id, approved_date AS approval_date, CONCAT(first_name," ",last_name) AS approver_name,
                        (
                            (
                              (
                                SELECT COALESCE(SUM(approved_quantity * approved_rate),0) FROM requisition_approval_material_items
                                WHERE requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                                AND source_type = "cash"
                              ) + (
                                SELECT COALESCE(SUM(approved_quantity * approved_rate),0) FROM requisition_approval_asset_items
                                WHERE requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                                AND source_type = "cash"
                              ) + (
                                SELECT COALESCE(SUM(approved_quantity * approved_rate),0) FROM requisition_approval_cash_items
                                WHERE requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                              ) + (
                                SELECT COALESCE(SUM(approved_quantity * approved_rate),0) FROM requisition_approval_service_items
                                WHERE requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                                AND source_type = "cash"
                              )
                            ) * (
                              CASE
                                WHEN requisitions.currency_id = '.$currency_id.'
                                THEN 1
                                ELSE
                                (SELECT exchange_rate FROM exchange_rate_updates WHERE exchange_rate_updates.currency_id = requisitions.currency_id ORDER BY id DESC LIMIT 1)/(SELECT exchange_rate FROM exchange_rate_updates WHERE exchange_rate_updates.currency_id = '.$currency_id.' ORDER BY id DESC LIMIT 1)
                              END
           
                            )
                        ) AS approved_amount, "requisition" AS request_type
                        FROM requisition_approvals
                        LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                        LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                        LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
                        WHERE status = "APPROVED"
                        AND is_final = 1
                        AND project_id = '.$this->{$this::DB_TABLE_PK}.'
                        AND approved_date >= "'.$from.'"
                        AND approved_date <= "'.$to.'"
                ) AS approved_cash_commitments
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
                        OR ( 
                            request_type = "order_payment"
                            AND requisition_approval_id NOT IN ( 
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
                            AND requisition_approval_id NOT IN (SELECT purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_payment_vouchers)
                        )
                        OR (
                              request_type = "scrc_requisition" AND requisition_approval_id NOT IN ( SELECT sub_contract_payment_requisition_approval_id FROM sub_contract_payment_requisition_approval_payment_vouchers )
                        )
                ';

        if(!$summation_of) {

            $query = $this->db->query($sql);
            $results = $query->result();

            $this->load->model(['requisition_approval']);
            $other_commitments = [];
            foreach($results as $result){
                $approval = new Requisition_approval();
                $approval->load($result->requisition_approval_id);
                $pdf_preview = anchor(base_url('requisitions/preview_approved_cash_requisition/'.$result->requisition_approval_id),$approval->requisition()->requisition_number(),'target="_blank"');
                $correspondence_number = $approval->requisition()->requisition_number();

                if($print){
                    $other_commitments[] = [
                        'approval_date' => $result->approval_date,
                        'pdf_preview_link' => $pdf_preview,
                        'approval_requisition_number' => $approval->requisition()->requisition_number(),
                        'correspondence_number' => $correspondence_number,
                        'approver_name' => $result->approver_name,
                        'approved_amount' => $result->approved_amount
                    ];
                }else{
                    $other_commitments[] = [
                        'approval_date' => $result->approval_date,
                        'pdf_preview_link' => $pdf_preview,
                        'approval_requisition_number' => $approval->requisition()->requisition_number(),
                        'approver_name' => $result->approver_name,
                        'approved_amount' => $result->approved_amount
                    ];
                }
            }

            return $other_commitments;
        } else {
            $query = $this->db->query($sql);
            return $query->row()->unpaid_cash_amount;
        }

    }

    public function total_ordered_amount($from = null, $to = null){
        $purchase_orders = $this->purchase_orders($from,$to);
        $amount = 0;
        foreach ($purchase_orders as $order){
            $amount += $order->total_order_in_base_currency();
        }
        return $amount;
    }

    /**
     * GOODS RECEIVED
     */

    public function order_grns($from = null,$to = null){
        $sql = 'SELECT goods_received_notes.grn_id FROM goods_received_notes
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                WHERE project_id = '.$this->{$this::DB_TABLE_PK};

        if(!is_null($from)){
            $sql .= ' AND receive_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND receive_date <= "'.$to.'" ';
        }

        $results = $this->db->query($sql)->result();
        $this->load->model('goods_received_note');
        $grns = [];
        foreach ($results as $row){
            $grn = new Goods_received_note();
            $grn->load($row->grn_id);
            $grns[] = $grn;
        }
        return $grns;
    }

    public function ordered_goods_received_value($from = null,$to = null){
        $amount = 0;
        $grns = $this->order_grns($from,$to);
        foreach ($grns as $grn){
            $amount += $grn->material_value();
        }
        return $amount;
    }

    public function site_goods_received_value($from = null, $to = null){
        $grns = $this->location()->grns($from,$to);
        $amount = 0;
        foreach ($grns as $grn){
            $amount += $grn->material_value();
        }
        return $amount;
    }

    public function imprest_voucher_retirement_grns($from = null,$to = null){

        $sql = 'SELECT imprest_voucher_retirement_grns.grn_id FROM imprest_voucher_retirement_grns
                LEFT JOIN goods_received_notes ON imprest_voucher_retirement_grns.grn_id = goods_received_notes.grn_id
                LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_grns.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                WHERE imprest_voucher_retirement_grns.grn_id IN (
                  SELECT grn_id FROM goods_received_note_material_stock_items
                  LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                  WHERE material_stocks.project_id = '.$this->{$this::DB_TABLE_PK};
        if(!is_null($from) || !is_null($to)){
            $sql .= ' AND date_received >= "'.$from.'"
                                 AND date_received <= "'.$to.'" ';
        }

        $sql .= ' UNION
                
                  SELECT grn_id FROM grn_asset_sub_location_histories
                  LEFT JOIN asset_sub_location_histories ON grn_asset_sub_location_histories.asset_sub_location_history_id = asset_sub_location_histories.id
                  WHERE asset_sub_location_histories.project_id = '.$this->{$this::DB_TABLE_PK};
        if(!is_null($from) || !is_null($to)){
            $sql .= ' AND received_date >= "'.$from.'"
                                  AND received_date <= "'.$to.'" ';
        }

        $sql .= ' )';


        $results = $this->db->query($sql)->result();
        $this->load->model('goods_received_note');
        $grns = [];
        foreach ($results as $row){
            $grn = new Goods_received_note();
            $grn->load($row->grn_id);
            $grns[] = $grn;
        }
        return $grns;
    }

    public function unreceived_goods_value($date = null,$print = false){
        $this->load->model(['purchase_order','currency']);
        $sql = 'SELECT purchase_order_id FROM project_purchase_orders
                LEFT JOIN purchase_orders ON project_purchase_orders.purchase_order_id = purchase_orders.order_id
                WHERE status != "CANCELLED"
                AND status != "CLOSED"';
        if(!is_null($date)){
            $sql .= ' AND issue_date <= "'.$date.'"';
        }
        $sql .= ' AND project_id = '.$this->{$this::DB_TABLE_PK};
        $project_orders = $this->db->query($sql)->result();
        $sql = 'SELECT currency_id FROM currencies WHERE is_native = 1 LIMIT 1';
        $native_currency_id = $this->db->query($sql)->row()->currency_id;
        $native_currency = new Currency();
        $native_currency->load($native_currency_id);
        $data['native_currency'] = $native_currency;
        $unreceived_goods_value = 0;
        $unreceived_goods = $project_orders_arr = [];

        $project = new Self();
        $project->load($this->{$this::DB_TABLE_PK});
        $data['project'] = $project;
        $data['as_of'] = $date;
        foreach($project_orders as $project_order){
            $order = new Purchase_order();
            $order->load($project_order->purchase_order_id);
            $receivable = $order->receivable();
            $unreceived_amount = $order->unreceived_amount();
            if($receivable && $unreceived_amount > 0) {
                $project_orders_arr[] = $project_order->purchase_order_id;
                $balance_in_base_currency = $this->currency->convert($order->currency_id,$native_currency_id,$unreceived_amount);
                $order_currency = new Currency();
                $order_currency->load($order->currency_id);
                $unreceived_goods[$project->project_name][$order->{$order::DB_TABLE_PK}] = [
                    'order_id'=> $order->{$order::DB_TABLE_PK},
                    'order_value'=>$order->order_items_value(),
                    'order_currency'=>$order_currency,
                    'received_value'=>$order->total_received_value(),
                    'unreceived_value'=>$order->unreceived_amount(),
                    'paid_amount'=>$order->amount_paid(false, false, null,$date),
                    'balance_in_base_currency'=>$balance_in_base_currency,
                ];
                $unreceived_goods_value += $balance_in_base_currency;
            }
        }
        $unreceived_goods[$project->project_name]['summation_base_currency'] = $unreceived_goods_value;
        $data['project_orders'] = $project_orders_arr;
        $data['unreceived_goods'] = $unreceived_goods;
        $unreceived_goods_pop_up = $print ? '' : $this->load->view('reports/project_financial_status_unreceived_goods_pop_up', $data, 'true');
        $unreceived_goods[$project->project_name]['pop_up'] = $unreceived_goods_pop_up;
        return $unreceived_goods;
    }

    /******************************************************************************
     * BUDGETS METHODS
     ******************************************************************************/

    public function project_special_budget()
    {
        $this->load->model('project_special_budget');
        $project_special_budgets = $this->project_special_budget->get(1,0,['project_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($project_special_budgets) ? array_shift($project_special_budgets) : new Project_special_budget();
    }

    public function contract_sum(){
        return $this->overal_tasks_amount() + $this->revised_tasks_amount() + $this->contract_sum_variation();
    }

    public function overal_tasks_amount(){
        $sql = 'SELECT COALESCE(SUM(quantity*rate),0) AS contract_sum FROM tasks
                LEFT JOIN activities ON tasks.activity_id = activities.activity_id
                WHERE project_id = '.$this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return $query->row()->contract_sum;
    }

    public function revised_tasks_amount(){
        $sql = 'SELECT (
                            (
                                SELECT COALESCE(SUM(revised_tasks.quantity*revised_tasks.rate),0) FROM revised_tasks
                                LEFT JOIN revision ON revised_tasks.revision_id = revision.id
                                WHERE revision.project_id = "'.$this->{$this::DB_TABLE_PK}.'"
                            )-(
                                SELECT COALESCE(SUM(tasks.quantity*tasks.rate),0) FROM tasks
                                LEFT JOIN activities ON tasks.activity_id = activities.activity_id
                                WHERE project_id = "'.$this->{$this::DB_TABLE_PK}.'" 
                                AND tasks.task_id IN (
                                
                                    SELECT task_id FROM revised_tasks
                                    LEFT JOIN revision ON revised_tasks.revision_id = revision.id
                                    WHERE revision.project_id = "'.$this->{$this::DB_TABLE_PK}.'"
                                )
                            )
                        ) AS revision_contract_sum';

        $query = $this->db->query($sql);
        return $query->row()->revision_contract_sum;
    }

    public function contract_sum_variation(){
        $sql = 'SELECT (
                (
                    SELECT COALESCE(SUM(contract_sum_variation),0) FROM project_contract_reviews
                    WHERE plus_or_minus_contract_sum = "plus" AND project_id =' . $this->{$this::DB_TABLE_PK} . '
                )-(
                    SELECT COALESCE(SUM(contract_sum_variation),0) FROM project_contract_reviews
                    WHERE plus_or_minus_contract_sum = "minus" AND project_id =' . $this->{$this::DB_TABLE_PK} . '
                ) 
                ) AS extended_contract_sum 
               ';

        $query = $this->db->query($sql);
        return $query->row()->extended_contract_sum;
    }

    public function material_budget_item_id($material_id){
        $sql = 'SELECT budget_id FROM material_budgets
                WHERE material_item_id = "'.$material_id.'" AND project_id = "'.$this->{$this::DB_TABLE_PK}.'" AND task_id IS NULL';
        $query = $this->db->query($sql);
        return $query->num_rows() > 0 ? $query->row()->budget_id : null;
    }

    public function miscellaneous_categories(){
        $this->load->model('miscellaneous_budget');
        $miscellaneous_budgets = $this->miscellaneous_budget->get(0,0,['project_id' => $this->{$this::DB_TABLE_PK}]);
        $categories = ['' => '&nbsp;'];
        foreach($miscellaneous_budgets as $miscellaneous_budget){
            $categories[$miscellaneous_budget->{$miscellaneous_budget::DB_TABLE_PK}] = $miscellaneous_budget->budget_name;
        }
        return $categories;
    }

    public function budget_spending_percentage($cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract'],$general_only = false){
        return round(($this->actual_cost($cost_types,null,null,true)/$this->budget_figure($cost_types,$general_only))*100,5);
    }

    public function actual_cost($cost_types = ['material','miscellaneous','permanent_labour','equipment','sub_contract','activities','tasks','casual_labour','imprest'], $from = null, $to = null,$general_only = false){
        if($general_only){
            $level = 'project';
        }else{
            $level = 'project_overall';
        }

        $cost_figure = 0;
        if(is_string($cost_types)){
            $cost_types = [$cost_types];
        }

        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material','permanent_labour'])) {
                $model = $cost_type . '_cost';
                $this->load->model($model);
                $cost_figure += $this->$model->actual_cost($this->{$this::DB_TABLE_PK}, $level,$from, $to);
            } else if(in_array($cost_type,['equipment','casual_labour'])){
                $model = 'project_plan_task_execution_'.$cost_type;
                $this->load->model($model);
                $cost_figure += $this->$model->actual_cost($this->{$this::DB_TABLE_PK}, $level,$from, $to);
            }

            if($cost_type == 'miscellaneous') {
                $this->load->model('payment_voucher_item');
                $cost_figure += $this->payment_voucher_item->cost_figure($this->{$this::DB_TABLE_PK},$level,$from, $to);
            } else if($cost_type == 'sub_contract'){
                $this->load->model('sub_contract_certificate_payment_voucher');
                $cost_figure += $this->sub_contract_certificate_payment_voucher->actual_cost($this->{$this::DB_TABLE_PK}, $level,$from, $to);
            } else if($cost_type == 'imprest'){
                $this->load->model('imprest_voucher');
                $cost_figure += $this->imprest_voucher->cost_figure($this->{$this::DB_TABLE_PK},$level,$from, $to);
            }
        }
        return $cost_figure;
    }

    public function budgeted_figure_work_performed(){
        $this->load->model('project_plan');
        $where['project_id'] = $this->{$this::DB_TABLE_PK};
        $project_plans = $this->project_plan->get(0,0,$where);
        $budgeted_figure = 0;
        foreach ($project_plans as $project_plan){
            $budgeted_figure += $project_plan->performed_budget();
        }
        return $budgeted_figure;
    }

    public function budgeted_figure_work_scheduled(){
        $this->load->model('project_plan');
        $where['project_id'] = $this->{$this::DB_TABLE_PK};
        $project_plans = $this->project_plan->get(0,0,$where);
        $bcws = 0;
        foreach ($project_plans as $project_plan){
            $bcws += $project_plan->planned_budget($project_plan->{$project_plan::DB_TABLE_PK});
        }
        return $bcws;
    }

    public function actual_cost_as_per_contractsum(){
        $this->load->model('activity');
        $where['project_id'] = $this->{$this::DB_TABLE_PK};
        $activities = $this->activity->get(0,0,$where);
        $cost_figure = 0;
        foreach ($activities as $activity){
            $tasks = $activity->tasks();
            foreach($tasks as $task){
                $executed_task_quantity = $task->project_plan_task_execution($task->{$task::DB_TABLE_PK});
                $cost_figure += $executed_task_quantity * $task->rate;
            }
        }
        return $cost_figure;
    }

    public function budget_figure($cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract'], $general_only = false){
        $level = null;
        if($general_only){
            $level = 'project';
        }

        $budget_figure = 0;
        if(is_string($cost_types)){
            $cost_types = [$cost_types];
        }

        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract'])) {
                $model = $cost_type . '_budget';
                $this->load->model($model);
                $budget_figure += $this->$model->budget_figure($this->{$this::DB_TABLE_PK}, $level);
            }
        }
        return $budget_figure > 0 ? $budget_figure : 0.0000000001/10E+300;
    }

    public function budget_figure_at_completion(){
        $this->load->model('activity');
        $where['project_id'] = $this->{$this::DB_TABLE_PK};
        $activities = $this->activity->get(0,0,$where);
        $budget_figure = 0;
        foreach($activities as $activitiy){
            $budget_figure += $activitiy->budget_figure_at_completion();
        }
        return $budget_figure;
    }

    /************************************************************************************************
     * COSTS METHODS
     *******************************************************************************************/

    public function cost_center_options($string = false){
        $sql = 'SELECT activity_name, activity_id FROM activities WHERE project_id = "'.$this->{$this::DB_TABLE_PK}.'"';
        $query = $this->db->query($sql);
        $activities = $query->result();
        $string ? $options = '<option value="">Project Shared</option>' : $options['Project Shared'][''] = 'Project Shared';
        foreach($activities as $activity){
            if($string){
                $options .= '<optgroup label="'.$activity->activity_name.'">';
            }
            $sql = 'SELECT task_id, task_name FROM tasks WHERE activity_id = "'.$activity->activity_id.'"';
            $query = $this->db->query($sql);
            $tasks = $query->result();
            foreach($tasks as $task){
                $string ? $options.= '<option value="'.$task->task_id.'">'.$task->task_name.'</option>' : $options[$activity->activity_name][$task->task_id] = $task->task_name;
            }
            if($string){
                $options .= '</optgroup>';
            }
        }
        return $options;
    }

    public function plan_cost_center_options($string = false){
        $sql = 'SELECT activity_name, activity_id FROM activities WHERE project_id = "'.$this->{$this::DB_TABLE_PK}.'"';
        $query = $this->db->query($sql);
        $activities = $query->result();
        $string ? $options = '<option value=""></option>' : $options[''] = '&nbsp;';
        foreach($activities as $activity){
            if($string){
                $options .= '<optgroup label="'.$activity->activity_name.'">';
            }
            $sql = 'SELECT task_id, task_name FROM tasks WHERE activity_id = "'.$activity->activity_id.'"';
            $query = $this->db->query($sql);
            $tasks = $query->result();
            foreach($tasks as $task){
                $string ? $options.= '<option value="'.$task->task_id.'">'.$task->task_name.'</option>' : $options[$activity->activity_name][$task->task_id] = $task->task_name;
            }
            if($string){
                $options .= '</optgroup>';
            }
        }
        return $options;
    }

    public function miscellaneous_costs_items_list($limit, $start, $keyword, $order, $project_id){
        $order_string = dataTable_order_string(['payment_voucher_no','payment_date','description','amount'],$order,'payment_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;


        $project_where_clause = ' WHERE projects.project_id ='.$project_id.' ';
        $task_where_clause = ' WHERE activities.project_id ='.$project_id.' ';


        $sql = 'SELECT * FROM (

                SELECT payment_voucher_items.payment_voucher_item_id FROM payment_voucher_items
                  LEFT JOIN project_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = project_payment_voucher_items.payment_voucher_item_id
                  LEFT JOIN projects ON project_payment_voucher_items.project_id = projects.project_id
                  '.$project_where_clause.'
                 
                 UNION 
                 
                SELECT payment_voucher_items.payment_voucher_item_id FROM payment_voucher_items
                  lEFT JOIN task_payment_voucher_items ON payment_voucher_items.payment_voucher_item_id = task_payment_voucher_items.payment_voucher_item_id
                  LEFT JOIN tasks ON task_payment_voucher_items.task_id = tasks.task_id
                  LEFT JOIN activities ON tasks.activity_id = activities.activity_id
                  '.$task_where_clause.'
 
                ) AS records_total ';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();


        if ($keyword != '') {
            $project_where_clause .= ' AND (payment_voucher_items.payment_voucher_id LIKE "%'.$keyword.'%" OR payment_date LIKE "%'.$keyword.'%" OR payment_voucher_items.description LIKE "%'.$keyword.'%" OR amount LIKE "%'.$keyword.'%" )';

            $task_where_clause .= ' AND (payment_voucher_items.payment_voucher_id LIKE "%'.$keyword.'%" OR payment_date LIKE "%'.$keyword.'%" OR payment_voucher_items.description LIKE "%'.$keyword.'%" OR amount LIKE "%'.$keyword.'%" )';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS * FROM (
 
                SELECT payment_date, payment_vouchers.payment_voucher_id AS payment_voucher_no, payment_voucher_items.description AS description, "Project Shared" AS cost_center, symbol, payment_voucher_items.amount AS amount, exchange_rate FROM project_payment_voucher_items
                LEFT JOIN payment_voucher_items ON project_payment_voucher_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id
                LEFT JOIN projects ON project_payment_voucher_items.project_id = projects.project_id
                '.$project_where_clause.'
                
                UNION
                
                SELECT payment_date, payment_vouchers.payment_voucher_id AS payment_voucher_no, payment_voucher_items.description AS description, tasks.task_name AS cost_center, symbol, payment_voucher_items.amount AS amount, exchange_rate FROM task_payment_voucher_items
                LEFT JOIN payment_voucher_items ON task_payment_voucher_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_item_id = payment_vouchers.payment_voucher_id
                LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id
                LEFT JOIN tasks ON task_payment_voucher_items.task_id = tasks.task_id
                LEFT JOIN activities ON tasks.activity_id = activities.activity_id
                '.$task_where_clause.'
                
                ) AS approved_project_payments'.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;


        $rows = [];
        foreach ($results as $row){

            $rows[] = [
                custom_standard_date($row->payment_date),
                'PV.No.'.add_leading_zeros($row->payment_voucher_no),
                $row->description,
                $row->cost_center,
                '<span class="pull-right">'.$row->symbol.' '.number_format($row->amount).'</span>',
            ];

        }


        $project = new self();
        $project->load($project_id);

        $json = [
            "total_amount" => $project->actual_cost(['miscellaneous']),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function project_approved_payments($from,$to,$approved = false){
        if($approved){
            $sql ='SELECT * FROM (
                    
                    
                    SELECT "INVOICE" AS payment_nature, purchase_order_payment_request_approvals.id AS payment_approval_id, purchase_order_payment_requests.id AS requisition_id, purchase_order_payment_requests.currency_id AS currency_id, approval_date AS approved_date, 
                    CONCAT(first_name," ",last_name) as approver_name
                    FROM purchase_order_payment_request_approval_invoice_items
                    LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                    LEFT JOIN employees ON purchase_order_payment_request_approvals.created_by = employees.employee_id
                    WHERE is_final = "1" 
                    AND purchase_order_payment_requests.status = "APPROVED"
                    AND project_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND approval_date >= "'.$from.'"
                    AND approval_date <= "'.$to.'" 
                    
                    UNION
                    
                    SELECT "NON INVOICE" AS payment_nature, purchase_order_payment_request_approvals.id AS payment_approval_id, purchase_order_payment_requests.id AS requisition_id, purchase_order_payment_requests.currency_id AS currency_id, approval_date AS approved_date, 
                    CONCAT(first_name," ",last_name) as approver_name
                    FROM purchase_order_payment_request_approval_cash_items
                    LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                    LEFT JOIN employees ON purchase_order_payment_request_approvals.created_by = employees.employee_id
                    WHERE is_final = "1" 
                    AND purchase_order_payment_requests.status = "APPROVED"
                    AND project_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND approval_date >= "'.$from.'"
                    AND approval_date <= "'.$to.'" 
                    
                    UNION
 
                    SELECT "REQUISITION" AS payment_nature, requisition_approvals.id AS payment_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id ,approved_date,
                    CONCAT(first_name," ",last_name) as approver_name
                    FROM requisition_approval_service_items
                    LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                    LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                    LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
                    WHERE requisition_approval_service_items.source_type = "cash" 
                    AND is_final = "1" 
                    AND requisitions.status = "APPROVED"  
                    AND project_id= '.$this->{$this::DB_TABLE_PK}.'
                    AND approved_date >= "'.$from.'"
                    AND approved_date <= "'.$to.'"
                    
                    UNION
                    
                    SELECT "REQUISITION" AS payment_nature, requisition_approvals.id AS payment_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id ,approved_date,
                    CONCAT(first_name," ",last_name) as approver_name
                    FROM requisition_approval_asset_items
                    LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                    LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                    LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
                    WHERE requisition_approval_asset_items.source_type = "cash" 
                    AND is_final = "1" 
                    AND requisitions.status = "APPROVED"  
                    AND project_id= '.$this->{$this::DB_TABLE_PK}.'
                    AND approved_date >= "'.$from.'"
                    AND approved_date <= "'.$to.'"
                    
                    UNION
 
                    SELECT "REQUISITION" AS payment_nature, requisition_approvals.id AS payment_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id, approved_date,
                    CONCAT(first_name," ",last_name) as approver_name
                    FROM requisition_approval_material_items
                    LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                    LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                    LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                    LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
                    WHERE requisition_approval_material_items.source_type = "cash" 
                    AND is_final = "1" 
                    AND requisitions.status = "APPROVED" 
                    AND project_id= '.$this->{$this::DB_TABLE_PK}.'
                    AND approved_date >= "'.$from.'"
                    AND approved_date <= "'.$to.'"

                    UNION
                    
                    SELECT "REQUISITION" AS payment_nature, requisition_approvals.id AS payment_approval_id, requisition_approvals.requisition_id, requisitions.currency_id AS currency_id ,approved_date,
                    CONCAT(first_name," ",last_name) as approver_name
                    FROM requisition_approval_cash_items
                    LEFT JOIN requisition_approvals ON requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                    LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
                    LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                    LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
                    WHERE is_final = "1" 
                    AND requisitions.status = "APPROVED" 
                    AND project_id= '.$this->{$this::DB_TABLE_PK}.'
                    AND approved_date >= "'.$from.'"
                    AND approved_date <= "'.$to.'"

                    UNION
                    
                    SELECT "SUB CONTRACT REQUISITION" AS payment_nature, sub_contract_payment_requisition_approvals.id AS payment_approval_id, sub_contract_payment_requisition_approvals.sub_contract_requisition_id AS requisition_id, sub_contract_payment_requisitions.currency_id AS currency_id ,sub_contract_payment_requisition_approvals.approval_date AS approved_date,
                     CONCAT(first_name," ",last_name) as approver_name
                    FROM sub_contract_payment_requisition_approval_items
                    LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                    LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                    LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id
                    LEFT JOIN sub_contract_certificates ON sub_contract_payment_requisition_items.certificate_id = sub_contract_certificates.id
                    LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                    LEFT JOIN employees ON sub_contract_certificates.created_by = employees.employee_id
                    WHERE is_final = "1"
                    AND sub_contract_payment_requisitions.status = "APPROVED" 
                    AND project_id= '.$this->{$this::DB_TABLE_PK}.'
                    AND approval_date >= "'.$from.'"
                    AND approval_date <= "'.$to.'"
                    
                ) AS project_approved_payments
                ORDER BY approved_date ASC';

        } else {

            $sql ='SELECT * FROM (
                    
                    SELECT "INVOICE" AS payment_nature, payment_vouchers.payment_voucher_id AS payment_id, payment_vouchers.currency_id AS currency_id,
                    (
                     SELECT COALESCE(SUM(amount*exchange_rate)) FROM payment_voucher_items
                     WHERE payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    )AS amount, payment_date
                    FROM payment_vouchers
                    LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                    LEFT JOIN invoices ON invoice_payment_vouchers.invoice_id = invoices.id
                    LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                    LEFT JOIN purchase_orders ON purchase_order_invoices.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                    WHERE project_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND payment_date >= "'.$from.'"
                    AND payment_date <= "'.$to.'"
                    
                    UNION
                    
                    SELECT "REQUISITION" AS payment_nature, payment_vouchers.payment_voucher_id AS payment_id, payment_vouchers.currency_id AS currency_id,
                      (
                        SELECT COALESCE(SUM(amount*exchange_rate)) FROM payment_voucher_items 
                        WHERE payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                      )AS amount, payment_date
                    FROM payment_vouchers
                    LEFT JOIN requisition_approval_payment_vouchers ON payment_vouchers.payment_voucher_id = requisition_approval_payment_vouchers.payment_voucher_id
                    LEFT JOIN requisition_approvals ON requisition_approval_payment_vouchers.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                    LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                    WHERE is_final = "1"
                    AND requisitions.status = "APPROVED"
                    AND project_id= '.$this->{$this::DB_TABLE_PK}.'
                    AND payment_date >= "'.$from.'"
                    AND payment_date <= "'.$to.'"
                    
                    UNION
                    
                    SELECT "SUB CONTRACT REQUISITION" AS payment_nature, payment_vouchers.payment_voucher_id AS payment_id, payment_vouchers.currency_id AS currency_id,
                    (
                     SELECT COALESCE(SUM((amount + withheld_amount)*exchange_rate)) FROM payment_voucher_items
                     LEFT JOIN withholding_taxes ON payment_voucher_items.payment_voucher_item_id = withholding_taxes.payment_voucher_item_id
                     WHERE payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    )AS amount, payment_date
                    FROM payment_vouchers
                    LEFT JOIN sub_contract_payment_requisition_approval_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_payment_requisition_approval_payment_vouchers.payment_voucher_id
                    LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_payment_vouchers.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                    LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                    LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                    LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
                    LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                    WHERE is_final = "1"
                    AND sub_contract_payment_requisitions.status = "APPROVED"
                    AND project_id= '.$this->{$this::DB_TABLE_PK}.'
                    AND payment_date >= "'.$from.'"
                    AND payment_date <= "'.$to.'"
                    
                ) AS project_approved_payments
                ORDER BY payment_date ASC';
        }

        $query = $this->db->query($sql);
        $results = $query->result();

        $this->load->model([
            'purchase_order_payment_request_approval',
            'requisition_approval',
            'payment_voucher'
        ]);

        $payments = [];
        foreach ($results as $row) {
            if($approved) {
                if ($row->payment_nature == 'INVOICE') {
                    $approved_payment = new Purchase_order_payment_request_approval();
                    $approved_payment->load($row->payment_approval_id);
                    $payment_request = $approved_payment->purchase_order_payment_request();
                    $amount = $approved_payment->total_approved_amount(true);
                    $reference = anchor(base_url('procurements/preview_approved_purchase_order_payments/' . $approved_payment->{$approved_payment::DB_TABLE_PK}), $payment_request->request_number(), ' target="_blank" ');

                } else if ($row->payment_nature == 'NON INVOICE') {
                    $approved_payment = new Purchase_order_payment_request_approval();
                    $approved_payment->load($row->payment_approval_id);
                    $payment_request = $approved_payment->purchase_order_payment_request();
                    $amount = $approved_payment->total_approved_amount(true);
                    $reference = anchor(base_url('procurements/preview_approved_purchase_order_payments/' . $approved_payment->{$approved_payment::DB_TABLE_PK}), $payment_request->request_number(), ' target="_blank" ');

                } else if ($row->payment_nature == 'REQUISITION') {
                    $approved_payment = new Requisition_approval();
                    $approved_payment->load($row->payment_approval_id);
                    $requisition = $approved_payment->requisition();
                    $amount = $approved_payment->total_approved_amount(true,'cash');
                    $reference = anchor(base_url('requisitions/preview_approved_cash_requisition/' . $approved_payment->{$approved_payment::DB_TABLE_PK}), $requisition->requisition_number(), ' target="_blank" ');
                }

                if($amount > 0) {
                    $payments[] = [
                        'approved_date' => $row->approved_date,
                        'payment_nature' => $row->payment_nature,
                        'reference' => $reference,
                        'amount' => $amount
                    ];
                }

            } else {

                $payment_voucher = new Payment_voucher();
                $payment_voucher->load($row->payment_id);

                if($row->amount > 0){
                    $payments[] = [
                        'approved_date' => $row->payment_date,
                        'payment_nature' => $row->payment_nature,
                        'reference' => anchor(base_url('finance/preview_payment_voucher/' . $payment_voucher->{$payment_voucher::DB_TABLE_PK}), 'PV No.'.$payment_voucher->payment_voucher_number(), ' target="_blank" '),
                        'amount' => $row->amount
                    ];
                }
            }
        }

        return $payments;
    }

    public function material_cost($general_only = false,$from = null,$to = null){
        $this->load->model('material_cost');
        $level = null;
        if($general_only){
            $level = 'project';
        }
        return $this->material_cost->actual_cost($this->{$this::DB_TABLE_PK},$level,$from,$to);
    }

    public function permanent_labour_cost($general_only = false,$from = null, $to = null){
        $project_id = $this->{$this::DB_TABLE_PK};
        if($general_only){
            $level = 'project';
        }else{
            $level = 'project_overall';
        }
        $this->load->model('permanent_labour_cost');
        return $this->permanent_labour_cost->actual_cost($project_id, $level,$from, $to);
    }

    public function miscellaneous_cost($general_only = false,$from = null, $to = null){
        if($general_only){
            $level = 'project';
        }else{
            $level = 'project_overall';
        }
        $this->load->model('payment_voucher_item');
        $project_id = $this->{$this::DB_TABLE_PK};
        return $this->payment_voucher_item->cost_figure($project_id,$level,$from, $to);

    }

    public function equipment_cost($general_only = false,$from = null,$to = null){
        if($general_only){
            $level = 'project';
        }else{
            $level = 'project_overall';
        }
        $this->load->model('project_plan_task_execution_equipment');
        return $this->project_plan_task_execution_equipment->actual_cost($this->{$this::DB_TABLE_PK}, $level,$from, $to);
    }

    public function casual_labour($general_only = false,$from = null,$to = null){
        if($general_only){
            $level = 'project';
        }else{
            $level = 'project_overall';
        }
        $this->load->model('project_plan_task_execution_casual_labour');
        return $this->project_plan_task_execution_casual_labour->actual_cost($this->{$this::DB_TABLE_PK}, $level,$from, $to);
    }

    public function sub_contract($general_only = false,$from = null,$to = null){
        if($general_only){
            $level = 'project';
        }else{
            $level = 'project_overall';
        }
        $this->load->model('sub_contract_certificate_payment_voucher');
        return $this->sub_contract_certificate_payment_voucher->actual_cost($this->{$this::DB_TABLE_PK}, $level,$from, $to);
    }

    public function imprest($general_only = false,$from = null,$to = null){
        if($general_only){
            $level = 'project';
        }else{
            $level = 'project_overall';
        }
        $this->load->model('imprest_voucher');
        return $this->imprest_voucher->cost_figure($this->{$this::DB_TABLE_PK},$level,$from, $to);
    }

    public function equipment_and_material($general_only = false,$from = null,$to = null){
        return $this->material_cost($general_only,$from,$to) + $this->equipment_cost($general_only,$from,$to);
    }

    public function labour($general_only = false,$from = null,$to = null){
        return $this->permanent_labour_cost($general_only,$from,$to) + $this->casual_labour($general_only,$from,$to) + $this->sub_contract($general_only,$from,$to);
    }

    public function overheads($date = null, $print = false){
        $this->load->model(['currency','requisition']);
        $sql ='SELECT approved_date, requisitions.requisition_id, requisition_approval_id, requisitions.currency_id AS requested_currency_id, payment_vouchers.currency_id AS payment_currency_id, (
                    (
                        SELECT COALESCE(SUM(
                                requested_quantity*(
                                    CASE 
                                        WHEN requisitions.vat_inclusive = "VAT COMPONENT" THEN requested_rate*(1+(0.01*requisitions.vat_percentage))
                                        ELSE requested_rate*1
                                    END
                                )
                            ),0
                        ) 
                        FROM requisition_cash_items 
                        WHERE requisition_cash_items.requisition_id = requisitions.requisition_id
                    ) + (
                        SELECT COALESCE(SUM(
                                requested_quantity*(
                                    CASE 
                                        WHEN requisitions.vat_inclusive = "VAT COMPONENT" THEN requested_rate*(1+(0.01*requisitions.vat_percentage))
                                        ELSE requested_rate*1
                                    END
                                )
                            ),0
                        ) 
                        FROM requisition_service_items 
                        WHERE requisition_service_items.requisition_id = requisitions.requisition_id
                        AND source_type = "cash"
                    )
                ) AS requested_amount, (
                    (
                        SELECT COALESCE(SUM(
                                approved_quantity*(
                                    CASE 
                                        WHEN requisition_approvals.vat_inclusive = "VAT COMPONENT" THEN approved_rate*(1+(0.01*requisition_approvals.vat_percentage))
                                        ELSE approved_rate*1
                                    END
                                )
                            ),0
                        ) 
                        FROM requisition_approval_cash_items 
                        WHERE requisition_approval_cash_items.requisition_approval_id = requisition_approvals.id
                    ) + (
                        SELECT COALESCE(SUM(
                            approved_quantity*(
                                CASE 
                                    WHEN requisition_approvals.vat_inclusive = "VAT COMPONENT" THEN approved_rate*(1+(0.01*requisition_approvals.vat_percentage))
                                    ELSE approved_rate*1
                                    END
                            )
                        ),0
                    ) 
                    FROM requisition_approval_service_items 
                    WHERE requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                    AND source_type = "cash"
                    )
                ) AS approved_amount, requisition_approval_payment_vouchers.payment_voucher_id, (
                   SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items WHERE payment_voucher_items.payment_voucher_id = requisition_approval_payment_vouchers.payment_voucher_id
                ) AS paid_amount, exchange_rate
                FROM payment_vouchers
                LEFT JOIN requisition_approval_payment_vouchers ON payment_vouchers.payment_voucher_id = requisition_approval_payment_vouchers.payment_voucher_id
                LEFT JOIN requisition_approvals ON requisition_approval_payment_vouchers.requisition_approval_id = requisition_approvals.id
                LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                WHERE status = "APPROVED"
                AND requisitions.requisition_id NOT IN (
                    SELECT requisition_id FROM requisition_material_items
                    UNION
                    SELECT requisition_id FROM requisition_asset_items
                )
                AND is_final = 1';
        if(!is_null($date)){
            $sql .= ' AND approved_date <= "'.$date.'"';
        }
        $sql .= ' AND project_id = '.$this->{$this::DB_TABLE_PK}.'';
        $project_overheads = $this->db->query($sql)->result();
        $sql = 'SELECT currency_id FROM currencies WHERE is_native = 1 LIMIT 1';
        $native_currency_id = $this->db->query($sql)->row()->currency_id;
        $native_currency = new Currency();
        $native_currency->load($native_currency_id);
        $data['native_currency'] = $native_currency;
        $total_requested_amount = $total_approved_amount = $total_paid_amount = 0;
        $overheads = $overheads_arr = [];

        $project = new Self();
        $project->load($this->{$this::DB_TABLE_PK});
        $data['project'] = $project;
        if(!empty($project_overheads)){
            foreach($project_overheads as $project_overhead){
                $requisition = new Requisition();
                $requisition->load($project_overhead->requisition_id);
                $overheads_arr[] = $requisition;
                $request_currency = new Currency();
                $request_currency->load($project_overhead->requested_currency_id);
                $payment_currency = new Currency();
                $payment_currency->load($project_overhead->payment_currency_id);

                $total_paid_amount += $project_overhead->paid_amount*$project_overhead->exchange_rate;
                $total_requested_amount += $project_overhead->requested_amount*$request_currency->rate_to_native($project_overhead->approved_date);
                $total_approved_amount += $project_overhead->approved_amount*$request_currency->rate_to_native($project_overhead->approved_date);
                $overheads[$project->project_name][$requisition->{$requisition::DB_TABLE_PK}] = [
                    'approved_date'=>$project_overhead->approved_date,
                    'request_currency'=>$request_currency,
                    'requisition_id'=>$project_overhead->requisition_id,
                    'requisition_approval_id'=>$project_overhead->requisition_approval_id,
                    'requested_amount'=>$project_overhead->requested_amount,
                    'approved_amount'=>$project_overhead->approved_amount,
                    'payment_voucher_id'=>$project_overhead->payment_voucher_id,
                    'payment_currency'=>$payment_currency,
                    'paid_amount'=>$project_overhead->paid_amount,
                    'paid_amount_base_currency'=>($project_overhead->paid_amount*$project_overhead->exchange_rate),
                    'exchange_rate'=>$project_overhead->exchange_rate,
                ];
            }
        }
        $overheads[$project->project_name]['total_paid_amount'] = $total_paid_amount;
        $overheads[$project->project_name]['total_requested_amount'] = $total_requested_amount;
        $overheads[$project->project_name]['total_approved_amount'] = $total_approved_amount;
        $data['overheads_arr'] = $overheads_arr;
        $data['overheads'] = $overheads;
        $overheads_pop_up = $print ? '' : $this->load->view('reports/project_financial_status_overheads_pop_up',$data,true);
        $overheads[$project->project_name]['pop_up'] = $overheads_pop_up;
        return $overheads;

    }

    /************************************************************************************************
     * PAYMENTS METHODS
     *******************************************************************************************/
    public function total_payments($from = null,$to = null,$list = false, $print = false){
        if($list){
            $sql = 'SELECT * FROM (';
        } else {
            $sql = 'SELECT COALESCE(SUM(amount),0) AS project_total_payments FROM (';
        }

        $sql .= ' SELECT "INVOICE" AS payment_nature,  CONCAT(purchase_orders.comments," - ","PO/",purchase_orders.order_id) AS comments, payment_vouchers.payment_voucher_id AS payment_id, payment_vouchers.currency_id AS currency_id,
            (
            SELECT COALESCE(SUM(amount*exchange_rate)) FROM payment_voucher_items
            WHERE payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
            )AS amount, payment_date
            FROM payment_vouchers
            LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
            LEFT JOIN invoices ON invoice_payment_vouchers.invoice_id = invoices.id
            LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
            LEFT JOIN purchase_orders ON purchase_order_invoices.purchase_order_id = purchase_orders.order_id
            LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
            WHERE project_id = '.$this->{$this::DB_TABLE_PK}.'
            AND payment_date >= "'.$from.'"
            AND payment_date <= "'.$to.'"
            
            UNION
            
            SELECT "REQUISITION" AS payment_nature, CONCAT(requisitions.requesting_comments," - ","RQ/",requisitions.requisition_id) AS comments, payment_vouchers.payment_voucher_id AS payment_id, payment_vouchers.currency_id AS currency_id,
            (
            SELECT COALESCE(SUM(amount*exchange_rate)) FROM payment_voucher_items 
            WHERE payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
            )AS amount, payment_date
            FROM payment_vouchers
            LEFT JOIN requisition_approval_payment_vouchers ON payment_vouchers.payment_voucher_id = requisition_approval_payment_vouchers.payment_voucher_id
            LEFT JOIN requisition_approvals ON requisition_approval_payment_vouchers.requisition_approval_id = requisition_approvals.id
            LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
            LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
            WHERE is_final = "1"
            AND requisitions.status = "APPROVED"
            AND project_id= '.$this->{$this::DB_TABLE_PK}.'
            AND payment_date >= "'.$from.'"
            AND payment_date <= "'.$to.'"
            
            UNION
            
            SELECT "SUB CONTRACT REQUISITION" AS payment_nature, CONCAT(sub_contract_payment_requisitions.requesting_comments," - ","SC-RQ/",sub_contract_payment_requisitions.sub_contract_requisition_id) AS comments, payment_vouchers.payment_voucher_id AS payment_id, payment_vouchers.currency_id AS currency_id,
            (
            SELECT COALESCE(SUM((amount + withheld_amount)*exchange_rate)) FROM payment_voucher_items
            LEFT JOIN withholding_taxes ON payment_voucher_items.payment_voucher_item_id = withholding_taxes.payment_voucher_item_id
            WHERE payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
            ) AS amount, payment_date
            FROM payment_vouchers
            LEFT JOIN sub_contract_payment_requisition_approval_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_payment_requisition_approval_payment_vouchers.payment_voucher_id
            LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_payment_vouchers.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
            LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
            LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
            LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
            LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
            WHERE is_final = "1"
            AND sub_contract_payment_requisitions.status = "APPROVED"
            AND project_id= '.$this->{$this::DB_TABLE_PK}.'
            AND payment_date >= "'.$from.'"
            AND payment_date <= "'.$to.'"
            
            ) AS project_approved_payments
            ORDER BY payment_date ASC';

        $query = $this->db->query($sql);
        if($list){
            $results = $query->result();

            $this->load->model([
                'purchase_order_payment_request_approval',
                'requisition_approval',
                'payment_voucher'
            ]);
            $payments = [];
            foreach ($results as $row) {
                $payment_voucher = new Payment_voucher();
                $payment_voucher->load($row->payment_id);

                if($row->amount > 0){

                    if($print){
                        $payments[] = [
                            'approved_date' => $row->payment_date,
                            'payment_nature' => $row->payment_nature,
                            'comments' => $row->comments,
                            'reference' => anchor(base_url('finance/preview_payment_voucher/' . $payment_voucher->{$payment_voucher::DB_TABLE_PK}), 'PV No.'.$payment_voucher->payment_voucher_number(), ' target="_blank" '),
                            'reference_without_anchor' => 'PV No.'.$payment_voucher->payment_voucher_number(),
                            'amount' => $row->amount,
                            'currency_symbol' => $payment_voucher->currency()->symbol
                        ];
                    }else{
                        $payments[] = [
                            'approved_date' => $row->payment_date,
                            'payment_nature' => $row->payment_nature,
                            'comments' => $row->comments,
                            'reference' => anchor(base_url('finance/preview_payment_voucher/' . $payment_voucher->{$payment_voucher::DB_TABLE_PK}), 'PV No.'.$payment_voucher->payment_voucher_number(), ' target="_blank" '),
                            'amount' => $row->amount,
                            'currency_symbol' => $payment_voucher->currency()->symbol
                        ];
                    }



                }
            }

            return $payments;
        } else {
            return $query->row()->project_total_payments;
        }
    }

    /********************************************************************************************
     * LABOUR METHODS
     *******************************************************************************************/

    public function team_member_access(){
        $this->db->select('member_id');
        $this->db->where(['project_id' => $this->{$this::DB_TABLE_PK},'employee_id' => $this->session->userdata('employee_id')]);
        $query = $this->db->get('project_team_members');
        $num_rows = $query->num_rows();
        return ($num_rows > 0);
    }

    public function manager_access(){
        $this->db->select('member_id');
        $this->db->where([
            'project_id' => $this->{$this::DB_TABLE_PK},
            'manager_access' => '1',
            'employee_id' => $this->session->userdata('employee_id')
        ]);
        $query = $this->db->get('project_team_members');
        $num_rows = $query->num_rows();
        return ($num_rows > 0);
    }

    public function allowed_access(){
        return check_permission('Administrative Actions') || check_permission('All Projects') || $this->team_member_access() || $this->created_by = $this->session->userdata('employee_id');
    }

    public function project_manager_employees_options($project_id){
        $this->load->model('department');
        $options = '<option value="">&nbsp;</option>';
        $departments = $this->department->get();
        foreach($departments as $department){
            $sql = 'SELECT employees.employee_id,CONCAT(first_name," ",middle_name," ",last_name) AS full_name FROM employees
                    LEFT JOIN project_team_members ON employees.employee_id = project_team_members.employee_id
                    WHERE project_team_members.project_id = "'.$project_id.'" AND employees.employee_id NOT IN (
                        SELECT employees.employee_id FROM employees
                        LEFT JOIN project_managers ON employees.employee_id = project_managers.employee_id
                        WHERE project_managers.project_id = "' . $project_id . '"
                    ) AND department_id = "'.$department->{$department::DB_TABLE_PK}.'"';
            $query = $this->db->query($sql);
            if($query->num_rows() > 0){
                $options .= '<optgroup label="'.$department->department_name.'">';
                $employees = $query->result();
                foreach($employees as $employee){
                    $options .= '<option value="'.$employee->employee_id.'">'.$employee->full_name.'</option>';
                }
                $options .= '</optgroup>';
            }
        }
        return $options;
    }


    /*************************************************************************************************
     * ACCOUNTS METHODS
     ***********************************************************************************************/

    public function project_accounts()
    {
        $this->load->model('project_account');
        $account_junctions = $this->project_account->get(0,0,['project_id' => $this->{$this::DB_TABLE_PK}]);
        $accounts = [];
        foreach ($account_junctions as $junction){
            $accounts[] = $junction->account();
        }
        return $accounts;
    }

    public function project_account_options()
    {
        $options = [];
        $accounts = $this->project_accounts();
        foreach ($accounts as $account){
            $options[$account->{$account::DB_TABLE_PK}] = $account->account_name;
        }
        return $options;
    }

    /*********************************************************************************************
     * TIME METHODS
     *********************************************************************************************/

    public function timeline_percentage($date = null){
        $date = $date != null ? $date : date('Y-m-d');
        $duration = $this->duration();
        $elapsed = $this->elapsed_days($date);
        return $elapsed != 0 ? round($elapsed*100/$duration,2) : 0;
    }

    public function completion_percentage(){
        $actual_cost_as_per_contractsum = $this->actual_cost_as_per_contractsum();
        $contract_sum = $this->contract_sum();
        return $contract_sum != 0 ? ($actual_cost_as_per_contractsum / $contract_sum) * 100 : 0;
    }

    public function elapsed_days($date = null){
        $date = $date != null ? $date : date('Y-m-d');
        if($date > $this->start_date) {
            $elapsed_days = number_of_days($this->start_date, $date);
        } else {
            $elapsed_days = 0;
        }
        return $elapsed_days >= 0 ? $elapsed_days : 0;
    }

    public function duration(){
        $duration = number_of_days($this->start_date.' 00:00',$this->completion_date().' 23:59');
        return $duration >= 0 ? $duration : 0;
    }

    public function final_duration(){
        $duration = number_of_days($this->start_date.' 00:00',$this->completion_date().' 23:59');
        return $duration >= 0 ? $duration : 0;
    }

    public function completion_date()
    {
        $this->load->model('project_contract_review');
        $extensions = $this->project_contract_review->get(0,0,['project_id' => $this->{$this::DB_TABLE_PK}],' review_date ASC');
        $completion_date = $this->end_date;
        foreach ($extensions as $extension){
            if($extension->plus_or_minus_duration == 'plus'){
                $completion_date = strftime('%Y-%m-%d',strtotime($completion_date.' + '.$extension->duration_variation.' '.$extension->duration_type));
            }else if($extension->plus_or_minus_duration == 'minus'){
                $completion_date = strftime('%Y-%m-%d',strtotime($completion_date.' - '.$extension->duration_variation.' '.$extension->duration_type));
            }
        }
        return $completion_date;
    }

    /**
     * MATERIAL STOCKS
     */

    public function material_balance_value($date = null)
    {
        $project_id = $this->{$this::DB_TABLE_PK};
        $this->load->model(['inventory_location', 'material_item']);
        $locations = $this->inventory_location->get();
        $balance_value = 0;
        foreach ($locations as $location) {
            $balance_value += $location->total_material_balance_value($project_id,$date);
        }
        return $balance_value;
    }

    public function old_project_material_balance_value($date = null){
        $project_id = $this->{$this::DB_TABLE_PK};
        $this->load->model(['inventory_location','material_item']);
        $locations = $this->inventory_location->get();
        $balance_value = 0;
        foreach ($locations as $location) {
            $balance_value += $location->project_total_material_balance_value($project_id,$date);
        }
        return $balance_value;
    }

    public function project_material_movement($date = null, $print = false){
        $this->load->model(['material_item']);
        $sql = '
                SELECT material_item_id, item_name, project_id, project_name, SUM(opening_stock) AS material_opening_stock, SUM(received_quantity) AS material_received_quantity, SUM(assigned_in_quantity) AS material_assigned_in_quantity, SUM(on_transit) AS material_on_transit, SUM(used_quantity) AS material_used_quantity, SUM(sold_quantity) AS material_sold_quantity, SUM(disposed_quantity) AS material_disposed_quantity, SUM(assigned_out_quantity) AS material_assigned_out_quantity, SUM(used_value) AS installed_value, SUM(assigned_out_value) AS item_assigned_out_value, SUM(received_value) AS item_received_value, SUM(sold_value) AS item_sold_value, SUM(disposed_value) AS item_disposed_value, average_price FROM (
                  SELECT material_item_id,
                         item_name,
                         location_id,
                         project_id,
                         project_name,
                         (
                                SELECT COALESCE(SUM(quantity), 0)
                                FROM material_opening_stocks
                                LEFT JOIN material_stocks
                                ON material_opening_stocks.stock_id = material_stocks.stock_id
                                LEFT JOIN sub_locations
                                ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                WHERE sub_locations.location_id = new_material_items_table.location_id
                                AND (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                THEN material_stocks.project_id IS NULL
                                ELSE material_stocks.project_id = new_material_items_table.project_id
                                END
                                )
                                AND material_stocks.item_id = new_material_items_table.material_item_id
                                AND material_stocks.date_received <= "'.$date.'"
                         )     AS opening_stock,
                         (
                             (
                                SELECT COALESCE(SUM(quantity), 0)
                                FROM material_items
                                LEFT JOIN material_stocks ON material_items.item_id = material_stocks.item_id
                                LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                WHERE sub_locations.location_id = new_material_items_table.location_id
                                AND (
                                    CASE
                                    WHEN new_material_items_table.project_id IS NULL THEN material_stocks.project_id IS NULL
                                    ELSE material_stocks.project_id = new_material_items_table.project_id
                                    END
                                )
                                AND material_items.item_id = new_material_items_table.material_item_id
                                AND material_stocks.date_received <= "'.$date.'"
                             ) -
                             (
                                     (
                                        SELECT COALESCE(SUM(external_material_transfer_items.quantity),0)
                                        FROM external_material_transfer_items
                                        LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                        WHERE source_location_id = new_material_items_table.location_id
                                        AND transfer_date <= "'.$date.'"
                                        AND  (
                                        CASE
                                        WHEN new_material_items_table.project_id IS NULL THEN external_material_transfers.project_id IS NULL
                                        ELSE external_material_transfers.project_id = new_material_items_table.project_id
                                        END
                                        )
                                        AND external_material_transfer_items.material_item_id = new_material_items_table.material_item_id
                                        AND status = "RECEIVED"
                                     ) + (
                                        SELECT COALESCE(SUM(inttrans_delivered_stock.quantity),0) FROM internal_material_transfer_items
                                        LEFT JOIN internal_material_transfers ON internal_material_transfer_items.transfer_id = internal_material_transfers.transfer_id
                                        LEFT JOIN material_stocks AS inttrans_delivered_stock ON internal_material_transfer_items.stock_id = inttrans_delivered_stock.stock_id
                                        LEFT JOIN sub_locations AS inttrans_delivery_sub_loc ON inttrans_delivered_stock.sub_location_id = inttrans_delivery_sub_loc.sub_location_id
                                        WHERE inttrans_delivery_sub_loc.location_id = new_material_items_table.location_id
                                        AND transfer_date <= "'.$date.'"
                                        AND (
                                        CASE
                                        WHEN new_material_items_table.project_id IS NULL THEN inttrans_delivered_stock.project_id IS NULL
                                        ELSE inttrans_delivered_stock.project_id = new_material_items_table.project_id
                                        END
                                        )
                                        AND inttrans_delivered_stock.item_id = new_material_items_table.material_item_id
                                     )
                                 )
                         ) AS received_quantity,(
                                SELECT COALESCE(SUM(material_stocks.quantity), 0)
                                FROM material_cost_center_assignment_items
                                LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                                LEFT JOIN material_stocks ON material_cost_center_assignment_items.stock_id = material_stocks.stock_id
                                LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                WHERE (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                THEN material_cost_center_assignments.destination_project_id IS NULL
                                ELSE material_cost_center_assignments.destination_project_id =
                                new_material_items_table.project_id
                                END
                                )
                                AND material_stocks.item_id = new_material_items_table.material_item_id
                                AND assignment_date <= "'.$date.'"
                                AND sub_locations.location_id = new_material_items_table.location_id
                         ) AS assigned_in_quantity,(
                                SELECT COALESCE(SUM(external_material_transfer_items.quantity),0)
                                FROM external_material_transfer_items
                                LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                WHERE source_location_id = new_material_items_table.location_id
                                AND transfer_date <= "'.$date.'"
                                AND (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL THEN external_material_transfers.project_id IS NULL
                                ELSE external_material_transfers.project_id = new_material_items_table.project_id
                                END
                                )
                                AND external_material_transfer_items.material_item_id = new_material_items_table.material_item_id
                                AND status = "ON TRANSIT"
                         ) AS on_transit,
                         (
                                SELECT COALESCE(SUM(material_costs.quantity), 0)
                                FROM material_costs
                                LEFT JOIN sub_locations ON material_costs.source_sub_location_id = sub_locations.sub_location_id
                                WHERE sub_locations.location_id = new_material_items_table.location_id
                                AND cost_date <= "'.$date.'"
                                AND material_costs.project_id = new_material_items_table.project_id
                                AND material_item_id = new_material_items_table.material_item_id
                         )     AS used_quantity,
                         (
                                SELECT COALESCE(SUM(stock_sales_material_items.quantity), 0)
                                FROM stock_sales_material_items
                                LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                                WHERE stock_sales_material_items.material_item_id =
                                new_material_items_table.material_item_id
                                AND sale_date <= "'.$date.'"
                                AND stock_sales.location_id = new_material_items_table.location_id
                                AND (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                 THEN stock_sales.project_id IS NULL
                                ELSE stock_sales.project_id = new_material_items_table.project_id
                                END
                                )
                         )     AS sold_quantity,
                         (
                                SELECT COALESCE(SUM(material_disposal_items.quantity), 0)
                                FROM material_disposal_items
                                LEFT JOIN material_disposals ON material_disposal_items.disposal_id = material_disposals.id
                                WHERE (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                THEN material_disposals.project_id IS NULL
                                ELSE material_disposals.project_id = new_material_items_table.project_id
                                END
                                )
                                AND disposal_date <= "'.$date.'"
                                AND material_disposal_items.material_item_id = new_material_items_table.material_item_id
                                AND material_disposals.location_id = new_material_items_table.location_id
                         )     AS disposed_quantity,
                         (
                                SELECT COALESCE(SUM(material_stocks.quantity), 0)
                                FROM material_cost_center_assignment_items
                                LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                                LEFT JOIN material_stocks ON material_cost_center_assignment_items.stock_id = material_stocks.stock_id
                                LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                WHERE (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                THEN material_cost_center_assignments.source_project_id IS NULL
                                ELSE material_cost_center_assignments.source_project_id =
                                new_material_items_table.project_id
                                END
                                )
                                AND material_stocks.item_id = new_material_items_table.material_item_id
                                AND assignment_date <= "'.$date.'"
                                AND sub_locations.location_id = new_material_items_table.location_id
                         )     AS assigned_out_quantity,
                         (
                             (
                                SELECT COALESCE(SUM(quantity*price), 0)
                                FROM material_items
                                LEFT JOIN material_stocks ON material_items.item_id = material_stocks.item_id
                                LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                WHERE sub_locations.location_id = new_material_items_table.location_id
                                AND (
                                    CASE
                                    WHEN new_material_items_table.project_id IS NULL THEN material_stocks.project_id IS NULL
                                    ELSE material_stocks.project_id = new_material_items_table.project_id
                                    END
                                )
                                AND material_items.item_id = new_material_items_table.material_item_id
                                AND material_stocks.date_received <= "'.$date.'"
                             ) -
                             (
                                     (
                                        SELECT COALESCE(SUM(external_material_transfer_items.quantity*external_material_transfer_items.price),0)
                                        FROM external_material_transfer_items
                                        LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                        WHERE source_location_id = new_material_items_table.location_id
                                        AND transfer_date <= "'.$date.'"
                                        AND  (
                                        CASE
                                        WHEN new_material_items_table.project_id IS NULL THEN external_material_transfers.project_id IS NULL
                                        ELSE external_material_transfers.project_id = new_material_items_table.project_id
                                        END
                                        )
                                        AND external_material_transfer_items.material_item_id = new_material_items_table.material_item_id
                                        AND status = "RECEIVED"
                                     ) + (
                                        SELECT COALESCE(SUM(inttrans_delivered_stock.quantity*inttrans_delivered_stock.price),0) FROM internal_material_transfer_items
                                        LEFT JOIN internal_material_transfers ON internal_material_transfer_items.transfer_id = internal_material_transfers.transfer_id
                                        LEFT JOIN material_stocks AS inttrans_delivered_stock ON internal_material_transfer_items.stock_id = inttrans_delivered_stock.stock_id
                                        LEFT JOIN sub_locations AS inttrans_delivery_sub_loc ON inttrans_delivered_stock.sub_location_id = inttrans_delivery_sub_loc.sub_location_id
                                        WHERE inttrans_delivery_sub_loc.location_id = new_material_items_table.location_id
                                        AND transfer_date <= "'.$date.'"
                                        AND (
                                        CASE
                                        WHEN new_material_items_table.project_id IS NULL THEN inttrans_delivered_stock.project_id IS NULL
                                        ELSE inttrans_delivered_stock.project_id = new_material_items_table.project_id
                                        END
                                        )
                                        AND inttrans_delivered_stock.item_id = new_material_items_table.material_item_id
                                     )
                                 )
                         ) AS received_value,
                         (
                                SELECT COALESCE(SUM(material_costs.quantity*material_costs.rate), 0)
                                FROM material_costs
                                LEFT JOIN sub_locations ON material_costs.source_sub_location_id = sub_locations.sub_location_id
                                WHERE sub_locations.location_id = new_material_items_table.location_id
                                AND cost_date <= "'.$date.'"
                                AND material_costs.project_id = new_material_items_table.project_id
                                AND material_item_id = new_material_items_table.material_item_id
                         )     AS used_value,
                         (
                                SELECT COALESCE(SUM(material_stocks.quantity*material_stocks.price), 0)
                                FROM material_cost_center_assignment_items
                                LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                                LEFT JOIN material_stocks ON material_cost_center_assignment_items.stock_id = material_stocks.stock_id
                                LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                WHERE (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                THEN material_cost_center_assignments.source_project_id IS NULL
                                ELSE material_cost_center_assignments.source_project_id =
                                new_material_items_table.project_id
                                END
                                )
                                AND material_stocks.item_id = new_material_items_table.material_item_id
                                AND assignment_date <= "'.$date.'"
                                AND sub_locations.location_id = new_material_items_table.location_id
                         )     AS assigned_out_value,
                         (
                                SELECT COALESCE(SUM(stock_sales_material_items.quantity*stock_sales_material_items.price), 0)
                                FROM stock_sales_material_items
                                LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                                WHERE stock_sales_material_items.material_item_id =
                                new_material_items_table.material_item_id
                                AND sale_date <= "'.$date.'"
                                AND stock_sales.location_id = new_material_items_table.location_id
                                AND (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                 THEN stock_sales.project_id IS NULL
                                ELSE stock_sales.project_id = new_material_items_table.project_id
                                END
                                )
                         )     AS sold_value,
                         (
                                SELECT COALESCE(SUM(material_disposal_items.quantity*material_disposal_items.rate), 0)
                                FROM material_disposal_items
                                LEFT JOIN material_disposals ON material_disposal_items.disposal_id = material_disposals.id
                                WHERE (
                                CASE
                                WHEN new_material_items_table.project_id IS NULL
                                THEN material_disposals.project_id IS NULL
                                ELSE material_disposals.project_id = new_material_items_table.project_id
                                END
                                )
                                AND disposal_date <= "'.$date.'"
                                AND material_disposal_items.material_item_id = new_material_items_table.material_item_id
                                AND material_disposals.location_id = new_material_items_table.location_id
                         )     AS disposed_value,
                         average_price
                  FROM (
                       SELECT DISTINCT super_table.item_id AS material_item_id,
                       item_name,
                       location_id,
                       main_table.project_id,
                       project_name,
                       average_price
                        FROM material_items AS super_table
                        INNER JOIN material_stocks AS main_table ON super_table.item_id = main_table.item_id
                        LEFT JOIN sub_locations AS sub_table ON main_table.sub_location_id = sub_table.sub_location_id
                        INNER JOIN material_average_prices  ON super_table.item_id = material_average_prices.material_item_id AND material_average_prices.material_stock_id = (
                            SELECT stock_id
                            FROM material_stocks
                            LEFT JOIN material_average_prices ON material_stocks.stock_id = material_average_prices.material_stock_id
                            LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                            WHERE material_stocks.item_id = super_table.item_id
                              AND material_stocks.project_id = main_table.project_id
                              AND sub_locations.location_id = sub_table.location_id
                              AND transaction_date <= "'.$date.'"
                            ORDER BY material_average_prices.average_price_id DESC
                            LIMIT 1
                        )
                        LEFT JOIN projects ON main_table.project_id = projects.project_id
                        WHERE main_table.project_id = '.$this->{$this::DB_TABLE_PK}.'
                        ORDER BY item_name, location_id, main_table.project_id
                   ) AS new_material_items_table
                ) AS new_material_items_table_alias GROUP BY material_item_id
                ';
        $items = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->result() : [];

        $sql = 'SELECT DISTINCT inventory_locations.* FROM material_stocks
                    LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                    LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id
                    WHERE material_stocks.project_id ='.$this->{$this::DB_TABLE_PK};
        $locations = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->result() : [];
        $project_material_items = [];
        $material_cost_value = $material_received_value = $installed_val =  $reallocated_value =   $material_balance_value = 0;
        $project = new self();
        $project->load($this->{$this::DB_TABLE_PK});
        $data['as_of'] = !is_null($date) ? $date : date('Y-m-d');
        $data['project'] = $project;
        $data['items'] = $items;
        foreach($items as $item){
            $material_item = new Material_item();
            $material_item->load($item->material_item_id);
            $balance_per_item = ($item->material_received_quantity - ($item->material_assigned_out_quantity+$item->material_used_quantity+$item->material_disposed_quantity+$item->material_sold_quantity));
//            $average_price = round($balance_per_item,5) > 0 ? ($item->item_received_value - ($item->item_assigned_out_value + $item->installed_value + $item->item_sold_value + $item->item_disposed_value))/$balance_per_item : $item->average_price;
            $average_price = $item->average_price;
            $project_material_items[$this->project_name][$item->item_name]['item'] = [
                'item_name'=>$item->item_name,
                'uom'=>$material_item->unit()->symbol,
                'project_id'=>$item->project_id,
                'material_ordered'=>$material_item->ordered_quantity_for_project($this->{$this::DB_TABLE_PK},$date),
                'material_opening_stock'=>$item->material_opening_stock,
                'item_received_value'=>$item->item_received_value,
                'material_received_quantity'=>$item->material_received_quantity,
                'material_assigned_in_quantity'=>$item->material_assigned_in_quantity,
                'material_on_transit'=>$item->material_on_transit,
                'installed_value'=>$item->installed_value,
                'material_used_quantity'=>$item->material_used_quantity,
                'item_sold_value'=>$item->item_sold_value,
                'material_sold_quantity'=>$item->material_sold_quantity,
                'item_disposed_value'=>$item->item_disposed_value,
                'material_disposed_quantity'=>$item->material_disposed_quantity,
                'item_assigned_out_value'=>$item->item_assigned_out_value,
                'material_assigned_out_quantity'=>$item->material_assigned_out_quantity,
                'average_price'=>$average_price,
                'balance'=>round($balance_per_item,5)
            ];
            foreach($locations as $location_with_item){
                $project_material_items[$this->project_name][$item->item_name][$location_with_item->location_name] = $material_item->location_balance($this->{$this::DB_TABLE_PK},$location_with_item->location_id);
            }

            $material_received_value += $average_price*$item->material_received_quantity;
//            $material_balance_value += $project_material_items[$this->project_name][$item->item_name]['balance_value'] = round($balance_per_item,5) > 0 ? ($item->item_received_value - ($item->item_assigned_out_value + $item->installed_value + $item->item_sold_value + $item->item_disposed_value)) : 0;
            $material_balance_value += $project_material_items[$this->project_name][$item->item_name]['balance_value'] = round($balance_per_item,5) > 0 ? $balance_per_item*$average_price : 0;
            $material_cost_value += $project_material_items[$this->project_name][$item->item_name]['used_value'] = $item->material_used_quantity*$average_price;
//            $installed_val += $item->material_used_quantity*$average_price;
            $installed_val += $item->installed_value;
//            $reallocated_value += $item->material_assigned_out_quantity*$average_price;
            $reallocated_value += $item->item_assigned_out_value;
        }
        $project_material_items[$this->project_name]['received_value'] = $material_received_value;
        $project_material_items[$this->project_name]['installed_value'] = $installed_val;
        $project_material_items[$this->project_name]['on_of_site_value'] = $on_site_val = $material_balance_value;
        $project_material_items[$this->project_name]['reallocated_value'] = $reallocated_value;
        $project_material_items[$this->project_name]['locations'] = $locations;

        $data['project_material_items'] = $project_material_items;
        $project_material_items_pop_up = $print ? '' : $this->load->view('reports/project_financial_status_project_material_items_pop_up',$data,true);
        $project_material_items[$this->project_name]['pop_up'] = $project_material_items_pop_up;
        return $project_material_items;
    }

    public function project_material_balance_value($date = null){
        $project_id = $this->{$this::DB_TABLE_PK};
        $sql = 'SELECT COALESCE(SUM(balance*average_price),0) AS material_balance_value FROM (
                    SELECT  material_item_id, item_name, location_id, project_id,project_name,(
                            (
                                SELECT COALESCE(SUM(quantity),0)
                                FROM material_items
                                LEFT JOIN material_stocks ON material_items.item_id = material_stocks.item_id
                                LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                WHERE sub_locations.location_id = new_material_items_table.location_id
                                AND (
                                   CASE
                                       WHEN new_material_items_table.project_id IS NULL THEN material_stocks.project_id IS NULL
                                       ELSE material_stocks.project_id = new_material_items_table.project_id
                                   END
                                )
                                AND material_items.item_id = new_material_items_table.material_item_id
                                AND material_stocks.date_received <= "'.$date.'"
                                GROUP BY material_items.item_id
                            ) - (
                                (
                                    (
                                        SELECT COALESCE(SUM(external_material_transfer_items.quantity),0)
                                        FROM external_material_transfer_items
                                        LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                        WHERE source_location_id = new_material_items_table.location_id
                                        AND transfer_date <= "'.$date.'"
                                        AND  (
                                            CASE
                                                WHEN new_material_items_table.project_id IS NULL THEN external_material_transfers.project_id IS NULL
                                                ELSE external_material_transfers.project_id = new_material_items_table.project_id
                                                END
                                        )
                                        AND external_material_transfer_items.material_item_id = new_material_items_table.material_item_id
                                        AND status != "CANCELLED"
                                    ) + (
                                        SELECT COALESCE(SUM(inttrans_delivered_stock.quantity),0) FROM internal_material_transfer_items
                                        LEFT JOIN internal_material_transfers ON internal_material_transfer_items.transfer_id = internal_material_transfers.transfer_id
                                        LEFT JOIN material_stocks AS inttrans_delivered_stock ON internal_material_transfer_items.stock_id = inttrans_delivered_stock.stock_id
                                        LEFT JOIN sub_locations AS inttrans_delivery_sub_loc ON inttrans_delivered_stock.sub_location_id = inttrans_delivery_sub_loc.sub_location_id
                                        WHERE inttrans_delivery_sub_loc.location_id = new_material_items_table.location_id
                                        AND transfer_date <= "'.$date.'"
                                        AND (
                                            CASE
                                                WHEN new_material_items_table.project_id IS NULL THEN inttrans_delivered_stock.project_id IS NULL
                                                ELSE inttrans_delivered_stock.project_id = new_material_items_table.project_id
                                                END
                                        )
                                        AND inttrans_delivered_stock.item_id = new_material_items_table.material_item_id
                                    )
                                ) + (
                                    SELECT COALESCE(SUM(material_costs.quantity),0)
                                    FROM material_costs
                                    LEFT JOIN sub_locations ON material_costs.source_sub_location_id = sub_locations.sub_location_id
                                    WHERE sub_locations.location_id = new_material_items_table.location_id
                                    AND cost_date <= "'.$date.'"
                                    AND material_costs.project_id = new_material_items_table.project_id
                                    AND material_item_id = new_material_items_table.material_item_id
                                ) + (
                                    SELECT COALESCE(SUM(stock_sales_material_items.quantity),0) FROM stock_sales_material_items
                                    LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                                    WHERE stock_sales_material_items.material_item_id = new_material_items_table.material_item_id
                                    AND sale_date <= "'.$date.'"
                                    AND stock_sales.location_id = new_material_items_table.location_id
                                    AND (
                                        CASE
                                            WHEN new_material_items_table.project_id IS NULL THEN stock_sales.project_id IS NULL
                                            ELSE stock_sales.project_id = new_material_items_table.project_id
                                            END
                                    )
                                ) + (
                                    SELECT COALESCE(SUM(material_disposal_items.quantity),0) FROM material_disposal_items
                                    LEFT JOIN material_disposals ON material_disposal_items.disposal_id = material_disposals.id
                                    WHERE (
                                          CASE
                                              WHEN new_material_items_table.project_id IS NULL THEN material_disposals.project_id IS NULL
                                              ELSE material_disposals.project_id = new_material_items_table.project_id
                                              END
                                    )
                                    AND disposal_date <= "'.$date.'"
                                    AND material_disposal_items.material_item_id = new_material_items_table.material_item_id
                                    AND material_disposals.location_id = new_material_items_table.location_id
                                ) + (
                                    SELECT COALESCE(SUM(material_stocks.quantity),0) FROM material_cost_center_assignment_items
                                    LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                                    LEFT JOIN material_stocks ON material_cost_center_assignment_items.stock_id = material_stocks.stock_id
                                    LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                                    WHERE (
                                          CASE
                                              WHEN new_material_items_table.project_id IS NULL THEN material_cost_center_assignments.source_project_id IS NULL
                                              ELSE material_cost_center_assignments.source_project_id = new_material_items_table.project_id
                                              END
                                    )
                                    AND material_stocks.item_id = new_material_items_table.material_item_id
                                    AND assignment_date <= "'.$date.'"
                                    AND sub_locations.location_id = new_material_items_table.location_id
                                )
                            )
                        ) AS balance, average_price FROM (
                    SELECT DISTINCT super_table.item_id AS material_item_id, item_name, location_id, main_table.project_id,project_name, average_price FROM material_items AS super_table
                    INNER JOIN material_stocks AS main_table ON super_table.item_id = main_table.item_id
                    LEFT JOIN sub_locations AS sub_table ON main_table.sub_location_id = sub_table.sub_location_id
                    INNER JOIN material_average_prices ON super_table.item_id = material_average_prices.material_item_id AND material_average_prices.material_stock_id = (
                        SELECT stock_id FROM material_stocks
                        LEFT JOIN material_average_prices ON material_stocks.stock_id = material_average_prices.material_stock_id
                        LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                        WHERE material_stocks.item_id = super_table.item_id
                        AND material_stocks.project_id = main_table.project_id
                        AND sub_locations.location_id = sub_table.location_id
                        AND transaction_date <= "'.$date.'"
                        ORDER BY material_average_prices.average_price_id DESC LIMIT 1
                    )
                    LEFT JOIN projects ON main_table.project_id = projects.project_id
                    WHERE main_table.project_id = '.$project_id.'
                    ORDER BY item_name, location_id, main_table.project_id
                    ) AS new_material_items_table
                ) AS new_material_items_table_alias';
        return $this->db->query($sql)->row()->material_balance_value;
    }

	public function overall_received_value($date = null){
		$date = is_null($date) ? date('Y-m-d') : $date;
		return $this->assigned_in_material_value($date) + $this->ordered_goods_received_value() + $this->material_opening_stock_value();
	}

    public function material_opening_stock_value(){
        $sql = 'SELECT COALESCE(SUM(quantity*price),0) AS opening_stock_value
                FROM material_stocks
                LEFT JOIN material_opening_stocks ON material_stocks.stock_id = material_opening_stocks.stock_id
                WHERE material_opening_stocks.project_id = '.$this->{$this::DB_TABLE_PK};

        return $this->db->query($sql)->row()->opening_stock_value;
    }

	public function assigned_in_material_value($date = null){
		$date = is_null($date) ? date('Y-m-d') : $date;
		$sql = 'SELECT COALESCE(SUM(quantity*price),0) AS borrowed_material_value FROM material_stocks
              LEFT JOIN material_cost_center_assignment_items ON material_stocks.stock_id = material_cost_center_assignment_items.stock_id
              LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
              WHERE assignment_date <= "'.$date.'" AND material_cost_center_assignments.id IS NOT NULL AND material_stocks.project_id = '.$this->{$this::DB_TABLE_PK};

		return $this->db->query($sql)->row()->borrowed_material_value;
	}

	public function assigned_out_material_value($date = null){
		$date = is_null($date) ? date('Y-m-d') : $date;
		$sql = 'SELECT COALESCE(SUM(quantity*price),0) AS borrowed_material_value FROM material_stocks
              LEFT JOIN material_cost_center_assignment_items ON material_stocks.stock_id = material_cost_center_assignment_items.stock_id
              LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
              WHERE assignment_date <= "'.$date.'" AND material_cost_center_assignments.id IS NOT NULL AND material_cost_center_assignments.source_project_id = '.$this->{$this::DB_TABLE_PK};

		return $this->db->query($sql)->row()->borrowed_material_value;
	}

    public function material_cost_center_assignments($direction = 'IN', $from = null, $to = null){
        $where = ($direction == 'IN' ? ' source_project_id ' : ' destination_project_id ').' = '.$this->{$this::DB_TABLE_PK};
        if(!is_null($from)){
            $where .= ' AND assignment_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $where .= ' AND assignment_date <= "'.$to.'" ';
        }
        $this->load->model('material_cost_center_assignment');
        return $this->material_cost_center_assignment->get(0,0,$where);
    }

    public function certificates($from = null,$to = null){
        $this->load->model('project_certificate');
        $where =' project_id = '.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($from)){
            $where .= ' AND certificate_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $where .= ' AND certificate_date <= "'.$to.'" ';
        }
        return $this->project_certificate->get(0,0,$where);
    }

    public function certified_amount($from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(certified_amount),0) AS certified_amount FROM project_certificates WHERE project_id = '.$this->{$this::DB_TABLE_PK};
        if(!is_null($from)){
            $sql .= ' AND certificate_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND certificate_date <= "'.$to.'" ';
        }
        $query = $this->db->query($sql);
        return $query->row()->certified_amount;
    }

    public function certificate_paid_amount($from = null, $to = null){
        $sql = 'SELECT (
                      (
                        COALESCE(SUM(amount),0)
                      ) + (
                        COALESCE(SUM(withheld_amount),0)
                      )
                  ) AS amount_paid
                FROM receipt_items AS main_table
                LEFT JOIN withholding_taxes ON main_table.id = withholding_taxes.receipt_item_id
                LEFT JOIN receipts ON main_table.receipt_id = receipts.id
                LEFT JOIN project_certificate_receipts ON receipts.id = project_certificate_receipts.receipt_id
                LEFT JOIN project_certificates ON project_certificate_receipts.certificate_id = project_certificates.id
                WHERE project_id ='.$this->{$this::DB_TABLE_PK};

        if(!is_null($from)){
            $sql .= ' AND receipt_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND receipt_date <= "'.$to.'" ';
        }

        $query = $this->db->query($sql);
        return $query->row()->amount_paid;
    }

    public function certificate_details($date = null, $print = false){
        $this->load->model('project_certificate');
        $sql = 'SELECT project_certificates.id AS project_certificate_id, certificate_date, certificate_number,certified_amount,(
                  SELECT (COALESCE(SUM(receipt_items.amount),0) + COALESCE(SUM(withheld_amount),0)) FROM receipt_items
                  LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                  LEFT JOIN project_certificate_receipts ON receipts.id = project_certificate_receipts.receipt_id
                  LEFT JOIN withholding_taxes ON receipt_items.id = withholding_taxes.receipt_item_id
                  WHERE project_certificate_receipts.certificate_id = project_certificates.id
                ) AS paid_amount
                FROM project_certificates
                WHERE project_id ='.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($date)){
            $sql .= ' AND certificate_date <= "'.$date.'"';
        }
        $results = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->result() : false;
        $certificates_details = $certificates = [];
        $total_certified_amount = $total_paid_amount = 0;
        $project = new self();
        $project->load($this->{$this::DB_TABLE_PK});
        $data['project'] = $project;
        $sql = 'SELECT currency_id FROM currencies WHERE is_native = 1 LIMIT 1';
        $native_currency_id = $this->db->query($sql)->row()->currency_id;
        $native_currency = new Currency();
        $native_currency->load($native_currency_id);
        $data['native_currency'] = $native_currency;

        if($results){
            foreach($results as $certificate){
                $project_certificate = new Project_certificate();
                $project_certificate->load($certificate->project_certificate_id);
                $certificates[] = $project_certificate;
                $total_paid_amount += $certificate->paid_amount;
                $total_certified_amount += $certificate->certified_amount;
                $certificates_details[$project->project_name][$certificate->project_certificate_id] = [
                    'certificate_date'=>$certificate->certificate_date,
                    'certificate_number'=>$certificate->certificate_number,
                    'certified_amount'=>$certificate->certified_amount,
                    'paid_amount'=>$certificate->paid_amount,
                ];
            }
        }
        $certificates_details[$project->project_name]['total_paid_amount'] = $total_paid_amount;
        $certificates_details[$project->project_name]['total_certified_amount'] = $total_certified_amount;
        $certificates_details[$project->project_name]['balance'] = ($total_certified_amount - $total_paid_amount);
        $data['certificates'] = $certificates;
        $data['certificates_details'] = $certificates_details;
        $project_certificates_pop_up = $print ? '' : $this->load->view('reports/project_financial_status_project_certificates_pop_up',$data,true);
        $certificates_details[$project->project_name]['pop_up'] = $project_certificates_pop_up;
        return $certificates_details;
    }

    /**PROJECTS OVERVIEW**/
    public function projects(){
        $this->load->model('project');
        $where =[
            'project_id'=> $this->{$this::DB_TABLE_PK},
            'end_date < '.date('Y-m-d'),
        ];
        $projects = $this->project->get(0,0,$where);
        return $projects;
    }

    public function completed_projects_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['reference_number','project_name'],$order,'reference_number');

        $where_clause = '';

        $sql = 'SELECT COUNT(id) AS records_total FROM project_closures';

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if($keyword!=null){
            $where_clause .= ' AND ( reference_number LIKE "%'.$keyword.'%" OR project_name LIKE "%'.$keyword.'%" )';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS projects.project_id AS project_id, reference_number, project_name, start_date, end_date, site_location FROM projects
                WHERE projects.project_id IN (
                  SELECT project_closures.project_id AS project_id FROM project_closures
                )
                '.$where_clause.' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;


        $rows = [];
        $cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract'];
        foreach ($results as $row){
            $project = new self();
            $project->load($row->project_id);
            $extended_contract_amount = $project->contract_sum_variation();
            $final_contract_amount = $project->budget_figure() + $extended_contract_amount;

            $rows[] = [
                $row->reference_number,
                anchor(base_url('projects/profile/' . $row->project_id), $row->project_name),
                number_format($project->budget_figure()),
                number_format($final_contract_amount),
                $project->duration(),
                $project->final_duration(),
                '',
                '',
                $row->site_location
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function project_revisions(){
        $this->load->model('revision');
        $where = 'project_id='.$this->{$this::DB_TABLE_PK};
        return $this->revision->get(0,0,$where,'revision_date DESC');
    }

    /**PROJECT PLANNING**/
    public function project_plans($from = null, $to = null){
        $this->load->model('project_plan');
        $where = 'project_id='.$this->{$this::DB_TABLE_PK};
        if(!is_null($from)){
            $where .= ' AND start_date >= "'.$from.'" ';
        } else if(!is_null($to)){
            $where .= ' AND end_date <= "'.$to.'" ';
        }
        return $this->project_plan->get(0,0,$where,'start_date DESC');
    }

    public function created_by()
    {
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function project_suppliers($from = null, $to = null){
        $project_purchase_orders = $this->purchase_orders($from, $to);
        $project_suppliers = [];
        foreach ($project_purchase_orders as $order){
            $project_suppliers[] = $order->stakeholder_id;
        }

        return $project_suppliers;
    }

    public function project_certified_amount($paid = false){
        $certificates = $this->certificates();
        $certified_amount = 0;
        foreach($certificates as $certificate){
            if($paid){
                $certified_amount += $certificate->amount_paid();
            } else {
                $certified_amount += $certificate->certified_amount;
            }
        }
        return $certified_amount;
    }

    /***SUB CONTRACTS***/
    public function sub_contracts($amount = false,$from = null, $to = null){
        $project_id = $this->{$this::DB_TABLE_PK};
        $level = "project_overall";

        $this->load->model(['sub_contract','sub_contract_item']);
        if(!$amount) {
            return $this->sub_contract->get(0, 0, ['project_id' => $project_id]);
        } else {
            return $this->sub_contract_item->actual_cost($project_id, $level,$from, $to);
        }
    }

    public function project_sub_contracts_details($date, $print){
        $this->load->model('sub_contract_certificate');
        $sql = 'SELECT main_table.id AS sub_contract_certificate_id,certificate_date, sub_contracts.id AS sub_contract_id, contract_name, certified_amount, certificate_number, sub_contract_certificate_payment_vouchers.payment_voucher_id, (
                    SELECT COALESCE(SUM(approved_amount),0) FROM sub_contract_payment_requisition_approval_items
                    LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id
                    LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                    LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                    WHERE status = "APPROVED"
                    AND is_final = 1 
                    AND sub_contract_payment_requisition_items.certificate_id = main_table.id
                ) AS approved_amount, (
                   SELECT (COALESCE(SUM(amount),0) + COALESCE(SUM(withheld_amount),0))
                   FROM payment_voucher_items
                   LEFT JOIN withholding_taxes ON payment_voucher_items.payment_voucher_item_id = withholding_taxes.payment_voucher_item_id
                   WHERE payment_voucher_items.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                ) AS paid_amount 
                FROM sub_contract_certificates AS main_table
                LEFT JOIN sub_contracts ON main_table.sub_contract_id = sub_contracts.id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON main_table.id = sub_contract_certificate_payment_vouchers.sub_contract_certificate_id 
                LEFT JOIN payment_vouchers ON sub_contract_certificate_payment_vouchers.payment_voucher_id = payment_vouchers.payment_voucher_id
                WHERE project_id ='.$this->{$this::DB_TABLE_PK}.'';
        if(!is_null($date)){
            $sql .= ' AND certificate_date <= "'.$date.'"';
        }
        $results = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->result() : false;
        $sub_contract_details = $certificates = [];
        $total_sub_contract_paid_amount = $total_sub_contract_approved_amount = $total_sub_contract_certified_amount = 0;
        $project = new self();
        $project->load($this->{$this::DB_TABLE_PK});
        $data['project'] = $project;
        $sql = 'SELECT currency_id FROM currencies WHERE is_native = 1 LIMIT 1';
        $native_currency_id = $this->db->query($sql)->row()->currency_id;
        $native_currency = new Currency();
        $native_currency->load($native_currency_id);
        $data['native_currency'] = $native_currency;
        if($results){
            foreach($results as $certificate){
                $sub_contract_certificate = new Sub_contract_certificate();
                $sub_contract_certificate->load($certificate->sub_contract_certificate_id);
                $certificates[] = $sub_contract_certificate;
                $total_sub_contract_paid_amount += $certificate->paid_amount;
                $total_sub_contract_certified_amount += $certificate->certified_amount;
                $total_sub_contract_approved_amount += $certificate->approved_amount;
                $sub_contract_details[$project->project_name][$certificate->sub_contract_certificate_id] = [
                    'certificate_date'=>$certificate->certificate_date,
                    'certificate_number'=>$certificate->certificate_number,
                    'contract_name'=>$certificate->contract_name,
                    'payment_voucher_id'=>$certificate->payment_voucher_id,
                    'certified_amount'=>$certificate->certified_amount,
                    'approved_amount'=>$certificate->approved_amount,
                    'paid_amount'=>$certificate->paid_amount
                ];
            }
        }
        $sub_contract_details[$project->project_name]['total_paid_amount'] = $total_sub_contract_paid_amount;
        $sub_contract_details[$project->project_name]['total_certified_amount'] = $total_sub_contract_certified_amount;
        $sub_contract_details[$project->project_name]['total_approved_amount'] = $total_sub_contract_approved_amount;
        $data['certificates'] = $certificates;
        $data['sub_contract_details'] = $sub_contract_details;
        $sub_contracts_pop_up = $print ? '' : $this->load->view('reports/project_financial_status_sub_contracts_pop_up',$data,true);
        $sub_contract_details[$project->project_name]['pop_up'] = $sub_contracts_pop_up;
        return $sub_contract_details;
    }

    public function sub_contract_certificates($total = false,$from = null,$to = null){
        $sql = 'SELECT '. ($total ? ' COALESCE(SUM(certified_amount),0) AS certified_amount ' : ' sub_contract_certificates.id AS certificate_id '). ' FROM sub_contract_certificates
                LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                WHERE project_id = '.$this->{$this::DB_TABLE_PK};

        if(!is_null($from)){
            $sql .= ' AND certificate_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND certificate_date <= "'.$to.'" ';
        }
        $query = $this->db->query($sql);
        if($total){
            $ret_val = $query->row()->certified_amount;
        } else {
            $this->load->model('sub_contract_certificate');
            $certificates = [];

            $results = $query->result();
            foreach ($results as $row) {
                $certificate = new Sub_contract_certificate();
                $certificate->load($row->certificate_id);
                $certificates[] = $certificate;
            }

            $ret_val = $certificates;
        }
        return $ret_val;
    }

    public function sub_contract_certificates_payments($total = false,$from = null,$to = null){
        $sql = 'SELECT COALESCE(SUM(amount),0) AS paid_amount FROM payment_voucher_items
                LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
                LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                WHERE project_id = '.$this->{$this::DB_TABLE_PK}.'';

        if(!is_null($from)){
            $sql .= ' AND certificate_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND certificate_date <= "'.$to.'" ';
        }
        $query = $this->db->query($sql);
        if($total){
            $ret_val = $query->row()->paid_amount;
        } else {
            $ret_val = 0;
        }

        return $ret_val;
    }

    public function ptoject_sub_locations()
    {
        $project_id = $this->{$this::DB_TABLE_PK};
        $sql = 'SELECT * FROM sub_locations
                LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id
              WHERE project_id = '.$project_id;
        $query = $this->db->query($sql);
        $results = $query->result();
        return !empty($results) ? $results : false;
    }

    public function project_sub_locations_dropdown_options()
    {
        $options = ['&nbsp;'];
        $results = $this->ptoject_sub_locations();
        if($results){
            foreach ($results as $row){
                $options[$row->sub_location_id] = $row->sub_location_name;
            }
        }
        return $options;
    }

    public function vat_amount_for_all_grns($date = null){
    	$this->load->model(['purchase_order_grn','purchase_order']);
    	$sql = 'SELECT purchase_order_grns.purchase_order_id, vat, clearance_vat, goods_received_notes.grn_id,purchase_order_grns.factor,purchase_order_grns.exchange_rate,purchase_order_grns.freight,purchase_order_grns.insurance,purchase_order_grns.other_charges 
				FROM purchase_order_grns
				LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
				LEFT JOIN goods_received_notes ON purchase_order_grns.goods_received_note_id = goods_received_notes.grn_id
				LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
				WHERE project_id = '.$this->{$this::DB_TABLE_PK}.'';
    	if(!is_null($date)){
    		$sql .= ' AND receive_date <= "'.$date.'"';
		}
    	$results = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->result() : false;
    	$total_vat_amount = 0;
    	if($results){
    		foreach($results as $result){
    			$order = new Purchase_order();
    			$order->load($result->purchase_order_id);
    			$order_value = $order->ordered_material_value()+$order->ordered_asset_value()+$order->ordered_service_value();
    			$unreceived_value = $order->unreceived_amount(true);
    			$received_value = $order_value - $unreceived_value;
				$order_items_value =  $result->factor > 0 ? ($received_value)/$result->factor : ($received_value);
				$freight_inspection_charges = $result->freight + $result->insurance + $result->other_charges;
				$order_items_value = ($order_items_value + $freight_inspection_charges)*$result->exchange_rate;
    			switch ($order->vat_inclusive){
					case "VAT COMPONENT":
						$total_vat_amount += ($order_items_value*0.01*$order->vat_percentage) + $result->clearance_vat;
					case "VAT PRICED":
						$total_vat_amount += $result->vat + $result->clearance_vat;
					default:
						$total_vat_amount += $result->vat + $result->clearance_vat;
				}
			}
		}
    	return $total_vat_amount;
	}

	public function project_objects_dropdown_options(){
        $sql = 'SELECT "TASKS" AS type, CONCAT("task_",task_id) AS id, task_name AS name FROM tasks WHERE activity_id IN (SELECT activities.activity_id FROM activities WHERE project_id ='.$this->{$this::DB_TABLE_PK}.')
        
                UNION
                SELECT "ACTIVITIES" AS type, CONCAT("activity_",activity_id) AS id, activity_name AS name FROM activities WHERE project_id ='.$this->{$this::DB_TABLE_PK}.'';
        $results = $this->db->query($sql)->result();
        $options = ['&nbsp;'=>'&nbsp;'];
        foreach($results as $result){
            $options[$result->type][$result->id] = $result->name;
        }
        return $options;
    }

    public function active_projects_with_plan($from = null,$to = null,$currency_id = null){
        $to = !is_null($to) ? $to : date('Y-m-d');
        $sql = 'SELECT DISTINCT * FROM projects
                INNER JOIN project_plans ON projects.project_id = project_plans.project_id  WHERE';
        if(!is_null($currency_id)) $sql .= ' project_plans.currency_id = '.$currency_id.'';
        if(!is_null($currency_id)) $sql .= ' AND';
        if(!is_null($from)) $sql .= ' project_plans.start_date >= "'.$from.'"';
        if(!is_null($from) || !is_null($currency_id)) $sql .= ' AND';
        if(!is_null($to)) $sql .= ' project_plans.end_date >= "'.$to.'"';

        $results = $this->db->query($sql)->result();
        $planned_active_projects_arr = [];
        if(!empty($results)){
            foreach ($results as $result){
                $project = new Project();
                $project->load($result->project_id);
                if($project->completion_date() >= $to){
                    $planned_active_projects_arr[] = $project->project_id;
                }
            }
        }
        return $planned_active_projects_arr;

    }

    public function project_plans_budget($budget_type,$from = null,$to = null){
        $project_plans = $this->project_plans($from,$to);
        $budget_value = 0;
        foreach ($project_plans as $project_plan){
            switch ($budget_type){
                case 'LABOUR':
                    $budget_value += $project_plan->labour_budget;
                    break;
                case 'EQUIPMENT_N_MATL':
                    $budget_value += $project_plan->equipment_n_material_budget;
                    break;
            }
        }
        return $budget_value;
    }

    public function active_projects_dropdown_options(){
        $categories = $this->project_category->get(0,0,'','category_name');
        $options = [];
        foreach($categories as $category){
            if($category->category_name != "MV DEPARTMENT") {
                $projects = $category->projects(true);
                if (!empty($projects)) {
                    foreach ($projects as $project) {
                        if ($project->completion_date() >= date('Y-m-d')) {
                            $project_id = $project->{$project::DB_TABLE_PK};
                            $options[$category->category_name][$project_id] = $project->project_name;
                        }
                    }
                }
            }
        }
        return $options;
    }

}

