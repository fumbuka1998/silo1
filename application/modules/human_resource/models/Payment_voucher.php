<?php

class Payment_voucher extends MY_Model{

    const DB_TABLE = 'payment_vouchers';
    const DB_TABLE_PK = 'payment_voucher_id';
    const JUNCTION_TYPES = ['requisition_approval','purchase_order_payment_request_approval','sub_contract_certificate','sub_contract_payment_requisition_approval'];

    public $employee_id;
    public $currency_id;
    public $exchange_rate;
    public $cheque_number;
    public $withholding_tax;
    public $confidentiality_chain_position;
    public $vat_percentage;
    public $payment_date;
    public $reference;
    public $payee;
    public $remarks;
    public $is_printed;

    public function payment_voucher_number(){
        return add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function cost_center_name()
    {
        $cost_center_name = '';
        foreach ($this::JUNCTION_TYPES as $TYPE){
            $model = $TYPE.'_payment_voucher';
            $this->load->model($model);
            $junctions = $this->$model->get(1,0,['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);
            if(!empty($junctions)){
                $junction = array_shift($junctions);
                if($TYPE == 'requisition_approval'){
                    $cost_center_name = $junction->requisition_approval()->requisition()->cost_center_name();
                } else if($TYPE == 'purchase_order_payment_request_approval'){
                    $cost_center_name = $junction->purchase_order_payment_request_approval()->cost_center_name();
                } else if($TYPE == 'sub_contract_certificate'){
                    $cost_center_name = $junction->sub_contract_certificate()->sub_contract()->project()->project_name;
                }
                break;
            }
        }
        return $cost_center_name;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function credit_account()
    {
        $this->load->model('account');
        $credit_account = new Account();
        $credit_account->load($this->credit_account_id);
        return $credit_account;
    }

    public function supplementary_accounts($action, $export = false){
        $accounts_string = '';
        if($action == 'CREDIT'){
            $sql = 'SELECT DISTINCT account_name,debit_account_id FROM payment_voucher_items
                    LEFT JOIN accounts ON payment_voucher_items.debit_account_id = accounts.account_id
                    WHERE payment_voucher_id = "'.$this->{$this::DB_TABLE_PK}.'"
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

    public function delete_items(){
        $this->db->delete('payment_voucher_items',['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('requisition_approval_payment_vouchers',['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('invoice_payment_vouchers',['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('purchase_order_payment_request_approval_payment_vouchers',['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);

    }

    public function payment_voucher_items(){
        $this->load->model('payment_voucher_item');
        return $this->payment_voucher_item->get(0,0,['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function requisition_approval_payment_voucher(){
        $this->load->model('requisition_approval_payment_voucher');
        $junctions = $this->requisition_approval_payment_voucher->get(1,0,[
            'payment_voucher_id' => $this->{$this::DB_TABLE_PK}
        ]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function purchase_order_payment_request_approval_payment_voucher(){
        $this->load->model('purchase_order_payment_request_approval_payment_voucher');
        $junctions = $this->purchase_order_payment_request_approval_payment_voucher->get(1,0,[
            'payment_voucher_id' => $this->{$this::DB_TABLE_PK}
        ]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function sub_contract_payment_requisition_approval_payment_voucher(){
        $this->load->model('sub_contract_payment_requisition_approval_payment_voucher');
        $junctions = $this->sub_contract_payment_requisition_approval_payment_voucher->get(1,0,[
            'payment_voucher_id' => $this->{$this::DB_TABLE_PK}
        ]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function invoice_payment_voucher(){
        $this->load->model('invoice_payment_voucher');
        $junctions = $this->invoice_payment_voucher->get(1,0,['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function invoice()
    {
        return $this->invoice_payment_voucher()->invoice();
    }

    public function payment_type(){
        return $this->invoice_payment_voucher() ? 'invoice' : 'expense';
    }

    public function project(){
        $requisition_apporval_junction = $this->requisition_approval_payment_voucher();
        if($requisition_apporval_junction){

        } else {
            $ret_val = 'N/A';
        }
    }

    public function payment_vouchers_list($limit, $start, $keyword, $order,$project_id = null){

        $for_project = !is_null($project_id) && $project_id != '' ;
        $order_string = dataTable_order_string(['payment_no','payment_date','reference','account_name','amount'],$order,'payment_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $sql = 'SELECT COUNT(payment_voucher_id) AS records_total FROM payment_vouchers ';

        $data['cost_center_type_options'] = [
            '' => '&nbsp',
            'task' => 'Task',
            'project' => 'Project',
            'cost_center' => 'Cost Center',
            'department' => 'Department'
        ];
        if($for_project){
            $sql .= ' WHERE credit_account_id IN (
                  SELECT account_id FROM project_accounts WHERE project_id = '.$project_id.'
                )';

            $this->load->model('project');
            $project = new Project();
            $project->load($project_id);
            $data['cost_center_options'] = $project->cost_center_options();
            unset($data['cost_center_type_options']['']);
        } else {
            unset($data['cost_center_type_options']['task']);

        }


        //Cost center dropdown options
        $data['project_dropdown_options'] = projects_dropdown_options();
        $this->load->model(['cost_center','department']);
        $data['cost_center_dropdown_options'] = $this->cost_center->dropdown_options();
        $data['department_dropdown_options'] = $this->department->department_options();

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        $where_clause = $for_project ? ' WHERE credit_account_id IN ( SELECT account_id FROM project_accounts WHERE project_id = '.$project_id.') ' : '';
        if($keyword !=''){
            $where_clause .= ($where_clause != '' ? ' AND ' : '  WHERE '). ' (payment_vouchers.payment_voucher_id LIKE "%'.$keyword.'%" OR payment_date LIKE "%'.$keyword.'%" OR reference LIKE "%'.$keyword.'%" OR account_name LIKE "%'.$keyword.'%" )';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS payment_vouchers.payment_voucher_id AS payment_no,payment_date,reference,accounts.account_name
                FROM payment_vouchers
		        LEFT JOIN accounts ON payment_vouchers.credit_account_id = accounts.account_id '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];

        $data['currency_options'] = currency_dropdown_options();
        $data['credit_account_options'] = account_dropdown_options(['CASH IN HAND','BANK']);
        $data['expense_debit_account_options'] = account_dropdown_options(['DIRECT EXPENSES','INDIRECT EXPENSES']);

        foreach ($results as $row){
            $payment = new self();
            $payment->load($row->payment_no);
            $this->load->model('payment_voucher_item');
            $data['credit_account'] = $payment->credit_account();
            $data['debit_account'] = $payment->payment_voucher_item->debit_account();
            $data['currency'] = $payment->currency();
            $data['payment'] = $payment;
            $data['payment_type'] = $payment->payment_type();
            $rows[] = [
                $payment->payment_voucher_number(),
                custom_standard_date($row->payment_date),
                $row->reference,
                $row->account_name,
                $data['currency']->symbol.'<span class="pull-right">'.number_format($payment->amount(),2).'</span>',
                $this->load->view('finance/payments/payment_list_actions',$data,true)
            ];
        }

        $data['data'] = $rows;
        $data['recordsFiltered'] = $records_filtered;
        $data['recordsTotal'] = $records_total;

        return json_encode($data);
    }

    public function amount(){
        $sql = 'SELECT COALESCE(SUM(amount),0) AS amount FROM payment_voucher_items
                WHERE payment_voucher_id = '.$this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return $query->row()->amount;
    }

    public function retired(){
        $this->load->model('imprest');
        $imprests = $this->imprest->get(1,0,['payment_voucher_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($imprests);
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function requested_currency(){
        $this->load->model('requisition_approval_payment_voucher');
        $junctions = $this->requisition_approval_payment_voucher->get(1,0,[
            'payment_voucher_id' => $this->{$this::DB_TABLE_PK}
        ]);
        if(!empty($junctions)){
            $junction = array_shift($junctions);
            return $junction->requisition_approval()->requisition()->currency();
        } else {
            return $this->currency();
        }
    }

    public function payment_voucher_origin(){
        $requistion_by_nature = $this->requisition_approval_payment_voucher();
        $payment_request_by_nature = $this->purchase_order_payment_request_approval_payment_voucher();
        $sub_contract_payment_by_nature = $this->sub_contract_payment_requisition_approval_payment_voucher();

        if($requistion_by_nature){
            $approval = $this->requisition_approval_payment_voucher()->requisition_approval();
        } else if($payment_request_by_nature){
            $approval = $this->purchase_order_payment_request_approval_payment_voucher()->purchase_order_payment_request_approval();
        } else if($sub_contract_payment_by_nature) {
            $approval = $this->sub_contract_payment_requisition_approval_payment_voucher()->sub_contract_payment_requisition_approval();
        } else {
            $approval = false;
        }
        return $approval;
    }

    public function cheque_list($from = null, $to = null){

    $sql = 'SELECT credit_account_id, payment_date, symbol , payee, cheque_number, amount FROM payment_vouchers 
            LEFT JOIN payment_voucher_items ON payment_vouchers.payment_voucher_id = payment_voucher_items.payment_voucher_id
            LEFT JOIN currencies ON payment_vouchers.currency_id = currencies.currency_id';

        if (!is_null($from)) {
            $sql .= ' WHERE payment_date >= "'.$from.'" ';
        }

        if (!is_null($to)) {
            $sql .= ''.is_null($from) ? ' WHERE' : 'AND'.' payment_date <= "'.$to.'" ';
        }

        $sql .= ' AND ( cheque_number IS NOT NULL AND cheque_number != "NILL" AND cheque_number != "NIL"  AND cheque_number != "") ';


    $query = $this->db->query($sql);
    $results = $query->result();

    $rows = [];

    $this->load->model('account');
    foreach ($results as $row){
        $account =  new Account();
        $account->load($row->credit_account_id);
        $rows[] = [
            'date' => $row->payment_date,
            'payee_name' => $row->payee,
            'cheque_number' => $row->cheque_number,
            'bank' => $account->bank()->bank_name,
            'currency_symbol' => $row->symbol,
            'amount' => $row->amount
        ];
    }

    return $rows;
    }

    public function bulk_payment_list($creditor_type =  null,$vendor_id = null,$currency_id = null){
        $this->load->model([
            'purchase_order_payment_request_approval',
            'account',
            'purchase_order_payment_request_approval_invoice_item',
            'sub_contract_payment_requisition_approval',
            'sub_contract_payment_requisition_approval_item',
            'withholding_tax',
            'payment_voucher_item',
            'requisition_approval'
        ]);

        $invoice_where_clause = ' WHERE is_final = "1" AND purchase_order_payment_requests.status = "APPROVED"  ';
        $sub_contract_where_clause = ' WHERE is_final = "1" AND sub_contract_payment_requisitions.status = "APPROVED"  ';

        $sql = ' SELECT SQL_CALC_FOUND_ROWS * FROM (
                    
                    SELECT "sub_contract_payment_requisition" AS request_type, sub_contract_payment_requisition_approval_items.id AS approved_invoice_item_id, sub_contract_payment_requisition_approvals.id AS requisition_approval_id, sub_contract_payment_requisitions.sub_contract_requisition_id AS requisition_id, sub_contract_payment_requisitions.currency_id, approval_date AS approved_date, CONCAT(first_name," ",last_name) as approver_name, "" AS creditor_id, contractor_id
                    FROM sub_contract_payment_requisition_approval_items
                    LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                    LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                    LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id
                    LEFT JOIN sub_contract_certificates ON sub_contract_payment_requisition_items.certificate_id = sub_contract_certificates.id
                    LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                    LEFT JOIN employees ON sub_contract_payment_requisition_approvals.created_by = employees.employee_id
                    '.$sub_contract_where_clause.'
                    
                    UNION 

                    SELECT "payment_request_invoice" AS request_type, purchase_order_payment_request_approval_invoice_items.id AS approved_invoice_item_id, purchase_order_payment_request_approvals.id AS requisition_approval_id, purchase_order_payment_requests.id AS requisition_id, purchase_order_payment_requests.currency_id, approval_date AS approved_date, CONCAT(first_name," ",last_name) as approver_name, vendor_id AS creditor_id, "" AS contractor_id
                    FROM purchase_order_payment_request_approval_invoice_items
                    LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN employees ON purchase_order_payment_request_approvals.created_by = employees.employee_id
                    '.$invoice_where_clause.'
                    
                ) AS approved_cash_requisitions
                 WHERE
                    (
                        ( 
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
                           
                        ) 
                    )';

        if(!is_null($vendor_id) && (!is_null($creditor_type) && $creditor_type == "vendor")){
            $sql .= ' AND creditor_id ='.$vendor_id.' ';
        }
        if(!is_null($vendor_id) && (!is_null($creditor_type) && $creditor_type == "contractor")){
            $sql .= ' AND contractor_id ='.$vendor_id.' ';
        }

        if(!is_null($currency_id)){
            $sql .= ' AND currency_id ='.$currency_id.' ';
        }

         $sql .= ' GROUP BY requisition_approval_id, currency_id,request_type,approved_invoice_item_id
                ORDER BY approved_date desc';

        $query = $this->db->query($sql);
        $approved_items = $query->result();

        $table_items = [];
        foreach ($approved_items as $item){
            if($item->request_type == 'payment_request_invoice'){
                $approved_invoice_item = new Purchase_order_payment_request_approval_invoice_item();
                $approved_invoice_item->load($item->approved_invoice_item_id);
                $payment_request_approval = $approved_invoice_item->purchase_order_payment_request_approval();
                $payment_request = $payment_request_approval->purchase_order_payment_request();
                $currency = $payment_request->currency();

                if($approved_invoice_item->approved_amount > 0) {
                    $table_items[] = [
                        'approved_invoice_item_id'=>$item->approved_invoice_item_id,
                        'requisition_approval_id'=>$payment_request_approval->{$payment_request_approval::DB_TABLE_PK},
                        'request_type'=>'payment_request_invoice',
                        'approval_date'=>custom_standard_date($item->approved_date),
                        'nature'=>'P.O Payment (Invoice)',
                        'request_no'=>anchor(base_url('procurements/preview_approved_purchase_order_payments/'.$payment_request_approval->{$payment_request_approval::DB_TABLE_PK}),$payment_request->request_number(),'target="_blank"'),
                        'requested_for'=>$payment_request_approval->cost_center_name(),
                        'approved_by'=>$item->approver_name,
                        'amount'=>$currency->symbol . ' ' . number_format($approved_invoice_item->approved_amount),
                        'status'=>'<span class="label label-warning">Not Paid</span>'
                    ];
                }

            } else if($item->request_type == 'sub_contract_payment_requisition'){
                $sub_contract_requisition_approval = new Sub_contract_payment_requisition_approval();
                $sub_contract_requisition_approval->load($item->requisition_approval_id);
                $cost_center_name = $sub_contract_requisition_approval->sub_contract_requisition()->cost_center_name();
                $currency = $sub_contract_requisition_approval->currency();

                $sub_contract_requisition = $sub_contract_requisition_approval->sub_contract_requisition();
                $approved_item = new Sub_contract_payment_requisition_approval_item();
                $approved_item->load($item->approved_invoice_item_id);
                $approved_amount = $approved_item->approved_amount;

                if($approved_amount > 0) {
                    $table_items[] = [
                        'approved_invoice_item_id'=>$item->approved_invoice_item_id,
                        'requisition_approval_id'=>$sub_contract_requisition_approval->{$sub_contract_requisition_approval::DB_TABLE_PK},
                        'request_type'=>'sub_contract_payment_requisition',
                        'approval_date'=>custom_standard_date($item->approved_date),
                        'nature'=>'Requisition(Sub Contract Payment)',
                        'request_no'=>anchor(base_url('requisitions/preview_approved_sub_contract_payment_requsition/'.$sub_contract_requisition_approval->{$sub_contract_requisition_approval::DB_TABLE_PK}),$sub_contract_requisition->sub_contract_requisition_number(),'target="_blank"'),
                        'requested_for'=>$cost_center_name,
                        'approved_by'=>$item->approver_name,
                        'amount'=>$currency->symbol . ' ' . number_format($approved_amount),
                        'status'=>'<span class="label label-warning">Not Paid</span>'
                    ];
                }

            }
        }
        return $table_items;
    }

}

