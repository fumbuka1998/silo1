<?php

class Material_cost_center_assignment extends MY_Model{
    
    const DB_TABLE = 'material_cost_center_assignments';
    const DB_TABLE_PK = 'id';

    public $assignment_date;
    public $location_id;
    public $source_project_id;
    public $destination_project_id;
    public $created_by;

    public function value()
    {
        $sql = 'SELECT COALESCE(SUM(quantity*material_stocks.price),0) AS mca_value FROM material_stocks
                LEFT JOIN material_cost_center_assignment_items i ON material_stocks.stock_id = i.stock_id
                WHERE i.material_cost_center_assignment_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->mca_value;
    }

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
        return 'MCA/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function cost_center_assignment_items(){
        $this->load->model('Material_cost_center_assignment_item');

        $where['material_cost_center_assignment_id'] = $this->{$this::DB_TABLE_PK};

        return $this->Material_cost_center_assignment_item->get(0,0,$where);
    }

    public function material_cost_center_assignments($limit, $start, $keyword, $order,$location_id){

        $order_string = dataTable_order_string(['assignment_date','id'],$order,'assignment_date');

        $where = 'location_id = "'.$location_id.'"';
        $sql = 'SELECT COUNT(id) AS records_total FROM (
                    SELECT id FROM material_cost_center_assignments WHERE '.$where.'
                    UNION 
                    SELECT id FROM asset_cost_center_assignments WHERE '.$where.'
                ) AS cost_center_assignments';
        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;

        if($keyword != ''){
            $where .= ' AND assignment_date LIKE "%'.$keyword.'%" OR id LIKE "%'.$keyword.'%" OR source_project_id IN (
                SELECT project_id FROM projects
                LEFT JOIN material_cost_center_assignments ON projects.project_id = material_cost_center_assignments.source_project_id
                WHERE project_name LIKE "%'.$keyword.'%"
            ) OR destination_project_id IN (
                SELECT project_id FROM projects 
                LEFT JOIN asset_cost_center_assignments ON projects.project_id = asset_cost_center_assignments.destination_project_id
                WHERE project_name LIKE "%'.$keyword.'%"
            )';
        }

        $sql = 'SELECT * FROM (
                    SELECT "material" AS assignment_type, material_cost_center_assignments.* FROM material_cost_center_assignments
                    UNION 
                    SELECT "asset" AS assignment_type, asset_cost_center_assignments.* FROM asset_cost_center_assignments
                ) AS cost_center_assignments WHERE '.$where.' ORDER BY '.$order_string.' LIMIT '.$limit.' OFFSET '.$start.'';
        $query = $this->db->query($sql);
        $cost_center_assignments = $query->result();

        $sql = 'SELECT COUNT(id) AS records_filtered FROM (
                    SELECT id FROM material_cost_center_assignments WHERE '.$where.'
                    UNION 
                    SELECT id FROM asset_cost_center_assignments WHERE '.$where.'
                ) AS cost_center_assignments';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $this->load->model(['inventory_location','material_cost_center_assignment','asset_cost_center_assignment']);
        $Inventory_location= new Inventory_location();
        $Inventory_location->load($location_id);
        $data['sub_location_options'] = $Inventory_location->sub_location_options();

        foreach($cost_center_assignments as $assignment){
            $assignment_type = $assignment->assignment_type;
            if($assignment_type == "material"){
                $cost_center_assignment = new Material_cost_center_assignment();
                $cost_center_assignment->load($assignment->id);
            } else {
                $cost_center_assignment = new Asset_cost_center_assignment();
                $cost_center_assignment->load($assignment->id);
            }
            $data['assignment_type'] = $assignment_type;
            $data['cost_center_assignment'] = $cost_center_assignment;
            $rows[] = [
                custom_standard_date($assignment->assignment_date),
                $cost_center_assignment->assignment_number(),
                $cost_center_assignment->source_name(),
                $cost_center_assignment->destination_name(),
                $cost_center_assignment->employee()->full_name(),
                $this->load->view('inventory/cost_center_assignment/cost_center_assignment_actions',$data,true)
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }


}

