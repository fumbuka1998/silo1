<?php

class Costs extends CI_Controller{

    public function __construct() {
        parent::__construct();
        check_login();
    }

    public function material_costs_list($level = 'activity',$cost_center_id = '',$from = '',$to = ''){
        $this->load->model('material_cost');
        $from = $this->input->post('from') != null ? $this->input->post('from') : $from;
        $to = $this->input->post('to') != null ? $this->input->post('to') : $to;
        $data['from'] = $from;
        $data['to'] = $to;
        if($level == 'project'){
            $where = ['project_id' => $cost_center_id];
            if($from != ''){
                $where['cost_date >=' ] = $from;
            }
            if($to != ''){
                $where['cost_date <=' ] = $to;
            }
            $this->load->model('project');
            $project = new Project();
            $project->load($cost_center_id);

            $material_costs = $this->material_cost->get(0,0,$where,' cost_date ASC ');
        } else if($level == 'activity'){
            $this->load->model('activity');
            $activity = new Activity();
            $activity->load($cost_center_id);
            $project = $activity->project();

            $data['activity_name'] = $activity->activity_name;
            $where = ' task_id IN(SELECT task_id FROM tasks WHERE activity_id = '.$cost_center_id.') ';
            if($from != ''){
                $where .=  ' AND cost_date >= "'.$from.'" ';
            }
            if($to != ''){
                $where .=  ' AND cost_date <= "'.$to.'" ';
            }

            $sql = 'SELECT item_name, material_costs.* FROM material_costs
                    LEFT JOIN material_items ON material_costs.material_item_id = material_items.item_id  
                    WHERE '.$where.' ORDER BY item_name ASC';
            $results = $this->db->query($sql)->result();
            $material_costs = [];
            $this->load->model('material_cost');
            foreach($results as $result){
                $cost_item = new Material_cost();
                $cost_item->load($result->material_cost_id);
                $material_costs[] = $cost_item;
            }

//            $material_costs = $this->material_cost->get(0,0,$where,' cost_date ASC ');
        } else {

            $this->load->model('task');
            $task = new Task();
            $task->load($cost_center_id);
            $project = $task->project();

            $data['activity_name'] = $task->activity()->activity_name;
            $data['task_name'] = $task->task_name;
            $where = ['task_id' => $cost_center_id];
            if($from != ''){
                $where['cost_date >=' ] = $from;
            }
            if($to != ''){
                $where['cost_date <=' ] = $to;
            }
            $material_costs = $this->material_cost->get(0,0,$where,' cost_date ASC ');
        }
        $data['material_costs'] = $material_costs;
        $data['project'] = $project;

        $html = $this->load->view('projects/costs/material/material_costs_report', $data, true);

        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();
		$footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
		$pdf->setFooter($footercontents);
        //generate the PDF!
        $pdf->WriteHTML($html);
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output('Material Costs Report' . date('Y-m-d') . '.pdf', 'I');


    }

    public function save_material_cost(){
        $this->load->model('material_cost');
        $item = new Material_cost();
        $edit = $item->load($this->input->post('item_id'));
        $item->material_item_id = $this->input->post('material_id');
        $item->source_sub_location_id = $this->input->post('source_sub_location_id');
        $item->quantity = $this->input->post('quantity');
        $item->project_id = $this->input->post('project_id');
        $item->cost_date = $this->input->post('date');
        $material_item = $item->material();
        $item->rate = $material_item->sub_location_average_price($item->source_sub_location_id,$item->project_id);
        $item->description = $this->input->post('description');
        $item->task_id = $this->input->post('cost_center_id');
        $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
        $item->employee_id = $this->session->userdata('employee_id');
        if($item->save()){
            //
        }
    }

    public function save_multiple_material_costs()
    {
        $project_id = $this->input->post('project_id');
        $dates = $this->input->post('dates');
        $source_sub_location_ids = $this->input->post('source_sub_location_ids');
        $material_ids = $this->input->post('material_ids');
        $cost_center_ids = $this->input->post('cost_center_ids');
        $quantities = $this->input->post('quantities');
        $descriptions = $this->input->post('descriptions');
        $this->load->model('material_cost');
        foreach ($dates as $index => $date){
            $material_cost = new Material_cost();
            $material_cost->cost_date = $date;
            $material_cost->material_item_id = $material_ids[$index];
            $material_cost->source_sub_location_id = $source_sub_location_ids[$index];
            $material_cost->project_id = $project_id;
            $material_cost->description = $descriptions[$index];
            $material_cost->quantity = $quantities[$index];
            $material_cost->task_id = $cost_center_ids[$index];
            $material_cost->task_id = $material_cost->task_id != '' ? $material_cost->task_id : null;
            $material_item = $material_cost->material();
            $material_cost->rate = $material_item->sub_location_average_price($material_cost->source_sub_location_id,$material_cost->project_id);
            $material_cost->employee_id = $this->session->userdata('employee_id');
            $material_cost->save();
        }
    }

