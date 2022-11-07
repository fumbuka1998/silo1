<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 21/06/2019
 * Time: 10:28
 */

class Asset_cost_center_assignment extends MY_Model{
    const DB_TABLE = 'asset_cost_center_assignments';
    const DB_TABLE_PK = 'id';

    public $assignment_date;
    public $location_id;
    public $source_project_id;
    public $destination_project_id;
    public $created_by;


    public function employee(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function sub_location()
    {
        $this->load->model('Sub_location');
        $sub_location = new Sub_location();
        $sub_location->load($this->sub_location_id);
        return $sub_location;
    }

    public function source_project()
    {
        $this->load->model('Project');
        $project = new Project();
        $project->load($this->source_project_id);
        return $project;
    }

    public function source_name()
    {
        return !is_null($this->source_project_id) ? $this->source_project()->project_name : 'UNASSIGNED MATERIALS';
    }

    public function destination_name()
    {
        return !is_null($this->destination_project_id) ? $this->destination_project()->project_name : 'UNASSIGNED MATERIALS';
    }

    public function destination_project()
    {
        $this->load->model('Project');
        $project = new Project();
        $project->load($this->destination_project_id);
        return $project;
    }

    public function assignment_number(){
        return 'ACA/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function cost_center_assignment_items(){
        $this->load->model('asset_cost_center_assignment_item');
        $where['asset_cost_center_assignment_id'] = $this->{$this::DB_TABLE_PK};
        return $this->asset_cost_center_assignment_item->get(0,0,$where);
    }
}