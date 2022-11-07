<?php

class Task extends MY_Model{

    const DB_TABLE = 'tasks';
    const DB_TABLE_PK = 'task_id';

    public $activity_id;
    public $task_name;
    public $start_date;
    public $end_date;
    public $measurement_unit_id;
    public $quantity;
    public $rate;
    public $predecessor;
    public $description;

    public function activity()
    {
        $this->load->model('activity');
        $activity = new Activity();
        $activity->load($this->activity_id);
        return $activity;
    }

    public function actual_cost($cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment'], $from = null, $to = null){

        $cost_figure = 0;
        if(is_string($cost_types)){
            $cost_types = [$cost_types];
        }

        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material','miscellaneous','permanent_labour'])) {
                $model = $cost_type . '_cost';
                $this->load->model($model);
                $cost_figure += $this->$model->actual_cost($this->{$this::DB_TABLE_PK}, 'task', $from, $to);

            } else if(in_array($cost_type,['equipment','casual_labour'])){
                $model = 'project_plan_task_execution_'.$cost_type;
                $this->load->model($model);
                $cost_figure += $this->$model->actual_cost($this->{$this::DB_TABLE_PK}, 'task', $from, $to);
            }

            if($cost_type == 'miscellaneous') {
                $this->load->model('payment_voucher_item');
                $cost_figure += $this->payment_voucher_item->cost_figure($this->{$this::DB_TABLE_PK}, 'task', $from, $to);
            } else if($cost_type == 'sub_contract'){
                $this->load->model('sub_contract_certificate_payment_voucher');
                $cost_figure += $this->sub_contract_certificate_payment_voucher->actual_cost($this->{$this::DB_TABLE_PK}, 'task',$from, $to);
            }
        }
        return $cost_figure;
    }

    public function budget_figure($cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract']){
        $budget_figure = 0;
        if(is_string($cost_types)){
            $cost_types = [$cost_types];
        }

        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract'])) {
                $model = $cost_type . '_budget';
                $this->load->model($model);
                $budget_figure += $this->$model->budget_figure($this->{$this::DB_TABLE_PK}, 'task');
            }
        }
        return $budget_figure > 0 ? $budget_figure : 0.0000000001/10E+300;
    }

    public function timeline_percentage($date = null){
        $date = $date != null ? $date : date('Y-m-d');
        $duration = $this->duration();
        $elapsed = $this->elapsed_days($date);
        return $elapsed > 0 ? round($elapsed*100/$duration,2) : 0;
    }

    public function completion_percentage(){
        $actual_cost_as_per_contractsum = $this->actual_cost_as_per_contractsum();
        $contract_sum = $this->contract_sum();
        return $contract_sum != 0 ? ($actual_cost_as_per_contractsum / $contract_sum) * 100 : 0;
    }

    public function budget_spending_percentage($cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract']){
        return round(($this->actual_cost($cost_types)/$this->budget_figure($cost_types))*100,2);
    }

    public function elapsed_days($date = null){
        $date = $date != null ? $date : date('Y-m-d');
        if($date > $this->start_date) {
            $elapsed_days = number_of_days($this->start_date, $date);
        } else {
            $elapsed_days = 0;
        }
        return $elapsed_days >= 0 ? $elapsed_days : 0;
    }

    public function contract_sum(){
        return $this->rate*$this->quantity;
    }

    public function duration(){
        $duration = number_of_days($this->start_date.' 00:00',$this->end_date.' 23:59');
        return $duration >= 0 ? $duration : 0;
    }

    public function project_id(){
        $sql = 'SELECT projects.project_id FROM projects
                    LEFT JOIN activities ON projects.project_id = activities.project_id
                    LEFT JOIN tasks ON activities.activity_id = tasks.activity_id
                    WHERE tasks.task_id = "'.$this->{$this::DB_TABLE_PK}.'"
                    ';
        $query = $this->db->query($sql);
        $row = $query->row();
        return intval($row->project_id);
    }

    public function project(){
        return $this->activity()->project();
    }

    public function measurement_unit(){
        $this->load->model('measurement_unit');;
        $measurement_unit = new Measurement_unit();
        $measurement_unit->load($this->measurement_unit_id);
        return $measurement_unit;
    }

    public function material_budget_item_id($material_id = 0){
        $sql = 'SELECT budget_id FROM material_budgets
                WHERE material_item_id = "'.$material_id.'" AND task_id = "'.$this->{$this::DB_TABLE_PK}.'"';
        $query = $this->db->query($sql);
        return $query->num_rows() > 0 ? $query->row()->budget_id : null;
    }

    public function project_plan_task_execution($task_id){
        $this->load->model('project_plan_task_execution');
        $where['task_id'] = $task_id;
        $executed_tasks = $this->project_plan_task_execution->get(0,0,$where);
        if(!empty($executed_tasks)) {
            $total_executed_quantity = 0;
            foreach ($executed_tasks as $task) {
                $total_executed_quantity += $task->executed_quantity;
            }
        }else{
            $total_executed_quantity = 0;
        }
        return $total_executed_quantity;
    }

    public function executed_task_budgeted_cost(){
        $executed_task_quantity = $this->project_plan_task_execution($this->{$this::DB_TABLE_PK});
        $budgeted_rate = $this->rate;
        $task_budgeted_cost = $executed_task_quantity * $budgeted_rate;
        return $task_budgeted_cost;
    }

    public function actual_cost_as_per_contractsum(){
        $executed_task_quantity = $this->project_plan_task_execution($this->{$this::DB_TABLE_PK});
        return $executed_task_quantity * $this->rate;
    }

    public function tasks_list($limit, $start, $keyword, $order, $level){
        $this->load->model([
            'activity',
            'project',
            'asset_item'
        ]);
        $activity_id = $this->input->post('activity_id');
        $activity_id = $activity_id != '' ? $activity_id : null;
        $project_id = $this->input->post('project_id');

        if($level == 'activity') {
            $activity = new Activity();
            $activity->load($activity_id);
            $data['activity'] = $activity;
            $project = $activity->project();
            $data['project'] = $project;
            $data['store_sub_location_options'] = $activity->store_sub_location_options();
            $acivity_where_clause = ' activity_id = "'.$activity_id.'"';
            $records_total = $this->task->count_rows($acivity_where_clause);

        } else {
            $project = new Project();
            $project->load($project_id);
            $data['project'] = $project;
            if(!is_null($activity_id) && $level == 'project') {
                $project_where_clause = ' activity_id = "'.$activity_id.'" ';
            } else {
                $project_where_clause = ' activity_id IN( SELECT activity_id FROM activities WHERE project_id = "' . $project_id . '")';
            }
            $records_total = $this->task->count_rows($project_where_clause);
        }

        $data['project_status'] = $project->status();
        $data['cost_center_options'] = $project->cost_center_options();
        $data['asset_item_options'] = $this->asset_item->dropdown_options();
        $data['measurement_unit_options'] = measurement_unit_dropdown_options();


        $order_string = dataTable_order_string(['task_name','start_date','end_date','quantity','rate'],$order,'start_date');

        $where = $level == 'activity' ? $acivity_where_clause : $project_where_clause;
        if($keyword != ''){
            $where .= ' AND (task_name LIKE "%'.$keyword.'%" OR start_date LIKE "%'.$keyword.'%" OR end_date LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%") ';
        }

        $tasks = $this->task->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach($tasks as $task){
            $data['task'] = $task;

            if($level == 'activity') {
                $rows[] = [
                    $task->task_name,
                    $task->start_date != '' ? custom_standard_date($task->start_date) : 'N/A',
                    $task->end_date != '' ? custom_standard_date($task->end_date) : 'N/A',
                    '<span style="text-align: center">' . $task->measurement_unit()->symbol . '</span>',
                    '<span class="pull-right">' . $task->quantity . '</span>',
                    '<span class="pull-right">' . number_format($task->rate, 2) . '</span>',
                    number_format(($task->quantity * $task->rate), 2),
                    '<span class="pull-right">' . $this->load->view('projects/activities/tasks/task_list_actions', $data, true) . '</span>'
                ];
            } else {
                $rows[] = [
                    $task->task_name,
                    $task->start_date != '' ? custom_standard_date($task->start_date) : 'N/A',
                    $task->end_date != '' ? custom_standard_date($task->end_date) : 'N/A',
                    '<span class="pull-right">' . $task->quantity . '</span>',
                    number_format(($task->quantity * $task->rate), 2),
                    number_format($task->budget_figure(),2),
                    $this->load->view('projects/budgets/list_actions',$data,true)
                ];
            }
        }
        $records_filtered = $this->task->count_rows($where);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);

    }

    public function subtasks_list($limit, $start, $keyword, $order){
        $this->load->model(array(
            'task',
            'project',
        ));
        $task_id = $this->input->post('task_id');
        $task = new self();
        $task->load($task_id);
        $project = $task->project();

        $data['task'] = $task;
        $data['project'] = $project;
        $data['project_status'] = $project->status();
        $data['cost_center_options'] = $project->cost_center_options();
        $data['measurement_unit_options'] = measurement_unit_dropdown_options();

        $order_string = dataTable_order_string(['start_date','task_name','end_date','quantity','rate'],$order,'start_date');

        $where = ' predecessor ='.$task_id.'';
        $records_total = $this->count_rows($where);
        if($keyword != ''){
            $where .= ' AND (task_name LIKE "%'.$keyword.'%" OR start_date LIKE "%'.$keyword.'%" OR end_date LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%") ';
        }
        $records_filtered = $this->count_rows($where);

        $tasks_arr = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        foreach($tasks_arr as $row){
            $subtask = new self();
            $subtask->load($row->task_id);
            $data['subtask'] = $subtask;
            $rows[] = [
                $row->task_name,
                $row->start_date != '' ? set_date($row->start_date) : 'N/A',
                $row->end_date != '' ? set_date($row->end_date) : 'N/A',
                '<span style="text-align: center">' . $subtask->measurement_unit()->symbol . '</span>',
                '<span class="pull-right">' . $row->quantity . '</span>',
                '<span class="pull-right">' . number_format($row->rate, 2) . '</span>',
                number_format(($row->quantity * $row->rate), 2),
                '<span class="pull-right">' . $this->load->view('projects/activities/tasks/subtasks/subtask_list_actions', $data, true) . '</span>'
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);

    }

    public function predecessor(){
        $pred = new self();
        $pred->load($this->predecessor);
        return $pred;
    }


    public function subtasks($count = false){
        $subtasks = $this->task->get(0,0,['predecessor'=>$this->{$this::DB_TABLE_PK}]);
        if($count){
            return count($subtasks);
        } else {
            return $subtasks;
        }
    }
}

