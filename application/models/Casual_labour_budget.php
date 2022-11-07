
<?php

class Casual_labour_budget extends MY_Model{
    
    const DB_TABLE = 'casual_labour_budgets';
    const DB_TABLE_PK = 'budget_id';

    public $project_id;
    public $task_id;
    public $casual_labour_type_id;
    public $rate_mode;
    public $duration;
    public $no_of_workers;
    public $rate;
    public $description;
    public $employee_id;

    public function casual_labour_type()
    {
        $this->load->model('casual_labour_type');
        $casual_employee_type = new Casual_labour_type();
        $casual_employee_type->load($this->casual_labour_type_id);
        return $casual_employee_type;
    }

    public function amount(){
        return $this->quantity * $this->rate;
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

    public function budget_figure($cost_center_id,$level = null){
        $sql = 'SELECT COALESCE(SUM(duration*no_of_workers*rate),0) AS budget_figure FROM  '.$this::DB_TABLE.' WHERE ';
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

    public function budget_items_list($cost_center_level, $cost_center_id, $limit, $start, $keyword, $order){
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

        $data = [
            'project' => $project,
            'cost_center_options' => $project->cost_center_options()
        ];
        $data['project_status'] = $project->status();


        //order string
        $order_string = dataTable_order_string(['labour_type','rate','quantity','amount','description'],$order,'labour_type');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        //Where clause
        if($cost_center_level == 'project'){
            $where_clause = ' project_id = "'.$cost_center_id.'" AND  task_id IS NULL ';
        } else {
            $where_clause = ' task_id = "'.$cost_center_id.'"';
        }

        $records_total = $this->count_rows($where_clause);


        if ($keyword != '') {
            $where_clause .= ' AND (casual_labour_types.name LIKE "%' . $keyword . '%" OR casual_labour_budgets.description LIKE "%' . $keyword . '%")';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS casual_labour_budgets.* ,(rate * no_of_workers*duration) AS amount,casual_labour_types.name as labour_type
                FROM casual_labour_budgets
                LEFT JOIN casual_labour_types ON casual_labour_budgets.casual_labour_type_id = casual_labour_types.type_id
                 WHERE 
                '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach ($results as $row) {
            $item = new Casual_labour_budget();
            $item->load($row->budget_id);
            $data['item'] = $item;
            $data['casual_labour_type'] = $item->casual_labour_type();
            $rows[] = [
                $row->labour_type,
                $row->rate_mode,
                $row->duration,
                '<span class="pull-right">' . number_format($row->rate) . '</span>',
                '<span class="pull-right">' . number_format($row->no_of_workers) . '</span>',
                '<span class="pull-right">' . number_format($row->amount) . '</span>',
                $row->description,
                $this->load->view('projects/budgets/labour/casual_labour/list_actions', $data, true)
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

}
