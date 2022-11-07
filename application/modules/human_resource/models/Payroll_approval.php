<?php

class Payroll_approval extends MY_Model
{

    const DB_TABLE = 'payroll_approvals';
    const DB_TABLE_PK = 'id';

    public $payroll_id;
    public $approved_date;
    public $approving_coments;
    public $approval_chain_level_id;
    public $returned_chain_level_id;
    public $status;
    public $is_final;
    public $created_by;

    public function approval_chain_level()
    {
        $this->load->model('approval_chain_level');
        $approval_chain_level = new Approval_chain_level();
        $approval_chain_level->load($this->approval_chain_level_id);
        return $approval_chain_level;
    }

}