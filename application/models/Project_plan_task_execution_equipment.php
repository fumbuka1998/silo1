<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/26/2018
 * Time: 12:16 PM
 */

class Project_plan_task_execution_equipment extends MY_Model
{
    const DB_TABLE = 'project_plan_task_execution_equipments';
    const DB_TABLE_PK = 'id';

    public $date;
    public $asset_id;
    public $plan_task_execution_id;
    public $rate_mode;
    public $rate;
    public $duration;
    public $quantity;
    public $description;
    public $created_by;

    public function project_plan_task_execution()
    {
        $this->load->model('project_plan_task_execution');
        $project_plan_task_execution = new Project_plan_task_execution();
        $project_plan_task_execution->load($this->plan_task_execution_id);
        return $project_plan_task_execution;
    }

    public function asset()
    {
        $this->load->model('asset');
        $asset = new Asset();
        $asset->load($this->asset_id);
        return $asset;
    }

    public function project_task_equipment_execution_list($limit, $start, $keyword, $order, $project_plan_id){
        $order_string = dataTable_order_string(['tasks.task_name','project_plan_task_execution_equipments.rate','project_plan_task_execution_equipments.quantity','project_plan_task_execution_equipments.duration'],$order,'tasks.task_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = ' WHERE project_plan_task_executions.project_plan_id ='.$project_plan_id.' ';

        $sql = 'SELECT COUNT(project_plan_task_execution_equipments.id) AS records_total FROM project_plan_task_execution_equipments
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_equipments.plan_task_execution_id = project_plan_task_executions.id
                '.$where_clause;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;


        if ($keyword != '') {
            $where_clause .= ' AND (tasks.task_name LIKE "%'.$keyword.'%" OR project_plan_task_execution_equipments.rate LIKE "%'.$keyword.'%" OR project_plan_task_execution_equipments.quantity LIKE "%'.$keyword.'%" OR project_plan_task_execution_equipments.duration LIKE "%'.$keyword.'%" )';
        }

        $sql = ' SELECT SQL_CALC_FOUND_ROWS project_plan_task_execution_equipments.id AS plan_equipment_execution_id, project_plan_task_execution_equipments.date AS execution_date, rate_mode, tasks.task_name AS task_assigned, asset_items.asset_name AS asset_name , project_plan_task_execution_equipments.rate AS rate, project_plan_task_execution_equipments.duration AS duration, project_plan_task_execution_equipments.quantity AS quantity
                FROM project_plan_task_execution_equipments
                LEFT JOIN assets ON project_plan_task_execution_equipments.asset_id = assets.id
                LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id 
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_equipments.plan_task_execution_id = project_plan_task_executions.id
                LEFT JOIN tasks ON project_plan_task_executions.task_id = tasks.task_id
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
        $data['project_plan'] = $project_plan;
        $data['project'] = $project;
        $data['location_asset_options'] = $this->asset->location_asset_options('location', null, $project->{$project::DB_TABLE_PK});

        $rows = [];
        foreach ($results as $row){
            $plan_equipment_execution = new self();
            $plan_equipment_execution->load($row->plan_equipment_execution_id);
            $data['plan_equipment_execution'] = $plan_equipment_execution;
            $amount = $row->rate * $row->quantity *$row->duration;

            $rows[] = [
                custom_standard_date($row->execution_date),
                wordwrap($row->asset_name,20,'<br/>'),
                wordwrap($row->task_assigned,40,'<br/>'),
                $row->rate_mode,
                $row->quantity,
                '<span class="pull-right">'.number_format($row->rate).'</span>',
                $row->duration.' '.$plan_equipment_execution->rate_mode(),
                '<span class="pull-right">'.number_format($amount).'</span>',
                $this->load->view('projects/executions/plan_task_execution_equipments/list_actions',$data,true)
            ];

        }

        $json = [
            "total_execution_amount" => $this->total_plan_execution_equipment_cost($project_plan_id),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function amount(){
        return $this->rate * $this->quantity;
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

    public function total_plan_execution_equipment_cost($project_plan_id){
        $sql = 'SELECT COALESCE(SUM(project_plan_task_execution_equipments.rate * project_plan_task_execution_equipments.quantity * project_plan_task_execution_equipments.duration),0) AS total_execution_amount 
                FROM project_plan_task_execution_equipments
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_equipments.plan_task_execution_id = project_plan_task_executions.id
                WHERE project_plan_task_executions.project_plan_id ='.$project_plan_id;

        $query = $this->db->query($sql);
        return $query->row()->total_execution_amount;
    }

    public function actual_cost($cost_center_id, $level = null, $from = null, $to = null){
        $sql = 'SELECT COALESCE(SUM(project_plan_task_execution_equipments.rate * project_plan_task_execution_equipments.quantity * project_plan_task_execution_equipments.duration),0) AS total_execution_amount 
                FROM project_plan_task_execution_equipments
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_equipments.plan_task_execution_id = project_plan_task_executions.id
                LEFT JOIN project_plans ON project_plan_task_executions.project_plan_id = project_plans.id
                WHERE';

        if($level == 'project') {
            $sql .= ' project_plans.project_id = "' . $cost_center_id. '"';
        } else if($level == 'task'){
            $sql .= ' project_plan_task_executions.task_id = "' . $cost_center_id . '"';
        } else if($level == 'activity'){
            $sql .= ' project_plan_task_executions.task_id IN (SELECT task_id FROM tasks WHERE activity_id = "'.$cost_center_id.'")';
        } else {
            $sql .= ' project_plans.project_id = "' . $cost_center_id . '" ';
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