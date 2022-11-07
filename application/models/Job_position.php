<?php

class Job_position extends MY_Model{
    
    const DB_TABLE = 'job_positions';
    const DB_TABLE_PK = 'job_position_id';

    public $position_name;
    public $description;

    public function job_positions_list(){
        $limit = $this->input->post('length');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'position_name';
                break;
            case 1;
                $order_column = 'description';
                break;
            case 2;
                $order_column = 'number_of_employees';
                break;
            default:
                $order_column = 'department_name';
        }

        $order_string = $order_column.' '.$order_dir;

        $sql = 'SELECT job_positions.*,
                (
                  SELECT COALESCE (COUNT(employee_id),0)
                  FROM employees
                  WHERE employees.position_id = job_positions.job_position_id
                ) AS number_of_employees
                FROM job_positions
            ';

        if($keyword != ''){
            $sql .= ' WHERE position_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $query = $this->db->query($sql);
        $records_total = $this->db->count_all('job_positions');

        $results = $query->result();
        $rows = [];

        $this->load->model('job_position');
        foreach($results as $row){
            $position = new Job_position();
            $position->load($row->job_position_id);
            $data['position'] = $position;
            $data['number_of_employees'] = $row->number_of_employees;
            $rows[] = [
                $row->position_name,
                $row->description,
                $row->number_of_employees,
                $this->load->view('human_resources/settings/job_positions_list_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    function job_position_options(){
        $options[''] = '&nbsp;';
        $job_positions = $this->get(0,0,'');
        foreach($job_positions as $job_position){
            $options[$job_position->{$job_position::DB_TABLE_PK}] = $job_position->position_name;
        }
        return $options;
    }

    public function average_salary($position_id = null){
        $position_id = is_null($position_id) ? $this->{$this::DB_TABLE_PK} : $position_id;
        $sql = 'SELECT AVG(salary) AS average_salary FROM employees_contracts
              LEFT JOIN employees ON employees_contracts.employee_id = employees.employee_id
               WHERE start_date <= CURDATE() AND end_date >= CURDATE()
               AND employees.position_id = "'.$position_id.'"
              ';
        $query = $this->db->query($sql);
        return $query->row()->average_salary;
    }

    public function budget_job_position_options($cost_center_level,$cost_center_id,$rate_mode){
        $sql = 'SELECT position_name,job_position_id
                FROM job_positions
                WHERE job_position_id NOT IN (
                  SELECT job_position_id FROM permanent_labour_budgets
                  WHERE  rate_mode = "' .$rate_mode.'"';
        if($cost_center_level == 'project'){
            $sql .= ' AND (project_id = "'.$cost_center_id.'" AND task_id IS NULL)';
        } else {
            $sql .= ' AND task_id = "'.$cost_center_id.'"';
        }
        $sql .= '
                )
       ';
        $query = $this->db->query($sql);
        $tool_types = $query->result();

        $options = '<option value="">&nbsp;</option>';
        foreach($tool_types as $type){
            $options .= '<option value="'.$type->job_position_id.'">'.$type->position_name.'</option>';
        }

        return $options;
    }


}

