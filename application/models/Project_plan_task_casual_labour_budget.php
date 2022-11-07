<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/5/2018
 * Time: 12:55 PM
 */

class Project_plan_task_casual_labour_budget extends MY_Model{
    const DB_TABLE = 'project_plan_task_casual_labour_budgets';
    const DB_TABLE_PK = 'id';

    public $project_plan_task_id;
    public $casual_labour_type_id;
    public $rate_mode;
    public $duration;
    public $no_of_workers;
    public $rate;
    public $description;
    public $created_by;

    public function amount(){
        return $this->rate * $this->no_of_workers * $this->duration;
    }

    public function project_plan_task()
    {
        $this->load->model('project_plan_task');
        $project_plan_task = new Project_plan_task();
        $project_plan_task->load($this->project_plan_task_id);
        return $project_plan_task;
    }

    public function casual_labour_type()
    {
        $this->load->model('casual_labour_type');
        $casual_labour_type = new Casual_labour_type();
        $casual_labour_type->load($this->casual_labour_type_id);
        return $casual_labour_type;
    }

    public function plan_labour_budget_list($limit, $start, $keyword, $order, $project_plan_id){
        $order_string = dataTable_order_string(['tasks.task_name','project_plan_task_casual_labour_budgets.rate','project_plan_task_casual_labour_budgets.no_of_workers','rate_mode','project_plan_task_casual_labour_budgets.duration','casual_labour_types.name'],$order,'tasks.task_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = ' WHERE project_plan_tasks.project_plan_id ='.$project_plan_id.' ';

        $sql = 'SELECT COUNT(project_plan_task_casual_labour_budgets.id) AS records_total FROM project_plan_task_casual_labour_budgets
                LEFT JOIN project_plan_tasks ON project_plan_task_casual_labour_budgets.project_plan_task_id = project_plan_tasks.id
                '.$where_clause;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;


        if ($keyword != '') {
            $where_clause .= ' AND (
                                        tasks.task_name LIKE "%'.$keyword.'%" 
                                        OR project_plan_task_casual_labour_budgets.rate LIKE "%'.$keyword.'%" 
                                        OR project_plan_task_casual_labour_budgets.no_of_workers LIKE "%'.$keyword.'%" 
                                        OR rate_mode LIKE "%'.$keyword.'%" 
                                        OR project_plan_task_casual_labour_budgets.duration LIKE "%'.$keyword.'%"  
                                        OR casual_labour_types.name LIKE "%'.$keyword.'%" 
                                    )';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS project_plan_task_casual_labour_budgets.id AS plan_labour_budget_id, rate_mode, tasks.task_name AS task_assigned, casual_labour_types.name AS labour_type_name, project_plan_task_casual_labour_budgets.rate AS rate, project_plan_task_casual_labour_budgets.duration AS duration, project_plan_task_casual_labour_budgets.description AS description, no_of_workers
                FROM project_plan_task_casual_labour_budgets
                LEFT JOIN casual_labour_types On project_plan_task_casual_labour_budgets.casual_labour_type_id = casual_labour_types.type_id
                LEFT JOIN project_plan_tasks ON project_plan_task_casual_labour_budgets.project_plan_task_id = project_plan_tasks.id
                LEFT JOIN tasks ON project_plan_tasks.task_id = tasks.task_id
                '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model('project_plan');
        $project_plan = new Project_plan();
        $project_plan->load($project_plan_id);
        $data['project_plan'] = $project_plan;
        $data['project'] = $project_plan->project();

        $rows = [];
        foreach ($results as $row){
            $plan_labour_budget = new self();
            $plan_labour_budget->load($row->plan_labour_budget_id);
            $data['plan_labour_budget'] = $plan_labour_budget;

            $rows[] = [
                wordwrap($row->labour_type_name,20,'<br/>'),
                wordwrap($row->task_assigned,40,'<br/>'),
                ucfirst($row->rate_mode),
                $row->no_of_workers,
                '<span class="pull-right">'.number_format($row->rate).'</span>',
                $row->duration.' '.$plan_labour_budget->rate_mode(),
                '<span class="pull-right">'.number_format($row->rate * $row->no_of_workers * $row->duration).'</span>',
                $this->load->view('projects/plans/project_plan_task_labour/list_actions',$data,true)
            ];

        }

        $json = [
            "total_budget_amount" => $this->total_plan_casual_labour_budget($project_plan_id),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function total_plan_casual_labour_budget($project_plan_id, $project_plan_task_id = null, $level = null){

        $sql = 'SELECT COALESCE(SUM(project_plan_task_casual_labour_budgets.rate * project_plan_task_casual_labour_budgets.no_of_workers*duration),0) AS total_budget_amount FROM project_plan_task_casual_labour_budgets
            LEFT JOIN project_plan_tasks ON project_plan_task_casual_labour_budgets.project_plan_task_id = project_plan_tasks.id
            WHERE project_plan_tasks.project_plan_id ='.$project_plan_id;
        if($level == 'task'){
            $sql .= ' AND project_plan_task_id ="'.$project_plan_task_id.'" ';
        }

        $query = $this->db->query($sql);
        return $query->row()->total_budget_amount;
    }

    public function rate_mode(){
        if($this->rate_mode =='hourly'){
            return 'Hour(s)';
        }else if($this->rate_mode == 'daily'){
            return 'Day(s)';
        }else{
            return 'Month(s)';
        }
    }


}