<?php

class Employee extends MY_Model{
    
    const DB_TABLE = 'employees';
    const DB_TABLE_PK = 'employee_id';

    public $first_name;
    public $middle_name;
    public $last_name;
    public $gender;
    public $date_of_birth;
    public $phone;
    public $alternative_phone;
    public $email;
    public $address;
    public $department_id;
    public $position_id;
    public $active;

    public function employees_list(){
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');
        $limit = $this->input->post('length');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'first_name';
                break;
            case 1;
                $order_column = 'phone';
                break;
            case 2;
                $order_column = 'alternative_phone';
                break;
            case 3;
                $order_column = 'email';
                break;
            case 4;
                $order_column = 'address';
                break;
            default:
                $order_column = 'first_name';
        }

        $order = $order_column.' '.$order_dir;

        $where = '';
        if($keyword != ''){
            $where .= 'first_name LIKE "%'.$keyword.'%" OR phone LIKE "%'.$keyword.'%" OR alternative_phone LIKE "%'.$keyword.'%"  OR email LIKE "%'.$keyword.'%"  OR address LIKE "%'.$keyword.'%" ';
        }

        $employees = $this->get($limit,$start,$where,$order);
        $rows = [];
        foreach($employees as $employee){
            $rows[] = [
                anchor(base_url('human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}),$employee->full_name()),
                $employee->phone,
                $employee->alternative_phone,
                $employee->email,
                $employee->address
            ];
        }
        $records_filtered = $this->employee->count_rows($where);
        $records_total = $this->employee->count_rows();
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function avatar_path(){
        $this->load->model('employee_avatar');
        $directory = 'images/employees_avatars/';
        $path = $directory.($this->gender == 'MALE' ? 'default-male.png' : 'default-female.png');
        $where['employee_id'] = $this->{$this::DB_TABLE_PK};
        $avatars = $this->employee_avatar->get(1,0,$where,' datetime_uploaded DESC');
        $avatar = array_shift($avatars);
        if(!empty($avatar)){
            $avatar_path = $directory.$avatar->avatar_name;
            $path = file_exists($avatar_path) ? $avatar_path : $path;
        }
        return base_url($path);
    }

    public function department()
    {
        $this->load->model('department');
        $department = new department();
        $department->load($this->department_id);
        return $department;
    }

    public function full_name(){
        return $this->first_name .' '.( $this->middle_name != '' ? $this->middle_name.' ' : '').$this->last_name;
    }

    public function user(){
        $this->load->model('user');
        $users = $this->user->get(1,0,['employee_id' => $this->{$this::DB_TABLE_PK}],'user_id DESC');
        return array_shift($users);

    }

    public function position()
    {
        $this->load->model('job_position');
        $job_position = new Job_position();
        $job_position->load($this->position_id);
        return $job_position;
    }

    public function project_team_member_employees_options($project_id){
        $options = '<option value="">&nbsp;</option>';

        $sql = 'SELECT employee_id,CONCAT(first_name," ",middle_name," ",last_name) AS full_name FROM employees
                WHERE employee_id NOT IN (
                    SELECT employees.employee_id FROM employees
                    LEFT JOIN project_team_members ON employees.employee_id = project_team_members.employee_id
                    WHERE project_team_members.project_id = "' . $project_id . '"
                ) ';
        $query = $this->db->query($sql);
        if($query->num_rows() > 0){
            $employees = $query->result();
            foreach($employees as $employee){
                $options .= '<option value="'.$employee->employee_id.'">'.$employee->full_name.'</option>';
            }
        }

        return $options;
    }

    public function salary($rate_mode = null){
        $sql = 'SELECT COALESCE(salary,0) AS salary FROM employees_contracts
              WHERE start_date <= CURDATE() AND end_date >= CURDATE()
              AND employee_id = "'.$this->{$this::DB_TABLE_PK}.'"
              LIMIT 1
              ';
        $query = $this->db->query($sql);
        $salary = $query->num_rows() == 1 ? $query->row()->salary : 0;
        if($rate_mode == 'hourly'){
            $salary = $salary/180;
        } else if($rate_mode == 'daily'){
            $salary = $salary*8/180;
        }
        return $salary;
    }

    public function has_project(){
        $sql = 'SELECT project_id FROM project_team_members WHERE employee_id = "'.$this->{$this::DB_TABLE_PK}.'" LIMIT 1';
        $query = $this->db->query($sql);
        return $query->num_rows() > 0;
    }

    public function email_options()
    {
        $emails = $this->get();

        $options = array();
        foreach ($emails as $email) {
            if(!empty($email->email) && $email->email != '' && $email->email != ' ' && $email->email != NULL){
                $full_name = $email->first_name .' '.( $email->middle_name != '' ? $email->middle_name.' ' : '').$email->last_name;
                $options[$email->email] = $full_name.' < '.$email->email.' >';
            }
        }
        return $options;
    }

}

