<?php

class Project_team_member extends MY_Model{
    
    const DB_TABLE = 'project_team_members';
    const DB_TABLE_PK = 'member_id';

    public $employee_id;
    public $project_id;
    public $job_position_id;
    public $manager_access;
    public $date_assigned;
    public $assignor_id;
    public $remarks;
    
    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function job_position()
    {
        $this->load->model('job_position');
        $job_position = new Job_position();
        $job_position->load($this->job_position_id);
        return $job_position;
    }

    public function team_members_list($project_id,$limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['employee_name','position_name','manager_access','assignor_name','date_assigned','remarks'],$order,'date_assigned');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        //Records Total
        $records_total = $this->count_rows(['project_id' => $project_id]);

        //Where clause
        $where_clause = 'project_team_members.project_id = "'.$project_id.'"';
        if($keyword != ''){
            $where_clause .= ' AND (members.first_name LIKE "%'.$keyword.'%" OR members.middle_name LIKE "%'.$keyword.'%" OR members.last_name LIKE "%'.$keyword.'%"  OR remarks LIKE "%'.$keyword.'%"  OR date_assigned LIKE "%'.$keyword.'%")';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS project_team_members.*,position_name, CONCAT(members.first_name," ",COALESCE(members.middle_name,"")," ",members.last_name) AS employee_name,
                CONCAT(assignors.first_name," ",COALESCE(assignors.middle_name,"")," ",assignors.last_name) as assignor_name
                FROM project_team_members
                LEFT JOIN job_positions ON project_team_members.job_position_id = job_positions.job_position_id
                LEFT JOIN employees AS members ON project_team_members.employee_id = members.employee_id
                LEFT JOIN employees AS assignors ON project_team_members.assignor_id = assignors.employee_id
                WHERE '.$where_clause.$order_string;
        

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;



        $this->load->model(['project','job_position']);
        $project = new Project();
        $project->load($project_id);
        $data['project'] = $project;
        $data['job_position_options'] = $this->job_position->job_position_options();
        
        $rows = [];
        foreach($results as $row){
            $member = new Project_team_member();
            $member->load($row->member_id);
            $data['member'] = $member;
            $employee_name = check_permission('Human Resources') ?  anchor(base_url('human_resources/employee_profile/'.$row->employee_id),$row->employee_name) : $row->employee_name;
            $assignor_name = check_permission('Human Resources') ?  anchor(base_url('human_resources/employee_profile/'.$row->assignor_id),$row->assignor_name) : $row->assignor_name;
            $rows[] = [
                $employee_name,
                $row->position_name,
                $row->manager_access == '1' ? 'MANAGER' : '',
                $assignor_name,
                custom_standard_date($row->date_assigned),
                $row->remarks,
                $this->load->view('projects/project_details/project_team_members_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function team_member_options($project_id){
        $sql = 'SELECT member_id, position_name, CONCAT(first_name," ",COALESCE(middle_name,"")," ",last_name) AS employee_name
                FROM project_team_members
                LEFT JOIN job_positions ON project_team_members.job_position_id = job_positions.job_position_id
                LEFT JOIN employees ON project_team_members.employee_id = employees.employee_id
                WHERE project_id = "'.$project_id.'"';
        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = '&nbsp;';
        foreach ($results as $row){
            $options[$row->position_name][$row->member_id] = $row->employee_name;
        }
        return $options;
    }

}

