<?php

class Owned_equipment_cost extends MY_Model
{

    const DB_TABLE = 'owned_equipment_costs';
    const DB_TABLE_PK = 'id';

    public $project_id;
    public $task_id;
    public $start_date;
    public $end_date;
    public $asset_id;
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
    public function asset()
    {
        $this->load->model('asset_register/Asset');
        $Asset=new Asset();
        $Asset->load($this->asset_id);
        return $Asset;
    }
    public function asset_options($asset_group_id){
        $this->load->model('asset_register/Asset');
        $where['asset_group_id'] =$asset_group_id;
        $asset_group = $this->Asset->get(1,0,$where);
        return !empty($asset_group) ? array_shift($asset_group) : false;
    }
    


    public function owned_equipment_costs_list($limit, $start, $keyword, $order,$project_id){

        //$task_id = $this->input->post('task_id');
        $task_id ='';

        if($task_id==''){

            $where = ' project_id = "'.$project_id.'" AND task_id IS NULL';

        } else {

           // $where = ' task_id = "'.$task_id.'"';
            $where = ' project_id = "'.$project_id.'" AND task_id = "'.$task_id.'" ';
        }

        $where = ' project_id = "'.$project_id.'"';

        $records_total = $this->count_rows($where);
        
        if($keyword != ''){
            $where .= 'asset_id LIKE "%'.$keyword.'%" OR start_date LIKE "%'.$keyword.'%"';
        }

        $order_string = dataTable_order_string(['asset_id','start_date'],$order,'asset_id');

        $Owned_equipment_costs = $this->get($limit,$start,$where,$order_string);
        $rows = [];
        $this->load->model('Asset');
        $this->load->model('Asset_group');
        $this->load->model('Employee');
        $this->load->model('Project');
        $this->load->model('Task');
        //$data['asset_options'] =$this->Asset->asset_dropdown_options();
        $data['asset_group_options'] =$this->Asset_group->dropdown_options();

        $data['employee_options']  = employee_options();

        $cost_figure=0;

        foreach ($Owned_equipment_costs as $Owned_equipment_cost){

            $cost_figure=  $cost_figure+ ((round(abs(strtotime($Owned_equipment_cost->start_date) - strtotime($Owned_equipment_cost->end_date))/86400)+1)* $Owned_equipment_cost->rate);

            $data['asset_options']=$Owned_equipment_cost->asset()->asset_group()->assets_dropdown_options();
            $data['Owned_equipment_cost'] = $Owned_equipment_cost;
            $data['project'] = $Owned_equipment_cost->project();
            $data['cost_center_options'] = $Owned_equipment_cost->project()->cost_center_options();

            $rows[] = [
                $Owned_equipment_cost->asset_name()->asset_name,
                $Owned_equipment_cost->task_name()->task_name,
                custom_standard_date($Owned_equipment_cost->start_date),
                custom_standard_date( $Owned_equipment_cost->end_date),
                $Owned_equipment_cost->rate_mode,
                '<span class="pull-right">' . number_format($Owned_equipment_cost->rate) . '</span>',
                '<span class="pull-right">' . number_format($Owned_equipment_cost->amount()) . '</span>',
                $Owned_equipment_cost->created_at,
                $Owned_equipment_cost->created_by()->full_name(),
                $Owned_equipment_cost->description,
               $this->load->view('projects/costs/equipments/owned_equipments/owned_cost_actions',$data,true)
            ];
        }

        
        $records_filtered = $this->count_rows($where);

        $json = [
            "cost_total" => $cost_figure,
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);

    }

    public function asset_name(){

        $this->load->model('asset_register/Asset');
        $Asset=new Asset();
        $Asset->load($this->asset_id);
        return $Asset;
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