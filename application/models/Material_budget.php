<?php

class Material_budget extends MY_Model{
    
    const DB_TABLE = 'material_budgets';
    const DB_TABLE_PK = 'budget_id';

    public $project_id;
    public $task_id;
    public $material_item_id;
    public $quantity;
    public $rate;
    public $description;
    public $employee_id;


    public function amount(){
        return $this->quantity * $this->rate;
    }

    public function material_item()
    {
        $this->load->model('material_item');
        $material = new Material_item();
        $material->load($this->material_item_id);
        return $material;
    }

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
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


        //order string
        $order_string = dataTable_order_string(['item_name','quantity','symbol','rate','amount','description'],$order,'item_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        //Where clause
        if($is_general){
            $where_clause = ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $where_clause = ' task_id = "'.$cost_center_id.'"';
        }

        //Total records
        $records_total = $this->count_rows($where_clause);

        //Get results
        if($keyword != ''){
            $where_clause .= ' AND (material_items.item_name LIKE "%'.$keyword.'%" OR material_budgets.description LIKE "%'.$keyword.'%")';
        }
        $sql = 'SELECT SQL_CALC_FOUND_ROWS material_budgets.* ,item_name, (rate*quantity) AS amount, symbol
                FROM material_budgets
                LEFT JOIN material_items ON material_budgets.material_item_id = material_items.item_id
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                WHERE '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        //Prepare rows
        $rows = [];
        foreach($results as $row){
            $item = new self;
            $item->load($row->budget_id);
            $data['item'] = $item;
            $data['material_item_name'] = $row->item_name;
            $data['unit_symbol'] = $row->symbol;
            $rows[] = [
                $row->item_name,
                '<span class="pull-right">'.$row->quantity.'</span>',
                $row->symbol,
                '<span class="pull-right">'.number_format($row->rate).'</span>',
                '<span class="pull-right">'.number_format($row->amount).'</span>',
                $row->description,
                $this->load->view('projects/budgets/material/list_actions',$data,true)
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

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function budget_figure($cost_center_id,$level = null){
        $sql = 'SELECT COALESCE(SUM(quantity*rate),0) AS budget_figure FROM  '.$this::DB_TABLE.' WHERE ';
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

