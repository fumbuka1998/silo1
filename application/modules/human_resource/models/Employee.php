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

    public function contract_employees_list(){
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

      
        $contract_types=contract_employees('all','today',$limit,$start,$where,$order);

        $employees=$contract_types['contracts'];

        $rows = [];
        foreach($employees as $employee){

            $contract=$employee->latest_employee_contract();

            $latest_designation=$employee->latest_employee_contract()->latest_contract_designation();

            $rows[] = [
                anchor(base_url('human_resource/Human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}),$employee->full_name()),
                $employee->phone,
                custom_standard_date($contract->start_date),
                custom_standard_date($contract->end_date),
                $latest_designation->employee_department()->department_name,
                $latest_designation->employee_job_position()->position_name,
                $latest_designation->employee_branch()->branch_name,
                $employee->address
                
            ];
        }
        
         $records_filtered = count($employees);
         $records_total = count($employees);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function non_contract_employee_list(){

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

       // $issue_date='2017-08-01';
        $contract_types=contract_employees('all','today',$limit,$start,$where,$order);
        //inspect_object($contract_types['closed_contracts']);exit;
        $employees=$contract_types['non_contracts'];

        //$employees = $this->get($limit,$start,$where,$order);
        $rows = [];
        foreach($employees as $employee){
            $rows[] = [
                anchor(base_url('human_resource/Human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}),$employee->full_name()),
                $employee->phone,
                $employee->alternative_phone,
                $employee->email,
                $employee->address
            ];
        }
        //$records_filtered = $this->employee->count_rows($where);
        //$records_total = $this->employee->count_rows();
         $records_filtered = count($employees);
         $records_total = count($employees);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function incomplete_contract_employee_list(){
        
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

       // $issue_date='2017-08-01';
        $contract_types=contract_employees('all','today',$limit,$start,$where,$order);
        //inspect_object($contract_types['closed_contracts']);exit;
        $employees=$contract_types['incomplete_contracts'];

        //$employees = $this->get($limit,$start,$where,$order);
        $rows = [];
        foreach($employees as $employee){
            $rows[] = [
                anchor(base_url('human_resource/Human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}),$employee->full_name()),
                $employee->phone,
                $employee->alternative_phone,
                $employee->email,
                $employee->address
            ];
        }
        //$records_filtered = $this->employee->count_rows($where);
        //$records_total = $this->employee->count_rows();
         $records_filtered = count($employees);
         $records_total = count($employees);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function expired_contract_employee_list(){
        
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

       // $issue_date='2017-08-01';
        $contract_types=contract_employees('all','today',$limit,$start,$where,$order);
        //inspect_object($contract_types['closed_contracts']);exit;
        $employees=$contract_types['expired_contracts'];

        //$employees = $this->get($limit,$start,$where,$order);
        $rows = [];
        foreach($employees as $employee){
            $rows[] = [
                anchor(base_url('human_resource/Human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}),$employee->full_name()),
                $employee->phone,
                $employee->alternative_phone,
                $employee->email,
                $employee->address
            ];
        }
        //$records_filtered = $this->employee->count_rows($where);
        //$records_total = $this->employee->count_rows();
         $records_filtered = count($employees);
         $records_total = count($employees);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function closed_contract_employee_list(){
        
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

       // $issue_date='2017-08-01';
        $contract_types=contract_employees('all','today',$limit,$start,$where,$order);
        //inspect_object($contract_types['closed_contracts']);exit;
        $employees=$contract_types['closed_contracts'];

        //$employees = $this->get($limit,$start,$where,$order);
        $rows = [];
        foreach($employees as $employee){
            $rows[] = [
                anchor(base_url('human_resource/Human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}),$employee->full_name()),
                $employee->phone,
                $employee->alternative_phone,
                $employee->email,
                $employee->address
            ];
        }
        //$records_filtered = $this->employee->count_rows($where);
        //$records_total = $this->employee->count_rows();
         $records_filtered = count($employees);
         $records_total = count($employees);

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
        $this->load->model('department');
        $options = '<option value="">&nbsp;</option>';
        $departments = $this->department->get();
        foreach($departments as $department){
            $sql = 'SELECT employee_id,CONCAT(first_name," ",middle_name," ",last_name) AS full_name FROM employees
                    WHERE employee_id NOT IN (
                        SELECT employees.employee_id FROM employees
                        LEFT JOIN project_team_members ON employees.employee_id = project_team_members.employee_id
                        WHERE project_team_members.project_id = "' . $project_id . '"
                    ) AND department_id = "'.$department->{$department::DB_TABLE_PK}.'"';
            $query = $this->db->query($sql);
            if($query->num_rows() > 0){
                $options .= '<optgroup label="'.$department->department_name.'">';
                $employees = $query->result();
                foreach($employees as $employee){
                    $options .= '<option value="'.$employee->employee_id.'">'.$employee->full_name.'</option>';
                }
                $options .= '</optgroup>';
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

    public function latest_employee_contract(){
        $this->load->model('Employee_contract');
        $Employee_contract= new Employee_contract();
        $Employee_contract=$Employee_contract->get(1,0,['employee_id'=>$this->{$this::DB_TABLE_PK}],'id DESC');
        return array_shift($Employee_contract);
    }

    public function approval_chain_levels()
    {
        $this->load->model('employee_approval_chain_level');
        $junctions = $this->employee_approval_chain_level->get(0,0,['employee_id' => $this->{$this::DB_TABLE_PK}]);
        $levels = [];
        foreach ($junctions as $junction){
            $levels[] = $junction->approval_chain_level();
        }
        return $levels;
    }

    public function clear_approval_chain_levels()
    {
        $this->db->where('employee_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete(['employee_approval_chain_levels']);
    }

    public function employee_bank()
    {
        $employee_id = $this->{$this::DB_TABLE_PK};
        $sql = 'SELECT * FROM employee_banks
                LEFT JOIN banks ON employee_banks.bank_id = banks.id WHERE employee_id = '.$employee_id;
        $query = $this->db->query($sql);
        return $query->result() ? $query->result() : false ;
    }





}

