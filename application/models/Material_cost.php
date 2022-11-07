<?php

class Material_cost extends MY_Model{

    const DB_TABLE = 'material_costs';
    const DB_TABLE_PK = 'material_cost_id';

    public $cost_date;
    public $project_id;
    public $task_id;
    public $material_item_id;
    public $source_sub_location_id;
    public $quantity;
    public $rate;
    public $description;
    public $is_updated;
    public $employee_id;

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

    public function material()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function amount(){
        return $this->quantity*$this->rate;
    }

    public function source_sub_location()
    {
        $this->load->model('sub_location');
        $source_sub_location = new Sub_location();
        $source_sub_location->load($this->source_sub_location_id);
        return $source_sub_location;
    }

    public function actual_cost($cost_center_id, $level = null, $from = null, $to = null, $material_item_id = null){
        $sql = 'SELECT COALESCE(SUM(quantity*rate),0) AS actual_costs FROM  '.$this::DB_TABLE.' WHERE ';
        if($level == 'project') {
            $sql  .= ' project_id = ' . $cost_center_id . ' AND task_id IS NULL';
        } else if($level == 'task'){
            $sql .= ' task_id = ' . $cost_center_id . ' ';
        } else if($level == 'activity'){
            $sql .= ' task_id IN (SELECT task_id FROM tasks WHERE activity_id = '.$cost_center_id.')';
        } else {
            $sql .= ' project_id = ' . $cost_center_id . ' ';
        }

        if($from != null){
            $sql .= ' AND cost_date >= "'.$from.'" ';
        }

        if($to != null){
            $sql .= ' AND cost_date <= "'.$to.'" ';
        }

        if(!is_null($material_item_id) && $material_item_id > 0){
            $sql .= ' AND material_item_id = '.$material_item_id.' ';
        }
        $query = $this->db->query($sql);
        return floatval($query->row()->actual_costs);
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
        $order_string = dataTable_order_string(['cost_date','item_name','quantity','symbol','rate','amount','description'],$order,'cost_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;


        if($is_general){
            $where = ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $where = ' task_id = "'.$cost_center_id.'"';
        }

        //Total records
        $records_total = $this->count_rows($where);

        if($keyword != ''){
            $where .= ' AND (item_name LIKE "%'.$keyword.'%" OR cost_date LIKE "%'.$keyword.'%" OR symbol LIKE "%'.$keyword.'%" OR quantity LIKE "%'.$keyword.'%" OR rate LIKE "%'.$keyword.'%" OR material_costs.description LIKE "%'.$keyword.'%")';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS material_cost_id,cost_date,quantity,rate,material_costs.description,(quantity*rate) AS amount,
                item_name, symbol, "project_overall" AS cost_type
                FROM material_costs
                LEFT JOIN material_items ON material_costs.material_item_id = material_items.item_id
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
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
            $material_cost = new self();
            $material_cost->load($row->{$this::DB_TABLE_PK});
            $data['item'] = $material_cost;
            $data['project'] = isset($data['project']) ? $data['project'] : $material_cost->project();
            $data['source_sub_location'] = $material_cost->source_sub_location();
            $data['cost_type'] = $row->cost_type;//in this method i just added this to help me differentiate the forms

            $data['material_item_name'] = $row->item_name;
            $data['unit_symbol'] = $row->symbol;

            $rows[] = [
                custom_standard_date($row->cost_date),
                $row->item_name,
                $row->quantity,
                $row->symbol,
                '<span class="pull-right">'.number_format($row->rate,2).'</span>',
                '<span class="pull-right">'.number_format($row->amount,2).'</span>',
                $row->description,
                $this->load->view('projects/costs/material/material_costs_list_actions',$data,true)
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

    public function execution_material_cost_list($project_id, $project_plan_id, $limit, $start, $keyword, $order){
        $data['cost_center_id'] = $project_id;

        $this->load->model(['project','project_plan']);
        $project = new Project();
        $project->load($project_id);
        $project_plan = new Project_plan();
        $project_plan->load($project_plan_id);

        $data['project'] = $project;
        $data['project_plan'] = $project_plan;
        $data['cost_center_options'] = $project->cost_center_options();

        $order_string = dataTable_order_string(['cost_date','item_name','quantity','symbol','rate','amount','description'],$order,'cost_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where = ' project_plans.project_id ='.$project_id.' AND project_plans.id='.$project_plan_id;

        $sql = 'SELECT COUNT(project_plan_task_execution_material_costs.id) AS records_total FROM project_plan_task_execution_material_costs
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_material_costs.plan_task_execution_id = project_plan_task_executions.id
                LEFT JOIN project_plans ON project_plan_task_executions.project_plan_id = project_plans.id
                LEFT JOIN projects ON project_plans.project_id = projects.project_id
                WHERE'.$where;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if($keyword != ''){
            $where .= ' AND (item_name LIKE "%'.$keyword.'%" OR cost_date LIKE "%'.$keyword.'%" OR symbol LIKE "%'.$keyword.'%" OR material_costs.quantity LIKE "%'.$keyword.'%" OR material_costs.rate LIKE "%'.$keyword.'%" OR material_costs.description LIKE "%'.$keyword.'%")';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS material_costs.material_cost_id AS material_cost_id,project_plan_task_execution_material_costs.plan_task_execution_id AS plan_task_execution_id, tasks.task_name AS task_name, material_costs.cost_date, material_costs.quantity AS quantity, material_costs.rate AS rate, material_costs.description AS description,(material_costs.quantity*material_costs.rate) AS amount,
                item_name, symbol, "executions" AS cost_type
                FROM project_plan_task_execution_material_costs
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_material_costs.plan_task_execution_id = project_plan_task_executions.id
                LEFT JOIN tasks  ON project_plan_task_executions.task_id = tasks.task_id 
                LEFT JOIN project_plans ON project_plan_task_executions.project_plan_id = project_plans.id
                LEFT JOIN projects ON project_plans.project_id = projects.project_id
                LEFT JOIN material_costs ON project_plan_task_execution_material_costs.material_cost_id = material_costs.material_cost_id
                LEFT JOIN material_items ON material_costs.material_item_id = material_items.item_id
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                WHERE'.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $data['cost_center_type'] = 'project';
        foreach($results as $row){
            $material_cost = new self();
            $material_cost->load($row->material_cost_id);
            $data['item'] = $material_cost;
            $data['project'] = isset($data['project']) ? $data['project'] : $material_cost->project();
            $data['source_sub_location'] = $material_cost->source_sub_location();
            $data['cost_type'] = $row->cost_type;

            $data['material_item_name'] = $row->item_name;
            $data['unit_symbol'] = $row->symbol;

            $rows[] = [
                custom_standard_date($row->cost_date),
                wordwrap($row->task_name,85,'<br/>'),
                wordwrap($row->item_name,20,'<br/>'),
                $row->symbol,
                $row->quantity,
                '<span class="pull-right">'.number_format($row->rate,2).'</span>',
                '<span class="pull-right">'.number_format($row->amount,2).'</span>',
                $this->load->view('projects/costs/material/material_costs_list_actions',$data,true)
            ];
        }
        $json = [
            "cost_total" => $this->total_plan_execution_material_cost($project_plan_id),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function total_plan_execution_material_cost($project_plan_id)
    {
        $sql = 'SELECT COALESCE(SUM(material_costs.quantity * material_costs.rate),0) AS actual_costs
                FROM project_plan_task_execution_material_costs
                LEFT JOIN project_plan_task_executions ON project_plan_task_execution_material_costs.plan_task_execution_id = project_plan_task_executions.id
                LEFT JOIN material_costs ON project_plan_task_execution_material_costs.material_cost_id = material_costs.material_cost_id
                WHERE project_plan_task_executions.project_plan_id='.$project_plan_id;

        $query = $this->db->query($sql);
        return floatval($query->row()->actual_costs);
    }

    public function delete_task_material_cost(){
        $this->db->delete('project_plan_task_execution_material_costs',['material_cost_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function project_plan_task_execution_material_cost(){
        $this->load->model('project_plan_task_execution_material_cost');
        $where['material_cost_id'] = $this->{$this::DB_TABLE_PK};
        $plan_task_material_costs = $this->project_plan_task_execution_material_cost->get(0,0,$where);
        foreach($plan_task_material_costs as $plan_task_material_cost){
            return $plan_task_material_cost;
        }
    }

    public function cost_center_name(){
        $sql = 'SELECT activity_name FROM tasks
                LEFT JOIN activities ON tasks.activity_id = activities.activity_id
                WHERE task_id = '.$this->task_id.' LIMIT 1
                ';
        return $this->db->query($sql)->row()->activity_name;
    }

}

