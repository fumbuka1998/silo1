<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 12:04 PM
 */

class Project_plan_task extends MY_Model{
    const DB_TABLE = 'project_plan_tasks';
    const DB_TABLE_PK = 'id';

    public $project_plan_id;
    public $task_id;
    public $quantity;
    public $output_per_day;
    public $created_by;

    public function duration(){
        return $this->quantity / $this->output_per_day;
    }

    public function task()
    {
        $this->load->model('task');
        $task = new Task();
        $task->load($this->task_id);
        return $task;
    }

    public function project_plan_tasks_list($limit,$start,$keyword,$order,$project_plan_id){
        $where=' project_plan_id = '.$project_plan_id.'';
        if ($keyword!=''){
            if($where != ''){
                $where .= ' AND ';
            }
            $where.=' (output_per_day LIKE "%'.$keyword.'%" OR task_id LIKE "%'.$keyword.'%" OR quantity LIKE "%'.$keyword.'%" ) ';
        }
        $order_string = dataTable_order_string(['task_id','quantity','output_per_day','duration'],$order,'task_id');

        $project_plan_tasks = $this->get($limit,$start,$where,$order_string);

        $this->load->model('project_plan');
        $project_plan = new Project_plan();
        $project_plan->load($project_plan_id);
        $data['plan_cost_center_options'] = $project_plan->project()->plan_cost_center_options();
        $tasks=[];
        foreach ($project_plan_tasks as $task){
            $project_plan_task = new self();
            $project_plan_task->load($task->id);
            $data['project_plan_task'] = $project_plan_task;
            $data['unit_symbol'] = $task->task()->measurement_unit()->symbol;
            $tasks[] = [
                wordwrap($project_plan_task->task()->task_name,85,'<br/>'),
                $task->task()->measurement_unit()->symbol,
                $task->quantity,
                $task->output_per_day,
                round(($task->quantity / $task->output_per_day),2),
                $this->load->view('projects/plans/project_plan_tasks/list_actions',$data,true)
            ];
        }

        $data['data']=$tasks;
        $data['recordsFiltered']= $this->count_rows($where);
        $data['recordsTotal']= $this->count_rows(['project_plan_id' => $project_plan_id]);
        return json_encode($data);

    }

    public function project_plan_task_materials(){
        $this->load->model('project_plan_task_material_budget');
        $where['project_plan_task_id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan_task_material_budget->get(0,0,$where);
    }

    public function project_plan_task_equipments(){
        $this->load->model('project_plan_task_equipment_budget');
        $where['project_plan_task_id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan_task_equipment_budget->get(0,0,$where);
    }

    public function project_plan_task_casual_labours(){
        $this->load->model('project_plan_task_casual_labour_budget');
        $where['project_plan_task_id'] = $this->{$this::DB_TABLE_PK};
        return $this->project_plan_task_casual_labour_budget->get(0,0,$where);
    }

    public function budget_scheduled($project_plan_id,$cost_types = ['material_budget','casual_labour_budget','equipment_budget']){
        $budget_figure = 0;
        foreach ($cost_types as $cost_type){
            if(in_array($cost_type,['material_budget','casual_labour_budget','equipment_budget'])) {
                $model = 'project_plan_task_'.$cost_type;
                $this->load->model($model);
                $method = 'total_plan_'.$cost_type;
                $budget_figure += $this->$model->$method($project_plan_id,$this->{$this::DB_TABLE_PK},'task');
            }
        }
        return $budget_figure > 0 ? $budget_figure : 0.00;
    }

    public function project_plan_task_execution(){
        $this->load->model('project_plan_task_execution');
        $where['task_id'] = $this->task_id;
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

    public function per_unit_bcwp(){
        $budget_scheduled = $this->budget_scheduled($this->project_plan_id);
        $quantity_scheduled = $this->quantity > 0 ? $this->quantity : pow(10,-11);
        return $budget_scheduled / $quantity_scheduled;
    }

}