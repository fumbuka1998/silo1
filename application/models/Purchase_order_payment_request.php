<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/24/2018
 * Time: 8:13 AM
 */

class Purchase_order_payment_request extends MY_Model{
    const DB_TABLE = 'purchase_order_payment_requests';
    const DB_TABLE_PK = 'id';

    public $approval_module_id;
    public $purchase_order_id;
    public $request_date;
    public $finalized_date;
    public $currency_id;
    public $requester_id;
    public $forward_to;
    public $finalizer_id;
    public $remarks;
    public $status;

    public function request_number(){
        return 'P.O-P.R/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function order_payment_request_list($limit, $start, $keyword, $order){

        $order_string = dataTable_order_string(['request_date','purchase_order_id','payment_request_no','stakeholder_id'],$order,'request_date');

        $status = $this->input->post('status');
		if($status != 'all' && $status != ''){
			$where = ' WHERE purchase_order_payment_requests.status = "'.$status.'" ';
		} else {
			$where = '';
		}

        $sql = 'SELECT COUNT(id) AS records_total FROM purchase_order_payment_requests '.$where;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if($keyword != ''){
            $where .= ($where != '' ? ' AND ' : 'WHERE').' (stakeholder_name LIKE "%'.$keyword.'%" OR purchase_order_id LIKE "%'.$keyword.'%" OR purchase_order_payment_requests.id LIKE "%'.$keyword.'%" OR request_date LIKE "%'.$keyword.'%" OR requester_id LIKE "%'.$keyword.'%" OR purchase_order_payment_requests.status LIKE "%' . $keyword . '%")';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS purchase_order_payment_requests.id AS payment_request_no,purchase_order_id,purchase_order_payment_requests.status,request_date,purchase_orders.stakeholder_id, stakeholder_name,CONCAT(employees.first_name , employees.last_name) AS employee_name
                 FROM purchase_order_payment_requests
                 LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                 LEFT JOIN stakeholders ON purchase_orders.stakeholder_id = stakeholders.stakeholder_id 
                 LEFT JOIN employees ON purchase_order_payment_requests.requester_id = employees.employee_id '.$where.' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;
        $this->load->model(['purchase_order','currency','approval_module']);
        $data['currency_dropdown_options'] = $this->currency->dropdown_options();


        $approval_module = $this->approval_module->get(1, 0, ['id' => 3]);
        $to_forward_options = array_shift($approval_module)->forwarding_to_employee_options();

        $data['first_approver_options'] = $to_forward_options;

        $rows = [];
        foreach($results as $row){
            $payment_request = new self();
            $payment_request->load($row->payment_request_no);
            $approval = $payment_request->last_approval();
            $purchase_order = $payment_request->purchase_order();
            $data['order_dropdown_options'] = [$payment_request->purchase_order_id => $purchase_order->order_number()];
            $data['invoice_options'] = $purchase_order->invoice_options($payment_request->currency_id);
            $data['payment_request_approval_id'] =  $approval ? $approval->{$approval::DB_TABLE_PK} : 0;
            $data['payment_request_approval'] = $payment_request->purchase_order_payment_request_approval();
            $data['payment_request'] = $payment_request;
            $data['approved_payment_requests'] = $payment_request->approvals();
            $data['Approval_module'] = $payment_request->approval_module();
            $data['current_approval_level'] = $payment_request->current_approval_level();
            $data['last_approval'] = $payment_request->last_approval();
            $data['to_foward_approval_level'] = $to_forward_options;
            $data['attachments'] = $payment_request->attachments();
            $vendor = $purchase_order->stakeholder();
            $currency = $payment_request->currency();

            $rows[] = [
                custom_standard_date($row->request_date),
                anchor(base_url('procurements/preview_purchase_order/'.$payment_request->purchase_order_id),$purchase_order->order_number(),' target="_blank" '),
                anchor(base_url('procurements/preview_purchase_order_payment_request/'.$row->payment_request_no),$payment_request->request_number(),' target="_blank" '),
                anchor(base_url('stakeholders/stakeholder_profile/'.$purchase_order->stakeholder_id),$vendor->stakeholder_name),
                $currency->symbol.'<span class="pull-right">'.number_format($payment_request->requested_amount(),2).'</span>',
                $payment_request->progress_status_label(),
                $this->load->view('procurements/order_payment_requests/purchase_order_payment_request_actions',$data,true)
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "total_requested_amount" => $this->total_requested_amount($status),
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function cost_center_name()
    {
        return $this->purchase_order()->cost_center_name();
    }

    public function delete_payment_request_items(){
        $this->db->delete('purchase_order_payment_request_cash_items',['purchase_order_payment_request_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('purchase_order_payment_request_invoice_items',['purchase_order_payment_request_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->requester_id);
        return $employee;
    }

    public function invoice_items()
    {
        $this->load->model('purchase_order_payment_request_invoice_item');
        return $this->purchase_order_payment_request_invoice_item->get(0,0,['purchase_order_payment_request_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function invoice_item()
    {
        $this->load->model('purchase_order_payment_request_invoice_item');
        $payment_request_invoice_items = $this->purchase_order_payment_request_invoice_item->get(0,0,['purchase_order_payment_request_id' => $this->{$this::DB_TABLE_PK}]);
        foreach($payment_request_invoice_items as $invoice_item){
            return $invoice_item;
        }
    }

    public function cash_items()
    {
        $this->load->model('purchase_order_payment_request_cash_item');
        return $this->purchase_order_payment_request_cash_item->get(0,0,['purchase_order_payment_request_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Purchase_order();
        $purchase_order->load($this->purchase_order_id);
        return $purchase_order;
    }

    public function approvals(){
        $this->load->model('purchase_order_payment_request_approval');
        $where['purchase_order_payment_request_id'] = $this->{$this::DB_TABLE_PK};
        $where['is_final'] = 1;
        $approved_payments = $this->purchase_order_payment_request_approval->get(0,0,$where);
        $approved_payment_requests = [];
        foreach ($approved_payments as $approved_payment){
            $approved_payment_requests[] = $approved_payment->purchase_order_payment_request_id;
        }
        return $approved_payment_requests;
    }

    public function approval_module()
    {
        $this->load->model('approval_module');
        $approval_module = new Approval_module();
        $approval_module->load($this->approval_module_id);
        return $approval_module;
    }

    public function last_approval(){
        $this->load->model('purchase_order_payment_request_approval');
        $approvals = $this->purchase_order_payment_request_approval->get(1,0,['purchase_order_payment_request_id' => $this->{$this::DB_TABLE_PK}],' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function final_approval(){
        $this->load->model('purchase_order_payment_request_approval');
        $approvals = $this->purchase_order_payment_request_approval->get(1,0,[
            'purchase_order_payment_id' => $this->{$this::DB_TABLE_PK},
            'is_final' => 1
        ],' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function current_approval_level(){
        $last_approval = $this->last_approval();
        $this->load->model('approval_module');
        if($last_approval){
            $current_level = $last_approval->approval_chain_level()->next_level();
        } else {
            $current_level = $this->approval_module->chain_levels(0,$this->approval_module_id,'active');
        }

        return !empty($current_level) ? (is_array($current_level) ? array_shift($current_level) : $current_level) : false;
    }

    public function next_level_employees_options($next_level){

        $sql = 'SELECT  employee_approval_chain_levels.employee_id AS employee_id, CONCAT(employees.first_name," ",employees.last_name) AS employee_name FROM employee_approval_chain_levels
                LEFT JOIN employees ON employee_approval_chain_levels.created_by = employees.employee_id
                LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                WHERE approval_chain_levels.approval_module_id = 3 AND approval_chain_levels.level='.$next_level->level;

        $query = $this->db->query($sql);
        $options[''] = '&nbsp;';
        $results = $query->result();

        foreach ($results as $row){
            $options[$row->employee_id] = $row->employee_name;
        }
        return $options;
    }

    public function next_approval_employees_options(){
        $current_level = $this->current_approval_level();
        if ($current_level) {
            $levels = $current_level->next_level();
            $next_level = !empty($levels) ? $levels : $current_level;
            return $this->next_level_employees_options($next_level);
        }
    }

    public function progress_status_label(){
        $current_level  = $this->current_approval_level();

        if($this->status != 'APPROVED' && $this->status != 'REJECTED' && $current_level){
            $last_approval = $this->last_approval();
            if($last_approval){
                $approver = is_null($last_approval->forward_to) ? $current_level->level_name : $last_approval->forwarded_to()->full_name();
            } else {
                $approver = is_null($this->forward_to) ? $current_level->level_name : $this->forwarded_to()->full_name();

            }
            $label = '<span style="font-size: 12px" class="label label-info">Waiting For '.$approver.'</span>';
        } else if($this->status != 'APPROVED' && $this->status == 'REJECTED'){
            $label = '<span style="font-size: 12px" class="label label-danger">Rejected</span>';
        } else {
            $label = '<span style="font-size: 12px" class="label label-success">Approval Completed</span>';
        }
        return $label;
    }

    public function purchase_order_payment_request_approvals($payment_request_id = null){
        $this->load->model('purchase_order_payment_request_approval');
        $payment_request_id = !is_null($payment_request_id)  ? $payment_request_id : $this->{$this::DB_TABLE_PK};
        return $this->purchase_order_payment_request_approval->get(0,0,['purchase_order_payment_request_id' => $payment_request_id],'id');
    }

    public function purchase_order_payment_request_approval(){
        $this->load->model('purchase_order_payment_request_approval');
        $where = ' purchase_order_payment_request_id = '.$this->{$this::DB_TABLE_PK}.' AND is_final = 1';
        $payment_request_approvals = $this->purchase_order_payment_request_approval->get(0,0,$where,'id DESC');
        return !empty($payment_request_approvals) ? array_shift($payment_request_approvals) : false;
    }

    public function requester()
    {
        $this->load->model('employee');
        $requester = new Employee();
        $requester->load($this->requester_id);
        return $requester;
    }

    public function forwarded_to()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->forward_to);
        return $employee;
    }

    public function requested_amount()
    {
        $sql = 'SELECT (
            (
              SELECT COALESCE (SUM(requested_amount),0) FROM purchase_order_payment_request_invoice_items
              WHERE purchase_order_payment_request_id =  '.$this->{$this::DB_TABLE_PK}.'
            ) + (
                SELECT COALESCE(SUM(requested_amount),0) FROM purchase_order_payment_request_cash_items
                WHERE purchase_order_payment_request_id = '.$this->{$this::DB_TABLE_PK}.'
            )
        ) AS requested_amount
        
        ';

        $query = $this->db->query($sql);
        return $query->row()->requested_amount;
    }

    public function total_requested_amount($status = 'ALL'){
        $status = strtoupper($status);
        $sql = 'SELECT COALESCE(SUM(requested_amount*exchange_rate),0)  AS total_requested_amount FROM (
                  SELECT requested_amount,exchange_rate,update_date,purchase_order_payment_request_cash_items.id FROM purchase_order_payment_request_cash_items
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_cash_items.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    LEFT JOIN currencies ON purchase_order_payment_requests.currency_id = currencies.currency_id
                    LEFT JOIN exchange_rate_updates a ON currencies.currency_id = a.currency_id
                    WHERE update_date = (
                      SELECT MAX(update_date) FROM exchange_rate_updates b
                      WHERE a.currency_id = b.currency_id
                    ) ';
        if($status != 'ALL'){
            $sql .= ' AND status = "'.$status.'" ';
        }

        $sql .= '  GROUP BY id 
                    
                    UNION ALL
                    
                    SELECT requested_amount,exchange_rate,update_date,purchase_order_payment_request_invoice_items.id FROM purchase_order_payment_request_invoice_items
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_invoice_items.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    LEFT JOIN currencies ON purchase_order_payment_requests.currency_id = currencies.currency_id
                    LEFT JOIN exchange_rate_updates a ON currencies.currency_id = a.currency_id
                    WHERE update_date = (
                      SELECT MAX(update_date) FROM exchange_rate_updates b
                      WHERE a.currency_id = b.currency_id
                    ) ';
        if($status != 'ALL'){
            $sql .= ' AND status = "'.$status.'" ';
        }

        $sql .= ' GROUP BY id
                ) AS artificial_table
                 ';
        $query = $this->db->query($sql);
        return round($query->row()->total_requested_amount,2);
    }

    public function attachments(){
        $this->load->model('purchase_order_payment_request_attachment');
        $junctions = $this->purchase_order_payment_request_attachment->get(0,0,['purchase_order_payment_request_id' => $this->{$this::DB_TABLE_PK}]);
        $attachments = [];
        foreach ($junctions as $junction){
            $attachments[] = $junction->attachment();
        }
        return $attachments;
    }
}
