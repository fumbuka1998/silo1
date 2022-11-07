<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/9/2018
 * Time: 10:19 AM
 */

class Project_plan_task_material_cost extends MY_Model{
    const DB_TABLE = 'project_plan_task_material_costs';
    const DB_TABLE_PK = 'id';

    public $project_plan_task_id;
    public $material_cost_id;

    public function project_plan_task()
    {
        $this->load->model('project_plan_task');
        $project_plan_task = new Project_plan_task();
        $project_plan_task->load($this->project_plan_task_id);
        return $project_plan_task;
    }

    public function material_cost()
    {
        $this->load->model('material_cost');
        $material_cost = new Material_cost();
        $material_cost->load($this->material_cost_id);
        return $material_cost;
    }
}