<?php

class Project_account extends MY_Model
{

    const DB_TABLE = 'project_accounts';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $account_id;

    public function project()
    {
        $this->load->model('project');
        $project = new project();
        $project->load($this->project_id);
        return $project;
    }

    public function account()
    {
        $this->load->model('account');
        $account = new account();
        $account->load($this->account_id);
        return $account;
    }
}

