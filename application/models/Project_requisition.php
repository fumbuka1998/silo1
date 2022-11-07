<?php

class Project_requisition extends MY_Model{
    
    const DB_TABLE = 'project_requisitions';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $requisition_id;

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->requisition_id);
        return $requisition;
    }

}

