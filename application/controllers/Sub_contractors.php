<?php

class Sub_contractors extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->model('sub_contractor');
     }

    public function index(){
        $this->load->model('sub_contractor');
        $data['title'] = 'Sub-Contractors';
        $data['number_of_sub_contractors'] = $this->db->count_all('sub_contractors');
        $this->load->view('sub_contractors/index',$data);
    }

    public function sub_contractors_list(){
        $posted_params = dataTable_post_params();
        if($posted_params['limit'] == null){
            $data['title'] = 'Sub-Contractors';
            $data['currency_options'] = currency_dropdown_options();
            $this->load->view('sub_contractors/list',$data);
        } else {
            echo $this->sub_contractor->sub_contractors_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        }
    }

    public function save_sub_contractor($id = 0){
        $sub_contractor = new Sub_contractor();
        $edit = $sub_contractor->load($id);
        $sub_contractor->name = $this->input->post('name');
        $sub_contractor->email = $this->input->post('email');
        $sub_contractor->phone = $this->input->post('phone');
        $sub_contractor->alternative_phone = $this->input->post('alternative_phone');
        $sub_contractor->address = $this->input->post('address');

        if(!$edit){
            $this->load->model('account');
            $account =  new Account();
            $account->account_name = $sub_contractor->name.' - Account Payable';
            $account->account_group_id = 1;
            $account->opening_balance = $this->input->post('account_opening_balance');
            $account->currency_id = $this->input->post('currency_id');
            $account->description = 'Account Payable For '.$sub_contractor->name;
            $account->save();
            $sub_contractor->account_id = $account->{$account::DB_TABLE_PK};
        }

        if($sub_contractor->save()){
            redirect(base_url('sub_contractors/profile/'.$sub_contractor->{$sub_contractor::DB_TABLE_PK}));
        }
    }

    public function profile($id = 0){
        $sub_contractor = new Sub_contractor();
        if($sub_contractor->load($id)){
            $data['title'] = $sub_contractor->name;
            $data['sub_contractor'] = $sub_contractor;
            $data['currency_options'] = currency_dropdown_options();
            $this->load->view('sub_contractors/profile',$data);
        }
    }

    public function sub_contracts_list($sub_contractor_id=0){

        $this->load->model('Sub_contract');
        $posted_params = dataTable_post_params();
        if($posted_params['limit'] == null){
            $data['title'] = 'Sub-Contracts';
            $this->load->view('sub_contractors/sub_contracts_tab',$data);
        } else {
            echo $this->Sub_contract->sub_contracts_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$sub_contractor_id);
        }
    }

// start for project

    public function save_project_sub_contract(){
        $this->load->model('Sub_contract');
        $Sub_contract = new Sub_contract();
        $edit = $Sub_contract->load($this->input->post('sub_contract_id'));
        $Sub_contract->sub_contractor_id = $this->input->post('sub_contractor_id');
        $Sub_contract->project_id = $this->input->post('project_id');
        $Sub_contract->contract_name = $this->input->post('contract_name');
        $Sub_contract->contract_date = $this->input->post('contract_date');
        $Sub_contract->description = $this->input->post('description');
        $Sub_contract->created_by = $this->session->userdata('employee_id');

        $Sub_contract->save();

    }

    public function project_sub_contracts_list($project_id=0){

        $this->load->model('Sub_contract');
        $Sub_contract= new Sub_contract();
        $posted_params = dataTable_post_params();
            echo $Sub_contract->project_sub_contracts_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$project_id);

    }

    public function delete_project_sub_contract(){
        $this->load->model('Sub_contract');
        $Sub_contract= new Sub_contract();
        if($Sub_contract->load($this->input->post('sub_contract_id'))){

            $Sub_contract->delete();
        }
    }



    public function save_sub_contract_item(){
        $this->load->model('Sub_contract_item');
        $sub_contract_item = new Sub_contract_item();
        $sub_contract_item ->sub_contract_id= $this->input->post('sub_contract_id');
        $sub_contract_item ->start_date= $this->input->post('start_date');
        $sub_contract_item ->end_date= $this->input->post('end_date');
        $sub_contract_item ->contract_sum= $this->input->post('contract_sum');
        $sub_contract_item ->description= $this->input->post('description');
        $sub_contract_item ->task_id= $this->input->post('task_id');
        $sub_contract_item->task_id = $sub_contract_item->task_id != '' ? $sub_contract_item->task_id : null;

        $sub_contract_item->save();

    }

    public function delete_sub_contract_item(){
        $this->load->model('Sub_contract_item');
        $sub_contract_item = new Sub_contract_item();
        if($sub_contract_item->load($this->input->post('sub_contract_item_id'))){

            $sub_contract_item->delete();
        }
    }

    public function load_sub_contract_items(){
        $this->load->model('Sub_contract');
        $sub_contract= new Sub_contract();
        if($sub_contract->load($this->input->post('sub_contract_id'))){
            $data['sub_contract_items'] = $sub_contract->sub_contract_items();
            $this->load->view('projects/sub_contracts/sub_contract_items/sub_contract_items_tab',$data);
        }
    }




}
