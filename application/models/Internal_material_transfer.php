<?php

class Internal_material_transfer extends MY_Model{
    
    const DB_TABLE = 'internal_material_transfers';
    const DB_TABLE_PK = 'transfer_id';

    public $transfer_date;
    public $location_id;
    public $project_id;
    public $employee_id;
    public $receiver;
    public $comments;

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function transfer_number(){
        return 'INT/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function material_items(){
        $where['transfer_id'] = $this->{$this::DB_TABLE_PK};
        $this->load->model('internal_material_transfer_item');
        return $this->internal_material_transfer_item->get(0,0,$where);
    }

    public function asset_items()
    {
        $this->load->model('internal_transfer_asset_item');
        return $this->internal_transfer_asset_item->get(0,0,['transfer_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }
//
//    public function delete()
//    {
//        parent::delete(); // TODO: Change the autogenerated stub
//
//        $sql = 'DELETE FROM material_stocks WHERE stock_id IN (
//            SELECT stock_id FROM internal_material_transfer_items WHERE transfer_id = '.$this->{$this::DB_TABLE_PK}.'
//        )';
//
//    }

}
