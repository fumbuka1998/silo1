<?php

class Project_purchase_order extends MY_Model{
    
    const DB_TABLE = 'project_purchase_orders';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $purchase_order_id;

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

}

