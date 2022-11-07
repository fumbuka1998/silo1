<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/26/2018
 * Time: 12:10 PM
 */

class Project_plan_task_execution_casual_labour extends MY_Model
{
    const DB_TABLE = 'project_plan_task_execution_casual_labour';
    const DB_TABLE_PK = 'id';

    public $plan_task_execution_id;
    public $date;
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

    public function project_plan_task_execution()
    {
        $this->load->model('project_plan_task_execution');
        $project_plan_task_execution = new Project_plan_task_execution();
        $project_plan_task_execution->load($this->plan_task_execution_id);
        return $project_plan_task_execution;
    }

    public function casual_labour_type()
    {
        $this->load->model('casual_labour_type');
        $casual_labour_type = new Casual_labour_type();
        $casual_labour_type->load($this->casual_labour_type_id);
        return $casual_labour_type;
    }

    public function project_plan_labour_execution_list($limit, $start, $keyword, $order, $project_plan_id){
        $order_string = dataTable_order_string(['tasks.task_name','project_plan_task_execution_casual_labour.rate','project_plan_task_execution_casual_labour.no_of_workers','rate_mode','project_plan_task_execution_casual_labour.duration','casual_labour_types.name'],$order,'tasks.task_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = ' WHERE project_plan_task_executions.project_plan_id ='.$project_plan_id.' ';

        $sql = 'SELECT COUNT(project_plan_task_execution_casual_labour.id) AS records_total FROM project_plan_task_execution_casual_labour
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_casual_labour.plan_task_execution_id = project_plan_task_executions.id
                '.$where_clause;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;


        if ($keyword != '') {
            $where_clause .= ' AND (
                                        tasks.task_name LIKE "%'.$keyword.'%" 
                                        OR project_plan_task_execution_casual_labour.rate LIKE "%'.$keyword.'%" 
                                        OR project_plan_task_execution_casual_labour.no_of_workers LIKE "%'.$keyword.'%" 
                                        OR rate_mode LIKE "%'.$keyword.'%" 
                                        OR project_plan_task_execution_casual_labour.duration LIKE "%'.$keyword.'%"  
                                        OR casual_labour_types.name LIKE "%'.$keyword.'%" 
                                    )';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS project_plan_task_execution_casual_labour.id AS plan_execution_labour_id, project_plan_task_execution_casual_labour.date AS execution_date, rate_mode, tasks.task_name AS task_assigned, casual_labour_types.name AS labour_type_name, project_plan_task_execution_casual_labour.rate AS rate, project_plan_task_execution_casual_labour.duration AS duration, project_plan_task_execution_casual_labour.description AS description, no_of_workers
                FROM project_plan_task_execution_casual_labour
                LEFT JOIN casual_labour_types On project_plan_task_execution_casual_labour.casual_labour_type_id = casual_labour_types.type_id
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_casual_labour.plan_task_execution_id = project_plan_task_executions.id
                LEFT JOIN tasks ON project_plan_task_executions.task_id = tasks.task_id
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
            $plan_execution_labour = new self();
            $plan_execution_labour->load($row->plan_execution_labour_id);
            $data['plan_execution_labour'] = $plan_execution_labour;

            $rows[] = [
                custom_standard_date($row->execution_date),
                $row->labour_type_name,
                $row->task_assigned,
                $row->rate_mode,
                $row->no_of_workers,
                '<span class="pull-right">'.number_format($row->rate).'</span>',
                $row->duration.' '.$plan_execution_labour->rate_mode(),
                '<span class="pull-right">'.number_format($row->rate * $row->no_of_workers * $row->duration).'</span>',
                $this->load->view('projects/executions/plan_task_execution_casual_labour/list_actions',$data,true)
            ];

        }

        $json = [
            "total_execution_amount" => $this->total_plan_execution_casual_labour_cost($project_plan_id),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function total_plan_execution_casual_labour_cost($project_plan_id){

        $sql = 'SELECT COALESCE(SUM(project_plan_task_execution_casual_labour.rate * project_plan_task_execution_casual_labour.no_of_workers*duration),0) AS total_execution_amount 
                FROM project_plan_task_execution_casual_labour
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_casual_labour.plan_task_execution_id = project_plan_task_executions.id
                WHERE project_plan_task_executions.project_plan_id ='.$project_plan_id;

        $query = $this->db->query($sql);
        return $query->row()->total_execution_amount;
    }

    public function rate_mode(){
        if($this->rate_mode =='Hourly'){
            return 'Hour(s)';
        }else if($this->rate_mode == 'Daily'){
            return 'Day(s)';
        }else{
            return 'Month(s)';
        }
    }

    public function actual_cost($cost_center_id, $level = null, $from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(project_plan_task_execution_casual_labour.rate * project_plan_task_execution_casual_labour.no_of_workers*duration),0) AS total_execution_amount 
                FROM project_plan_task_execution_casual_labour
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_casual_labour.plan_task_execution_id = project_plan_task_executions.id
                LEFT JOIN project_plans ON project_plan_task_executions.project_plan_id = project_plans.id
                WHERE ';

        if($level == 'project') {
            $sql .= ' project_plans.project_id = "' . $cost_center_id. '" AND project_plan_task_executions.task_id IS NULL ';
        } else if($level == 'task'){
            $sql .= ' project_plan_task_executions.task_id = "' . $cost_center_id . '"';
        } else if($level == 'activity'){
            $sql .= ' project_plan_task_executions.task_id IN (SELECT task_id FROM tasks WHERE activity_id = "'.$cost_center_id.'")';
        } else {
            $sql .= ' project_plans.project_id = "' . $cost_center_id . '"  ';
        }

        if($from != null){
            $sql .= ' AND date >= "'.$from.'" ';
        }

        if($to != null){
            $sql .= ' AND date <= "'.$to.'" ';
        }

        $query = $this->db->query($sql);
        return $query->row()->total_execution_amount;
    }
}