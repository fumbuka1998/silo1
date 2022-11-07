<?php

class Payroll extends MY_Model
{

    const DB_TABLE = 'payroll';
    const DB_TABLE_PK = 'id';

    public $payroll_for;
    public $foward_to;
    public $department_id;
    public $status;
    public $approved_by;
    public $created_by;

    public function current_approval_level(){

        if($this->foward_to){
            $this->load->model('approval_chain_level');
            $chain_level = new Approval_chain_level();
            $chain_level->load($this->foward_to);
            return $chain_level;
        } else {
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
    }

    public function last_approval(){
        $this->load->model('payroll_approval');
        $approvals = $this->payroll_approval->get(1,0,['payroll_id' => $this->{$this::DB_TABLE_PK}],' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function payroll_dropdown_options()
    {
        $payroll_dropdowns = $this->get();
        $options[''] = '&nbsp;';
        foreach ($payroll_dropdowns as $payroll_dropdown){
            $payroll = new Payroll();
            $payroll->load($payroll_dropdown->{$this::DB_TABLE_PK});
            if(strtoupper($payroll->status) == 'APPROVED'){
                $options[$payroll_dropdown->{$this::DB_TABLE_PK}] = strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll_dropdown->payroll_for.'-1')))->format('F')) . ' ' . date('Y', strtotime($payroll_dropdown->payroll_for.'-1'));
            }
        }
        return $options;
    }
}