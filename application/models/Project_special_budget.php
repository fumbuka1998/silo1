<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 16/04/2019
 * Time: 10:22
 */
class Project_special_budget extends MY_Model
{

    const DB_TABLE = 'project_special_budgets';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $currency_id;
    public $material_amount;
    public $labour_amount;

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function total_budget(){
        return $this->material_amount+$this->labour_amount;
    }


}

