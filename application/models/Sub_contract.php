<?php

class Sub_contract extends MY_Model{

    const DB_TABLE = 'sub_contracts';
    const DB_TABLE_PK = 'id';

    public $stakeholder_id;
    public $project_id;
    public $contract_name;
    public $contract_date;
    public $description;
    public $created_by;


    public function sub_contracts_list($limit, $start, $keyword, $order,$stakeholder_id){
        $order_string = dataTable_order_string(['contract_name','stakeholder_id','contract_date'],$order,'contract_name');
        $where = ' stakeholder_id = "'.$stakeholder_id.'" ';

        if($keyword != ''){
            $where .= 'contract_name LIKE "%'.$keyword.'%" OR stakeholder_id LIKE "%'.$keyword.'%" OR contract_date LIKE "%'.$keyword.'%" ';
        }
        $sub_contracts = $this->get($limit,$start,$where,$order_string);
        $rows = [];

        foreach($sub_contracts as $sub_contract){
            $data['sub_contract'] = $sub_contract;
            $project = $sub_contract->project();
            $rows[] = [
               anchor(base_url('projects/profile/'.$project->{$project::DB_TABLE_PK}),$sub_contract->project()->project_name),
               $sub_contract->contract_name,
               $sub_contract->contract_date,
               $sub_contract->created_at,
               $sub_contract->employee()->full_name(),
               $sub_contract->description

            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows(['stakeholder_id' => $stakeholder_id]);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function project_sub_contracts_list($limit, $start, $keyword, $order,$project_id){
        $order_string = dataTable_order_string(['contract_name','stakeholder_id','contract_date'],$order,'contract_name');
        $where = 'project_id = "'.$project_id.'"';

        $this->load->model('Project');
        $project= new Project();
        $project->load($project_id);
        $data['cost_center_options'] = $project->cost_center_options();

        if($keyword != ''){
            $where = 'contract_name LIKE "%'.$keyword.'%" OR stakeholder_id LIKE "%'.$keyword.'%" ';
        }

        $sub_contracts = $this->get($limit,$start,$where,$order_string);
        $rows = [];

        foreach($sub_contracts as $sub_contract){
            $data['sub_contract'] = $sub_contract;
            $this->load->model(['stakeholder']);
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();

            $rows[] = [
                $sub_contract-> contract_name,
                strftime(" %d - %b - %Y",strtotime($sub_contract-> contract_date)),
                $sub_contract-> stakeholder()->stakeholder_name,
                $this->load->view('projects/sub_contracts/sub_contract_actions',$data,true)

            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows(['project_id' => $project_id]);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function stakeholder()
    {
        $this->load->model('stakeholder');
        $stakeholder= new Stakeholder();
        $stakeholder->load($this->stakeholder_id);
        return $stakeholder;
    }

    public function employee(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function project(){
        $this->load->model('Project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function sub_contract_items(){
        $this->load->model('Sub_contract_item');
        $sub_contract_items=$this->Sub_contract_item->get(0,0,[
            'sub_contract_id' => $this->{$this::DB_TABLE_PK}]);
        return $sub_contract_items;

    }

    public function sub_contract_amount()
    {
        $sql = 'SELECT COALESCE(SUM(contract_sum),0) AS amount_figure FROM sub_contracts_items
                WHERE sub_contract_id = '.$this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return $query->row()->amount_figure;
    }

    public function certified_amount(){
        $sql = 'SELECT COALESCE(SUM(certified_amount),0) AS certified_amount FROM sub_contract_certificates
            WHERE sub_contract_id = '.$this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return $query->row()->certified_amount;
    }

    public function paid_amount(){
        $sql = 'SELECT (
                           (
                            SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items
                            LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                            LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                            LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
                            WHERE sub_contract_id = '.$this->{$this::DB_TABLE_PK};
                $sql .= ' ) + (
                            SELECT COALESCE(SUM(withheld_amount),0) FROM withholding_taxes
                            LEFT JOIN payment_voucher_items ON withholding_taxes.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                            LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                            LEFT JOIN sub_contract_certificate_payment_vouchers ON payment_vouchers.payment_voucher_id = sub_contract_certificate_payment_vouchers.payment_voucher_id
                            LEFT JOIN sub_contract_certificates ON sub_contract_certificate_payment_vouchers.sub_contract_certificate_id = sub_contract_certificates.id
                            WHERE sub_contract_id = '.$this->{$this::DB_TABLE_PK};

            $sql .= '       )
                        ) AS paid_sub_contract_cost ';

        $query = $this->db->query($sql);
        return $query->row()->paid_sub_contract_cost;

    }

    public function drop_down_option($project_id = null){
        if(!is_null($project_id)){
            $sub_contracts = $this->get(0,0,['projedct_id'=>$project_id]);
        } else {
            $sub_contracts = $this->get();
        }
        $options[] = '&nbsp;';
        foreach($sub_contracts as $sub_contract){
            $options[$sub_contract->{$sub_contract::DB_TABLE_PK}] = $sub_contract->contract_name;
        }
        return $options;
    }

    public function certificates($not_paid = false){
        $this->load->model('sub_contract_certificate');
        $where = ' WHERE sub_contract_id = '.$this->{$this::DB_TABLE_PK}.'';
        if($not_paid){
            $where .= ' AND (
                parent_table.certified_amount - (
                    SELECT COALESCE(SUM(approved_amount*1.18),0) AS approved_amount 
                    FROM sub_contract_payment_requisition_approval_items
                    LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id
                    LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                    LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                    WHERE is_final = 1 AND status = "APPROVED" AND certificate_id = parent_table.id
                )
            ) > 0';
        }
        $sql = 'SELECT * FROM sub_contract_certificates AS parent_table'.$where;
        $query = $this->db->query($sql);
        $sub_contract_certificates = $query->result();
        $options[] = '&nbsp;';
        foreach($sub_contract_certificates as $sub_contract_certificate){
            $options[$sub_contract_certificate->id] = $sub_contract_certificate->certificate_number;
        }
        return $options;
    }


}

