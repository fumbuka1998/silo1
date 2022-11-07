<?php

class Cash_requisition extends MY_Model{
    
    const DB_TABLE = 'cash_requisitions';
    const DB_TABLE_PK = 'id';

    public $account_id;
    public $request_date;
    public $required_date;
    public $approved_date;
    public $requester_id;
    public $approver_id;
    public $requesting_remarks;
    public $approving_remarks;
    public $status;

    public function delete_items(){
        $this->db->where('requisition_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete(['cash_requisition_items']);
    }

    public function account($account_id = null)
    {
        $this->load->model('account');
        $account_id = is_null($account_id) ? $this->account_id : $account_id;
        $account = new Account();
        $account->load($account_id);
        return $account;
    }

    public function requisition_number(){
        return add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function cash_requisitions_list($account_id, $limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['request_date','approved_date','id','requested_amount','status'],$order,'request_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where = ' account_id = "'.$account_id.'" ';

        $records_total = $this->count_rows($where);


        if ($keyword != '') {
            $where .= 'AND (request_date LIKE "%' . $keyword . '%" OR status LIKE "%' . $keyword . '%" OR required_date LIKE "%' . $keyword . '%")
            ';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS cash_requisitions.id, request_date, approved_date, status, SUM(requested_quantity*requested_rate) AS requested_amount, SUM(approved_rate*approved_quantity) AS approved_amount
                        FROM cash_requisitions
                        LEFT JOIN cash_requisition_items ON cash_requisitions.id = cash_requisition_items.cash_requisition_id
                        WHERE '.$where.' GROUP BY id'.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $account = $this->account($account_id);
        $data['account'] = $account;
        $data['account_is_site_petty_cash'] = $account->is_site_petty_cash();
        $data['expense_pv_cost_center_type_options'] = $account->expense_pv_cost_center_type_options();
        $data['expense_pv_cost_center_options'] = $account->expense_pv_cost_center_options();
        $expense_pv_debit_account_group = $account->is_site_petty_cash() ? 'DIRECT EXPENSES' : 'INDIRECT EXPENSES';
        $data['expense_pv_debit_account_options'] = $account->expense_pv_debit_account_options($expense_pv_debit_account_group);
        foreach ($results as $row) {

            $status_label_class = 'label label-';
            if ($row->status == 'PENDING') {
                $status_label_class .= 'info';
            } else if ($row->status == 'APPROVED' || $row->status == 'PAID') {
                $status_label_class .= 'success';
            } else if ($row->status == 'CLOSED') {
                $status_label_class .= 'primary';
            } else {
                $status_label_class .= 'danger';
            }

            $data['requisition_id'] = $row->id;
            $data['status'] = $row->status;
            $data['items'] = $this->cash_requisition_items($row->id);
            $rows[] = [
                custom_standard_date($row->request_date),
                $row->approved_date != null ? custom_standard_date($row->approved_date) : '',
                add_leading_zeros($row->id),
                number_format($row->requested_amount),
                '<span class="label label-' . $status_label_class . '">' . $row->status . '</span>',
                $this->load->view('finance/account_profile/cash_requisition_list_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function cash_requisition_items($cash_requisition_id = null){
        $cash_requisition_id = is_null($cash_requisition_id) ? $this->{$this::DB_TABLE_PK} : $cash_requisition_id;
        $this->load->model('cash_requisition_item');
        return $this->cash_requisition_item->get(0,0,['cash_requisition_id' => $cash_requisition_id]);
    }

    public function requester()
    {
        $this->load->model('employee');
        $requiester = new Employee();
        $requiester->load($this->requester_id);
        return $requiester;
    }

    public function approver()
    {
        $this->load->model('employee');
        $approver = new Employee();
        $approver->load($this->approver_id);
        return $approver;
    }

}

