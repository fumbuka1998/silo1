<?php

class Activity extends MY_Model{
    
    const DB_TABLE = 'activities';
    const DB_TABLE_PK = 'activity_id';

    public $activity_name;
    public $project_id;
    public $description;


    public function start_date(){
        $sql = 'SELECT MIN(start_date) AS start_date FROM tasks
                WHERE activity_id = "'.$this->{$this::DB_TABLE_PK}.'"
                AND start_date IS NOT NULL';
        $query = $this->db->query($sql);
        $results = $query->result();
        return !empty($results) > 0 ? array_shift($results)->start_date : false;
    }

    public function end_date(){
        $sql = 'SELECT MAX(end_date) AS end_date FROM tasks
                WHERE activity_id = "'.$this->{$this::DB_TABLE_PK}.'"
                AND end_date IS NOT NULL';
        $query = $this->db->query($sql);
        $results = $query->result();
        return !empty($results) > 0 ? array_shift($results)->end_date : false;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function tasks($total = false){
        $this->load->model('task');
        $where['activity_id'] = $this->{$this::DB_TABLE_PK};
        return $total ? $this->task->count_rows($where) : $this->task->get(0,0,$where,' start_date ASC');
    }

    public function contract_sum(){
        $sql = 'SELECT COALESCE(SUM(quantity*rate),0) AS contract_sum FROM tasks
              tasks WHERE tasks.activity_id = "'.$this->{$this::DB_TABLE_PK}.'"
              ';
        $query = $this->db->query($sql);
        return $query->row()->contract_sum;
    }

    public function actual_cost($cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment','sub_contract'], $from = null, $to = null){

        $cost_figure = 0;
        if(is_string($cost_types)){
            $cost_types = [$cost_types];
        }

        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material','miscellaneous','permanent_labour'])) {
                $model = $cost_type . '_cost';
                $this->load->model($model);
                $cost_figure += $this->$model->actual_cost($this->{$this::DB_TABLE_PK}, 'activity',$from, $to);

            }else if(in_array($cost_type,['equipment','casual_labour'])){
                $model = 'project_plan_task_execution_'.$cost_type;
                $this->load->model($model);
                $cost_figure += $this->$model->actual_cost($this->{$this::DB_TABLE_PK}, 'activity',$from, $to);
            }

            if($cost_type == 'miscellaneous') {
                $this->load->model('payment_voucher_item');
                $cost_figure += $this->payment_voucher_item->cost_figure($this->{$this::DB_TABLE_PK}, 'activity',$from, $to);
            } else if($cost_type == 'sub_contract'){
                $this->load->model('sub_contract_certificate_payment_voucher');
                $cost_figure += $this->sub_contract_certificate_payment_voucher->actual_cost($this->{$this::DB_TABLE_PK}, 'activity',$from, $to);
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
                $model = $cost_type .'_budget';
                $this->load->model($model);
                $budget_figure += $this->$model->budget_figure($this->{$this::DB_TABLE_PK},'activity');
            }
        }
        return $budget_figure > 0 ? $budget_figure : 0.0000000001/10E+300;
    }

    public function timeline_percentage($date = null){
        $date = $date != null ? $date : date('Y-m-d');
        $duration = $this->duration();
        $elapsed = $this->elapsed_days($date);
        return $duration > 0 ? round($elapsed*100/$duration,2) : 0;
    }

    public function completion_percentage(){
        $actual_cost_as_per_contractsum = $this->actual_cost_as_per_contractsum();
        $contract_sum = $this->contract_sum();
        return $contract_sum != 0 ? ($actual_cost_as_per_contractsum / $contract_sum) * 100 : 0;
    }

    public function total_activity_tasks_quantity($executed = false){
        $tasks = $this->tasks();
        $total_task_quantity = 0;
        if(!empty($tasks)) {
            foreach ($tasks as $task) {
                if($executed){
                    $total_task_quantity += $task->project_plan_task_execution($task->{$task::DB_TABLE_PK});
                }else {
                    $total_task_quantity += $task->quantity;
                }
            }
        }else{
            $total_task_quantity += 0;
        }
        return $total_task_quantity;
    }
    
    public function budget_spending_percentage($cost_types = ['material','miscellaneous','permanent_labour','casual_labour','equipment']){
        return round(($this->actual_cost($cost_types)/$this->budget_figure($cost_types))*100,2);
    }

    public function budget_figure_at_completion(){
        $tasks = $this->tasks();
        $budget_figure = 0;
        foreach($tasks as $task) {
            $budget_figure += $task->contract_sum();
        }
        return $budget_figure;
    }

    public function elapsed_days($date = null){
        $date = $date != null ? $date : date('Y-m-d');
        $start_date = $this->start_date();
        $start_date = $start_date ? $start_date : 0;
        if($date > $start_date) {
            $elapsed_days = number_of_days($start_date, $date);
        } else {
            $elapsed_days = 0;
        }
        return $start_date != false && $elapsed_days > 0 ? $elapsed_days : 0;
    }

    public function duration(){
        $start_date = $this->start_date();
        $end_date = $this->end_date();
        $start_date = $start_date ? $start_date : 0;
        $end_date = $end_date ? $end_date : 0;
        $duration = number_of_days($start_date,$end_date);
        return $duration >= 0 ? $duration : 0;
    }

    public function store_sub_location_options(){
        $sql = 'SELECT sub_location_id, sub_location_name FROM sub_locations
                LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id
                LEFT JOIN projects ON inventory_locations.project_id = projects.project_id
                LEFT JOIN activities ON projects.project_id = activities.project_id
                WHERE activities.activity_id = "'.$this->{$this::DB_TABLE_PK}.'"
        ';
        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = '&nbsp;';

        foreach($results as $result){
            $options[$result->sub_location_id] = $result->sub_location_name;
        }

        return $options;
    }

    public function actual_cost_as_per_contractsum(){
        $tasks = $this->tasks();
        $cost_figure = 0;
        foreach($tasks as $task) {
            $executed_task_quantity = $task->project_plan_task_execution($this->{$this::DB_TABLE_PK});
            $cost_figure += $executed_task_quantity * $task->rate;
        }
        return $cost_figure;
    }

    public function dropdown_options($project_id){
        $activities = $this->get(0,0,['project_id'=>$project_id], 'activity_id ASC');
        $options[''] = 'All';
        foreach($activities as $activity){
            $options[$activity->{$activity::DB_TABLE_PK}] = $activity->activity_name;
        }
        return $options;
    }

}

