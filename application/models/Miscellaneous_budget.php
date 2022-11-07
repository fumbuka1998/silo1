<?php

class Miscellaneous_budget extends MY_Model{
    
    const DB_TABLE = 'miscellaneous_budgets';
    const DB_TABLE_PK = 'budget_id';

    public $project_id;
    public $task_id;
    public $expense_account_id;
    public $amount;
    public $description;
    public $employee_id;

    public function expense_account()
    {
        $this->load->model('account');
        $expense_account = new Account();
        $expense_account->load($this->expense_account_id);
        return $expense_account;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function budget_items_list($cost_center_level, $cost_center_id){

        $is_general = $cost_center_level == 'project';
        $data['cost_center_level'] = $cost_center_level;
        $data['cost_center_id'] = $cost_center_id;

        if($cost_center_level == 'project'){
            $this->load->model('project');
            $project = new Project();
            $project->load($cost_center_id);
        } else {
            $this->load->model('task');
            $task = new Task();
            $task->load($cost_center_id);
            $project = $task->project();
        }

        $data['cost_center_options'] = $project->cost_center_options();
        $data['project'] = $project;
        $data['project_status'] = $project->status();

        $limit = $this->input->post('length');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'account_name';
                break;
            case 1;
                $order_column = 'amount';
                break;
            case 2;
                $order_column = 'description';
                break;
            default:
                $order_column = 'budget_name';
        }

        $order_string = $order_column.' '.$order_dir;

        $sql = 'SELECT miscellaneous_budgets.*,account_name
                FROM miscellaneous_budgets
                LEFT JOIN accounts ON miscellaneous_budgets.expense_account_id = accounts.account_id
                WHERE
                ';

        if($is_general){
            $sql .= ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $sql .= ' task_id = "'.$cost_center_id.'"';
        }

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if($keyword != ''){
            $sql .= ' AND (account_name LIKE "%'.$keyword.'%" OR miscellaneous_budgets.description LIKE "%'.$keyword.'%")';
        }

        $sql .= " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $query = $this->db->query($sql);
        $results = $query->result();
        $records_filtered = $query->num_rows();
        $rows = [];
        foreach($results as $row){
            $item = new self;
            $item->load($row->{$this::DB_TABLE_PK});
            $data['expense_account_name'] = $row->account_name;
            $data['item'] = $item;
            $rows[] = [
                $row->account_name,
                '<span class="pull-right">'.number_format($item->amount).'</span>',
                $item->description,
                $this->load->view('projects/budgets/miscellaneous/list_actions',$data,true)
            ];
        }

        $json = [
            "budget_total" => $this->budget_figure($cost_center_id,$cost_center_level),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function budget_figure($cost_center_id,$level = null){
        $sql = 'SELECT COALESCE(SUM(amount),0) AS budget_figure FROM  '.$this::DB_TABLE.' WHERE ';
        if($level == 'project') {
            $sql  .= ' project_id = "' . $cost_center_id . '" AND task_id IS NULL';
        } else if($level == 'task'){
            $sql .= ' task_id = "' . $cost_center_id . '"';
        } else if($level == 'activity'){
            $sql .= ' task_id IN (SELECT task_id FROM tasks WHERE activity_id = "'.$cost_center_id.'")';
        } else {
            $sql .= ' project_id = "' . $cost_center_id . '" ';
        }
        $query = $this->db->query($sql);
        return $query->row()->budget_figure;
    }
}

