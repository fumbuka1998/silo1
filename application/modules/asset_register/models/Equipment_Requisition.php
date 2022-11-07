<?php

class Equipment_Requisition extends MY_Model{
    
    const DB_TABLE = 'requisitions';
    const DB_TABLE_PK = 'requisition_id';

    public $approval_module_id;
    public $request_date;
    public $required_date;
    public $finalized_date;
    public $requesting_comments;
    public $requester_id;
    public $finalizer_id;
    public $status;

    public function requisition_number(){
        return add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function project_requisition(){
        $this->load->model('project_requisition');
        $junction_items = $this->project_requisition->get(1,0,['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junction_items) ? array_shift($junction_items) : false;
    }

    public function cost_center_requisition(){
        $this->load->model('cost_center_requisition');
        $junction_items = $this->cost_center_requisition->get(1,0,['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junction_items) ? array_shift($junction_items) : false;
    }

    public function cost_center_name(){
        $for_project = $this->project_requisition();
        if($for_project){
            $source = $for_project->project()->project_name;
        } else {
            $source = $this->cost_center_requisition()->cost_center()->cost_center_name;
        }
        return $source;
    }

    public function requester()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->requester_id);
        return $employee;
    }

    public function finalizer()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->finalizer_id);
        return $employee;
    }

    public function material_items($approved_vendor_id = 'all'){
        $this->load->model('requisition_material_item');
        $where['requisition_id'] = $this->{$this::DB_TABLE_PK};
        if($approved_vendor_id != 'all'){
           $where['approved_vendor_id'] = $approved_vendor_id;
        }
        return $this->requisition_material_item->get(0,0,$where);
    }

    public function equipment_items($approved_vendor_id = 'all'){
        $this->load->model('requisition_equipment_item');
        $where['requisition_id'] = $this->{$this::DB_TABLE_PK};
        if($approved_vendor_id != 'all'){
           $where['approved_vendor_id'] = $approved_vendor_id;
        }
        return $this->requisition_equipment_item->get(0,0,$where);
    }

    public function cash_items(){
        $this->load->model('requisition_cash_item');
        $where['requisition_id'] = $this->{$this::DB_TABLE_PK};
        return $this->requisition_cash_item->get(0,0,$where);
    }

    public function delete_items(){
        $this->db->where('requisition_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete(['requisition_material_items','requisition_cash_items']);
    }

    public function delete_junctions(){
        $this->db->where('requisition_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete(['project_requisitions','cost_center_requisitions']);
    }

    public function vendor_options(){
        $sql = 'SELECT * FROM (
                    SELECT vendor_id, vendors.vendor_name FROM requisition_material_items
                    LEFT JOIN vendors ON requisition_material_items.approved_vendor_id = vendors.vendor_id
                    WHERE requisition_material_items.requisition_id = "' . $this->{$this::DB_TABLE_PK} . '"
                    AND requisition_material_items.approved_vendor_id IS NOT NULL

                    UNION

                    SELECT vendor_id, vendors.vendor_name FROM requisition_tools_items
                    LEFT JOIN vendors ON requisition_tools_items.approved_vendor_id = vendors.vendor_id
                    WHERE requisition_tools_items.requisition_id = "' . $this->{$this::DB_TABLE_PK} . '"
                    AND requisition_tools_items.approved_vendor_id IS NOT NULL
                ) AS vendors_options
                GROUP BY vendors_options.vendor_id;
                ';
        $query = $this->db->query($sql);
        $vendors = $query->result();
        $options = '<option value="">&nbsp;</option>';
        $approved_vendors = [];
        if(!empty($vendors)){
            $options .= '<optgroup label="Approved Vendors"></optgroup>';
            foreach($vendors as $vendor){
                $approved_vendors[] = $vendor->vendor_id;
                $options .= '<option value="'.$vendor->vendor_id.'">'.$vendor->vendor_name.'</option>';
            }
        }

        $sql_other_vendors = 'SELECT vendor_id, vendor_name FROM vendors
                ';
        $query = $this->db->query($sql_other_vendors);
        $other_vendors = $query->result();
        if(!empty($other_vendors)){
            $options .= '<optgroup label="Other Vendors"></optgroup>';
            foreach($other_vendors as $vendor){
                if(!in_array($vendor->vendor_id, $approved_vendors)) {
                    $options .= '<option value="' . $vendor->vendor_id . '">' . $vendor->vendor_name . '</option>';
                }
            }
        }
        return $options;
    }

    public function is_project_related(){
        return $this->project_requisition();
    }

    public function requester_title(){
        if($this->is_project_related()) {
        $sql = 'SELECT position_name AS title FROM project_team_members
                  LEFT JOIN projects ON project_team_members.project_id = projects.project_id
                  LEFT JOIN job_positions ON project_team_members.job_position_id = job_positions.job_position_id
                  LEFT JOIN project_requisitions ON projects.project_id = project_requisitions.project_id
                  WHERE employee_id = "' . $this->requester_id . '" AND project_requisitions.requisition_id = "'.$this->{$this::DB_TABLE_PK}.'"
                  ';
        $query = $this->db->query($sql);
        return $query->row()->title;
        } else {
            return $this->requester()->position()->position_name;
        }
    }

    public function attachments(){
        $this->load->model('requisition_attachment');
        return $this->requisition_attachment->get(0,0,['requisition_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function requisitions_list( $project_id, $limit, $start, $keyword, $order){

        $this->load->model(['vendor','account','inventory_location']);
        $data['currency_options'] = currency_dropdown_options();
        $data['vendor_options'] = $this->vendor->vendor_options();
        $data['approval_module_options'] = approval_module_dropdown_options();
        $data['main_location_options'] = $this->inventory_location->dropdown_options('main');
        $data['account_options'] = account_dropdown_options(['BANK','CASH IN HAND']);
        $data['expense_accounts_options'] = $this->account->dropdown_options(['DIRECT EXPENSES','INDIRECT EXPENSES']);


        if($project_id){
            $data['cashbook_options'] = $this->account->requisition_cashbook_options($project_id);
        }

        //order string
        $order_string = dataTable_order_string(['request_date','requisition_id','','required_date'],$order,'request_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;


       

        $where = '';
        if ($keyword != '') {
            $where .= ' (request_date LIKE "%' . $keyword . '%" OR status LIKE "%' . $keyword . '%" OR required_date LIKE "%' . $keyword . '%")
            ';
        }

        if($this->input->post('job_position_id') != null){
            $my_desk_requisitions = 'SELECT requisition_id FROM requisition_approvals
                                      LEFT JOIN approval_chain_levels ON requisition_approvals.approval_chain_level_id = approval_chain_levels.id
                                      
                    ';
        }

        $where = $where != '' ? ' WHERE '.$where : '';

        $sql = ' SELECT SQL_CALC_FOUND_ROWS requisitions.requisition_id, request_date, required_date,status
                        FROM requisitions JOIN requisition_equipment_items on  requisition_equipment_items.requisition_id=requisitions.requisition_id
                        '.$where.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $records_total = $records_filtered;

        $rows = [];
        foreach ($results as $row) {

            $requisition = new self();
            $requisition->load($row->requisition_id);
            $data['requisition'] = $requisition;
            
            $rows[] = [
                custom_standard_date($row->request_date),
                $requisition->requisition_number(),
                $requisition->cost_center_name(),
                $row->required_date != null ? custom_standard_date($row->required_date) : 'N/A',
                $requisition->progress_status_label(),
                $this->load->view('equipments_requisitions/requisitions_list_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }
    
    public function insert_project_requisition($project_id){
        $this->load->model('project_requisition');
        $junction_item = new Project_requisition();
        $junction_item->project_id = $project_id;
        $junction_item->requisition_id = $this->{$this::DB_TABLE_PK};
        $junction_item->save();
    }

    public function insert_cost_center_requisition($cost_center_id){
        $this->load->model('cost_center_requisition');
        $junction_item = new Cost_center_requisition();
        $junction_item->cost_center_id = $cost_center_id;
        $junction_item->requisition_id = $this->{$this::DB_TABLE_PK};
        $junction_item->save();
    }

    public function approval_module()
    {
        $this->load->model('approval_module');
        $approval_module = new Approval_module();
        $approval_module->load($this->approval_module_id);
        return $approval_module;
    }

    public function last_approval(){
        $this->load->model('requisition_approval');
        $approvals = $this->requisition_approval->get(1,0,['requisition_id' => $this->{$this::DB_TABLE_PK}],' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function current_approval_level(){
        $last_approval = $this->last_approval();
        $this->load->model('approval_module');
        $current_level = !$last_approval ? $this->approval_module->chain_levels(0,$this->approval_module_id,'active') : $last_approval->approval_chain_level()->next_level();
        return !empty($current_level) ? (is_array($current_level) ? array_shift($current_level) : $current_level) : false;
    }

    public function progress_status_label(){
        $current_level  = $this->current_approval_level();
        if($current_level){
            $label = '<span style="font-size: 12px" class="label label-info">Waiting to be '.$current_level->label.' by '.$current_level->job_position()->position_name.'</span>';
        } else {
            $label = '<span style="font-size: 12px" class="label label-success">Approval Chain Complete</span>';
        }
        return $label;
    }

    public function requisition_approvals($requisition_id = null){
        $this->load->model('requisition_approval');
        $requisition_id = !is_null($requisition_id)  ? $requisition_id : $this->{$this::DB_TABLE_PK};
        return $this->requisition_approval->get(0,0,['requisition_id' => $requisition_id]);
    }
}

