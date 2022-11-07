<?php
Class Sub_contract_budget extends MY_Model{

    const DB_TABLE = 'sub_contract_budgets';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $task_id;
    public $description;
    public $amount;
    public $created_by;

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

    public function budget_items_list($cost_center_level, $cost_center_id, $limit, $start, $keyword, $order){
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


        if($is_general){
            $where_clause = ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $where_clause = ' task_id = "'.$cost_center_id.'"';
        }

        //Total Records
        $records_total = $this->count_rows($where_clause);


        if($keyword != ''){
            $where_clause .= ' AND (tasks.task_name LIKE "%'.$keyword.'%" OR sub_contruct_budgets.description LIKE "%'.$keyword.'%")';
        }

        //order string
        $order_string = dataTable_order_string(['amount','task_id','description'],$order,'task_id');

        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;


        $sql = 'SELECT SQL_CALC_FOUND_ROWS id,task_id,project_id,amount,description
                FROM sub_contract_budgets 
                WHERE 
                '.$where_clause.$order_string;


        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $this->load->model('task');
        foreach($results as $row){
            $item = new Sub_contract_budget();
            $item->load($row->id);
            $data['item'] = $item;
            $data['project'] = $item->project();
            $task = new Task();
            $task->load($item->task_id);
            $rows[] = [
                $task->task_name ? :'Project Shared',
                $row->description,
                '<span class="pull-right">'.number_format($row->amount).'</span>',
                $this->load->view('projects/budgets/sub_contracts/list_actions',$data,true)
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