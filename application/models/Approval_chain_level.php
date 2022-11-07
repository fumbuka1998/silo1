<?php

class Approval_chain_level extends MY_Model{
    
    const DB_TABLE = 'approval_chain_levels';
    const DB_TABLE_PK = 'id';
    
    public $approval_module_id;
    public $level;
    public $label;
    public $level_name;
    public $change_source;
    public $special_level;
    public $status;
    public $created_by;
   

    public function approval_module(){
        $this->load->model('approval_module');
        $approval_module = new Approval_module();
        $approval_module->load($this->approval_module_id);
        return $approval_module;
    }

    public function approval_chains(){
        $approval_chains = $this->get();
        return $approval_chains;
    }

    public function next_level(){
        $next_levels = $this->get(1,0,[
            'approval_module_id' => $this->approval_module_id,
            'status' => "active",
            'level > ' => $this->level,
            'special_level' => 0
        ],' level ASC');
        return !empty($next_levels) ? array_shift($next_levels) : false;
    }

    public function employee_approval_chain_levels(){
        $this->load->model('human_resource/employee_approval_chain_level');
        return $this->employee_approval_chain_level->get(0,0,['approval_chain_level_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function employees(){
        $junctions = $this->employee_approval_chain_levels();
        $employees = [];
        foreach ($junctions as $junction){
            $employees[] = $junction->employee();
        }
        return $employees;
    }

    public function employee_options()
    {
        $employees = $this->employees();
        $options[''] = '&nbsp;';
        foreach ($employees as $employee){
            $options[$employee->{$employee::DB_TABLE_PK}] = $employee->full_name();
        }
        return $options;
    }

    public function can_approve_positions(){
        $employees = $this->employee_approval_chain_levels();
        $positions = [];
        foreach ($employees as $employee){
            $positions[] = $employee->employee_id;
        }

        return $positions;
    }

    public function previous_levels(){
        return $this->get(0,0,[
            'level < ' => $this->level,
            'approval_module_id' => $this->approval_module_id,
            'status' => 'ACTIVE'
        ]);
    }

    public function previous_level_options(){
        $previous_levels = $this->previous_levels();
        $options[0] = '&nbsp';
        foreach ($previous_levels as  $level){
            $options[$level->{$level::DB_TABLE_PK}] = $level->level_name;
        }
        return $options;
    }

}

