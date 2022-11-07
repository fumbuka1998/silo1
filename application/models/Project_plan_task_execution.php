<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/26/2018
 * Time: 12:07 PM
 */

class Project_plan_task_execution extends MY_Model{
    const DB_TABLE = 'project_plan_task_executions';
    const DB_TABLE_PK = 'id';

    public $project_plan_id;
    public $task_id;
    public $executed_quantity;
    public $execution_date;
    public $created_by;



    public function task()
    {
        $this->load->model('task');
        $task = new task();
        $task->load($this->task_id);
        return $task;
    }

    public function project_plan()
    {
        $this->load->model('project_plan');
        $project_plan = new Project_plan();
        $project_plan->load($this->project_plan_id);
        return $project_plan;
    }

    public function project_plan_task_execution_list($project_id, $project_plan_id, $limit, $start, $keyword, $order){
    $data['cost_center_id'] = $project_id;

    $this->load->model(['project','project_plan']);
    $project = new Project();
    $project->load($project_id);
    $project_plan = new Project_plan();
    $project_plan->load($project_plan_id);

    $data['project'] = $project;
    $data['project_plan'] = $project_plan;
    $data['cost_center_options'] = $project->cost_center_options();

    $order_string = dataTable_order_string(['execution_date','tasks.task_name','executed_quantity'],$order,'execution_date');
    $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

    $where = ' project_plans.project_id ='.$project_id.' AND project_plans.id='.$project_plan_id;

    $sql = 'SELECT COUNT(project_plan_task_executions.id) AS records_total FROM project_plan_task_executions
                LEFT JOIN project_plans ON project_plans.id = project_plan_task_executions.project_plan_id
                WHERE'.$where;

    $query = $this->db->query($sql);
    $records_total = $query->row()->records_total;

    if($keyword != ''){
        $where .= ' AND (execution_date LIKE "%'.$keyword.'%" OR tasks.task_name LIKE "%'.$keyword.'%" OR executed_quantity LIKE "%'.$keyword.'%" )';
    }

    $sql = 'SELECT SQL_CALC_FOUND_ROWS project_plan_task_executions.id AS plan_task_execution_id, project_plan_task_executions.execution_date AS execution_date,project_plan_task_executions.project_plan_id AS project_plan_id,tasks.task_name AS task_name, project_plan_task_executions.task_id AS task_id, project_plan_task_executions.executed_quantity AS quantity
                FROM project_plan_task_executions
                LEFT JOIN project_plans ON project_plans.id = project_plan_task_executions.project_plan_id
                LEFT JOIN tasks ON project_plan_task_executions.task_id = tasks.task_id
                WHERE'.$where.$order_string;

    $query = $this->db->query($sql);
    $results = $query->result();

    $sql = 'SELECT FOUND_ROWS() AS records_filtered';
    $query = $this->db->query($sql);
    $records_filtered = $query->row()->records_filtered;

    $rows = [];
    $data['cost_center_type'] = 'project';
    foreach($results as $row){
        $plan_task_execution = new self();
        $plan_task_execution->load($row->plan_task_execution_id);
        $data['task'] = $plan_task_execution->task();
        $remaining_task = 0;
        $task_quantity = $plan_task_execution->task()->quantity;
        $executed_task_quantity = $plan_task_execution->task()->project_plan_task_execution($row->task_id);
        $remaining_task += $task_quantity - $executed_task_quantity;

        $rows[] = [
            custom_standard_date($row->execution_date),
            $row->task_name,
            $row->quantity,
            $remaining_task,
            ''
        ];
    }
    $json = [
        "recordsTotal" => $records_total,
        "recordsFiltered" => $records_filtered,
        "data" => $rows
    ];
    return json_encode($json);
}

    public function duration(){
        return $this->quantity / $this->output_per_day;
    }

    public function plan_tasks($total = false){
        $this->load->model('project_plan_task');
        $where['project_plan_id'] = $this->{$this::DB_TABLE_PK};
        if($total){
            return $this->project_plan_task->quantity;
        } else {
            return !empty($this->project_plan_task->get(0, 0, $where)) ? $this->project_plan_task->get(0, 0, $where) : false;
        }
    }

    public function material_items(){
        $this->load->model('material_item');
        return $this->material_item->get(0,0,['plan_task_execution_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function previuos_task_execution($plan_task_execution_id){
        $task = $this->task();
        $where = 'task_id ='.$task->{$task::DB_TABLE_PK}.' AND id="'.($plan_task_execution_id - 1).'"';
        $previous_executions = $this->get(0,0,$where,'id DESC');
        !empty($previous_executions) ? array_shift($previous_executions) : false;
        if($previous_executions){
            foreach($previous_executions as $previous_execution){
                return $previous_execution->executed_quantity;
            }
        }else {
            return 0 ;
        }
    }

    public function plan_task_execution_materials(){
        $this->load->model('project_plan_task_execution_material_cost');
        $where['plan_task_execution_id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan_task_execution_material_cost->get(0,0,$where);
    }

    public function plan_task_execution_equipments(){
        $this->load->model('project_plan_task_execution_equipment');
        $where['plan_task_execution_id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan_task_execution_equipment->get(0,0,$where);
    }

    public function plan_task_execution_casual_labour(){
        $this->load->model('project_plan_task_execution_casual_labour');
        $where['plan_task_execution_id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan_task_execution_casual_labour->get(0,0,$where);
    }

    public function plan_task_execution_material_cost(){
        $this->load->model('project_plan_task_execution_material_cost');
        $where['plan_task_execution_id'] = $this->{$this::DB_TABLE_PK};
        $plan_task_execution_material_costs = $this->project_plan_task_execution_material_cost->get(0,0,$where);
        foreach($plan_task_execution_material_costs as $plan_task_execution_material_cost){
            return $plan_task_execution_material_cost;
        }
    }

}