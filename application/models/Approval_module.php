<?php

class Approval_module extends MY_Model
{

    const DB_TABLE = 'approval_modules';
    const DB_TABLE_PK = 'id';

    public $module_name;

    public function approval_modules()
    {

        $approval_modules = $this->get();
        return $approval_modules;
    }

    public function greatest_chain_level($id = null)
    {
        $approval_module_id = !is_null($id) ? $id : $this->{$this::DB_TABLE_PK};
        $this->load->model('approval_chain_level');
        $where = [
            'approval_module_id' => $approval_module_id,
            'special_level' => 0
        ];
        $levels = $this->approval_chain_level->get(1, 0, $where, ' level DESC');
        return array_shift($levels);
    }

    public function chain_levels($after = 0, $approval_module_id = null, $status = null, $special_access = 'all')
    {

        $approval_module_id = !is_null($approval_module_id) ? $approval_module_id : $this->{$this::DB_TABLE_PK};

        if ($special_access && strtolower($special_access) == 'all') {
            $where = [
                'approval_module_id' => $approval_module_id,
                ' level > ' => $after
            ];
        } else if ($special_access) {
            $where = [
                ' approval_module_id' => $approval_module_id,
                ' status' => "active",
                ' level > ' => $after,
                ' special_level' => 1
            ];
        } else {
            $where = [
                'approval_module_id' => $approval_module_id,
                ' level > ' => $after,
                'special_level' => 0
            ];
        }

        if (!is_null($status)) {
            $where['status'] = $status;
        }
        $this->load->model('approval_chain_level');
        $levels = $this->approval_chain_level->get(0, 0, $where, ' level ASC');
        return $levels;
    }

    public function has_special_level()
    {
        $levels = $this->chain_levels(0, $this->id, null, true);
        return !empty($levels) ? true : false;
    }

    public function chain_level_dropdown_options($special_access = false)
    {
        $levels = $this->chain_levels(0, null, null, $special_access);
        $options = ['' => '&nbsp;'];
        foreach ($levels as $level) {
            $options[$level->{$level::DB_TABLE_PK}] = $level->level_name;
        }
        return $options;
    }

    public function to_forward_level_options()
    {
        $levels = $this->chain_levels(0, null, null, true);
        $options = ['' => '&nbsp;'];
        foreach ($levels as $level) {
            $options[$level->{$level::DB_TABLE_PK}] = $level->level_name;
        }
        return $options;
    }

    public function forwarding_to_employee_options()
    {
        $chain_levels = $this->chain_levels(0, null, null, true);
        $options = ['' => '&nbsp;'];
        foreach ($chain_levels as $chain_level) {
            $employee_options = $chain_level->employee_options();
            $no = 0;
            foreach ($employee_options as $index => $employee_option) {
                $no++;
                if ($no > 1) $options[$index] = $employee_option . ' - ' . strtoupper($chain_level->level_name);
            }
        }
        return $options;
    }

    public function approval_chain_level_options()
    {

        $this->load->model('approval_chain_level');
        $approval_chain_levels = $this->approval_chain_level->get(0, 0, [
            'approval_module_id' => $this->{$this::DB_TABLE_PK},
            'status' => 'active',
            'special_level' => 0
        ], 'level ASC');
        //$options[''] = '&nbsp;';
        $options = '<option value="0">At the beginning</option>';

        $count = count($approval_chain_levels);
        $i = 0;
        foreach ($approval_chain_levels as $approval_chain_level) {
            $i++;
            $options .= '<option ' . ($i == $count ? 'selected' : '') . ' value="' . $approval_chain_level->level . '">After' . ' ' . $approval_chain_level->level_name . '</option>';
        }
        return $options;
    }

    public function approval_chain_level_to_approve_options()
    {
        $this->load->model('approval_chain_level');
        $approval_chain_levels = $this->approval_chain_level->get(0, 0, [
            'approval_module_id' => $this->{$this::DB_TABLE_PK},
            'status' => 'active'
        ], 'level ASC');
        $options = '<option value="">&nbsp;</option>';
        foreach ($approval_chain_levels as $approval_chain_level) {
            $options .= '<option value="' . $approval_chain_level->level . '">Waiting for' . ' ' . $approval_chain_level->level_name . '</option>';
        }
        return $options;
    }

    public function previous_approval_chain_level_options()
    {

        $this->load->model('approval_chain_level');
        $approval_chain_levels = $this->approval_chain_level->get(0, 0, [
            'approval_module_id' => $this->{$this::DB_TABLE_PK},
            'status' => 'active'
        ], 'level ASC');

        $options = '<option  value="">';

        foreach ($approval_chain_levels as $approval_chain_level) {

            $options .= '<option  value="' . $approval_chain_level->id . '">' . $approval_chain_level->level_position_name()->position_name . '</option>';
        }
        return $options;
    }

    public function approval_module_options($limit = null)
    {
        $limit = !is_null($limit) ? $limit : '';
        $approval_modules = $this->get($limit);
        $options[''] = 'All';
        foreach ($approval_modules as $approval_module) {
            $options[$approval_module->{$this::DB_TABLE_PK}] = $approval_module->module_name;
        }
        return $options;
    }

    public function employee_power($approval_module_id, $module, $module_id)
    {
        $employee_id = $this->session->userdata('employee_id');
        $module_model = $module;
        $class = ucfirst($module);
        $this->load->model([$module_model, 'approval_module']);
        $item_request = new $class();
        $item_request->load($module_id);
        $level = $item_request->current_approval_level() ? $item_request->current_approval_level()->level : 100;
        $current_approval_level = $level;

        $sql = 'SELECT level FROM employee_approval_chain_levels 
                LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                WHERE approval_module_id = ' . $approval_module_id . '
                AND employee_id =' . $employee_id . '
                AND status = "active"
                AND special_level = 0
                ORDER BY level DESC
                LIMIT 1';

        $query_two = $this->db->query($sql);
        if ($query_two->num_rows() > 0) {
            $this_employees_level = $query_two->row()->level;
            return $this_employees_level > $current_approval_level ? true : false;
        } else {
            return false;
        }
    }
}
