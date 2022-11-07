<?php

class Clients extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->model('client');
     }

    public function index(){
        check_permission('Clients', true);
        $limit = $this->input->post('length');
        if($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->client->clients_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $data['title'] = 'Clients';
            $data['currency_options'] = currency_dropdown_options();
            $this->load->view('clients/index', $data);
        }
    }

    public function save($id = 0){
        $client = new Client();
        $edit = $client->load($id);
        $client->client_name = $this->input->post('client_name');
        $client->phone = $this->input->post('phone');
        $client->alternative_phone = $this->input->post('alternative_phone');
        $client->email = $this->input->post('email');
        $client->address = $this->input->post('address');
        
        if(!$edit){
            $this->load->model('account');
            $account =  new Account();
            $account->account_name = $client->client_name.' - Account Receivable';
            $account->account_group_id = 2;
            $account->opening_balance = $this->input->post('account_opening_balance');
            $account->description = 'Account Receivable For '.$client->client_name;
            $account->save();
            $client->account_id = $account->{$account::DB_TABLE_PK};
        }

        if($client->save()){
            $description = 'Client '.$client->client_name.' was ';
            $action = $edit ? 'Client Update' : 'Client Registration';
            $description .= $edit ? 'updated' : 'created';
            system_log($action,$description);
            redirect(base_url('clients/profile/'.$client->{$client::DB_TABLE_PK}));
        } else {
            redirect(base_url());
        }
    }

    public function profile($id = 0){
        $client = new Client();
        if($client->load($id)) {
            $data['client'] = $client;
            $data['title'] = $client->client_name;
            $this->load->view('clients/profile', $data);
        } else {
            redirect(base_url());
        }
    }

    public function projects_list($client_id = 0){
        $this->load->model('project');
        $limit = $this->input->post('length');
        if($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->project->projects_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],null,$client_id);
        } else {
            $data['title'] = 'Projects List';
            $data['project'] = new Project();
            $this->load->view('projects/projects_list', $data);
        }
    }
}

