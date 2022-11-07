<?php

class Contractor extends MY_Model{
    
    const DB_TABLE = 'contractors';
    const DB_TABLE_PK = 'id';

    public $contractor_name;
    public $phone;
    public $alternative_phone;
    public $email;
    public $address;

    public function contractors_list($limit, $start, $keyword, $order){ //ptm
        //order string
        $order_string = dataTable_order_string(['contractor_name','phone','alternative_phone','email','address'],$order,'contractor_name');

        $where = '';
        if($keyword != ''){
            $where .= ' contractor_name LIKE "%'.$keyword.'%" OR phone LIKE "%'.$keyword.'%" OR alternative_phone LIKE "%'.$keyword.'%"  OR email LIKE "%'.$keyword.'%"  OR address LIKE "%'.$keyword.'%" ';
        }

        $contractors = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach($contractors as $contractor){
            $rows[] = [
                anchor(base_url('contractors/profile/'.$contractor->{$contractor::DB_TABLE_PK}),$contractor->contractor_name),
                $contractor->phone,
                $contractor->alternative_phone,
                $contractor->email,
                $contractor->address
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

    public function contractor_options($with_us = false)
    {
        $options[''] = $with_us ? get_company_details()->company_name : '&nbsp;';
        $sub_contractors = $this->get(0,0,'','contractor_name');
        foreach($sub_contractors as $sub_contractor){
            $options[$sub_contractor->{$sub_contractor::DB_TABLE_PK}] = $sub_contractor->contractor_name;
        }
        return $options;
    }

    public function sub_contracts($project_id = null){
        $this->load->model('sub_contract');
        if(!is_null($project_id)){
            $where = [
                'project_id'=>$project_id,
                'contractor_id'=>$this->{$this::DB_TABLE_PK}
            ];
        } else {
            $where = ['contractor_id'=>$this->{$this::DB_TABLE_PK}];
        }
        return $this->sub_contract->get(0,0,$where);
    }

    public function sub_contract_options($project_id = null){
        $contractors = $this->contractor->get(0,0,'','contractor_name');
        $options[] = '&nbsp;';
        foreach($contractors as $contractor){
            if(!is_null($project_id)){
                $sub_contracts = $contractor->sub_contracts($project_id);
            } else {
                $sub_contracts = $contractor->sub_contracts();
            }
            foreach($sub_contracts as $sub_contract){
                $options[$contractor->contractor_name][$sub_contract->{$sub_contract::DB_TABLE_PK}] = $sub_contract->contract_name;
            }

        }
        return $options;
    }

    public function contractor_with_unpaid_contracts(){
        $sql = 'SELECT contractors.id FROM contractors 
                LEFT JOIN sub_contracts ON contractors.id = sub_contracts.contractor_id
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
            $contractor = new self();
            $contractor->load($row->id);
            $options['contractor_'.$contractor->{$contractor::DB_TABLE_PK}] = $contractor->contractor_name;
        }
        return $options;
    }

    public function account_options(){
        $this->load->model('contractor_account');
        $junctions = $this->contractor_account->get(0,0,['contractor_id'=>$this->{$this::DB_TABLE_PK}]);
        $options[] = '&nbsp;';
        foreach($junctions as $junction){
            $account = $junction->account();
            $options[$account->{$account::DB_TABLE_PK}] = $account->account_name;
        }
        return $options;
    }

    public function contractor_acount_group_id(){
        $this->load->model('account_group');
        $account_groups = $this->account_group->get(0,0,['group_name'=>'ACCOUNT PAYABLE']);
        $account_group = array_shift($account_groups);
        return $account_group->{$account_group::DB_TABLE_PK};
    }



}

