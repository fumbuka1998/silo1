<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/26/2018
 * Time: 12:19 PM
 */

class Project_plan_task_execution_material_cost extends MY_Model
{
    const DB_TABLE = 'project_plan_task_execution_material_costs';
    const DB_TABLE_PK = 'id';

    public $plan_task_execution_id;
    public $material_cost_id;


    public function material_cost(){
        $this->load->model('material_cost');
        $material_cost = new Material_cost();
        $material_cost->load($this->material_cost_id);
        return $material_cost;
    }

    public function project_plan_task_execution(){
        $this->load->model('project_plan_task_execution');
        $plan_task_execution = new Project_plan_task_execution();
        $plan_task_execution->load($this->plan_task_execution_id);
        return $plan_task_execution;
    }
}