<?php

class Permanent_labour_budget extends MY_Model{
    
    const DB_TABLE = 'permanent_labour_budgets';
    const DB_TABLE_PK = 'budget_id';

    public $project_id;
    public $task_id;
    public $job_position_id;
    public $salary_rate;
    public $description;
    public $rate_mode;
    public $duration;
    public $no_of_staff;
    public $allowance_rate;
    public $employee_id;
    

    public function job_position()
    {
        $this->load->model('job_position');
        $job_position = new Job_position();
        $job_position->load($this->job_position_id);
        return $job_position;
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


        if($is_general){
            $where_clause = ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $where_clause = ' task_id = "'.$cost_center_id.'"';
        }

        //Total Records
        $records_total = $this->count_rows($where_clause);


        if($keyword != ''){
            $where_clause .= ' AND (job_positions.position_name LIKE "%'.$keyword.'%" OR permanent_labour_budgets.description LIKE "%'.$keyword.'%")';
        }

        //order string
        $order_string = dataTable_order_string([
            'position_name','rate_mode','duration','salary_rate',
            'allowance_rate','no_of_staff','salary_amount','allowance_amount','total_amount','description'
        ],$order,'rate');

        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;


        $sql = 'SELECT SQL_CALC_FOUND_ROWS permanent_labour_budgets.* ,job_positions.position_name,
                (salary_rate*no_of_staff*duration) AS salary_amount, 
                (allowance_rate*no_of_staff*duration) AS allowance_amount,
                ((allowance_rate*no_of_staff*duration)+(salary_rate*no_of_staff*duration)) AS total_amount
                FROM permanent_labour_budgets 
                LEFT JOIN job_positions ON permanent_labour_budgets.job_position_id = job_positions.job_position_id
                WHERE 
                '.$where_clause.$order_string;


        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];

        foreach($results as $row){
            $item = new Permanent_labour_budget();
            $item->load($row->budget_id);
            $data['job_position'] = $item->job_position();
            $data['item'] = $item;
            $data['project'] = $item->project();
            $data['project_status'] = $item->project()->status();
            $rows[] = [

                $row->position_name,
                $row->rate_mode,
                $row->duration,
                '<span class="pull-right">'.number_format($row->salary_rate).'</span>',
                '<span class="pull-right">'.number_format($row->allowance_rate).'</span>',
                $row->no_of_staff,
                '<span class="pull-right">'.number_format($row->salary_amount).'</span>',
                '<span class="pull-right">'.number_format($row->allowance_amount).'</span>',
                '<span class="pull-right">'.number_format($row->allowance_amount+$row->salary_amount).'</span>',
                $row->description,
                $this->load->view('projects/budgets/labour/permanent_labour/list_actions',$data,true)
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
        $sql = 'SELECT (COALESCE(SUM(salary_rate*duration*no_of_staff),0) + COALESCE(SUM(allowance_rate*duration*no_of_staff),0)) AS budget_figure FROM  '.$this::DB_TABLE.' WHERE ';
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
        return doubleval($query->row()->budget_figure);
    }

}

?>