    public function save_bulk_material_cost(){
        $this->load->model('material_cost');
        $material_ids=$this->input->post('material_ids');
        $quantities=$this->input->post('quantities');
        $descriptions=$this->input->post('descriptions');
        $rates=$this->input->post('rates');
        $item_ids=$this->input->post('item_ids');

        foreach ($material_ids as $index => $material_id){

            $item = new Material_cost();
            $edit = $item->load($item_ids[$index]);
            $item->cost_date = $this->input->post('date');
            $item->source_sub_location_id = $this->input->post('source_sub_location_id');
            $item->project_id = $this->input->post('project_id');
            $item->task_id = $this->input->post('cost_center_id');
            $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
            $item->material_item_id = $material_ids[$index];
            $item->quantity = $quantities[$index];
            $item->rate = $rates[$index];
            $item->description = $descriptions[$index];
            $item->employee_id = $this->session->userdata('employee_id');
            $item->save();

        }
    }

    public function save_executions_material_cost(){
        $this->load->model(['material_cost','project_plan_task_execution']);
        $item = new Material_cost();
        $edit = $item->load($this->input->post('item_id'));
        $item->material_item_id = $this->input->post('material_id');
        $item->source_sub_location_id = $this->input->post('source_sub_location_id');
        $item->quantity = $this->input->post('quantity');
        $item->project_id = $this->input->post('project_id');
        $item->cost_date = $this->input->post('date');
        $item->rate = $this->input->post('rate');
        $item->description = $this->input->post('description');
        $plan_task_execution_id = $this->input->post('plan_task_execution_id');
        $plan_task_execution = new Project_plan_task_execution();
        $plan_task_execution->load($plan_task_execution_id);
        $task = $plan_task_execution->task();
        $item->task_id = $task->{$task::DB_TABLE_PK};
        $item->employee_id = $this->session->userdata('employee_id');
        if($item->save()){
            if($edit){
                $item->delete_task_material_cost();
            }
            $this->load->model('project_plan_task_execution_material_cost');
            $task_execution_material_cost = new Project_plan_task_execution_material_cost();
            $task_execution_material_cost->material_cost_id = $item->{$item::DB_TABLE_PK};
            $task_execution_material_cost->plan_task_execution_id = $plan_task_execution_id;
            $task_execution_material_cost->save();
        }
    }

    public function executin_cost_item_delete(){
        $this->load->model('material_cost');
        $item_id = $this->input->post('item_id');
        $item = new Material_cost();
        if($item->load($item_id)){
            $item->delete_task_material_cost();
            $item->delete();
        }

    }

