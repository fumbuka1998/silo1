<?php

class Permanent_labour_cost extends MY_Model{
    
    const DB_TABLE = 'permanent_labour_costs';
    const DB_TABLE_PK = 'permanent_labour_cost_id';

    public $project_team_member_id;
    public $working_mode;
    public $cost_date;
    public $duration;
    public $salary_rate;
    public $allowance_rate;
    public $task_id;
    public $start_date;
    public $end_date;
    public $description;
    public $employee_id;


    public function allowance_cost($cost_center_id,$level,$from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(allowance_rate*duration),0) AS total_allowance FROM '.$this::DB_TABLE.'
                LEFT JOIN project_team_members ON permanent_labour_costs.project_team_member_id = project_team_members.member_id
                WHERE ';
        if($level == 'project') {
            $sql  .= ' project_id = "' . $cost_center_id . '" AND task_id IS NULL';
        } else if($level == 'task'){
            $sql .= ' task_id = "' . $cost_center_id . '"';
        } else if($level == 'activity'){
            $sql .= ' task_id IN (SELECT task_id FROM tasks WHERE activity_id = "'.$cost_center_id.'")';
        } else {
            $sql .= ' project_id = "' . $cost_center_id . '" ';
        }

        if($from != null){
            $sql .= ' AND cost_date >= "'.$from.'" ';
        }

        if($to != null){
            $sql .= ' AND cost_date <= "'.$to.'" ';
        }
        
        $query = $this->db->query($sql);
        return doubleval($query->row()->total_allowance);
    }

    public function salary_cost($cost_center_id,$level,$from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(salary_rate*duration),0) AS total_salary FROM '.$this::DB_TABLE.'
                LEFT JOIN project_team_members ON permanent_labour_costs.project_team_member_id = project_team_members.member_id
                WHERE ';
        if($level == 'project') {
            $sql  .= ' project_id = "' . $cost_center_id . '" AND task_id IS NULL';
        } else if($level == 'task'){
            $sql .= ' task_id = "' . $cost_center_id . '"';
        } else if($level == 'activity'){
            $sql .= ' task_id IN (SELECT task_id FROM tasks WHERE activity_id = "'.$cost_center_id.'")';
        } else {
            $sql .= ' project_id = "' . $cost_center_id . '" ';
        }

        if($from != null){
            $sql .= ' AND cost_date >= "'.$from.'" ';
        }

        if($to != null){
            $sql .= ' AND cost_date <= "'.$to.'" ';
        }

        $query = $this->db->query($sql);
        return floatval($query->row()->total_salary);
    }

    public function actual_cost($cost_center_id,$cost_center_level,$from = null, $to = null){
        return $this->allowance_cost($cost_center_id,$cost_center_level,$from, $to) + $this->salary_cost($cost_center_id,$cost_center_level, $from, $to);
    }

    public function project_team_member()
    {
        $this->load->model('project_team_member');
        $project_team_member = new Project_team_member();
        $project_team_member->load($this->project_team_member_id);
        return $project_team_member;
    }

    public function costs_items_list($cost_center_level, $cost_center_id, $limit, $start, $keyword, $order){
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

        //order string
        $order_string = dataTable_order_string(['employee_name','position_name','working_mode','cost_date','duration','description'],$order,'dates');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        if($is_general){
            $where = ' project_team_members.project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $where = ' task_id = "'.$cost_center_id.'"';
        }

        //Total records
        $sql = 'SELECT COUNT(permanent_labour_cost_id) AS number_of_costs FROM permanent_labour_costs
                LEFT JOIN project_team_members ON permanent_labour_costs.project_team_member_id = project_team_members.member_id
                WHERE '.$where;
        $query = $this->db->query($sql);
        $records_total = $query->row()->number_of_costs;

        if($keyword != ''){
            $where .= ' AND (first_name LIKE "%'.$keyword.'%" OR middle_name LIKE "%'.$keyword.'%" OR last_name LIKE "%'.$keyword.'%" OR position_name LIKE "%'.$keyword.'%"
             OR cost_date LIKE "%'.$keyword.'%" OR start_date LIKE "%'.$keyword.'%" OR end_date LIKE "%'.$keyword.'%" OR position_name LIKE "%'.$keyword.'%" 
             OR working_mode LIKE "%'.$keyword.'%" OR permanent_labour_costs.description LIKE "%'.$keyword.'%")';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS permanent_labour_costs.*,CONCAT(first_name," ",last_name) AS employee_name,position_name,task_id
                FROM permanent_labour_costs
                LEFT JOIN project_team_members ON permanent_labour_costs.project_team_member_id = project_team_members.member_id
                LEFT JOIN job_positions ON project_team_members.job_position_id = job_positions.job_position_id
                LEFT JOIN employees ON project_team_members.employee_id = employees.employee_id
                WHERE '.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();


        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $data['cost_center_type'] = 'project';
        foreach($results as $row){
            $labour_cost_item = new self();
            $labour_cost_item->load($row->{$this::DB_TABLE_PK});
            $data['employee_id'] = $row->employee_id;
            $data['item_id'] = $row->{$this::DB_TABLE_PK};
            $amount = $row->task_id == null ? $labour_cost_item->allowance_cost($cost_center_id,'project',$labour_cost_item->start_date,$labour_cost_item->end_date) : $labour_cost_item->allowance_cost($cost_center_id,'task',$labour_cost_item->start_date,$labour_cost_item->end_date);
            $dates = $row->working_mode == 'date_range' ? custom_standard_date($row->start_date).' - '.custom_standard_date($row->end_date) : custom_standard_date($row->cost_date);
            $rows[] = [
                $row->employee_name,
                $row->position_name,
                strtoupper(str_replace('_', ' ', $row->working_mode)),
                $dates,
                $row->duration,
                '<span style="text-align: right">'.number_format($amount,2).'</span>',
                $row->description,
                $this->load->view('projects/costs/labour/permanent_labour_cost_list_actions',$data,true)
            ];
        }

        $json = [
            "cost_total" => $this->actual_cost($cost_center_id,$cost_center_level),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }


}

