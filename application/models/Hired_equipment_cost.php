<?php

class Hired_equipment_cost extends MY_Model
{

    const DB_TABLE = 'hired_equipment_costs';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $task_id;
    public $start_date;
    public $end_date;
    public $hired_equipment_id;
    public $rate_mode;
    public $rate;
    public $description;
    public $created_at;
    public $created_by;


    public function amount(){

        return ((round(abs(strtotime($this->start_date) - strtotime($this->end_date))/86400)+1)* $this->rate);
    }

    public function project()
    {
        $this->load->model('Project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function equipment()
    {
        $this->load->model('asset_register/Hired_equipment');
        $Hired_equipment=new Hired_equipment();
        $Hired_equipment->load($this->hired_equipment_id);
        return $Hired_equipment;
    }

    
  
    public function hired_equipment_costs_list($limit, $start, $keyword, $order,$project_id){


        $where = 'project_id = "'.$project_id.'" AND ';

        $task_id = $this->input->post('task_id');
        //$task_id ='';

        if($task_id ==''){

            $where .= ' task_id IS NULL';

        }else{

            //$where = ' task_id = "'.$task_id.'"';
            $where .= ' task_id = "'.$task_id.'" ';
        }
        
        $where = ' project_id = "'.$project_id.'"';
      
        if($keyword != ''){

            $where .= 'hired_equipment_id LIKE "%'.$keyword.'%" OR start_date LIKE "%'.$keyword.'%"';
        }

        $order_string = dataTable_order_string(['hired_equipment_id','start_date'],$order,'hired_equipment_id');

        $hired_equipment_costs = $this->get($limit,$start,$where,$order_string);

        $records_total = $this->count_rows($where);

        $rows = [];
        $this->load->model('Asset');
        $this->load->model('Asset_group');
        $this->load->model('Employee');
        $this->load->model('Project');
        $this->load->model('Task');

        $data['asset_group_options'] =$this->Asset_group->dropdown_options();

        $data['employee_options']  = employee_options();

        $cost_figure=0;

        foreach ($hired_equipment_costs as $hired_equipment_cost){

                $cost_figure=  $cost_figure+ ((round(abs(strtotime($hired_equipment_cost->start_date) - strtotime($hired_equipment_cost->end_date))/86400)+1)* $hired_equipment_cost->rate);


            $data['asset_options']=$hired_equipment_cost->equipment()->equipment_group()->equipments_dropdown_options();
            $data['hired_equipment_cost'] = $hired_equipment_cost;
            $data['project'] = $hired_equipment_cost->project();
            $data['cost_center_options'] = $hired_equipment_cost->project()->cost_center_options();

            $rows[] = [
                $hired_equipment_cost->equipment()->equipment_code,
                $hired_equipment_cost->task_name()->task_name,
                custom_standard_date($hired_equipment_cost->start_date),
                custom_standard_date( $hired_equipment_cost->end_date),
                $hired_equipment_cost->rate_mode,
                number_format($hired_equipment_cost->rate),
                number_format($hired_equipment_cost->amount()),
                $hired_equipment_cost->created_at,
                $hired_equipment_cost->created_by()->full_name(),
                $hired_equipment_cost->description,
                $this->load->view('projects/costs/equipments/hired_equipments/hired_cost_actions',$data,true)

            ];
        }

        $records_filtered = $this->count_rows($where);

        $json = [
            "cost_total"=>$cost_figure,
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

   
    public function task_name(){

        $this->load->model('Task');
        $Tasks=new Task();
        $Tasks->load($this->task_id);
        return $Tasks;
    }

    public function created_by()
    {
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }



}