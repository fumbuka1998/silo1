<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/3/2018
 * Time: 11:14 AM
 */

class Project_plan_task_equipment_budget extends MY_Model{
    const DB_TABLE = 'project_plan_task_equipment_budgets';
    const DB_TABLE_PK = 'id';

    public $asset_id;
    public $project_plan_task_id;
    public $rate_mode;
    public $rate;
    public $duration;
    public $quantity;
    public $description;
    public $created_by;

    public function amount(){
        return $this->rate * $this->quantity;
    }

    public function project_plan_task()
    {
        $this->load->model('project_plan_task');
        $project_plan_task = new Project_plan_task();
        $project_plan_task->load($this->project_plan_task_id);
        return $project_plan_task;
    }

    public function asset()
    {
        $this->load->model('asset');
        $asset = new Asset();
        $asset->load($this->asset_id);
        return $asset;
    }

    public function plan_equipment_budget_list($limit, $start, $keyword, $order, $project_plan_id){
        $order_string = dataTable_order_string(['tasks.task_name','project_plan_task_equipment_budgets.rate','project_plan_task_equipment_budgets.quantity','project_plan_task_equipment_budgets.duration'],$order,'tasks.task_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = ' WHERE project_plan_tasks.project_plan_id ='.$project_plan_id.' ';

        $sql = 'SELECT COUNT(project_plan_task_equipment_budgets.id) AS records_total FROM project_plan_task_equipment_budgets
                LEFT JOIN project_plan_tasks ON project_plan_task_equipment_budgets.project_plan_task_id = project_plan_tasks.id
                '.$where_clause;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;


        if ($keyword != '') {
            $where_clause .= ' AND (tasks.task_name LIKE "%'.$keyword.'%" OR project_plan_task_equipment_budgets.rate LIKE "%'.$keyword.'%" OR project_plan_task_equipment_budgets.quantity LIKE "%'.$keyword.'%" OR project_plan_task_equipment_budgets.duration LIKE "%'.$keyword.'%" )';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS project_plan_task_equipment_budgets.id AS plan_equipment_budget_id, rate_mode, tasks.task_name AS task_assigned, asset_items.asset_name AS asset_name , project_plan_task_equipment_budgets.rate AS rate, project_plan_task_equipment_budgets.duration AS duration, project_plan_task_equipment_budgets.quantity AS quantity
                FROM project_plan_task_equipment_budgets
                LEFT JOIN assets ON project_plan_task_equipment_budgets.asset_id = assets.id
                LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                LEFT JOIN project_plan_tasks ON project_plan_task_equipment_budgets.project_plan_task_id = project_plan_tasks.id
                LEFT JOIN tasks ON project_plan_tasks.task_id = tasks.task_id
                '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model(['project_plan','asset']);
        $project_plan = new Project_plan();
        $project_plan->load($project_plan_id);
        $project = $project_plan->project();
        $data['project_plan'] = $project;
        $data['project'] = $project_plan->project();
        $data['location_asset_options'] = $this->asset->location_asset_options('location', null, $project->{$project::DB_TABLE_PK});

        $rows = [];
        foreach ($results as $row){
            $plan_equipment_budget = new self();
            $plan_equipment_budget->load($row->plan_equipment_budget_id);
            $data['plan_equipment_budget'] = $plan_equipment_budget;

            $rows[] = [
                wordwrap($row->asset_name,20,'<br/>'),
                wordwrap($row->task_assigned,40,'<br/>'),
                $row->rate_mode,
                $row->quantity,
                '<span class="pull-right">'.number_format($row->rate).'</span>',
                $row->duration.' '.$plan_equipment_budget->rate_mode(),
                '<span class="pull-right">'.number_format($row->rate * $row->quantity * $row->duration).'</span>',
                $this->load->view('projects/plans/project_plan_task_equipments/list_actions',$data,true)
            ];

        }

        $json = [
            "total_budget_amount" => $this->total_plan_equipment_budget($project_plan_id),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function total_plan_equipment_budget($project_plan_id, $project_plan_task_id = null, $level = null)
    {
        $sql = 'SELECT COALESCE(SUM(project_plan_task_equipment_budgets.rate * project_plan_task_equipment_budgets.quantity * project_plan_task_equipment_budgets.duration),0) AS total_budget_amount FROM project_plan_task_equipment_budgets
                LEFT JOIN project_plan_tasks ON project_plan_task_equipment_budgets.project_plan_task_id = project_plan_tasks.id
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