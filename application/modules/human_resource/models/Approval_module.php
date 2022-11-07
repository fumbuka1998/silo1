<?php

class Approval_module extends MY_Model{
    
    const DB_TABLE = 'approval_modules';
    const DB_TABLE_PK = 'id';

    public $module_name;
    
    public function approval_modules(){

        $approval_modules = $this->get();
        return $approval_modules;
    }

    public function greatest_chain_level($id = null){
        $approval_module_id = !is_null($id) ? $id : $this->{ $this::DB_TABLE_PK };
        $this->load->model('approval_chain_level');
        $where = [
            'approval_module_id' => $approval_module_id,
            'special_level' => 0
        ];
        $levels = $this->approval_chain_level->get(1,0,$where,' level DESC');
        return array_shift($levels);
    }

    public function chain_levels($after = 0,$approval_module_id=null,$status = null,$special_access = false){
        
        $approval_module_id = !is_null($approval_module_id) ? $approval_module_id : $this->{ $this::DB_TABLE_PK };


        if($special_access && strtolower($special_access) == 'all') {
            $where = [
                'approval_module_id' => $approval_module_id,
                ' level > ' => $after
            ];
        } else if($special_access) {
            $where = [
                'approval_module_id' => $approval_module_id,
                ' level > ' => $after,
                'special_level' => 1
            ];
        } else {
            $where = [
                'approval_module_id' => $approval_module_id,
                ' level > ' => $after,
                'special_level' => 0
            ];
        }

         if(!is_null($status)){
             $where['status'] = $status;
         }
        $this->load->model('approval_chain_level');
        $levels = $this->approval_chain_level->get(0,0,$where,' level ASC');
        return $levels;
    }

    public function chain_level_dropdown_options($special_access = false)
    {
        $levels = $this->chain_levels($after = 0,$approval_module_id=null,$status = null,$special_access);
        $options = ['' => '&nbsp;'];
        foreach ($levels as $level){
            $options[$level->{$level::DB_TABLE_PK}] = $level->level_name;
        }
        return $options;
    }

    public function to_forward_level_options($all = false)
    {
        if($all){
            $levels = $this->chain_levels(0, null, null, false);
        } else {
            $levels = $this->chain_levels(0, null, null, true);
        }
        $options = ['' => '&nbsp;'];
        foreach ($levels as $level){
            $options[$level->{$level::DB_TABLE_PK}] = $level->level_name;
        }
        return $options;
    }

    public function approval_chain_level_options(){

        $this->load->model('approval_chain_level');
        $approval_chain_levels = $this->approval_chain_level->get(0,0,[
            'approval_module_id' => $this->{$this::DB_TABLE_PK},
            'status' => 'active',
            'special_level' => 0
        ],'level ASC');
       //$options[''] = '&nbsp;';
        $options ='<option value="0">At the beginning</option>';

        $count = count($approval_chain_levels);
        $i = 0;
        foreach ($approval_chain_levels as $approval_chain_level){
            $i++;
         $options .='<option '.($i == $count ? 'selected' : '').' value="'.$approval_chain_level->level.'">After'.' '.$approval_chain_level->level_name.'</option>';
       }
        return $options;
    }

    public function previous_approval_chain_level_options(){

        $this->load->model('approval_chain_level');
        $approval_chain_levels = $this->approval_chain_level->get(0,0,[
            'approval_module_id' => $this->{$this::DB_TABLE_PK},
            'status' => 'active'
        ],'level ASC');
    
        $options ='<option  value="">';
     
        foreach ($approval_chain_levels as $approval_chain_level){
          
         $options .='<option  value="'.$approval_chain_level->id.'">'.$approval_chain_level->level_position_name()->position_name.'</option>';
       }
        return $options;
    }

    public function approval_module_options(){

        $approval_modules = $this->get();
        $options[''] = '&nbsp;';
        foreach ($approval_modules as $approval_module){
            $options[$approval_module->{$this::DB_TABLE_PK}] = $approval_module->module_name;
        }
        return $options;
    }

    public function employee_power($approval_module_id){
        $employee_id = $this->session->userdata('employee_id');
        $sql = 'SELECT level FROM approval_chain_levels
                WHERE approval_module_id ='.$approval_module_id.'
                AND status = "active"
                AND special_level = 0
                ORDER BY level ASC LIMIT 1';

        $query_one = $this->db->query($sql);
        $first_level = $query_one->row()->level;

        $sql = 'SELECT level FROM employee_approval_chain_levels 
                LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                WHERE approval_module_id = '.$approval_module_id.'
                AND employee_id ='.$employee_id.'
                AND status = "active"
                AND special_level = 0
                LIMIT 1';

        $query_two = $this->db->query($sql);
        if($query_two->num_rows() > 0) {
            $this_employees_level = $query_two->row()->level;
            return $this_employees_level > $first_level ? true : false;
        } else {
            return false;
        }

    }

}

