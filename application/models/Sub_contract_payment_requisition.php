<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/22/2018
 * Time: 9:11 AM
 */

class Sub_contract_payment_requisition extends MY_Model{
    const DB_TABLE = 'sub_contract_payment_requisitions';
    const DB_TABLE_PK = 'sub_contract_requisition_id';

    public $approval_module_id;
    public $currency_id;
    public $request_date;
    public $required_date;
    public $finalized_date;
    public $requester_id;
    public $finalizer_id;
    public $vat_inclusive;
    public $vat_percentage;
    public $requesting_comments;
    public $finalizing_comments;
    public $foward_to;
    public $status;



    public function sub_contract_requisition_number(){
        return 'SC-RQ/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function currency(){
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function approval_module()
    {
        $this->load->model('approval_module');
        $approval_module = new Approval_module();
        $approval_module->load($this->approval_module_id);
        return $approval_module;
    }

    public function finalizer()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->finalizer_id);
        return $employee;
    }

    public function requester()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->requester_id);
        return $employee;
    }

    public function foward_to(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->foward_to);
        return $employee;
    }

    public function forwarded_to_employee_special_level()
    {
        $sql = 'SELECT approval_chain_level_id FROM employee_approval_chain_levels
                LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                WHERE approval_chain_levels.approval_module_id = ' . $this->approval_module_id . '
                AND employee_id = ' . $this->foward_to . '
                AND status = "active"
                AND special_level = 1';

        $query = $this->db->query($sql);
        return !empty($query->row()) > 0 ? $query->row() : false;
    }

    public function cost_center_name(){
        $this->load->model('sub_contract_payment_requisition_item');
        $sub_contract_requisition_items = $this->sub_contract_payment_requisition_item->get(0,0,['sub_contract_requisition_id'=>$this->{$this::DB_TABLE_PK}]);
        if(!empty($sub_contract_requisition_items)){
            $sub_contract_requisition_item = array_shift($sub_contract_requisition_items);
            $cost_center_name = $sub_contract_requisition_item->certificate()->sub_contract()->project()->project_name;
            return $cost_center_name;
        } else {
            return false;
        }
    }

    public function project(){
        $this->load->model('sub_contract_payment_requisition_item');
        $sub_contract_requisition_items = $this->sub_contract_payment_requisition_item->get(0,0,['sub_contract_requisition_id'=>$this->{$this::DB_TABLE_PK}]);
        $sub_contract_requisition_item = array_shift($sub_contract_requisition_items);
        return $sub_contract_requisition_item->certificate()->sub_contract()->project();
    }

    public function department(){
        return $this->project()->category()->category_name;
    }

    public function sub_contract_requisition_items(){
        $this->load->model('sub_contract_payment_requisition_item');
        return $this->sub_contract_payment_requisition_item->get(0,0,['sub_contract_requisition_id'=>$this->{$this::DB_TABLE_PK}]);
    }

    public function delete_items(){
        $this->db->delete('sub_contract_payment_requisition_items',['sub_contract_requisition_id'=>$this->{$this::DB_TABLE_PK}]);
    }

    public function total_requested_amount(){
        $this->load->model('sub_contract_payment_requisition_item');
        $sub_contract_requisition_items = $this->sub_contract_payment_requisition_item->get(0,0,['sub_contract_requisition_id'=>$this->{$this::DB_TABLE_PK}]);
        $total_requested_amount = 0;
        foreach($sub_contract_requisition_items as $requisition_item){
            if($this->vat_inclusive == 1){
                $total_requested_amount += $requisition_item->requested_amount*1.18;
            } else {
                $total_requested_amount += $requisition_item->requested_amount;
            }
        }
        return $total_requested_amount;
    }

    public function requisition_amount(){
        $this->load->model(['sub_contract_payment_requisition_approval']);
        $approvals = $this->sub_contract_payment_requisition_approval->get(0,0,['sub_contract_requisition_id'=>$this->{$this::DB_TABLE_PK}],'id DESC');
        $last_approval = !empty($approvals) ? array_shift($approvals) : false;
        if($last_approval){
            $sub_contract_requisition_amount = $last_approval->approval_items(true);
        } else {
            $sub_contract_requisition_amount = $this->total_requested_amount();
        }

        return $sub_contract_requisition_amount;
    }

    public function progress_status_label()
    {
        $current_level  = $this->current_approval_level();
        $last_approval = $this->last_approval();
        if ($this->status == 'REJECTED') {
            $label = '<span style="font-size: 12px" class="label label-danger">Rejected By ' . $this->finalizer()->full_name() . '</span>';
        } else if ($this->status == 'INCOMPLETE') {
            $label = '<span style="font-size: 12px" class="label label-warning">' . $this->status . '</span>';
        } else if ($this->status != 'APPROVED') {
            if ($last_approval && $last_approval->forward_to) {
                $label = '<span style="font-size: 12px" class="label label-info">Waiting For ' . $last_approval->forward_to()->full_name() . '</span>';
            } else if ($this->foward_to && !$last_approval) {
                $label = '<span style="font-size: 12px" class="label label-info">Waiting For ' . $this->foward_to()->full_name() . '</span>';
            } else if ($current_level) {
                $label = '<span style="font-size: 12px" class="label label-info">Waiting For ' . $current_level->level_name . '</span>';
            } else {
                $label = '<span style="font-size: 12px" title="The Level being waited to approve has either been removed or disabled" class="label label-danger">Level Not Found!</span>';
            }
        } else {
                $label = '<span style="font-size: 12px" class="label label-success">Approval Completed</span>';
        }
        return $label;
    }

    public function last_approval(){
        $this->load->model('sub_contract_payment_requisition_approval');
        $approvals = $this->sub_contract_payment_requisition_approval->get(1,0,['sub_contract_requisition_id' => $this->{$this::DB_TABLE_PK}],' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function final_approval(){
        $this->load->model('sub_contract_payment_requisition_approval');
        $approvals = $this->sub_contract_payment_requisition_approval->get(1,0,[
            'sub_contract_requisition_id' => $this->{$this::DB_TABLE_PK},
            'is_final' => 1
        ],' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function current_approval_level()
    {
        $last_approval = $this->last_approval();
        $this->load->model('approval_module');
        if ($last_approval) {
            if (!is_null($last_approval->returned_chain_level_id)) {
                $this->load->model('approval_chain_level');
                $current_level = new Approval_chain_level();
                $current_level->load($last_approval->returned_chain_level_id);
            } else {
                $current_level = $last_approval->approval_chain_level()->next_level();
            }
        } else {
            $current_level = $this->approval_module->chain_levels(0, $this->approval_module_id, 'active');
        }

        return !empty($current_level) ? (is_array($current_level) ? array_shift($current_level) : $current_level) : false;
    }

    public function special_level_approval($employees = false){
        $this->load->model('approval_chain_level');
        $level_id = $this->foward_to;
        if($level_id) {
            $approval_chain_level = new Approval_chain_level();
            $approval_chain_level->load($level_id);
            if ($employees) {
                $employees_with_special_approval = $approval_chain_level->can_approve_positions();
                return $employees_with_special_approval;
            } else {
                $special_level_approval = $approval_chain_level->special_level;
                return $special_level_approval;
            }
        } else {
            return false;
        }
    }

    public function sub_contract_requisition_approval(){
        $this->load->model('sub_contract_payment_requisition_approval');
        $sub_contract_approvals = $this->sub_contract_payment_requisition_approval->get(1,0,['sub_contract_requisition_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($sub_contract_approvals) ? array_shift($sub_contract_approvals) : false;
    }

    public function sub_contract_requisition_approvals($sub_contract_requisition_id = null){
        $this->load->model('sub_contract_payment_requisition_approval');
        $sub_contract_requisition_id = !is_null($sub_contract_requisition_id)  ? $sub_contract_requisition_id : $this->{$this::DB_TABLE_PK};
        return $this->sub_contract_payment_requisition_approval->get(0,0,['sub_contract_requisition_id' => $sub_contract_requisition_id]);
    }

    public function attachments(){
        $this->load->model('sub_contract_payment_requisition_attachment');
        $junctions = $this->sub_contract_payment_requisition_attachment->get(0,0,['sub_contract_payment_requisition_id' => $this->{$this::DB_TABLE_PK}]);
        $attachments = [];
        foreach ($junctions as $junction){
            $attachments[] = $junction->attachment();
        }
        return $attachments;
    }

    public function forwarded_to_employee_approval($ret_id = false){
        $this->load->model('sub_contract_payment_requisition_approval');
        $last_approval = $this->last_approval();
        if(!$ret_id){
            return $last_approval ? !is_null($last_approval->forward_to) : !is_null($this->foward_to);
        }
        if($last_approval){
            $forwarded_to_employee = $last_approval->forward_to;
            if($forwarded_to_employee == $this->session->userdata('employee_id')){
                return $ret_id ? $forwarded_to_employee : true;
            }
        } else {
            $forwarded_to_employee = $this->foward_to;
            if($forwarded_to_employee == $this->session->userdata('employee_id')){
                return $ret_id ? $forwarded_to_employee : true;
            }
        }
    }

}