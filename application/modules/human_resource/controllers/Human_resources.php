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
        $this->load->view('index',$data);
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
           // redirect(base_url('human_resource/human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}));
            redirect(base_url('human_resource/Human_resources/employee_profile/'.$employee->{$employee::DB_TABLE_PK}));
        } else {
            redirect(base_url());
        }
    }

    public function employees_lists(){

        $limit = $this->input->post('length');
        if($limit != '') {
            $this->load->model('Human_resource/Employee');
            echo $this->employee->employees_list();
        } else {

            check_permission('Human Resources', true);
            $data['employee'] = new Employee();
            $this->load->model(['department','job_position']);
            $data['job_position_options'] = $this->job_position->job_position_options();
            $data['department_options'] = $this->department->department_options();
            $data['title'] = 'Employees ';
            $this->load->view('employees/employees_lists', $data);
        }
    }

    public function contract_employee_list(){
        $limit = $this->input->post('length');
        $this->load->model('Human_resource/Employee');
        echo $this->employee->contract_employees_list();
    }

    public function incomplete_contract_employee_list(){
        $limit = $this->input->post('length');
        $this->load->model('Human_resource/Employee');
        echo $this->employee->incomplete_contract_employee_list();
    }

    public function non_contract_employee_list(){

            $limit = $this->input->post('length');
            $this->load->model('Human_resource/Employee');
            echo $this->employee->non_contract_employee_list();

    }

    public function closed_contract_employee_list(){

            $limit = $this->input->post('length');
            $this->load->model('Human_resource/Employee');
            echo $this->employee->closed_contract_employee_list();

    }

    public function expired_contract_employee_list(){

            $limit = $this->input->post('length');
            $this->load->model('Human_resource/Employee');
            echo $this->employee->expired_contract_employee_list();

    }

     public function employee_profile($id = 0){
        if(check_permission('Human Resources') || $this->session->userdata('employee_id') == $id) {
            $employee = new Employee();
            if ($employee->load($id)) {
                $this->load->model([
                    'Department',
                    'job_position',
                    'Ssf',
                    'Bank',
                    'Branch',
                    'allowance',
                    'loan',
                    'account_group',
                    'bank',
                    'account',
                    'employee_confidentiality_level'
                ]);

                $data['account_group_options'] = $this->account_group->account_group_options();
                $data['currency_options'] = currency_dropdown_options();
                $data['bank_options'] = $this->bank->bank_dropdown_options();
                $data['title'] = 'Accounts List';

                $data['job_position_options'] = $this->job_position->job_position_options();
                $data['department_options'] = $this->Department->department_options();
                $data['ssf_options'] = $this->Ssf->ssf_options();
                $data['allowance_options'] = $this->allowance->allowance_dropdown_options();
                $data['bank_options'] = $this->Bank->bank_dropdown_options();
                $data['branch_options'] = $this->Branch->branch_options();
                $data['employee'] = $employee;
                $data['title'] = $employee->full_name();
                $data['permissions'] = $this->permissions();
                $data['loan_type_options'] = $this->loan->loan_type_dropdown_options();
                $data['confidentiality_levels'] = $this->employee_confidentiality_level->dropdown_options(true);
                $data['all_loans'] = false;
                $data['loan_account_group_id'] = $this->account_group->account_group_selection('ACCOUNT RECEIVABLE');
                $data['loan_account_status'] = $this->account->check_loan_account($id);
                $this->load->view('employees/profile', $data);
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

            if(!$edit) {

                $this->load->model(['account', 'employee', 'employee_account']);
                $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "ACCOUNT PAYABLE"';
                $query = $this->db->query($sql);
                $group = $query->result();

                $employee = new Employee();
                $employee->load($this->input->post('employee_id'));

                if ($group) {
                    $new_account = new Account();
                    $new_account->account_name = strtoupper($employee->full_name()) . " SALARY ACCOUNT";
                    $new_account->account_group_id = $group[0]->account_group_id;
                    $new_account->opening_balance = 0;
                    $new_account->description = strtoupper($employee->full_name()) . " SALARY ACCOUNT";
                    if ($new_account->save()) {

                        $found_acount = $this->account->get(1, 0, '', 'account_id DESC');
                        $account = array_shift($found_acount);

                        $employee_account = new Employee_account();
                        $employee_account->account_id = $account->account_id;
                        $employee_account->employee_id = $this->input->post('employee_id');
                        $employee_account->created_by = $this->session->userdata('employee_id');
                        $employee_account->save();
                    };
                }
            }

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
        return $this->permission->get(0,0,'','name ASC');
    }

    public function save_user(){
        if(check_permission('Human Resources')  || $this->session->userdata('employee_id') == $this->input->post('employee_id')) {
            $this->load->model(['user','employee_approval_chain_level']);
            $user = new User();
            $edit = $user->load($this->input->post('user_id'));
            $user->username = $this->input->post('username');
            $user->employee_id = $this->input->post('employee_id');
            $confidentiality_level_id = $this->input->post('confidentiality_level_id');
            $user->confidentiality_level_id = $confidentiality_level_id != '' ? $confidentiality_level_id : 1;
            $password = trim($this->input->post('password'));
            if ($password != '') {
                echo $user->password = sha1(md5($password));
            }

            $user->active = $this->input->post('active');
            $user->save();
            if (check_permission('Human Resources')) {
                $data['employee'] = $user->employee();
                $data['employee']->clear_approval_chain_levels();

                $action = $edit ? 'User Update' : 'User Creation';
                $description = $user->username . ' was ' . ($edit ? 'updated' : 'created');
                system_log($action, $description);

                $this->load->model(['user_permission','user_permission_privilege']);
                $user->delete_permissions();
                $permission_ids = $this->input->post('permission_ids');
                $permission_ids = $permission_ids != '' ? $permission_ids : [];
                $permission_privilege_ids = $this->input->post('permission_privilege_ids');
                $permission_privilege_ids = $permission_privilege_ids != '' ? $permission_privilege_ids : [];
                inspect_object($permission_ids);
                inspect_object($permission_privilege_ids);
                if (!empty($permission_ids)) {
                    foreach ($permission_ids as $permission_index => $permission_id) {
                        if($permission_id != '') {
                            $user_permission = new User_permission();
                            $user_permission->permission_id = $permission_id;
                            $user_permission->user_id = $user->{$user::DB_TABLE_PK};
                            if ($user_permission->save()) {
                                if (!empty($permission_privilege_ids)) {
                                    foreach ($permission_privilege_ids[$permission_index] as $privillege_index => $permission_privilege_id) {
                                        if ($permission_privilege_id != '') {
                                            $user_permission_privilege = new User_permission_privilege();
                                            $user_permission_privilege->user_permission_id = $user_permission->{$user_permission::DB_TABLE_PK};
                                            $user_permission_privilege->permission_privilege_id = $permission_privilege_id;
                                            $user_permission_privilege->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $chain_level_ids = $this->input->post('approval_chain_level_ids');
                if(!empty($chain_level_ids)) {
                    foreach ($chain_level_ids as $chain_level_id) {
                        $junction = new Employee_approval_chain_level();
                        $junction->employee_id = $user->employee_id;
                        $junction->created_by = $this->input->post('employee_id');
                        $junction->approval_chain_level_id = $chain_level_id;
                        $junction->save();
                    }
                }
            }
            $data['user'] = $user;
            $data['employee'] = $user->employee();
            $data['permissions'] = $this->permissions();
            $response['username_and_password_form'] = $this->load->view('employees/username_and_password_form', $data,true);
            $response['user_permissions'] = $this->load->view('employees/user_permissions_tab', $data,true);
            $response['authorised_approvals'] = $this->load->view('employees/authorised_approvals_tab', $data,true);
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
        $this->load->model('official_ssf');
            $data['ssf_title'] = 'Social Security Fund';
            $data['official_ssf_option']  = $this->official_ssf->official_ssf_options();

        $this->load->model('official_hif');
            $data['hif_title'] = 'Health Insurance Fund';
            $data['official_hif_option']  = $this->official_hif->official_hif_options();

        $this->load->model('Tax_table');
        $Tax_table = new Tax_table();
        $data['tax_table_rates']=$Tax_table->tax_table_rates();


        $this->load->view('settings/index',$data);
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

    public function display_tax_tables()
    {
        $this->load->model('Tax_table');
        $Tax_table = new Tax_table();
        $data['tax_table_rates']=$Tax_table->tax_table_rates();

        echo $this->load->view('settings/tax_tables/display_tax_tables', $data);
    }

    public function payroll()
    {

        $generate_payroll = $this->input->post('gererate_payroll');
        $print = $this->input->post('print') ? true: false ;
        $department_id = $this->input->post('department_id');
        $employee_id = $this->input->post('employee_id');
        $submit = $this->input->post('submit') ? true: false ;
        $terminating_date = $this->input->post('terminating_date');

        $this->load->model(['department','employee']);
        $departments = new Department();
        $data['departments'] = $departments;

      if($generate_payroll || $print || $submit){

          $data['save'] = false;
          $sql = 'SELECT * FROM payroll WHERE payroll_for = "'.date('Y-m', strtotime($this->input->post('payroll_date'))).'" AND department_id = '.$department_id.' AND status != "Rejected"';
          $query = $this->db->query($sql);
          $results = $query->result();

          $payroll_date = $this->input->post('payroll_date');
          $data['payroll_date'] = $payroll_date;
          $data['all_allowances'] = $this->allowances();
          $data['all_ssfs'] = $this->ssfs();
          $data['all_hifs'] = $this->hifs();
          $data['all_loans'] = $this->loans();
          if(!$results){

              $sql = 'SELECT DISTINCT view_employee_basic_detail.*  FROM view_employee_basic_detail WHERE department_id = '.$department_id;
              $query = $this->db->query($sql);
              $results = $query->result();

              if(!$results){
                  echo "<br/><div style='text-align: center; font-size: 20px; font-weight: bold' class='alert alert-info'><i class='fa fa-warning'></i> Sorry You Can't Generate Payroll For This Department</div>";
              }else {

                  $departments->load($department_id);
                  $data['departments'] = $departments;
                  $this->load->model(['tax_table_item', 'tax_table']);

                  $employee_data = [];
                  foreach ($results as $employee) {
                      if($employee->basic_salary > 0){
                          $employee_name = explode(' ', $employee->employee_name);
                          if ($employee_name[2]) {
                              $employee_full_name = ucfirst($employee_name[0]) . ' ' . ucfirst(mb_substr($employee_name[1], 0, 1, 'utf-8')) . ' ' . ucfirst($employee_name[2]);
                          } else {
                              $employee_full_name = ucfirst($employee_name[0]) . ' ' . ucfirst($employee_name[1]);
                          }

                          //////  Checking if the contract started on this month
                          $date_contract_ends = $this->check_contract_continuation($payroll_date, $employee->start_date);
                          $flag = $date_contract_ends['flag'];
                          $continuation_date = $date_contract_ends['continuation_date'];
                          $include_in_payroll = $date_contract_ends['include_in_payroll'];
                          $contract_status = $date_contract_ends['contract_status'];

                          //////  Checking if the contract ended this month
                          $date_contract_ends = $this->chek_contract_end($payroll_date, $employee->end_date);
                          $flag = $date_contract_ends['flag'];
                          $terminating_date = $date_contract_ends['terminating_date'];
                          $include_in_payroll = $date_contract_ends['include_in_payroll'];
                          $contract_status = $date_contract_ends['contract_status'];

                          ///// Checking if the contract was closed this month
                          if ($employee->close_date) {
                              $date_contract_ends = $this->check_contract_close($payroll_date, $employee->close_date);
                              $flag = $date_contract_ends['flag'];
                              $terminating_date = $date_contract_ends['terminating_date'];
                              $include_in_payroll = $date_contract_ends['include_in_payroll'];
                              $contract_status = $date_contract_ends['contract_status'];

                          }
                          if ($include_in_payroll) {

                              $data['id'] = $employee->employee_id;
                              $data['employee_name'] = $employee_full_name;
                              $data['employee_title'] = $employee->title;
                              $data['employee_location'] = $employee->location;
                              $data['employee_basic_salary'] = $employee->basic_salary;
                              $recalculated = false;
                              ////  calculating gross salary
                              $gross_salary = ($employee->basic_salary + $this->sum_employee_allowances($employee->employee_id));

                              if ($this->session->userdata($employee->employee_id . $employee_full_name)) {
                                  $gross_salary = $this->session->userdata($employee->employee_id . $employee_full_name);
                                  $flag = false;
                                  $recalculated = true;
                              }

                              if ($employee_id == $employee->employee_id) {
                                  $gross_salary = $this->calculate_gross_per_worked_days(date('d', strtotime($terminating_date)), $gross_salary);
                                  $emplyee_gross_data = [$employee->employee_id . $employee_full_name => $gross_salary];
                                  $this->session->set_userdata($emplyee_gross_data);
                                  $flag = false;
                                  $recalculated = true;
                              }

                              $data['employee_gross_salary'] = $gross_salary;
                              $data['employee_ssfs'] = $this->find_employee_ssfs($employee->employee_id, $payroll_date, $gross_salary);
                              ///  $data['employee_deducted_nssf'] = $this->calculate_employee_deductions($gross_salary, 10);
                              $taxable_amount = ($gross_salary - $this->calculate_employee_deductions($gross_salary, 10));
                              $data['employee_taxable_amount'] = $taxable_amount;

                              $tax_table = $this->tax_table->get(1, 0, '', 'id DESC');
                              $current_tax_table = !empty($tax_table) ? array_shift($tax_table) : false;

                              if ($current_tax_table) {
                                  $taxable_amount_data = $this->tax_table_item->employee_taxable_salary_taxtable_details($taxable_amount, $current_tax_table->id);
                                  $employee_paye = round($this->tax_table->paye_formula(
                                      $taxable_amount,
                                      $taxable_amount_data['minimum_group_taxable_amount'],
                                      $taxable_amount_data['group_rate'],
                                      $taxable_amount_data['group_additional_amount']
                                  )
                                  );
                              } else {
                                  $employee_paye = 0;
                              }

                              $data['employee_paye'] = $employee_paye;
                              $loans = $this->sum_employee_loans_monthly_payments($employee->employee_id, $payroll_date);
                              $paye_and_loans = [$employee_paye, $loans];

                              $data['employee_loans'] = $this->find_employee_loans($employee->employee_id, $payroll_date);
                              $data['employee_netpay'] = $this->calculate_employee_netpay($taxable_amount, $paye_and_loans);
                              $data['sdl'] = $this->calculate_employer_deductions($gross_salary, 4.5);
                              $data['wcf'] = $this->calculate_employer_deductions($gross_salary, 1);
                              ////  $data['nssf'] = $this->calculate_employer_deductions($gross_salary,10, 10);
                              $data['employer_paying_ssf'] = $this->find_employee_ssfs($employee->employee_id, $payroll_date, $gross_salary, true);
                              ////  $data['nhif'] = $this->calculate_employer_deductions($gross_salary,6);
                              $data['employer_paying_hifs'] = $this->find_employee_hifs($employee->basic_salary);
                              $data['contract_status'] = $contract_status;
                              $data['flag'] = $flag;
                              $data['department_id'] = $department_id;
                              $data['terminating_date'] = $terminating_date ? $terminating_date : $continuation_date;
                              $data['worked_days'] = date('d', strtotime($terminating_date ? $terminating_date : $continuation_date));
                              $data['recalculated'] = $recalculated;
                              $data['employee_allowances'] = $this->find_employee_allowances($employee->employee_id);

                              $employee_data[] = $data;
                              $data['print'] = false;
                          }

                      }
                  }
                  $data['employee_data'] = array_sort($employee_data, 'employee_gross_salary', SORT_DESC);


                  if ($print) {
                      $data['print'] = $print;

                      $html = $this->load->view('payroll/payroll_sheet', $data, true);

                      //load mPDF library
                      $this->load->library('m_pdf');
                      //actually, you can pass mPDF parameter on this load() function
                      $pdf = $this->m_pdf->load();
                      $pdf->AddPage(
                          '', // L - landscape, P - portrait
                          '', '', '', '',
                          15, // margin_left
                          15, // margin right
                          15, // margin top
                          15, // margin bottom
                          9, // margin header
                          6, '', '', '', '', '', '', '', '', '', 'A4-L'
                      ); // margin footer
                      $footercontents = '
                        <div>
                            <div style="text-align: left; float: left; width: 50%">
                                <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                            </div>
                            <div>
                                <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                            </div>
                            <div style="text-align: center">
                            {PAGENO}
                            </div>
                        </div>';
                      $pdf->SetFooter($footercontents);
                      $pdf->WriteHTML($html);
                      //$this->mpdf->Output($file_name, 'D'); // download force

                      $pdf->Output('Payroll.pdf', 'I'); // view in the explorer


                  } else if ($generate_payroll) {
                      $data['print'] = $print;
                     echo $this->load->view('payroll/payroll_table', $data);

                     //// inspect_object($this->load->view('payroll/payroll_table', $data));
                  }else{

                      $this->load->model(['payroll','payroll_employee_basic_info','payroll_employee_allowance','payroll_employer_deduction']);
                      $payroll = new Payroll();
                      $payroll->payroll_for = date('Y-m',strtotime($payroll_date));
                      $payroll->department_id = $department_id;
                      $payroll->foward_to = NULL ;    ////// has to follow the approval chain
                      $payroll->status = '';
                      $payroll->approved_by = $this->session->userdata('employee_id');
                      $payroll->created_by = $this->session->userdata('employee_id');
                      if($payroll->save()){
                          $saved_payroll = $this->payroll->get(1,0,'','id DESC');
                          $found_payroll = array_shift($saved_payroll);

                          foreach ($data['employee_data'] as $employee){
                              $basic_info = new Payroll_employee_basic_info();
                              $basic_info->employee_id = $employee['id'];
                              $basic_info->payroll_id = $found_payroll->id;
                              $basic_info->title = $employee['employee_title'];
                              $basic_info->location = $employee['employee_location'];
                              $basic_info->basic_salary = $employee['employee_basic_salary'];
                              $basic_info->gross_salary = $employee['employee_gross_salary'];
                              $deducted_nssf = $employee['employee_ssfs'];
                              $basic_info->deducted_nssf = !empty($deducted_nssf) ? $deducted_nssf[0]['ssf_deducted_amount'] : 0;
                              $basic_info->taxable_amount = $employee['employee_taxable_amount'];
                              $basic_info->paye = $employee['employee_paye'];

                              if($employee['employee_loans']){

                                  $basic_info->heslb_loan = 0;
                                  $basic_info->heslb_loan_repay = 0;
                                  $basic_info->heslb_loan_balance = 0;
                                  $basic_info->company_loan = 0;
                                  $basic_info->company_loan_repay = 0;
                                  $basic_info->company_loan_balance = 0;
                                  $basic_info->advance_payment = 0;


                                  foreach ($employee['employee_loans'] as $the_loan){
                                      $loan = new Loan();
                                      if($loan->load($the_loan['loan_id'])){

                                          if(strtoupper(explode(' ',$loan->loan_type)[0]) == 'HESLB'){
                                              $basic_info->heslb_loan = $the_loan['total_loan_amount'];
                                              $basic_info->heslb_loan_repay = $the_loan['monthly_deduction_amount'];
                                              $basic_info->heslb_loan_balance = $the_loan['loan_balance_amount'];
                                          }

                                          if(strtoupper(explode(' ',$loan->loan_type)[0]) == 'COMPANY'){
                                              $basic_info->company_loan = $the_loan['total_loan_amount'];
                                              $basic_info->company_loan_repay = $the_loan['monthly_deduction_amount'];
                                              $basic_info->company_loan_balance = $the_loan['loan_balance_amount'];
                                          }

                                          inspect_object(strtoupper(explode(' ',$loan->loan_type)[0]));
                                          inspect_object($employee['employee_loans']);
                                          inspect_object($basic_info);

                                          if(strtoupper(explode(' ',$loan->loan_type)[0]) == 'ADVANCE'){
                                              $basic_info->advance_payment = $the_loan['total_loan_amount'];
                                          }
                                      };
                                  }

                              }else{

                                  $basic_info->heslb_loan = 0;
                                  $basic_info->heslb_loan_repay = 0;
                                  $basic_info->heslb_loan_balance = 0;
                                  $basic_info->company_loan = 0;
                                  $basic_info->company_loan_repay = 0;
                                  $basic_info->company_loan_balance = 0;
                                  $basic_info->advance_payment = 0;
                              }



                              $basic_info->net_pay = $employee['employee_netpay'];
                              if($basic_info->save()){

                                  foreach ($data['all_allowances'] as $found_allowance){

                                      $allowance = new Payroll_employee_allowance();
                                      $allowance->employee_id = $employee['id'];
                                      $allowance->payroll_id = $found_payroll->id;
                                      $allowance->allowance_name = $found_allowance->allowance_name;

                                      if($employee['employee_allowances']){

                                         foreach ($employee['employee_allowances'] as $emp_allow){
                                             if($emp_allow['allowance_name'] == $found_allowance->allowance_name){
                                                 $allowance->allowance_amount = $emp_allow['allowance_amount'];
                                             }else{
                                                 $allowance->allowance_amount = 0;
                                             }
                                         }
                                      }else{
                                          $allowance->allowance_amount = 0;
                                      }

                                      $allowance->save();

                                      };

                                  }

                              ///// saving sdl
                              $deduction = new Payroll_employer_deduction();
                              $deduction->payroll_id = $found_payroll->id;
                              $deduction->employee_id = $employee['id'];
                              $deduction->deduction_name = 'sdl';
                              $deduction->deduction_amount = $employee['sdl'];
                              $deduction->save();


                              ///// saving wcf
                              $deduction = new Payroll_employer_deduction();
                              $deduction->payroll_id = $found_payroll->id;
                              $deduction->employee_id = $employee['id'];
                              $deduction->deduction_name = 'wcf';
                              $deduction->deduction_amount = $employee['wcf'];
                              $deduction->save();

                              ///// saving ssfs
                              foreach ($employee['employer_paying_ssf'] as $ssf){

                                  $deduction = new Payroll_employer_deduction();
                                  $deduction->payroll_id = $found_payroll->id;
                                  $deduction->employee_id = $employee['id'];
                                  $deduction->deduction_name = $ssf['ssf_name'];
                                  $deduction->deduction_amount = $ssf['ssf_deducted_amount'] != '' ? $ssf['ssf_deducted_amount'] : 0;
                                  $deduction->save();
                              }

                              ///// saving hifs
                              foreach ($employee['employer_paying_hifs'] as $hif){

                                  $deduction = new Payroll_employer_deduction();
                                  $deduction->payroll_id = $found_payroll->id;
                                  $deduction->employee_id = $employee['id'];
                                  $deduction->deduction_name = $hif['hif_name'];
                                  $deduction->deduction_amount = $hif['hif_deduction_amount'];
                                  $deduction->save();
                              }

                              };

                      };
                  }

              }

          }else{
              $data['print'] = false;
              $data['save'] = true;
              $data['payroll'] = $results;
              $departments->load($results[0]->department_id);
              $data['departments'] = $departments;
              $data['payroll_date'] = $this->input->post('payroll_date');
              $employee = new Employee();
              $data['employee'] = $employee;

              $payroll_id = $results[0]->id;

              $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
              $query = $this->db->query($sql);
              $results = $query->result();
              $data['employee_basic_info'] = $results;

              $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id.' GROUP BY allowance_name';
              $query = $this->db->query($sql);
              $results = $query->result();
              $data['all_allowances_found'] = $results;

              $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id;
              $query = $this->db->query($sql);
              $results = $query->result();

              $data['employee_allowances_found'] = $results;

              $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' GROUP BY deduction_name';
              $query = $this->db->query($sql);
              $results = $query->result();

              $data['all_employer_deductions'] = $results;

              $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id;
              $query = $this->db->query($sql);
              $results = $query->result();

              $data['employee_deductions_found'] = $results;


              $this->load->model('payroll');
              $payroll = new Payroll();
              $payroll->load($payroll_id);


              $this->load->model(['payroll','approval_chain_level']);
              $payroll = new Payroll();
              $payroll->load($payroll_id);
              $level = new Approval_chain_level();

              if($payroll->current_approval_level()){
                  $level->load($payroll->current_approval_level()->id);
                  $can_approve = false;
                  $next_level = false;
                  $data['current_level'] = $payroll->current_approval_level()->id;
              }else{
                  $can_approve = false;
                  $next_level = false;
              }


              $data['can_approve'] = $can_approve;
              $data['next_level'] = $next_level;


              if(!$print){
                  echo $this->load->view('payroll/payroll_table', $data);
              }else{
                  $data['print'] = true;

                  $html = $this->load->view('payroll/payroll_sheet', $data, true);

                  //load mPDF library
                  $this->load->library('m_pdf');
                  //actually, you can pass mPDF parameter on this load() function
                  $pdf = $this->m_pdf->load();
                  $pdf->AddPage(
                      '', // L - landscape, P - portrait
                      '', '', '', '',
                      15, // margin_left
                      15, // margin right
                      15, // margin top
                      15, // margin bottom
                      9, // margin header
                      6, '', '', '', '', '', '', '', '', '', 'A4-L'
                  ); // margin footer
                  $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                  $pdf->SetFooter($footercontents);
                  $pdf->WriteHTML($html);
                  //$this->mpdf->Output($file_name, 'D'); // download force

                  $pdf->Output('Payroll.pdf', 'I'); // view in the explorer
              }
          }

      }else{
          $this->load->view('payroll/index', $data);
      }
    }

    public function departments_list()
    {
        $sql = 'SELECT * FROM departments';
        $query = $this->db->query($sql);
        $results = $query->result();
        $data['departments'] = $results;
        echo $this->load->view('payroll/departments', $data);
    }

    public function payroll_list(){
        $department_id = $this->input->post('department_id');

        $sql = 'SELECT * FROM payroll WHERE department_id = '.$department_id;
        $query = $this->db->query($sql);
        $results = $query->result();
        $data['payrolls'] = $results;
        echo $this->load->view('payroll/payrolls', $data);
    }

    public function payroll_list_display()
    {
        $print = $this->input->post('print') ? true: false ;
        $department_id = $this->input->post('department_id');
        $submit = $this->input->post('submit') ? true: false ;

        $this->load->model(['department','employee']);
        $departments = new Department();
        $data['departments'] = $departments;

        $data['save'] = false;
        $sql = 'SELECT * FROM payroll WHERE department_id = '.$department_id.' AND id = '. $this->input->post('payroll_id');
        $query = $this->db->query($sql);
        $results = $query->result();

        $data['print'] = false;
        $data['save'] = true;
        $data['payroll'] = $results;
        $departments->load($results[0]->department_id);
        $data['departments'] = $departments;
        $data['payroll_date'] = $this->input->post('payroll_date');
        $employee = new Employee();
        $data['employee'] = $employee;

        $payroll_id = $results[0]->id;

        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();
        $data['employee_basic_info'] = $results;

        $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id.' GROUP BY allowance_name';
        $query = $this->db->query($sql);
        $results = $query->result();
        $data['all_allowances_found'] = $results;

        $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $data['employee_allowances_found'] = $results;

        $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' GROUP BY deduction_name';
        $query = $this->db->query($sql);
        $results = $query->result();

        $data['all_employer_deductions'] = $results;

        $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $data['employee_deductions_found'] = $results;

        $this->load->model('payroll');
        $payroll = new Payroll();
        $payroll->load($payroll_id);


        $this->load->model(['payroll','approval_chain_level']);
        $payroll = new Payroll();
        $payroll->load($payroll_id);
        $level = new Approval_chain_level();

        if($payroll->current_approval_level()){
            $level->load($payroll->current_approval_level()->id);
            $can_approve = in_array($this->session->userdata('employee_id'),$level->can_approve_positions()) && $payroll->status != 'Rejected' ? true : false;
            $next_level = $level->next_level() ? true : false;
            $data['current_level'] =$payroll->current_approval_level()->id;
        }else{
            $can_approve = false;
            $next_level = false;
        }


        $data['can_approve'] = $can_approve;
        $data['next_level'] = $next_level;

        if(!$print){
            echo $this->load->view('payroll/payroll_table', $data);
        }else{
            $data['print'] = true;

            $html = $this->load->view('payroll/payroll_sheet', $data, true);

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                '', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6, '', '', '', '', '', '', '', '', '', 'A4-L'
            ); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('Payroll.pdf', 'I'); // view in the explorer
        }
    }

    public function check_payroll()
    {
        $sql = 'SELECT * FROM payroll WHERE payroll_for = "'.date('Y-m', strtotime($this->input->post('payroll_date'))).'" AND department_id = '.$this->input->post('department_id').' AND status != "Rejected"';
        $query = $this->db->query($sql);
        $results = $query->result();

        if($results){
            echo '1';
        }else{
            echo '0';
        }
    }

    public function check_payroll_status()
    {

        $aliye_approve = '';
        $status = '';

        ////// check payroll approval
        $sql = 'SELECT * FROM payroll_approvals WHERE payroll_id = '. $this->input->post('payroll_id').' ORDER BY id DESC';
        $query = $this->db->query($sql);
        $results = $query->result();
        if($results){
            $sql = 'SELECT * FROM approval_chain_levels WHERE id = '.$results[0]->approval_chain_level_id;
            $query = $this->db->query($sql);
            $found_result = $query->result();
            $aliye_approve = set_date($results[0]->approved_date).' '.$results[0]->status.' by '.$found_result[0]->level_name;
            $status = $results[0]->status;
        }

        ////// check payroll rejection
        $sql = 'SELECT * FROM rejected_payrolls WHERE payroll_id = '. $this->input->post('payroll_id');
        $query = $this->db->query($sql);
        $results = $query->result();
        if($results){
            $sql = 'SELECT * FROM approval_chain_levels WHERE id = '.$results[0]->current_level;
            $query = $this->db->query($sql);
            $found_result = $query->result();
            $aliye_approve = set_date(explode(' ',$results[0]->created_at)[0]).' '.$results[0]->status.' by '.$found_result[0]->level_name;
            $status = $results[0]->status;
        }

        echo $status.'@'.$aliye_approve;

    }

    ///// Payroll Journals
    public function payroll_approval()
    {
        $this->load->model(['payroll','payroll_approval']);
        $approval = new Payroll_approval();
        $approval->payroll_id = $this->input->post('payroll_id');
        $approval->approved_date = $this->input->post('approval_date');
        $approval->approving_coments = $this->input->post('coments');
        $approval->approval_chain_level_id = $this->input->post('current_level');
        $approval->returned_chain_level_id = null;
        $approval->status = $this->input->post('status');
        $approval->is_final = $this->input->post('is_final');
        $approval->created_by = $this->session->userdata('employee_id');

        if($approval->save()){
            $payroll = new Payroll();
            $payroll->load($this->input->post('payroll_id'));
            $payroll->status = $this->input->post('status');
            $payroll->save();
        };

        $last_approval = $this->payroll_approval->get(1,0,['payroll_id' => $this->input->post('payroll_id'),
            'is_final' => 1,
            'status' => 'Approved'
        ]);
        if($last_approval){
            $this->payroll_journals($this->input->post('payroll_id'), $this->input->post('approval_date'));
        }
    }

    public function chek_payroll_account()
    {
        $sql = 'SELECT * FROM accounts WHERE account_name = "PAYROLL EXPENSES"';
        $query = $this->db->query($sql);
        $account = $query->result();

        return $account ? $account : false;

    }

    public function chek_heslb_account()
    {
        $sql = 'SELECT * FROM accounts WHERE account_name = "HESLB ACCOUNT"';
        $query = $this->db->query($sql);
        $account = $query->result();

        return $account ? $account : false;

    }

    public function check_cash_account()
    {
        $sql = 'SELECT * FROM accounts WHERE account_name = "PAYROLL EXPENSES"';
        $query = $this->db->query($sql);
        $account = $query->result();

        return $account ? $account : false;
    }

    public function check_paye_account()
    {
        $sql = 'SELECT * FROM accounts WHERE account_name = "PAYE EXPENSES ACCOUNT"';
        $query = $this->db->query($sql);
        $account = $query->result();

        return $account ? $account : false;
    }

    public function check_deduction_account($deduction_name)
    {
        $sql = 'SELECT * FROM accounts WHERE account_name = "'.$deduction_name.'"';
        $query = $this->db->query($sql);
        $account = $query->result();
        return $account ? $account : false;
    }

    public function payroll_journals_backup($payroll_id, $transaction_date)
    {
        $this->load->model(['journal_voucher','journal_voucher_credit_account','journal_voucher_item','employee','payroll','department']);
        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $payroll = new Payroll();
        $payroll->load($payroll_id);

        $department = new Department();
        $department->load($payroll->department_id);

        $gross_sum = 0;
        foreach ($results as $rs){
            $gross_sum += $rs->gross_salary;
        }

        if(!$this->chek_payroll_account()){
            $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "DIRECT EXPENSES"';
            $query = $this->db->query($sql);
            $group = $query->result();

            if($group){
                $this->load->model('account');
                $new_account = new Account();
                $new_account->account_name = "PAYROLL EXPENSES";
                $new_account->account_group_id = $group[0]->account_group_id;
                $new_account->opening_balance = 0;
                $new_account->description = 'Direct Expenses Payroll Account';
                $new_account->save();
            }

        }

        foreach ($results as $result){

            $employee = new Employee();
            $employee->load($result->employee_id);

            $journal_voucher = new Journal_voucher();
            $journal_voucher->transaction_date = $transaction_date;
            $journal_voucher->reference = 'Payroll no: '.add_leading_zeros($payroll_id);
            $journal_voucher->journal_type = 'CASH PAYMENT';
            $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
            $journal_voucher->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
            $journal_voucher->remarks = 'Payroll salary payment for '.$employee->full_name().' ('.strtoupper($employee->position()->position_name).')';
            $journal_voucher->created_by = $this->session->userdata('employee_id');

            if($journal_voucher->save()){

                $found_voucher = $this->journal_voucher->get(1,0,'','journal_id DESC');
                $voucher = array_shift($found_voucher);

                $sql = 'SELECT * FROM accounts 
                       LEFT JOIN employee_accounts ON employee_accounts.account_id = accounts.account_id
                       WHERE employee_accounts.employee_id = '.$result->employee_id.' AND accounts.account_name LIKE "%salary%"';
                $query = $this->db->query($sql);
                $account_found = $query->result();
                $employee_salary_account_id = $account_found[0]->account_id;

                $journal_credid_account = new Journal_voucher_credit_account();
                $journal_credid_account->account_id = $account_found[0]->account_id;
                $journal_credid_account->journal_voucher_id = $voucher->journal_id;
                $journal_credid_account->amount = $result->net_pay;
                $journal_credid_account->narration = 'Payroll salary payment for '.$employee->full_name().' ('.strtoupper($employee->position()->position_name).')';
                if($journal_credid_account->save()){

                    $sql = 'SELECT * FROM journal_voucher_items WHERE debit_account_id = '.$this->chek_payroll_account()[0]->account_id;
                    $query = $this->db->query($sql);
                    $dr_account = $query->result();
                    if(!$dr_account){
                        $journal_item = new Journal_voucher_item();
                        $journal_item->journal_voucher_id = $voucher->journal_id;
                        $journal_item->debit_account_id = $this->chek_payroll_account()[0]->account_id;
                        $journal_item->amount = $gross_sum;
                        $journal_item->narration = 'Total Payroll Netpay for '.strtoupper($department->department_name).' DEPARTMENT '.$payroll->payroll_for;
                        $journal_item->save();
                    }

                }
            }

        }
    }

    public function payroll_journals($payroll_id, $transaction_date)
    {
        $this->load->model(['payroll_journal_voucher','journal_voucher','journal_voucher_credit_account','journal_voucher_item','employee','payroll','department']);
        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $payroll = new Payroll();
        $payroll->load($payroll_id);

        $department = new Department();
        $department->load($payroll->department_id);

        $payroll_date = strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll->payroll_for)))->format('F')) . ' ' . date('Y', strtotime($payroll->payroll_for));


        $sql = 'SELECT COALESCE(SUM(deduction_amount),0) AS deduction_amount FROM payroll_employer_deductions
                WHERE payroll_id = '.$payroll_id.' AND deduction_name = "NSSF"';

        $query = $this->db->query($sql);
        $sum_employee_nssf = $query->row()->deduction_amount;

        $gross_sum = 0;
        $sum_paye = 0;
        foreach ($results as $rs){
            $gross_sum += $rs->gross_salary;
            $sum_paye += $rs->paye;
        }

        if(!$this->chek_payroll_account()){
            $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "DIRECT EXPENSES"';
            $query = $this->db->query($sql);
            $group = $query->result();

            if($group){
                $this->load->model('account');
                $new_account = new Account();
                $new_account->account_name = "PAYROLL EXPENSES";
                $new_account->account_group_id = $group[0]->account_group_id;
                $new_account->opening_balance = 0;
                $new_account->description = 'Direct Expenses Payroll Account';
                $new_account->save();
            }

        }

        $journal_voucher = new Journal_voucher();
        $journal_voucher->transaction_date = $transaction_date;
        $journal_voucher->reference = 'PAYROLL FOR '.$payroll_date;
        $journal_voucher->journal_type = 'CASH PAYMENT';
        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $journal_voucher->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
        $journal_voucher->currency_id = 1;
        $journal_voucher->remarks = strtoupper($department->department_name).' DEPARTMENT, PAYROLL EXPENSES FOR '.$payroll_date;
        $journal_voucher->created_by = $this->session->userdata('employee_id');

        if($journal_voucher->save()){

            $journal_item = new Journal_voucher_item();
            $journal_item->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
            $journal_item->debit_account_id = $this->chek_payroll_account()[0]->account_id;
            $journal_item->amount = $gross_sum;
            $journal_item->narration = strtoupper($department->department_name).' DEPARTMENT, PAYROLL EXPENSES FOR '.$payroll_date;
            if($journal_item->save()){

                foreach ($results as $result){

                    $employee = new Employee();
                    $employee->load($result->employee_id);

                    ///// NETPAY PAYABLE PART
                    $sql = 'SELECT * FROM employee_accounts 
                       LEFT JOIN accounts ON employee_accounts.account_id = accounts.account_id
                       WHERE employee_id = '.$result->employee_id.' AND accounts.account_name LIKE "%salary%"';
                    $query = $this->db->query($sql);
                    $account_found = $query->result();

                    $journal_credit_account = new Journal_voucher_credit_account();
                    $journal_credit_account->account_id = $account_found[0]->account_id;
                    $journal_credit_account->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
                    $journal_credit_account->amount = $result->net_pay;
                    $journal_credit_account->narration = 'PAYROLL NET PAYMENT '.strtoupper($employee->full_name()).' ('.strtoupper($employee->position()->position_name).')';
                    $journal_credit_account->save();

                }

                ////// NSSF DEDUCTION PART
                $deduction_name = 'NSSF';
                if(!$this->check_deduction_account(strtoupper($deduction_name.' - ACCOUNT PAYABLE'))){
                    $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "ACCOUNT PAYABLE"';
                    $query = $this->db->query($sql);
                    $group = $query->result();

                    if($group){
                        $this->load->model('account');
                        $new_account = new Account();
                        $new_account->account_name = strtoupper($deduction_name.' - ACCOUNT PAYABLE');
                        $new_account->account_group_id = $group[0]->account_group_id;
                        $new_account->opening_balance = 0;
                        $new_account->description = $deduction_name.' Account Payable';
                        $new_account->save();
                    }
                }

                $journal_credit_account = new Journal_voucher_credit_account();
                $journal_credit_account->account_id = $this->check_deduction_account(strtoupper($deduction_name.' - ACCOUNT PAYABLE'))[0]->account_id;
                $journal_credit_account->journal_voucher_id =  $journal_voucher->{$journal_voucher::DB_TABLE_PK};
                $journal_credit_account->amount = $sum_employee_nssf;
                $journal_credit_account->narration = 'PAYROLL '.$deduction_name.' PAYMENT FOR '.strtoupper($department->department_name).' DEPARTMENT, PAYROLL FOR '.$payroll_date;
                $journal_credit_account->save();


                ////// PAYE PART
                if(!$this->check_paye_account()){
                    $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "DIRECT EXPENSES"';
                    $query = $this->db->query($sql);
                    $group = $query->result();

                    if($group){
                        $this->load->model('account');
                        $new_account = new Account();
                        $new_account->account_name = strtoupper('PAYE EXPENSES ACCOUNT');
                        $new_account->account_group_id = $group[0]->account_group_id;
                        $new_account->opening_balance = 0;
                        $new_account->description = 'Direct Expenses P.A.Y.E Account';
                        $new_account->save();
                    }

                }

                $journal_credit_account = new Journal_voucher_credit_account();
                $journal_credit_account->account_id = $this->check_paye_account()[0]->account_id;
                $journal_credit_account->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
                $journal_credit_account->amount = $sum_paye;
                $journal_credit_account->narration = 'PAYROLL P.A.Y.E PAYMENT FOR '.strtoupper($department->department_name).' DEPARTMENT, PAYROLL EXPENSES FOR '.$payroll_date;
                $journal_credit_account->save();

                $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' GROUP BY deduction_name';
                $query = $this->db->query($sql);
                $results = $query->result();

                $pjv = new Payroll_journal_voucher();
                $pjv->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
                $pjv->payroll_id = $payroll_id;
                $pjv->save();

                foreach ($results as $result){
                    $this->payroll_deduction_journals($payroll_id, $transaction_date, $result->deduction_name, strtoupper($result->deduction_name) == 'NSSF' ? $sum_employee_nssf : 0);
                }
            }
        }
    }

    public function payroll_deduction_journals($payroll_id, $transaction_date, $deduction_name, $employee_deduction = 0)
    {
        $this->load->model(['journal_voucher','journal_voucher_credit_account','journal_voucher_item','employee','payroll','department','']);
        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $payroll = new Payroll();
        $payroll->load($payroll_id);

        $department = new Department();
        $department->load($payroll->department_id);

        $payroll_date = strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll->payroll_for)))->format('F')) . ' ' . date('Y', strtotime($payroll->payroll_for));

        $journal_voucher = new Journal_voucher();
        $journal_voucher->transaction_date = $transaction_date;
        $journal_voucher->reference = 'PAYROLL FOR '.$payroll_date;
        $journal_voucher->journal_type = 'CASH PAYMENT';
        $journal_voucher->currency_id = 1;
        $journal_voucher->remarks = strtoupper($deduction_name).' CONTRIBUTTIONS FOR '.strtoupper($department->department_name).' DEPARTMENT, PAYROLL FOR '.$payroll_date;
        $journal_voucher->created_by = $this->session->userdata('employee_id');
        if($journal_voucher->save()){
            $found_voucher = $this->journal_voucher->get(1, 0, '', 'journal_id DESC');
            $voucher = array_shift($found_voucher);

            $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = ' . $payroll_id . ' AND deduction_name = "' . $deduction_name . '"';
            $query = $this->db->query($sql);
            $results = $query->result();

            $sum = 0;
            foreach ($results as $result) {
                $sum += $result->deduction_amount;
            }

            if (!$this->check_deduction_account(strtoupper($deduction_name . ' - ACCOUNT PAYABLE'))) {
                $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "ACCOUNT PAYABLE"';
                $query = $this->db->query($sql);
                $group = $query->result();

                if ($group) {
                    $this->load->model('account');
                    $new_account = new Account();
                    $new_account->account_name = strtoupper($deduction_name . ' - ACCOUNT PAYABLE');
                    $new_account->account_group_id = $group[0]->account_group_id;
                    $new_account->opening_balance = 0;
                    $new_account->description = $deduction_name . ' Account Payable';
                    $new_account->save();
                }

            }


            $journal_item = new Journal_voucher_item();
            $journal_item->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
            $journal_item->debit_account_id = $this->chek_payroll_account()[0]->account_id;
            $journal_item->amount = ($sum - $employee_deduction);
            $journal_item->narration = strtoupper($department->department_name).' DEPARTMENT, PAYROLL EXPENSES FOR '.$payroll_date;
            if($journal_item->save()) {

                $journal_credid_account = new Journal_voucher_credit_account();
                $journal_credid_account->account_id = $this->check_deduction_account(strtoupper($deduction_name . ' - ACCOUNT PAYABLE'))[0]->account_id;
                $journal_credid_account->journal_voucher_id = $voucher->journal_id;
                $journal_credid_account->amount = ($sum - $employee_deduction);
                $journal_credid_account->narration = strtoupper($deduction_name) . ' CONTRIBUTTIONS FOR ' . strtoupper($department->department_name) . ' DEPARTMENT, PAYROLL FOR ' . $payroll_date;
                $journal_credid_account->save();
            }

        }
    }

    public function payroll_loan_payments($payroll_id)
    {
        $this->load->model(['employee']);
        $posted_params = dataTable_post_params();
        $limit = $posted_params['limit'];
        $order = $posted_params['order'];
        $start = $posted_params['start'];
        $keyword = $posted_params['keyword'];

        $order_string = dataTable_order_string(['employee_id','title','location','advance_payment','heslb_loan_repay','company_loan_repay'],$order,'employee_id');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where = 'WHERE payroll_id = '.$payroll_id;

        $sql = 'SELECT COUNT(payroll_employee_basic_info.id) AS records_total FROM payroll_employee_basic_info '.$where;
        $query = $this->db->query($sql);
        $records_total = $query->row()->records_total;


        if ($keyword != '') {
            $where .= ' AND title LIKE "%'.$keyword.'%"';
        }

        $sql = 'SELECT * FROM payroll_employee_basic_info '.$where.$order_string;
        $query = $this->db->query($sql);
        $results = $query->result();

        $rows = [];
        $sum_advance = 0;
        $sum_heslb = 0;
        $sum_company = 0;
        foreach ($results as $result){
            $employee = new Employee();
            $employee->load($result->employee_id);
            $sum_advance += $result->advance_payment;
            $sum_heslb += $result->heslb_loan_repay;
            $sum_company += $result->company_loan_repay;
            $rows[] =[
                $employee->full_name(),
                $result->title,
                $result->location,
                $result->advance_payment,
                $result->heslb_loan_repay,
                $result->company_loan_repay
            ];

        }

        $sql = 'SELECT COUNT(payroll_employee_basic_info.id) AS records_filtered FROM payroll_employee_basic_info '.$where.$order_string;
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $json = [
            "recordsTotal" => $records_total,
            "advance_total" => $sum_advance,
            "heslb_total" => $sum_heslb,
            "comany_total" => $sum_company,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);

    }

    public function chek_payroll_payemts()
    {
        $payroll_id = $this->input->post('payroll_id');
        $data = '';

        $sql = 'SELECT * FROM payroll_payments WHERE payroll_id = '.$payroll_id.' AND loan_name LIKE "%ADVANCE%"';
        $query = $this->db->query($sql);
        $found_advance = $query->result();
        if($found_advance){
            $data = '1';
        } else {
            if($this->check_if_there_is_loan_to_pay($payroll_id, 'advance_payment') == true){
                $data = '0';
            }else{
                $data = '2';
            }

        }

        $sql = 'SELECT * FROM payroll_payments WHERE payroll_id = '.$payroll_id.' AND loan_name LIKE "%HESLB%"';
        $query = $this->db->query($sql);
        $found_heslb = $query->result();
        if($found_heslb){
            $data = $data.'-1';
        } else {
            if($this->check_if_there_is_loan_to_pay($payroll_id, 'heslb_loan_repay') == true){
                $data = $data.'-0';
            }else{
                $data = $data.'-2';
            }
        }

        $sql = 'SELECT * FROM payroll_payments WHERE payroll_id = '.$payroll_id.' AND loan_name LIKE "%COMPANY%"';
        $query = $this->db->query($sql);
        $found_company = $query->result();
        if($found_company){
            $data = $data.'-1';
        } else {
            if($this->check_if_there_is_loan_to_pay($payroll_id, 'company_loan_repay') == true){
                $data = $data.'-0';
            }else{
                $data = $data.'-2';
            }
        }

        echo $data;
    }

    public function calculate_employee_netpay($taxable_amount, $paye_and_loans = [])
    {
        $netpay = $taxable_amount;
        foreach ($paye_and_loans as $amount){
            $netpay = $netpay - $amount;
        }
        return $netpay;
    }

    public function calculate_gross_per_worked_days($number_of_working_days, $gross_salary){
        return round(($gross_salary/30)*($number_of_working_days));
    }

    public function calculate_employer_deductions( $source_amount, $employer_deduction_percent, $employee_deduction_percent = null)
    {
        if ($employee_deduction_percent) {
            return round(($source_amount*($employer_deduction_percent/100)) + ($source_amount*($employee_deduction_percent/100)));
        }else{
            return round($source_amount * ($employer_deduction_percent / 100));
        }
    }

    public function calculate_employee_deductions($source_amount, $employee_deduction_percent)
    {
        return round($source_amount * ($employee_deduction_percent / 100));

    }

    public function find_employee_allowances($employee_id)
    {
        $this->load->model(['employee_allowance','allowance']);
        $allowances = $this->employee_allowance->get(0,0,['employee_id' => $employee_id]);
        $data = [];
        foreach ($allowances as $allowance){
            $found_allowance = new Allowance();
            $found_allowance->load($allowance->allowance_id);
            $data[] = [
                'employee_allowance_id' => $allowance->id,
                'allowance_name' => $found_allowance->allowance_name,
                'allowance_amount' => $allowance->allowance_amount
            ];
        }
        return $data;
    }

    public function allowances()
    {
        $this->load->model('allowance');
        return $this->allowance->get();

    }

    public function ssfs(){
        $this->load->model('ssf');
        return $this->ssf->get();
    }

    public function hifs(){
        $this->load->model('hif');
        return $this->hif->get();
    }

    public function loans()
    {
        $this->load->model('loan');
        return $this->loan->get();
    }

    public function sum_employee_allowances($employee_id)
    {
        $this->load->model(['employee_allowance','allowance']);
        $allowances = $this->employee_allowance->get(0,0,['employee_id' => $employee_id]);
        $sum = 0;
        foreach ($allowances as $allowance){
            $sum = $sum + $allowance->allowance_amount;
        }
        return $sum;
    }

    public function find_employee_ssfs($employee_id, $payroll_date, $source_amount, $include_employer = false)
    {
        $lastDayThisMonth_day = date('d', strtotime(date("Y-m-t", strtotime($payroll_date))));
        $lastDayThisMonth_date =  date("Y-m-t", strtotime($payroll_date));

        $this->load->model(['ssf','employee_ssf']);
        $ssf_details = $this->employee_ssf->get(0,0, ['employee_id' => $employee_id]);
        $data = [];
        foreach ($ssf_details as $detail){

            $contract_end_date = date_create($detail->start_date);
            $month_date = date_create($lastDayThisMonth_date);

            $diff=date_diff($contract_end_date,$month_date);
            $difference_indication = $diff->format("%R");

            //// cheking if deduction has started (ie before or within this month)
            if($difference_indication == '+'){
                $ssf = new Ssf();
                $ssf->load($detail->ssf_id);
                $data[] = [
                    'ssf_id' => $ssf->id,
                    'ssf_name' => $ssf->ssf_name,
                    'ssf_deducted_amount' => $include_employer != false ? $this->calculate_employer_deductions($source_amount, $ssf->employer_deduction_percent ,$ssf->employee_deduction_percent ) : $this->calculate_employee_deductions($source_amount, $ssf->employee_deduction_percent)
                ];
            }
        }
        return $data;
    }

    /////// this fucntion should be improved
    public function find_employee_hifs($source_amount, $include_employee = false)
    {
        $this->load->model('hif');
        $hif_detais = $this->hif->get();
        $data = [];
        foreach ($hif_detais as $detail){
            $data[] = [
                'hif_id' => $detail->id,
                'hif_name' => $detail->hif_name,
                'hif_deduction_amount' => $this->calculate_employer_deductions($source_amount, $detail->employer_deduction_percent)
            ];
        }
        return $data;
    }

    public function chek_contract_end($payroll_date, $end_date)
    {
        $lastDayThisMonth_day = date('d', strtotime(date("Y-m-t", strtotime($payroll_date))));
        $lastDayThisMonth_date =  date("Y-m-t", strtotime($payroll_date));

        $contract_end_date = date_create($end_date);
        $month_date = date_create($lastDayThisMonth_date);

        $diff=date_diff($contract_end_date,$month_date);
        $difference_indication = $diff->format("%R");
        $date_difference = $diff->format("%a");

        ////$new_diff = $diff->format("%R%a");

        $terminating_date = '';

        if($difference_indication == '+'){
            if($date_difference <= $lastDayThisMonth_day ){
                $contract_status = 'Contract Ended On '.set_date($end_date);  /// within this month
                $flag = true;
                $include_in_payroll = true;
                $terminating_date = $end_date;
            }else{
                $contract_status = 'Contract Ended On '.set_date($end_date); /// befoere this month
                $flag = true;
                $include_in_payroll = false;
            }
        }else{
            $contract_status = 'Contract Is Still Active Up To '.set_date($end_date);
            $flag = false;
            $include_in_payroll = true;
        }

        $data['contract_status'] = $contract_status;
        $data['flag'] = $flag;
        $data['include_in_payroll'] = $include_in_payroll;
        $data['terminating_date'] = $terminating_date;

        return $data;

    }

    public function check_contract_close($payroll_date, $close_date)
    {
        $lastDayThisMonth_day = date('d', strtotime(date("Y-m-t", strtotime($payroll_date))));
        $lastDayThisMonth_date =  date("Y-m-t", strtotime($payroll_date));

        $contract_end_date = date_create($close_date);
        $month_date = date_create($lastDayThisMonth_date);

        $diff=date_diff($contract_end_date,$month_date);
        $difference_indication = $diff->format("%R");
        $date_difference = $diff->format("%a");

        $terminating_date = '';

        if($difference_indication == '+'){
            if($date_difference <= $lastDayThisMonth_day ){
                $contract_status = 'Contract Closed On '.set_date($close_date); //// within this month
                $flag = true;
                $include_in_payroll = true;
                $terminating_date = $close_date;
            }else{
                $contract_status = 'Contract Closed On '.set_date($close_date);  //// before this month
                $flag = true;
                $include_in_payroll = false;
            }
        }else{
            $contract_status = 'Contract Is Still Active Up To '.set_date($close_date);
            $flag = false;
            $include_in_payroll = true;
        }

        $data['contract_status'] = $contract_status;
        $data['flag'] = $flag;
        $data['include_in_payroll'] = $include_in_payroll;
        $data['terminating_date'] = $terminating_date;

        return $data;

    }

    public function check_contract_continuation($payroll_date, $start_date)
    {
        $lastDayThisMonth_day = date('d', strtotime(date("Y-m-t", strtotime($payroll_date))));
        $lastDayThisMonth_date =  date("Y-m-t", strtotime($payroll_date));

        $contract_end_date = date_create($start_date);
        $month_date = date_create($lastDayThisMonth_date);

        $diff=date_diff($contract_end_date,$month_date);
        $difference_indication = $diff->format("%R");
        $date_difference = $diff->format("%a");

        ////$new_diff = $diff->format("%R%a");

        $continuation_date = '';

        if($difference_indication == '+'){
            if($date_difference <= $lastDayThisMonth_day ){
                $contract_status = 'Contract Started On '.set_date($start_date);  /// within this month
                $flag = true;
                $include_in_payroll = true;
                $continuation_date = $start_date;
            }else{
                $contract_status = 'Contract Started On '.set_date($start_date); /// befoere this month
                $flag = true;
                $include_in_payroll = true;
            }
        }else{
            $contract_status = 'Contract will start on '.set_date($start_date);
            $flag = false;
            $include_in_payroll = false;
        }

        $data['contract_status'] = $contract_status;
        $data['flag'] = $flag;
        $data['include_in_payroll'] = $include_in_payroll;
        $data['continuation_date'] = $continuation_date;

        return $data;

    }

    public function employee_loans_list($employee_id)
    {
        $this->load->model('employee_loan');
        $posted_params = dataTable_post_params();
        echo $this->employee_loan->employee_loans_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$employee_id);
    }

    public function save_employee_loan()
    {
        $this->load->model('employee_loan');
        $loan = new Employee_loan();
        $edit = $loan->load($this->input->post('employee_loan_id'));
        $loan->employee_id = $this->input->post('employee_id');
        $loan->loan_id = $this->input->post('loan_id');
        $loan->loan_approved_date = $this->input->post('approved_date');
        $loan->loan_deduction_start_date = $this->input->post('deduction_start_date');
        $loan->total_loan_amount = $this->input->post('total_loan_amount');
        $loan->monthly_deduction_amount = $this->input->post('monthly_deduction_rate');
        $loan->loan_balance_amount = $this->input->post('total_loan_amount');
        $loan->loan_application_form_path = $this->input->post('application_letter');
        $loan->description = $this->input->post('description');
        $loan->created_by = $this->session->userdata('employee_id');
        $loan->save();
    }

    public function delete_employee_loan()
    {
        $this->load->model('employee_loan');
        $employee_loan = new Employee_loan();
        $employee_loan->load($this->input->post('employee_loan_id'));
        try{
            $employee_loan->delete();
            echo 'success-Loan Deleted Successful ';
        }catch (Exception $e){
            echo 'error-This Loan Cannot be deleted ';
        }

    }

    ////// employee loans
    public function loan_repay_list($employee_id = false)
    {
        $this->load->model('employee_loan_repay');
        $posted_params = dataTable_post_params();
        echo $this->employee_loan_repay->loan_repay_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$employee_id);
    }

    public function save_employee_loan_repay()
    {
//        $this->load->model(['employee_loan_repay','employee_loan']);
//        $payment = new Employee_loan_repay();
//        $payment->employee_id = $this->input->post('employee_id');
//        $payment->loan_id = $this->input->post('loan_id');
//        $payment->employee_loan_id = $this->input->post('employee_loan_id');
//        $payment->paid_amount = $this->input->post('paid_amount');
//
//        $employee_loan = new Employee_loan();
//        $employee_loan->load($this->input->post('employee_loan_id'));
//        $new_loan_balance = $employee_loan->loan_balance_amount - $this->input->post('paid_amount');
//
//        $payment->loan_balance_amount = $new_loan_balance;
//        $payment->paid_date = $this->input->post('paid_date');
//        $payment->description = $this->input->post('description');
//        $payment->created_by = $this->session->userdata('employee_id');
//        if($payment->save()){
//            $employee_loan = new Employee_loan();
//            $employee_loan->load($this->input->post('employee_loan_id'));
//            $employee_loan->loan_balance_amount = $new_loan_balance;
//            $employee_loan->save();
//        }
    }

    public function loan_payment_history()
    {
        $this->load->model(['loan','employee_loan_repay','employee_loan']);
        $loan_id = $this->input->post('loan_id');
        $employee_loan_id = $this->input->post('employee_loan_id');
        $employee_id = $this->input->post('employee_id');
        $loan = new Loan();
        $loan->load($loan_id);
        $employee_loan = new Employee_loan();
        $employee_loan->load($employee_loan_id);

        $employee = new Employee();
        $employee->load($employee_id);
        $mployee_fullname = $employee->full_name();

        $data['employee_data'] = $employee;
        $data['loan'] = $loan;
        $data['employee_loan'] = $employee_loan;
        $data['employee_loan_payments'] = $this->employee_loan_repay->loan_payment_history($loan_id, $employee_id);

        $html = $this->load->view('employees/employee_loans/loan_payment_history_sheet', $data, true);

        //load mPDF library
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();
        $pdf->AddPage(
            '', // L - landscape, P - portrait
            '', '', '', '',
            15, // margin_left
            15, // margin right
            15, // margin top
            15, // margin bottom
            9, // margin header
            6, '', '', '', '', '', '', '', '', '', 'A4-P'
        ); // margin footer
        $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
        $pdf->SetFooter($footercontents);
        $pdf->WriteHTML($html);
        //$this->mpdf->Output($file_name, 'D'); // download force

        $pdf->Output($mployee_fullname.' Loan Payment History.pdf', 'I'); // view in the explorer
    }

    public function verfy_employee_loan()
    {
        $employee_id = $this->input->post('employee_id');
        $loan_id = $this->input->post('loan_id');
        $sql = 'SELECT * FROM view_employee_basic_detail WHERE employee_id = '.$employee_id;
        $query = $this->db->query($sql);
        $results = $query->result();
        if($results){
            $gross_salary = 0;
            $basic_salary = 0;
            foreach ($results as $employee){
                $gross_salary =  ($employee->basic_salary + $this->sum_employee_allowances($employee->employee_id));
                $basic_salary = $employee->basic_salary;
            }

            $this->load->model('loan');
            $loan = new Loan();
            $loan->load($loan_id);
            switch (strtoupper($loan->loan_type)){
                case 'HESLB':
                    echo 'heslb '.$this->calculate_employee_deductions($gross_salary, 15);
                    break;
                case 'ADVANCE PAYMENTS':
                    echo 'advance '.($basic_salary/2);
                    break;
                default:
                    echo 'default 1';
                    break;
            }
        }else{
            echo 'default 0';
        }

    }

    public function find_employee_loans($employee_id, $payroll_date)
    {
        $lastDayThisMonth_day = date('d', strtotime(date("Y-m-t", strtotime($payroll_date))));
        $lastDayThisMonth_date =  date("Y-m-t", strtotime($payroll_date));

        $this->load->model('employee_loan');
        $loans = $this->employee_loan->get(0,0,['employee_id' => $employee_id]);
        $data = [];
        foreach ($loans as $loan){

            $contract_end_date = date_create($loan->loan_deduction_start_date);
            $month_date = date_create($lastDayThisMonth_date);

            $diff=date_diff($contract_end_date,$month_date);
            $difference_indication = $diff->format("%R");

            //// cheking if deduction has started (ie before or within this month)
            if($difference_indication == '+') {
                if($loan->loan_balance_amount > 0){
                    $data[] = [
                        'employee_id' => $loan->employee_id,
                        'loan_id' => $loan->loan_id,
                        'loan_approved_date' => $loan->loan_approved_date,
                        'load_deduction_start_date' => $loan->loan_deduction_start_date,
                        'total_loan_amount' => $loan->total_loan_amount,
                        'monthly_deduction_amount' => $loan->monthly_deduction_amount,
                        'loan_balance_amount' => $loan->loan_balance_amount
                    ];
                }
            }else{
                if($loan->loan_balance_amount > 0){
                    $data[] = [
                        'employee_id' => $loan->employee_id,
                        'loan_id' => $loan->loan_id,
                        'loan_approved_date' => $loan->loan_approved_date,
                        'load_deduction_start_date' => $loan->loan_deduction_start_date,
                        'total_loan_amount' => $loan->total_loan_amount,
                        'monthly_deduction_amount' => 0,
                        'loan_balance_amount' => $loan->loan_balance_amount
                    ];
                }
            }
        }
        return $data;
    }

    public function sum_employee_loans_monthly_payments($employee_id, $payroll_date)
    {
        $lastDayThisMonth_day = date('d', strtotime(date("Y-m-t", strtotime($payroll_date))));
        $lastDayThisMonth_date =  date("Y-m-t", strtotime($payroll_date));

        $this->load->model('employee_loan');
        $loans = $this->employee_loan->get(0,0,['employee_id' => $employee_id]);
        $payment_sum = 0;
        foreach ($loans as $loan){

            $contract_end_date = date_create($loan->loan_deduction_start_date);
            $month_date = date_create($lastDayThisMonth_date);

            $diff=date_diff($contract_end_date,$month_date);
            $difference_indication = $diff->format("%R");

            //// cheking if deduction has started (ie before or within this month)
            if($difference_indication == '+') {
                if($loan->loan_balance_amount > 0){
                    $payment_sum = $payment_sum + $loan->monthly_deduction_amount;
                }
            }
        }
        return $payment_sum;
    }

    public function employee_loan_list(){
        $this->load->model(['loan','employee_contract','account','payroll']);
        $data['permissions'] = $this->permissions();
        $data['loan_type_options'] = $this->loan->loan_type_dropdown_options();
        $data['contract_employee_dropdown_options'] = $this->account->dropdown_options(['ACCOUNT RECEIVABLE']);
        $data['account_dropdown_options'] = $this->account->dropdown_options(['CASH','BANK','ACCOUNT PAYABLE']);
        $data['all_loans'] = true;
        $data['payroll_options'] = $this->payroll->payroll_dropdown_options();
        $this->load->view('employees/employee_loans/employee_loan_lists', $data);

    }

    public function payroll_submissions(){
        $this->load->model(['loan','employee_contract','account','payroll']);
        $data['permissions'] = $this->permissions();
        $data['loan_type_options'] = $this->loan->loan_type_dropdown_options();
        $data['contract_employee_dropdown_options'] = $this->account->dropdown_options(['ACCOUNT RECEIVABLE']);
        $data['account_dropdown_options'] = $this->account->dropdown_options(['CASH','BANK','ACCOUNT PAYABLE']);
        $data['all_loans'] = true;
        $data['payroll_options'] = $this->payroll->payroll_dropdown_options();
        $this->load->view('employees/employee_loans/index', $data);

    }

    ///////  payroll payments
    public function payroll_advance_payment_repay()
    {
        $payroll_id = $this->input->post('payroll_id');

        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $this->load->model(['receipt','receipt_item','employee','employee_account','employee_loan','payroll_payment']);

        foreach ($results as $result) {

            $sql = 'SELECT * FROM accounts 
                       LEFT JOIN employee_accounts ON employee_accounts.account_id = accounts.account_id
                       WHERE employee_accounts.employee_id = '.$result->employee_id.' AND accounts.account_name LIKE "%loan%"';
            $query = $this->db->query($sql);
            $account_found = $query->result();
            $employee_loan_account_id = $account_found[0]->account_id;


            $employee = new Employee();
            $employee->load($result->employee_id);

            if($result->advance_payment > 0) {
                $receipt = new Receipt();
                $receipt->debit_account_id = $this->input->post('dr_account');
                $receipt->credit_account_id = $employee_loan_account_id;
                $receipt->receipt_date = $this->input->post('received_date');
                $receipt->reference = 'Advance Payment for '.strtoupper($employee->full_name()).'Payroll no: '.add_leading_zeros($result->payroll_id);
                $receipt->currency_id = 1;
                $receipt->exchange_rate = 1;
                $receipt->withholding_tax = 0;
                $receipt->remarks = $this->input->post('coments');
                $receipt->created_by = $this->session->userdata('employee_id');

                if ($receipt->save()) {
                    $last_receipt = $this->receipt->get(1, 0, '', 'id DESC');
                    $found_receipt = array_shift($last_receipt);
                    $receipt_item = new Receipt_item();
                    $receipt_item->receipt_id = $found_receipt->id;
                    $receipt_item->amount = $result->advance_payment;
                    $receipt_item->remarks = 'Advance Payment for '.strtoupper($employee->full_name()).' Payroll no: '.add_leading_zeros($result->payroll_id);
                    if ($receipt_item->save()) {
                        $employee_loan = new Employee_loan();

//                        $found_loan = $this->employee_loan->get(1,0,['employee_id' => $result->employee_id,
//                            'loan_account_id' => $employee_loan_account_id]);
//                        $account = array_shift($found_loan);

                        $found_loan = $this->employee_loan->get(1,0,['employee_id' => $result->employee_id,
                            'loan_account_id' => $employee_loan_account_id, 'loan_id' => $this->load_company_loan_details('advance')[0]->id]);
                        $account = array_shift($found_loan);

                        $employee_loan->load($account->id);
                        if($employee_loan->loan_balance_amount > 0) {
                            $employee_loan->loan_balance_amount = ($employee_loan->loan_balance_amount - $result->advance_payment);
                        }else{
                            $employee_loan->status = "COMPLETE";
                        }
                        $employee_loan->save();

                    };
                }

            }
        }

        $payroll_payment = new Payroll_payment();
        $payroll_payment->payroll_id = $payroll_id;
        $payroll_payment->loan_name = 'ADVANCE PAYMENTS';
        $payroll_payment->created_by = $this->session->userdata('employee_id');
        $payroll_payment->save();

    }

    public function payroll_heslb_payment_repay()
    {
        $payroll_id = $this->input->post('payroll_id');

        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $this->load->model(['payment_voucher','payment_voucher_item','employee_account','employee','employee_loan','payroll_payment']);

        if(!$this->chek_heslb_account()){
            $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "ACCOUNT PAYABLE"';
            $query = $this->db->query($sql);
            $group = $query->result();

            if($group){
                $this->load->model('account');
                $new_account = new Account();
                $new_account->account_name = "HESLB ACCOUNT";
                $new_account->account_group_id = $group[0]->account_group_id;
                $new_account->opening_balance = 0;
                $new_account->description = 'HESLB Account Payable';
                $new_account->save();
            }

        }

        foreach ($results as $result) {

            if($result->heslb_loan_repay > 0) {

                $employee = new Employee();
                $employee->load($result->employee_id);

                $payment = new Payment_voucher();
                $payment->payment_date = $this->input->post('received_date');
                $payment->reference = 'HESLB Loan Payment for ' . strtoupper($employee->full_name()) . ' Payroll no: ' . add_leading_zeros($result->payroll_id);
                $payment->credit_account_id = $this->input->post('cr_account');

                $employee_account = $this->employee_account->get(1, 0, ['account_id' => $this->input->post('dr_account')]);
                $found_account = array_shift($employee_account);
                $employee = new Employee();
                $employee->load($found_account->employee_id);

                $payment->payee = $employee->full_name();
                $payment->currency_id = 1;
                $payment->exchange_rate = 1;
                $payment->vat_percentage = 0;
                $payment->remarks = $this->input->post('coments');
                $payment->employee_id = $this->session->userdata('employee_id');

                if ($payment->save()) {
                    $voucher = $this->payment_voucher->get(1, 0, '', 'payment_voucher_id DESC');
                    $found_voucher = array_shift($voucher);

                    $voucher_item = new Payment_voucher_item();
                    $voucher_item->payment_voucher_id = $found_voucher->payment_voucher_id;
                    $voucher_item->debit_account_id = $this->chek_heslb_account()[0]->account_id;
                    $voucher_item->amount = $result->heslb_loan_repay;
                    $voucher_item->vat_amount = 0;
                    $voucher_item->description = 'HESLB Loan Payment for ' . strtoupper($employee->full_name()) . 'Payroll no: ' . add_leading_zeros($result->payroll_id);
                    if ($voucher_item->save()) {

                        $sql = 'SELECT * FROM accounts 
                       LEFT JOIN employee_accounts ON employee_accounts.account_id = accounts.account_id
                       WHERE employee_accounts.employee_id = ' . $result->employee_id . ' AND accounts.account_name LIKE "%loan%"';
                        $query = $this->db->query($sql);
                        $account_found = $query->result();

                        $employee_loan = new Employee_loan();
//
//                        $found_loan = $this->employee_loan->get(1, 0, ['employee_id' => $result->employee_id,
//                            'loan_account_id' => $account_found[0]->account_id]);
//                        $account = array_shift($found_loan);

                        $found_loan = $this->employee_loan->get(1,0,['employee_id' => $result->employee_id,
                            'loan_account_id' => $account_found[0]->account_id, 'loan_id' => $this->load_company_loan_details('heslb')[0]->id]);
                        $account = array_shift($found_loan);


                        $employee_loan->load($account->id);
                        if($employee_loan->loan_balance_amount > 0) {
                            $employee_loan->loan_balance_amount = ($employee_loan->loan_balance_amount - $result->company_loan_repay);
                        }else{
                            $employee_loan->status = "COMPLETE";
                        }
                        $employee_loan->save();
                    };
                };
            }
        }

        $payroll_payment = new Payroll_payment();
        $payroll_payment->payroll_id = $payroll_id;
        $payroll_payment->loan_name = 'HESLB PAYMENTS';
        $payroll_payment->created_by = $this->session->userdata('employee_id');
        $payroll_payment->save();

    }

    public function payroll_heslb_preview()
    {
        $payroll_id = $this->input->post('payroll_id');
        $loan_name = $this->input->post('loan_name');

        $this->load->model(['employee', 'department', 'payroll']);

        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        foreach ($results as $result){
            $employee = new Employee();
            $employee->load($result->employee_id);

            if(($loan_name == 'heslb' && $result->heslb_loan_repay > 0) || ($loan_name == 'company' && $result->company_loan_repay > 0) || ($loan_name == 'advance' && $result->advance_payment > 0)){
                $data['employee_info'][] = [
                    'employee_name' => strtoupper($employee->full_name()),
                    'csee_number' => '-',
                    'total_loan' => $loan_name == 'heslb' ? $result->heslb_loan : ($loan_name == 'company' ? $result->company_loan : 0),
                    'loan_repay' => $loan_name == 'heslb' ? $result->heslb_loan_repay : ($loan_name == 'company' ? $result->company_loan_repay : $result->advance_payment),
                    'loan_balance' => $loan_name == 'heslb' ? $result->heslb_loan_balance : ($loan_name == 'company' ? $result->company_loan_balance : 0),
                ];
            }
        }

        $data['loan_name'] = $loan_name;

        $payroll = new Payroll();
        $payroll->load($payroll_id);
        $departments = new Department();
        $departments->load($payroll->department_id);

        $employee = new Employee();

        $data['departments'] = $departments;
        $data['payroll_date'] = $payroll->payroll_for;
        $data['payroll_id'] = $payroll_id;
        $data['employee'] = $employee;

        $html = $this->load->view('employees/employee_loans/employee_payroll_loan_list_sheet', $data, true);

        //load mPDF library
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();
        $pdf->AddPage(
            '', // L - landscape, P - portrait
            '', '', '', '',
            15, // margin_left
            15, // margin right
            15, // margin top
            15, // margin bottom
            9, // margin header
            6, '', '', '', '', '', '', '', '', '', 'A4-P'
        ); // margin footer
        $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
        $pdf->SetFooter($footercontents);
        $pdf->WriteHTML($html);
        //$this->mpdf->Output($file_name, 'D'); // download force

        $pdf->Output('Payroll.pdf', 'I'); // view in the explorer
    }

    public function payroll_company_loan_repay()
    {
        $payroll_id = $this->input->post('payroll_id');

        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $this->load->model(['receipt','receipt_item','employee','employee_account','employee_loan','payroll_payment']);

        foreach ($results as $result) {

            $sql = 'SELECT * FROM accounts 
                       LEFT JOIN employee_accounts ON employee_accounts.account_id = accounts.account_id
                       WHERE employee_accounts.employee_id = '.$result->employee_id.' AND accounts.account_name LIKE "%loan%"';
            $query = $this->db->query($sql);
            $account_found = $query->result();
            $employee_loan_account_id = $account_found[0]->account_id;


            $employee = new Employee();
            $employee->load($result->employee_id);

            if($result->company_loan_repay > 0) {
                $receipt = new Receipt();
                $receipt->debit_account_id = $this->input->post('dr_account');
                $receipt->credit_account_id = $employee_loan_account_id;
                $receipt->receipt_date = $this->input->post('received_date');
                $receipt->reference = 'Company Loan Payment for '.strtoupper($employee->full_name()).'Payroll no: '.add_leading_zeros($result->payroll_id);
                $receipt->currency_id = 1;
                $receipt->exchange_rate = 1;
                $receipt->withholding_tax = 0;
                $receipt->remarks = $this->input->post('coments');
                $receipt->created_by = $this->session->userdata('employee_id');

                if ($receipt->save()) {
                    $last_receipt = $this->receipt->get(1, 0, '', 'id DESC');
                    $found_receipt = array_shift($last_receipt);
                    $receipt_item = new Receipt_item();
                    $receipt_item->receipt_id = $found_receipt->id;
                    $receipt_item->amount = $result->company_loan_repay;
                    $receipt_item->remarks = 'Company loan Payment for '.strtoupper($employee->full_name()).' Payroll no: '.add_leading_zeros($result->payroll_id);
                    if ($receipt_item->save()) {
                        $employee_loan = new Employee_loan();

                        $found_loan = $this->employee_loan->get(1,0,['employee_id' => $result->employee_id,
                            'loan_account_id' => $employee_loan_account_id, 'loan_id' => $this->load_company_loan_details('company')[0]->id]);
                        $account = array_shift($found_loan);

                        $employee_loan->load($account->id);
                        if($employee_loan->loan_balance_amount > 0) {
                            $employee_loan->loan_balance_amount = ($employee_loan->loan_balance_amount - $result->company_loan_repay);
                        }else{
                            $employee_loan->status = "COMPLETE";
                        }
                        $employee_loan->save();

                    };


                }

            }
        }

        $payroll_payment = new Payroll_payment();
        $payroll_payment->payroll_id = $payroll_id;
        $payroll_payment->loan_name = 'COMPANY LOAN PAYMENTS';
        $payroll_payment->created_by = $this->session->userdata('employee_id');
        $payroll_payment->save();
    }

    public function load_accounts()
    {
        $this->load->model('account');
        if($this->input->post('data_to_sent') == 'heslb'){

            if(!$this->chek_heslb_account()){
                $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "ACCOUNT PAYABLE"';
                $query = $this->db->query($sql);
                $group = $query->result();

                if($group){
                    $this->load->model('account');
                    $new_account = new Account();
                    $new_account->account_name = "HESLB ACCOUNT";
                    $new_account->account_group_id = $group[0]->account_group_id;
                    $new_account->opening_balance = 0;
                    $new_account->description = 'HESLB Account Payable';
                    $new_account->save();
                }
            }

            $options[''] = '&nbsp;';
            $options[$this->chek_heslb_account()[0]->account_id] = $this->chek_heslb_account()[0]->account_name;
            echo form_dropdown('dr_account', $options, '', ' class="form-control searchable" ');


        }else if ($this->input->post('data_to_sent') == 'TRUE'){
            echo form_dropdown('dr_account', $this->account->dropdown_options(['CASH','BANK']), '', ' class="form-control searchable" ');
        }else if($this->input->post('data_to_sent') == 'cash-bank'){
            echo form_dropdown('cr_account', $this->account->dropdown_options(['CASH','BANK']), '', ' class="form-control searchable" ');
        }else{
            $account_name = $this->input->post('data_to_sent');
            $sql = 'SELECT * FROM accounts WHERE account_name LIKE "%'.$account_name.'%"';
            $query = $this->db->query($sql);
            $results = $query->result();

            if($results){
                $options[''] = '&nbsp;';
                foreach ($results as $result){
                    $options[$result->account_id] = strtoupper($result->account_name);
                }
                echo form_dropdown('dr_account', $options, '', ' class="form-control searchable" ');
            }else{
                echo form_dropdown('dr_account', $this->account->dropdown_options(['ACCOUNT PAYABLE']), '', ' class="form-control searchable" ');
            }
        }
    }

    public function load_company_loan_details($loan_for)
    {
        $sql = 'SELECT * FROM loans WHERE loan_type LIKE "%'.$loan_for.'%"';
        $query = $this->db->query($sql);
        return $query->result();

    }

    public function reject_payroll()
    {
        $this->load->model('payroll');
        $payroll = new Payroll();
        $payroll->load($this->input->post('payroll_id'));
        $payroll->status = $this->input->post('status');
        if($payroll->save()){
            $this->load->model('rejected_payroll');
            $rejected_payroll = new Rejected_payroll();
            $rejected_payroll->payroll_id = $this->input->post('payroll_id');
            $rejected_payroll->current_level = $this->input->post('current_level');
            $rejected_payroll->reject_coments = $this->input->post('coments');
            $rejected_payroll->status = $this->input->post('status');
            $rejected_payroll->created_by = $this->session->userdata('employee_id');
            $rejected_payroll->save();
        };

    }

    public function load_payroll_department()
    {
        $this->load->model(['department','payroll']);
        $payroll_id = $this->input->post('payroll_id');

        $payroll = new Payroll();
        $payroll->load($payroll_id);
        $departments = new Department();
        $departments->load($payroll->department_id);

        $data['departments'] = $departments;
        $data['payroll_date'] = $payroll->payroll_for;
        $data['type'] = $this->input->post('type');

        echo $this->load->view('payroll/payroll_department_head', $data, 'true');

    }

    ///////// deductions

    public function payroll_deductions_list()
    {
        $this->load->model(['employee','department','payroll','payroll_payment']);
        $payroll_id = $this->input->post('payroll_id');

        $sql = 'SELECT id, employee_id, title, location, basic_salary, gross_salary, deducted_nssf, taxable_amount, paye FROM payroll_employee_basic_info
                WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $data = [];
        foreach ($results as $item){

            $employee = new Employee();
            $employee->load($item->employee_id);

            $data['employee_info'][$item->id] = [
                'id' => $item->id,
                'emplyee_id' => $item->employee_id,
                'employee_full_name' => strtoupper($employee->full_name()),
                'title' => $item->title,
                'location' => $item->location,
                'basic_salary' => $item->basic_salary,
                'gross_salary' => $item->gross_salary,
                'deducted_nssf' => $item->deducted_nssf,
                'taxable_amount' => $item->taxable_amount,
                'paye' => $item->paye
            ];

            $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' AND employee_id = '.$item->employee_id;
            $query = $this->db->query($sql);
            $results2 = $query->result();

            foreach ($results2 as $item2){
                $data['employee_info'][$item->id][$item2->deduction_name] = $item2->deduction_amount;
            }

        }


        $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' GROUP BY deduction_name';
        $query = $this->db->query($sql);
        $results3 = $query->result();

        $data['all_deductions'] = $results3;

        $payroll = new Payroll();
        $payroll->load($payroll_id);
        $departments = new Department();
        $departments->load($payroll->department_id);

        $payroll_payment = new Payroll_payment();

        $data['departments'] = $departments;
        $data['payroll_date'] = $payroll->payroll_for;
        $data['payroll_payment'] = $payroll_payment;
        $data['payroll_id'] = $payroll_id;

        echo $this->load->view('employees/employee_deductions/employee_payroll_deduction_table', $data, 'true');

    }

    public function payroll_deduction_payments()
    {
        $payroll_id = $this->input->post('payroll_id');
        $submission_date = $this->input->post('paid_date');
        $dr_account = $this->input->post('dr_account');
        $cr_account = $this->input->post('cr_account');
        $coments = $this->input->post('coments');
        $deduction_name = $this->input->post('deduction_name');

        $sql = 'SELECT id, employee_id, paye FROM payroll_employee_basic_info
                WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $data = [];
        foreach ($results as $item){

            $employee = new Employee();
            $employee->load($item->employee_id);

            $data['employee_info'][$item->id] = [
                'id' => $item->id,
                'employee_name' => $employee->full_name(),
                'paye' => $item->paye
            ];

            $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' AND employee_id = '.$item->employee_id;
            $query = $this->db->query($sql);
            $results2 = $query->result();

            foreach ($results2 as $item2){
                $data['employee_info'][$item->id][$item2->deduction_name] = $item2->deduction_amount;
            }
        }

        ////inspect_object($data['employee_info']);

        $this->load->model(['payment_voucher','payment_voucher_item','employee','department','payroll','payroll_payment','payroll_payment_voucher']);

        $payroll = new Payroll();
        $payroll->load($payroll_id);

        $department = new Department();
        $department->load($payroll->department_id);

        $payroll_date = strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll->payroll_for)))->format('F')) . ' ' . date('Y', strtotime($payroll->payroll_for));

        $payment = new Payment_voucher();
        $payment->payment_date = $submission_date;
        $payment->reference = strtoupper($deduction_name).'  PAYMENT FROM '. strtoupper($department->department_name).' PAYROLL OF '.$payroll_date;
        $payment->credit_account_id = $cr_account;
        $payment->payee = strtoupper($deduction_name);
        $payment->currency_id = 1;
        $payment->exchange_rate = 1;
        $payment->vat_percentage = 0;
        $payment->remarks = $coments;
        $payment->employee_id = $this->session->userdata('employee_id');

        if($payment->save()){

            $voucher = $this->payment_voucher->get(1, 0, '', 'payment_voucher_id DESC');
            $found_voucher = array_shift($voucher);

            foreach ($data['employee_info'] as $emp_data) {
                $debit_amount  =  array_key_exists($deduction_name, $emp_data) ? $emp_data[$deduction_name] : 0;
                if($debit_amount > 0) {
                    $employee = new Employee();
                    $employee->load($item->employee_id);

                    $voucher_item = new Payment_voucher_item();
                    $voucher_item->payment_voucher_id = $found_voucher->payment_voucher_id;
                    $voucher_item->debit_account_id = $dr_account;
                    $voucher_item->amount = $debit_amount;
                    $voucher_item->vat_amount = 0;
                    $voucher_item->description = strtoupper($deduction_name) . ' Payment for ' . strtoupper($emp_data['employee_name']) . ' FROM ' . strtoupper($department->department_name) . ' PAYROLL OF ' . $payroll_date;
                    $voucher_item->save();
                }
            }

            $payroll_payment = new Payroll_payment();
            $payroll_payment->payroll_id = $payroll_id;
            $payroll_payment->loan_name = strtoupper($deduction_name);
            $payroll_payment->created_by = $this->session->userdata('employee_id');
            $payroll_payment->save();
        }

        $payroll_payment_voucher = new Payroll_payment_voucher();
        $payroll_payment_voucher->payroll_id = $payroll_id;
        $payroll_payment_voucher->payment_voucher_id = $found_voucher->payment_voucher_id;
        $payroll_payment_voucher->payment_name = $deduction_name;
        $payroll_payment_voucher->created_by = $this->session->userdata('employee_id');
        $payroll_payment_voucher->save();

    }

    public function payroll_deduction_preview()
    {
        $this->load->model(['payroll', 'department', 'employee','employee_ssf']);

        $payroll_id = $this->input->post('payroll_id');
        $payment_name = $this->input->post('deduction_name');
        $sql = 'SELECT * FROM payroll_payment_vouchers WHERE payroll_id = '.$payroll_id.' AND payment_name = "'.$payment_name.'"';
        $query = $this->db->query($sql);
        $results = $query->result();

        if($results){
            $data['payment_info'] = [];
            foreach ($results as $result){
                $sql = 'SELECT * FROM payment_voucher_items WHERE payment_voucher_id = '.$result->payment_voucher_id;
                $query = $this->db->query($sql);
                $data['payment_info'] = $query->result();
            }
            $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
            $query = $this->db->query($sql);
            $data['sallary_info'] = $query->result();
            $data['deduction_name'] = $payment_name;

            $payroll = new Payroll();
            $payroll->load($payroll_id);
            $departments = new Department();
            $departments->load($payroll->department_id);

            $employee = new Employee();
            $employee_ssf = new Employee_ssf();

            $sql = 'SELECT * FROM ssfs WHERE ssf_name LIKE "%'.$payment_name.'%"';
            $query = $this->db->query($sql);
            $retun_data = $query->result();
            if($retun_data){
                $data['ssf'] = $retun_data;
            }else{
                $data['ssf'] = false;
            }



            $data['departments'] = $departments;
            $data['payroll_date'] = $payroll->payroll_for;
            $data['payroll_id'] = $payroll_id;
            $data['employee'] = $employee;
            $data['employee_ssf'] = $employee_ssf;

            $html = $this->load->view('employees/employee_deductions/employee_deduction_contributtor_table', $data, true);

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                '', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6, '', '', '', '', '', '', '', '', '', 'A4-P'
            ); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('Payroll.pdf', 'I'); // view in the explorer


        }
    }

    ////// net payable
    public function payroll_netpayable_list()
    {
        $this->load->model(['employee','department','payroll','payroll_payment']);
        $payroll_id = $this->input->post('payroll_id');

        $sql = 'SELECT id, employee_id, title, location, basic_salary, gross_salary, deducted_nssf, taxable_amount, paye, net_pay FROM payroll_employee_basic_info
                WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $data = [];
        foreach ($results as $item){

            $employee = new Employee();
            $employee->load($item->employee_id);

            $data['employee_info'][$item->id] = [
                'id' => $item->id,
                'emplyee_id' => $item->employee_id,
                'employee_full_name' => strtoupper($employee->full_name()),
                'title' => $item->title,
                'location' => $item->location,
                'net_pay' => $item->net_pay
            ];

        }

        $payroll = new Payroll();
        $payroll->load($payroll_id);
        $departments = new Department();
        $departments->load($payroll->department_id);

        $payroll_payment = new Payroll_payment();

        $data['departments'] = $departments;
        $data['payroll_date'] = $payroll->payroll_for;
        $data['payroll_payment'] = $payroll_payment;
        $data['payroll_id'] = $payroll_id;

        echo $this->load->view('employees/employee_net_payable/employee_netpayable_table', $data, 'true');

    }

    public function payroll_netpayable_payments()
    {
        $payroll_id = $this->input->post('payroll_id');
        $submission_date = $this->input->post('paid_date');
        $cr_account = $this->input->post('cr_account');
        $coments = $this->input->post('coments');

        $sql = 'SELECT id, employee_id, net_pay FROM payroll_employee_basic_info
                WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        ////inspect_object($data['employee_info']);

        $this->load->model(['payment_voucher','payment_voucher_item','employee','department','payroll','payroll_payment','payroll_payment_voucher']);

        $payroll = new Payroll();
        $payroll->load($payroll_id);

        $department = new Department();
        $department->load($payroll->department_id);

        $payroll_date = strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll->payroll_for)))->format('F')) . ' ' . date('Y', strtotime($payroll->payroll_for));

        $payment = new Payment_voucher();
        $payment->payment_date = $submission_date;
        $payment->reference = 'NET PAYABLE PAYMENT FROM '. strtoupper($department->department_name).' PAYROLL OF '.$payroll_date;
        $payment->credit_account_id = $cr_account;
        $payment->payee = 'NET PAYABLE';
        $payment->currency_id = 1;
        $payment->exchange_rate = 1;
        $payment->vat_percentage = 0;
        $payment->remarks = $coments;
        $payment->employee_id = $this->session->userdata('employee_id');

        if($payment->save()){

            $voucher = $this->payment_voucher->get(1, 0, '', 'payment_voucher_id DESC');
            $found_voucher = array_shift($voucher);

            foreach ($results as $item){

                $employee = new Employee();
                $employee->load($item->employee_id);

                $sql = 'SELECT * FROM accounts 
                       LEFT JOIN employee_accounts ON employee_accounts.account_id = accounts.account_id
                       WHERE employee_accounts.employee_id = '.$item->employee_id.' AND accounts.account_name LIKE "%salary%"';
                $query = $this->db->query($sql);
                $account_found = $query->result();
                $employee_salary_account_id = $account_found[0]->account_id;

                $voucher_item = new Payment_voucher_item();
                $voucher_item->payment_voucher_id = $found_voucher->payment_voucher_id;
                $voucher_item->debit_account_id = $employee_salary_account_id;
                $voucher_item->amount = $item->net_pay;
                $voucher_item->vat_amount = 0;
                $voucher_item->description = 'NET PAYABLE PAYMENT ' . strtoupper($employee->full_name()) .' FROM '. strtoupper($department->department_name).' PAYROLL OF '.$payroll_date;
                $voucher_item->save();

            }

            $payroll_payment = new Payroll_payment();
            $payroll_payment->payroll_id = $payroll_id;
            $payroll_payment->loan_name = strtoupper('net payable');
            $payroll_payment->created_by = $this->session->userdata('employee_id');
            $payroll_payment->save();

            $payroll_payment_voucher = new Payroll_payment_voucher();
            $payroll_payment_voucher->payroll_id = $payroll_id;
            $payroll_payment_voucher->payment_voucher_id = $found_voucher->payment_voucher_id;
            $payroll_payment_voucher->payment_name ='net payable';
            $payroll_payment_voucher->created_by = $this->session->userdata('employee_id');
            $payroll_payment_voucher->save();
        }
    }

    public function payroll_netpayable_preview()
    {
        $this->load->model(['payroll', 'department', 'employee','employee_ssf']);

        $payroll_id = $this->input->post('payroll_id');
        $payment_name = 'net payable';
        $sql = 'SELECT * FROM payroll_payment_vouchers WHERE payroll_id = '.$payroll_id.' AND payment_name = "'.$payment_name.'"';
        $query = $this->db->query($sql);
        $results = $query->result();

        if($results){
            $data['payment_info'] = [];
            foreach ($results as $result){
                $sql = 'SELECT * FROM payment_voucher_items WHERE payment_voucher_id = '.$result->payment_voucher_id;
                $query = $this->db->query($sql);
                $data['payment_info'] = $query->result();
            }
            $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
            $query = $this->db->query($sql);
            $data['sallary_info'] = $query->result();

            $payroll = new Payroll();
            $payroll->load($payroll_id);
            $departments = new Department();
            $departments->load($payroll->department_id);

            $employee = new Employee();

            $data['departments'] = $departments;
            $data['payroll_date'] = $payroll->payroll_for;
            $data['payroll_id'] = $payroll_id;
            $data['employee'] = $employee;

            $html = $this->load->view('employees/employee_net_payable/employee_net_payable_sheet', $data, true);

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                '', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6, '', '', '', '', '', '', '', '', '', 'A4-P'
            ); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('Payroll.pdf', 'I'); // view in the explorer


        }
    }

    public function check_if_there_is_loan_to_pay($payroll_id, $loan_name)
    {
        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $sum = 0;
        foreach ($results as $result){
            $sum += $result->$loan_name;
        }
        return $sum > 0 ? true : false;
    }

    //// salary slip
    public function payroll_salary_slip_table()
    {

        $payroll_id = $this->input->post('payroll_id');
        $employee_ids = $this->input->post('employee_checkbox') ? $this->input->post('employee_checkbox') : false;
        $print = $this->input->post('print') ? true : false;

        if ($employee_ids) {
            $arry_length = sizeof($employee_ids);
            $count = 0;
            $condition = ' AND (';
            foreach ($employee_ids as $id){
                if($count > 0 && $count < $arry_length){
                    $condition .= ' OR employee_id = '.$id;
                }else{
                    $condition .= ' employee_id = '.$id;
                }
                $count++;
            }
            $condition .= ' )';
        }else{
            $condition = '';
        }

        $this->load->model(['employee', 'payroll', 'department']);
        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id.$condition;
        $query = $this->db->query($sql);
        $results = $query->result();

        foreach($results as $result){
            $employee = new Employee();
            $employee->load($result->employee_id);
            $total_deductions = $result->deducted_nssf + $result->paye + $result->heslb_loan_repay + $result->company_loan_repay + $result->advance_payment;
            $total_earnings = $result->basic_salary;

            $data['employee_info'][$result->employee_id] = [
                'employee_id' => $result->employee_id,
                'employee_name' => $employee->full_name(),
                'title' => $result->title,
                'location' => $result->location,
                'basic_salary' => $result->basic_salary,
                'gross_salary' => $result->gross_salary,
                'deducted_nssf' => $result->deducted_nssf,
                'taxable_amount' => $result->taxable_amount,
                'paye' => $result->paye,
                'heslb_loan' => $result->heslb_loan,
                'heslb_loan_repay' => $result->heslb_loan_repay,
                'heslb_loan_balance' => $result->heslb_loan_balance,
                'company_loan' => $result->company_loan,
                'company_loan_repay' => $result->company_loan_repay,
                'company_loan_balance' => $result->company_loan_balance,
                'advance_payment' => $result->advance_payment,
                'net_pay' => $result->net_pay,
                'total_deductions' => $total_deductions
            ];


            $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id.' AND employee_id = '.$result->employee_id;
            $query = $this->db->query($sql);
            $found_allowances = $query->result();

            foreach ($found_allowances as $allowance){
                $data['employee_info'][$result->employee_id][$allowance->allowance_name] = $allowance->allowance_amount;
                $total_earnings += $allowance->allowance_amount;
                $data['employee_info'][$result->employee_id]['total_earnings'] = $total_earnings;
            }

            $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' AND employee_id = '.$result->employee_id;
            $query = $this->db->query($sql);
            $found_deductions = $query->result();

            foreach ($found_deductions as $deduction){
                $data['employee_info'][$result->employee_id][$deduction->deduction_name] = $deduction->deduction_amount;
            }

        }

        $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id.' GROUP BY allowance_name';
        $query = $this->db->query($sql);
        $data['all_allowances'] = $query->result();

        $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' GROUP BY deduction_name ';
        $query = $this->db->query($sql);
        $data['all_deductions'] = $query->result();

        $sql = 'SELECT * FROM payroll_payment_vouchers
                LEFT JOIN payment_vouchers ON payroll_payment_vouchers.payment_voucher_id = payment_vouchers.payment_voucher_id
                WHERE payment_name  = "net payable"';
        $query = $this->db->query($sql);
        $data['payment_date'] = $query->result()? $query->result()[0]->payment_date : false ;

        $payroll = new Payroll();
        $payroll->load($payroll_id);
        $departments = new Department();
        $departments->load($payroll->department_id);

        $employee = new Employee();

        $data['departments'] = $departments;
        $data['payroll_date'] = $payroll->payroll_for;
        $data['payroll_id'] = $payroll_id;
        $data['employees'] = $employee;
        $data['payroll_id'] = $payroll_id;

        if($print){

            $html = $this->load->view('employees/employee_salary_slip/employee_salary_slip_sheet', $data, true);

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                '', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6, '', '', '', '', '', '', '', '', '', 'A4-P'
            ); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('Payroll.pdf', 'I'); // view in the explorer

        }else{
            echo $this->load->view('employees/employee_salary_slip/employee_salary_slip_table', $data, true);
        }

    }

    public function check_special_levels()
    {
        $this->load->model('approval_module');
        $approval_modules = $this->approval_module->get(0,0,' id = 4 ');
        $special_level_dropdowns = [];

        foreach ($approval_modules as $approval_module){
            $special_level_dropdowns = $approval_module->to_forward_level_options(true);
        }
        echo form_dropdown('special_level_approval', stringfy_dropdown_options($special_level_dropdowns), '', ' class="form-control searchable" ');
    }





}

