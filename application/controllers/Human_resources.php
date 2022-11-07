<?php

class Human_resources extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->model('employee');
     }

    public function index(){
        check_permission('Human Resources',true);
        $this->load->model('department');
        $data['number_of_employees'] = $this->employee->count_rows();
        $data['number_of_departments'] = $this->department->count_rows();
        $data['title'] = 'Human Resources';
        $this->load->view('human_resources/index',$data);
    }

    public function save_employee($id = 0){
        $employee = new Employee();
        $edit = $employee->load($id);
        $employee->date_of_birth = $this->input->post('date_of_birth') != '' ? $this->input->post('date_of_birth') : null;
        $employee->first_name = $this->input->post('first_name');
        $employee->middle_name = $this->input->post('middle_name');
        $employee->last_name = $this->input->post('last_name');
        $employee->gender = $this->input->post('gender');
        $employee->email = $this->input->post('email');
        $employee->phone = $this->input->post('phone');
        $employee->alternative_phone = $this->input->post('alternative_phone');
        $employee->address = $this->input->post('address');
        $employee->department_id = $this->input->post('department_id');
        $employee->position_id = $this->input->post('position_id');
        $employee->position_id =! '' ? $employee->position_id : null;
        $employee->active = !$edit ? 1 : $employee->active;
        if($employee->save()){

            if(!empty($_FILES['avatar'])){

                //Upload Photo

                $config = [
                    'upload_path' => "./images/employees_avatars",
                    'allowed_types' => 'gif|jpg|png|jpeg'
                ];

                $this->load->library('upload',$config);

                if($this->upload->do_upload('avatar')){
                    $this->load->model('employee_avatar');
                    $employee_avatar = new Employee_avatar();
                    $employee_avatar->avatar_name = $this->upload->data()['file_name'];
                    $employee_avatar->datetime_uploaded = datetime();
                    $employee_avatar->employee_id = $employee->{$employee::DB_TABLE_PK};
                    $employee_avatar->save();
                }
            }

            $description = $employee->full_name().' was ';
            if(!$edit){
                $action = 'Employee Registration';
                $description .= 'registered';
            } else {
                $action = 'Employee Update';
                $description .= 'edited';
            }
            system_log($action,$description);
            redirect(base_url('human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}));
        } else {
            redirect(base_url());
        }
    }

    public function employees_list(){
        $limit = $this->input->post('length');
        if($limit != '') {
            $this->load->model('employee');
            echo $this->employee->employees_list();
        } else {
            check_permission('Human Resources', true);
            $data['employee'] = new Employee();
            $this->load->model(['department','job_position']);
            $data['job_position_options'] = $this->job_position->job_position_options();
            $data['department_options'] = $this->department->department_options();
            $data['title'] = 'Employees List';
            $this->load->view('human_resources/employees/employees_list', $data);
        }
    }

    public function employee_profile($id = 0){
        if(check_permission('Human Resources') || $this->session->userdata('employee_id') == $id) {
            $employee = new Employee();
            if ($employee->load($id)) {
                $this->load->model(['department','job_position']);
                $data['job_position_options'] = $this->job_position->job_position_options();
                $data['department_options'] = $this->department->department_options();
                $data['employee'] = $employee;
                $data['title'] = $employee->full_name();
                $data['permissions'] = $this->permissions();
                $this->load->view('human_resources/employees/profile', $data);
            } else {
                redirect(base_url());
            }
        } else {
            redirect(base_url());
        }

    }

    public function save_employee_contract(){
        $this->load->model('employee_contract');
        $contract = new Employee_contract();
        $edit = $contract->load($this->input->post('contract_id'));
        $contract->start_date = $this->input->post('start_date');
        $contract->end_date = $this->input->post('end_date');
        $contract->description = $this->input->post('description');
        $contract->employee_id = $this->input->post('employee_id');
        $contract->date_registered = $edit ? $contract->date_registered : date('Y-m-d');
        $contract->salary = $this->input->post('salary');
        $contract->registrar_id = $this->session->userdata('employee_id');
        if($contract->save()){
            $description = 'Contract with ID number '.$contract->{$contract::DB_TABLE_PK}.' was ';
            $description .= $edit ? 'updated' : 'registered';
            $action = $edit ? 'Employee Contract Update' : 'Employee Contract Registration';
            system_log($action,$description);
        }
    }

    public function employee_contracts($employee_id = 0){
        if(check_permission('Human Resources') || $this->session->userdata('employee_id') == $employee_id) {
            $this->load->model('employee_contract');
            echo $this->employee_contract->employee_contracts_list($employee_id);
        }
    }

    private function permissions(){
        $this->load->model('permission');
        return $this->permission->get();
    }

    public function save_user(){
        if(check_permission('Human Resources')  || $this->session->userdata('employee_id') == $this->input->post('employee_id')) {
            $this->load->model(['user','employee_approval_chain_level']);
            $user = new User();
            $edit = $user->load($this->input->post('user_id'));
            $user->username = $this->input->post('username');
            $user->employee_id = $this->input->post('employee_id');
            $password = trim($this->input->post('password'));
            if ($password != '') {
                $user->password = sha1(md5($password));
            }

            $user->active = $this->input->post('active');
            $user->save();
            if (check_permission('Human Resources')) {

                $action = $edit ? 'User Update' : 'User Creation';
                $description = $user->username . ' was ' . ($edit ? 'updated' : 'created');
                system_log($action, $description);

                $this->load->model('user_permission');
                $user->delete_permissions();
                $permission_ids = $this->input->post('permission_ids');
                $permission_ids = $permission_ids != '' ? $permission_ids : [];
                if (is_array($permission_ids)) {
                    foreach ($permission_ids as $permission_id) {
                        $user_permission = new User_permission();
                        $user_permission->permission_id = $permission_id;
                        $user_permission->user_id = $user->{$user::DB_TABLE_PK};
                        $user_permission->save();
                    }
                }

                $chain_level_ids = $this->input->post('approval_chain_level_ids');
                foreach ($chain_level_ids as $chain_level_id){
                    $junction = new Employee_approval_chain_level();
                    $junction->employee_id =$user->employee_id;
                    $junction->created_by = $this->input->post('employee_id');
                    $junction->approval_chain_level_id = $chain_level_id;
                    $junction->save();
                }
            }
            $data['employee'] = $user->employee();
            $data['permissions'] = $this->permissions();
            $response['username_and_password_form'] = $this->load->view('human_resources/employees/username_and_password_form', $data,true);
            $response['user_permissions'] = $this->load->view('human_resources/employees/user_account_tab', $data,true);
            $response['authorised_approvals'] = $this->load->view('human_resources/employees/authorised_approvals_tab', $data,true);
            echo json_encode($response);
        }
    }

    public function departments(){
        $limit = $this->input->post('length');
        if($limit != '') {
            $this->load->model('department');
            echo $this->department->departments_list();
        } else {
            check_permission('Human Resources', true);
            $data['title'] = 'Departments';
            $this->load->view('human_resources/departments/index', $data);
        }
    }

    public function save_department(){
        $this->load->model('department');
        $department = new Department();
        $edit = $department->load($this->input->post('department_id'));
        $department->department_name = $this->input->post('department_name');
        $department->description = $this->input->post('description');
        if($department->save()){
            $action = $edit ? 'Department Update' : 'Department Registration';
            $description = 'Department '.$department->department_name.' was '.($edit ? 'updated' : 'registered');
            system_log($action,$description);
        }
    }

    public function delete_department(){
        $this->load->model('department');
        $department = new Department();
        if($department->load($this->input->post('department_id'))){
            $description = 'Department '.$department->department_name.' was deleted';
            $department->delete();
            system_log('Department Delete',$description);
        }
    }

    public function settings(){
        check_permission('Human Resources', true);
        $data['title'] = 'Human Resources Settings';
        $this->load->view('human_resources/settings/index',$data);
    }

    public function save_job_position(){
        $this->load->model('job_position');
        $job_position = new Job_position();
        $edit = $job_position->load($this->input->post('position_id'));
        $job_position->position_name = $this->input->post('position_name');
        $job_position->description = $this->input->post('description');
        if($job_position->save()){
            $action = $edit ? 'Job Position Update' : 'Job Position Registration';
            $description = 'Job Position '.$job_position->postion_name.' was '.($edit ? 'updated' : 'registered');
            system_log($action,$description);
        }
    }

    public function job_positions(){
        $this->load->model('job_position');
        echo $this->job_position->job_positions_list();
    }

    public function delete_job_position(){
        $this->load->model('job_position');
        $job_position = new Job_position();
        if($job_position->load($this->input->post('position_id'))){
            $description = 'Job Position '.$job_position->position_name.' was deleted';
            $job_position->delete();
            system_log('Job Position Delete',$description);
        }
    }

    public function casual_labour_types_list(){
        $this->load->model('casual_labour_type');
        $limit = $this->input->post('length');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');
        $order = $this->input->post('order')[0];
        echo $this->casual_labour_type->casual_labour_types_list($limit, $start, $keyword, $order);
    }

    public function save_casual_labour_type(){
        $this->load->model('casual_labour_type');
        $type = new Casual_labour_type();
        $edit = $type->load($this->input->post('type_id'));
        $type->name = $this->input->post('name');
        $type->description = $this->input->post('description');
        if($type->save()){
            //Callback
        }
    }

    public function delete_casual_labour_type(){
        $this->load->model('casual_labour_type');
        $type = new Casual_labour_type();
        if($type->load($this->input->post('type_id'))){
            $type->delete();
        }
    }

    public function load_team_member_salary_rate(){
        $rate_mode = $this->input->post('working_mode') == 'hours' ? 'hourly' : 'daily';
        $this->load->model('project_team_member');
        $team_member = new Project_team_member();
        $team_member->load($this->input->post('member_id'));
        echo $team_member->employee()->salary($rate_mode);
    }

    public function job_position_average_salary(){
        $this->load->model('job_position');
        echo $this->job_position->average_salary($this->input->post('job_position_id'));
    }

    public function download_attendance_excel($from,$to)
    {
        $this->load->library('excel');
        $object = new PHPExcel();

        $filename = 'Attendance sheet_'.$from.'_to_'.$to;
        $object->setActiveSheetIndex(0);
        $this->excel->getProperties()->setCreator($this->session->userdata('employee_name'));
        $this->excel->getProperties()->setTitle($filename);

        $active_sheet = $object->getActiveSheet();
        $active_sheet->setTitle($filename);
        $active_sheet->setPrintGridlines(TRUE);

        $this->load->model(['attendance', 'employee']);
        $where = [
            'date >= "'.$from.'" ',
            'date <= "'.$to.'" '
        ];
        $attendances = $this->activity->get(0, 0, $where);
        $style['column_heading'] = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '2f2f2f'],
            ],

            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'accbe1'],
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ]
        ];


        for ($col_index = 'A'; $col_index !== 'J'; $col_index++) {
            $active_sheet->getColumnDimension($col_index)->setAutoSize(true);
        }

        $active_sheet->setCellValue('A1', 'Date');
        $active_sheet->setCellValue('B1', 'Time');
        $active_sheet->setCellValue('C1', 'Employee');
        $active_sheet->setCellValue('D1', 'Type');
        $active_sheet->setCellValue('E1', 'Crated At');

        $active_sheet->getStyle('A1:E1')->applyFromArray($style['column_heading']);

        if (!empty($activities)) {
            $index = 2;
            foreach($attendances as $attendance){
                $active_sheet->setCellValue('A' . $index, $attendance->date);
                $active_sheet->setCellValue('B' . $index, $attendance->time);
                $active_sheet->setCellValue('C' . $index, $attendance->employee()->full_name());
                $active_sheet->setCellValue('D' . $index, ucfirst($attendance->type));
                $active_sheet->setCellValue('E' . $index, $attendance->created_at);
                $index++;
            }
        }

        $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        ob_end_clean();
        $objWriter->save('php://output');
    }

}

