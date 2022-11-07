<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 11:49 AM
 */

class Project_plan extends MY_Model{
    const DB_TABLE = 'project_plans';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $start_date;
    public $end_date;
    public $title;
    public $created_by;

    public function project_plans_list($limit,$start,$keyword,$order,$level,$project_id){
        $this->load->model(['project','asset']);
        $project = new Project();
        $project->load($project_id);
        $data['project'] = $project;
        $data['store_sub_location_options'] = $project->location()->sub_location_options();
        $data['location_asset_options'] = $this->asset->location_asset_options('location', null, $project_id);

        $where = ' project_id ='.$project_id.'';

        $sql = 'SELECT COUNT(project_plans.id) AS records_total FROM project_plans WHERE'.$where;

        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if ($keyword!=''){
            if($where != ''){
                $where .= ' AND ';
            }
            $where.=' (title LIKE "%'.$keyword.'%" OR project_plans.start_date LIKE "%'.$keyword.'%"  OR project_plans.end_date LIKE "%'.$keyword.'%" ) ';
        }
        $order_string = dataTable_order_string(['title','project_plans.start_date','project_plans.end_date'],$order,' title');

        $sql = '  SELECT SQL_CALC_FOUND_ROWS project_plans.id AS project_plan_id, title, project_plans.start_date AS plan_start_date, project_plans.end_date AS plan_end_date                    
                  FROM project_plans WHERE'.$where.' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $plans =[];
        foreach ($results as $plan){
            $project_plan = new self();
            $project_plan->load($plan->project_plan_id);
            $data['project_plan'] = $project_plan;
            $data['plan_cost_center_options'] = $project_plan->project()->plan_cost_center_options();
            $data['task_options'] = $project_plan->project_plan_tasks_options($project_id,$plan->project_plan_id);
            if($level=='executions'){
                $plans[] = [
                    $plan->title,
                    custom_standard_date($plan->plan_start_date),
                    custom_standard_date($plan->plan_end_date),
                    '<span class="pull-right">'.number_format(($project_plan->plan_execution_cost($project_plan->{$project_plan::DB_TABLE_PK})),2).'</span>',
                    $this->load->view('projects/executions/project_execution_list_actions',$data,true)
                ];
            }else{
                $plans[] = [
                    $plan->title,
                    custom_standard_date($plan->plan_start_date),
                    custom_standard_date($plan->plan_end_date),
                    '<span class="pull-right">'.number_format(($project_plan->planned_budget($project_plan->{$project_plan::DB_TABLE_PK})),2).'</span>',
                    $this->load->view('projects/plans/project_plan_list_actions',$data,true)
                ];
            }
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $plans
        ];
        return json_encode($json);
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function project_plan_tasks_options($project_id,$project_plan_id,$string = false,$executed = false){
        $sql = 'SELECT activity_name, activity_id FROM activities WHERE project_id = "'.$project_id.'"';
        $query = $this->db->query($sql);
        $activities = $query->result();
        $string ? $options = '<option value="">'.' '.'</option>' : $options[''] = '&nbsp;';

        foreach ($activities as $activity) {
            if ($string) {
                $options .= '<optgroup label="' . $activity->activity_name . '">';
            }
            if($executed){
                $sql = 'SELECT DISTINCT tasks.task_id AS task_id, project_plan_task_executions.id AS execution_task_id, task_name FROM tasks
                        LEFT JOIN project_plan_task_executions ON tasks.task_id = project_plan_task_executions.task_id
                        LEFT JOIN project_plans ON project_plan_task_executions.project_plan_id = project_plans.id
                        WHERE tasks.activity_id = "' . $activity->activity_id . '" AND project_plans.id=' . $project_plan_id;
                $query = $this->db->query($sql);
                $tasks = $query->result();
                foreach ($tasks as $task) {
                    $string ? $options .= '<option value="' . $task->execution_task_id . '">' . $task->task_name . '</option>' : $options[$activity->activity_name][$task->execution_task_id] = $task->task_name;
                }
            } else {
                $sql = 'SELECT project_plan_tasks.id AS plan_task_id, task_name FROM project_plan_tasks
                    LEFT JOIN tasks ON project_plan_tasks.task_id = tasks.task_id
                    WHERE tasks.activity_id = "' . $activity->activity_id . '" AND project_plan_tasks.project_plan_id=' . $project_plan_id;
                $query = $this->db->query($sql);
                $tasks = $query->result();
                foreach ($tasks as $task) {
                    $string ? $options .= '<option value="' . $task->plan_task_id . '">' . $task->task_name . '</option>' : $options[$activity->activity_name][$task->plan_task_id] = $task->task_name;
                }
            }
            if ($string) {
                $options .= '</optgroup>';
            }
        }

        return $options;
    }

    public function plan_tasks($total = false){
        $this->load->model('project_plan_task');
        $where['project_plan_id'] = $this->{$this::DB_TABLE_PK};
        if($total){
            return $this->project_plan_task->count_rows($where);
        } else {
            return !empty($this->project_plan_task->get(0, 0, $where)) ? $this->project_plan_task->get(0, 0, $where) : false;
        }
    }

    public function plan_task_quantity(){
        $project_plan_tasks= $this->plan_tasks();
        if(!empty($project_plan_tasks)) {
            foreach ($project_plan_tasks as $task) {
                return $task->quantity;
            }
        } else {
           return 0;
        }
    }

    public function budget_material_options($cost_center_level,$cost_center_id){
        $sql = 'SELECT item_name,item_id
                FROM material_items
                WHERE item_id NOT IN(
                    SELECT material_item_id FROM material_budgets
                    WHERE ';
        if($cost_center_level == 'project'){
            $sql .= ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $sql .= ' task_id = "'.$cost_center_id.'"';
        }
        $sql .= '
                )
       ';

        $query = $this->db->query($sql);
        $material_items = $query->result();

        $options = '<option value="">&nbsp;</option>';
        foreach($material_items as $item){
            $options .= '<option value="'.$item->item_id.'">'.$item->item_name.'</option>';
        }
        return $options;
    }

    public function casual_labour_type_options(){
        $this->load->model('casual_labour_type');
        $labour_types = $this->casual_labour_type->get();
        $options['']= '&nbsp;';
        foreach($labour_types as $type){
            $options [$type->type_id] = $type->name;
        }
        return $options;
    }

    public function planned_budget($project_plan_id,$cost_types = ['material_budget','casual_labour_budget','equipment_budget']){
        $budget_figure = 0;
        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material_budget','casual_labour_budget','equipment_budget'])) {
                $model = 'project_plan_task_'.$cost_type;
                $this->load->model($model);
                $method = 'total_plan_'.$cost_type;
                $budget_figure += $this->$model->$method($project_plan_id);
            }
        }
        return $budget_figure > 0 ? $budget_figure : 0.00;
    }

    public function performed_budget(){
        $this->load->model('project_plan_task');
        $where['project_plan_id'] = $this->{$this::DB_TABLE_PK};
        $project_plan_tasks = $this->project_plan_task->get(0,0,$where);
        $bcwp = 0;
        foreach($project_plan_tasks as $plan_task){
            $per_unit_bcwp =  $plan_task->per_unit_bcwp();
            $executed_quantity = $plan_task->project_plan_task_execution();
            $bcwp += $per_unit_bcwp * $executed_quantity;
        }
        return $bcwp;
    }

    public function plan_actual_cost($project_plan_id,$cost_types = ['execution_material','execution_casual_labour','execution_equipment']){
        $this->load->model(['material_cost']);
        $budget_figure = 0;
        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['execution_material','execution_casual_labour','execution_equipment'])) {
                if($cost_type=='execution_material') {
                    $budget_figure += $this->material_cost->total_plan_execution_material_cost($project_plan_id);
                } else {
                    $model = 'project_plan_task_'.$cost_type;
                    $this->load->model($model);
                    $method = 'total_plan_'.$cost_type.'_cost';
                    $budget_figure += $this->$model->$method($project_plan_id);
                }
            }
        }
        return $budget_figure > 0 ? $budget_figure : 0.00;
    }

    public function cost_center_options($string = false){
        $sql = 'SELECT activity_name, activity_id FROM activities WHERE project_id = "'.$this->project_id.'"';
        $query = $this->db->query($sql);
        $activities = $query->result();
        $string ? $options = '<option value=""></option>' : $options[''] = '&nbsp;';
        foreach($activities as $activity){
            if($string){
                $options .= '<optgroup label="'.$activity->activity_name.'">';
            }
            $sql = 'SELECT task_id, task_name FROM tasks WHERE activity_id = "'.$activity->activity_id.'"';
            $query = $this->db->query($sql);
            $tasks = $query->result();
            foreach($tasks as $task){
                $string ? $options.= '<option value="'.$task->task_id.'">'.$task->task_name.'</option>' : $options[$activity->activity_name][$task->task_id] = $task->task_name;
            }
            if($string){
                $options .= '</optgroup>';
            }
        }
        return $options;
    }

    public function budgeted_figure_work_scheduled(){
        return $this->planned_budget($this->{$this::DB_TABLE_PK});
    }

    public function created_by()
    {
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function plan_executed_tasks($total = false){
        $this->load->model('project_plan_task_execution');
        $where['project_plan_id'] = $this->{$this::DB_TABLE_PK};
        if($total){
            return $this->project_plan_task_execution->count_rows($where);
        } else {
            return !empty($this->project_plan_task_execution->get(0, 0, $where)) ? $this->project_plan_task_execution->get(0, 0, $where) : false;
        }
    }

    public function plan_execution_cost($project_plan_id,$cost_types = ['material','casual_labour','equipment']){
        $budget_figure = 0;
        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material','casual_labour','equipment'])) {
                if($cost_type == 'material'){
                    $model = $cost_type.'_cost';
                } else {
                    $model = 'project_plan_task_execution_' . $cost_type;
                }
                $this->load->model($model);
                $method = 'total_plan_execution_'.$cost_type.'_cost';
                $budget_figure += $this->$model->$method($project_plan_id);
            }
        }
        return $budget_figure > 0 ? $budget_figure : 0.00;
    }

    public function project_plan_executions(){
        $this->load->model('project_plan');
        $where['id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan->get(0,0,$where,'start_date DESC');
    }



    public function project_plans(){
        $this->load->model('project_plan');
        $where['id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan->get(0,0,$where,'start_date DESC');
    }






}