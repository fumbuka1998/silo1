<?php
class Equipment_budget extends MY_Model{

    const DB_TABLE = 'equipment_budgets';
    const DB_TABLE_PK = 'id';

    public $asset_item_id;
    public $project_id;
    public $task_id;
    public $rate_mode;
    public $rate;
    public $duration;
    public $quantity;
    public $description;
    public $created_by;


    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }
    public function amount(){

        return $this->quantity * $this->rate * $this->duration;
    }

    public function budget_items_list($cost_center_level, $cost_center_id,$limit, $start, $keyword, $order){

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

        //Where clause
        if($is_general){
            $where_clause = ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $where_clause = ' task_id = "'.$cost_center_id.'"';
        }


        $records_total = $this->count_rows($where_clause);

        if($keyword != ''){
            $where_clause .= ' rate_mode LIKE "%'.$keyword.'%" OR rate LIKE "%'.$keyword.'%" OR duration LIKE "%'.$keyword.'%" OR quantity LIKE "%'.$keyword.'%"';
        }

        $order_string = dataTable_order_string(['asset_item_id','rate_mode','rate','duration','quantity'],$order,'asset_item_id');


        $Equipment_budgets = $this->get($limit,$start,$where_clause,$order_string);
               

        $rows = [];
        $this->load->model(['asset_group','asset_item','Employee','Project','Task']);
        $budget_figure= $this->budget_figure($cost_center_id,$cost_center_level);
        $data['asset_group_options']  = $this->asset_group->dropdown_options();
        $data['asset_item_options']  = $this->asset_item->dropdown_options();
        $data['employee_options']  = employee_options();
        foreach ($Equipment_budgets as $Equipment_budget){
            $data['Equipment_budget'] = $Equipment_budget;
            $data['project'] = $Equipment_budget->project();
            $data['cost_center_options'] = $Equipment_budget->project()->cost_center_options();
            $rows[] = [
                $Equipment_budget->asset_item()->asset_name,
                $Equipment_budget->task_name()->task_name,
                $Equipment_budget->rate_mode,
                '<span class="pull-right">' .  number_format($Equipment_budget->rate) . '</span>',
                '<span class="pull-right">' .  $Equipment_budget->duration . '</span>',
                '<span class="pull-right">' .  $Equipment_budget->quantity . '</span>',
                '<span class="pull-right">' . number_format($Equipment_budget->amount()). '</span>',
                $Equipment_budget->description,
                $this->load->view('projects/budgets/equipment/list_actions',$data,true)
            ];
        }

      
        $records_filtered = $this->count_rows($where_clause);

        $json = [
            "budget_total"=> $budget_figure,
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function asset_group(){

        $this->load->model('Asset_group');
        $Asset_group=new Asset_group();
        $Asset_group->load($this->asset_group_id);
        return $Asset_group;
    }

    public function asset_item(){

        $this->load->model('asset_item');
        $Asset_item = new Asset_item();
        $Asset_item->load($this->asset_item_id);
        return $Asset_item;
    }

    public function task_name(){

        $this->load->model('Task');
        $Tasks=new Task();
        $Tasks->load($this->task_id);
        return $Tasks;
    }

    public function budget_figure($cost_center_id,$level = null){
        $sql = 'SELECT COALESCE(SUM(quantity*rate*duration),0) AS budget_figure FROM  '.$this::DB_TABLE.' WHERE ';
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
        return $query->row()->budget_figure;
    }


}