    public function execution_material_cost_list($project_plan_id){
        $this->load->model('material_cost');
        $project_id = $this->input->post('project_id');
        $posted_params = dataTable_post_params();
        echo $this->material_cost->execution_material_cost_list($project_id, $project_plan_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    //owned_equipment cost

    public function owned_equipment_cost_list($project_id=0){
        $this->load->model('Owned_equipment_cost');
        $posted_params = dataTable_post_params();
        echo $this->Owned_equipment_cost->owned_equipment_costs_list( $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$project_id);
    }

    public function save_owned_equipment_cost(){
        $this->load->model('Owned_equipment_cost');
        $Owned_equipment_cost = new Owned_equipment_cost();
        $edit = $Owned_equipment_cost->load($this->input->post('owned_equipment_cost_id'));
        $Owned_equipment_cost->project_id = $this->input->post('project_id');
        $Owned_equipment_cost->task_id = $this->input->post('task_id');
        $Owned_equipment_cost->task_id = trim($Owned_equipment_cost->task_id) != '' ? $Owned_equipment_cost->task_id : null;
        $Owned_equipment_cost->start_date= $this->input->post('start_date');
        $Owned_equipment_cost->end_date =$this->input->post('end_date');
        $Owned_equipment_cost->asset_id = $this->input->post('asset_id');
        $Owned_equipment_cost->rate_mode = $this->input->post('rate_mode');
        $Owned_equipment_cost->rate = $this->input->post('rate');
        $Owned_equipment_cost->description = $this->input->post('description');
        $Owned_equipment_cost->created_by = $this->session->userdata('employee_id');
        if($Owned_equipment_cost->save()){
            //
        }
    }

    public function delete_owned_equipment_cost(){
        $this->load->model('Owned_equipment_cost');
        $Owned_equipment_cost=new Owned_equipment_cost();
        if($Owned_equipment_cost->load($this->input->post('owned_equipment_cost_id'))){
            $Owned_equipment_cost->delete();
        }

    }

    //Hired equupment costs

    public function hired_equipment_cost_list($project_id=0){
        $this->load->model('Hired_equipment_cost');
        $posted_params = dataTable_post_params();
        echo $this->Hired_equipment_cost->hired_equipment_costs_list( $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$project_id);
    }

    public function save_hired_equipment_cost(){
        $this->load->model('Hired_equipment_cost');
        $Hired_equipment_cost = new Hired_equipment_cost();
        $edit = $Hired_equipment_cost->load($this->input->post('hired_equipment_cost_id'));
        $Hired_equipment_cost->project_id = $this->input->post('project_id');
        $Hired_equipment_cost->task_id = $this->input->post('task_id');
        $Hired_equipment_cost->task_id = trim($Hired_equipment_cost->task_id) != '' ? $Hired_equipment_cost->task_id : null;
        $Hired_equipment_cost->start_date= $this->input->post('start_date');
        $Hired_equipment_cost->end_date =$this->input->post('end_date');
        $Hired_equipment_cost->hired_equipment_id = $this->input->post('hired_equipment_id');
        $Hired_equipment_cost->rate_mode = $this->input->post('rate_mode');
        $Hired_equipment_cost->rate = $this->input->post('rate');
        $Hired_equipment_cost->description = $this->input->post('description');
        $Hired_equipment_cost->created_by = $this->session->userdata('employee_id');
        if($Hired_equipment_cost->save()){

        }
    }

    public function delete_hired_equipment_cost(){
        $this->load->model('Hired_equipment_cost');
        $Hired_equipment_cost=new Hired_equipment_cost();
        if($Hired_equipment_cost->load($this->input->post('hired_equipment_cost_id'))){
            $Hired_equipment_cost->delete();
        }

    }

    public function save_permanent_labour_cost(){
        $this->load->model('permanent_labour_cost');
        $member_ids = $this->input->post('member_ids');

        $task_id = $this->input->post('cost_center_id');
        $task_id = $task_id != '' ? $task_id : null;
        $employee_id = $this->session->userdata('employee_id');
        foreach ($member_ids as $index => $member_id){
            $cost_item = new Permanent_labour_cost();
            $cost_item->task_id = $task_id;
            $cost_item->project_team_member_id = $member_id;
            $cost_item->working_mode = $this->input->post('working_modes')[$index];
            if($cost_item->working_mode == 'date_range'){
                $cost_item->start_date = $this->input->post('start_dates')[$index];
                $cost_item->cost_date = $cost_item->start_date;
                $cost_item->end_date = $this->input->post('end_dates')[$index];
            } else {
                $cost_item->cost_date = $this->input->post('cost_dates')[$index];
                $cost_item->end_date = $cost_item->start_date = null;
            }
            $cost_item->duration = $this->input->post('durations')[$index];
            $cost_item->salary_rate = $this->input->post('salary_rates')[$index];
            $cost_item->allowance_rate = $this->input->post('allowances')[$index];
            $cost_item->description = $this->input->post('allowances')[$index];
            $cost_item->description = $this->input->post('descriptions')[$index];
            $cost_item->employee_id = $employee_id;
            $cost_item->save();
        }
    }

    public function costs_items_list($cost_center_level,$cost_center_id){
        $model = $this->input->post('cost_type').'_cost';
        $this->load->model($model);
        $posted_params = dataTable_post_params();
        echo $this->$model->costs_items_list($cost_center_level, $cost_center_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function cost_item_delete(){
        $item_id = $this->input->post('item_id');
        $model = $this->input->post('cost_type').'_cost';
        $this->load->model($model);
        $class_name = ucfirst($model);
        $item = new $class_name();
        if($item->load($item_id)){
            $item->delete();
        }

    }

    public function load_project_costs_summary(){
        $cost_center_id = $this->input->post('cost_center_id');
        $data['general_only'] = false;
        if($cost_center_id == 'project_overall' || $cost_center_id == ''){
            $this->load->model('project');
            $cost_center = new Project();
            $cost_center->load($this->input->post('project_id'));
            if($cost_center_id != 'project_overall'){
                $data['general_only'] = true;
            }
        } else {
            $this->load->model('task');
            $cost_center = new Task();
            $cost_center->load($cost_center_id);
        }
        $data['cost_center'] = $cost_center;
        $this->load->view('projects/costs/costs_summary_table',$data);
    }

    public function miscellaneous_costs_items_list($project_id){
        $this->load->model('project');
        $posted_params = dataTable_post_params();
        echo $this->project->miscellaneous_costs_items_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$project_id);
    }

    public function load_cost_center_dropdown_options(){
    	$this->load->model('cost_center');
    	echo stringfy_dropdown_options($this->cost_center->dropdown_options());
	}

}
