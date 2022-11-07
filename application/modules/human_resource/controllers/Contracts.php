<?php

class Contracts extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->load->model('Employee_contract');
    }

    public function index()
    {
        $this->load->model('Employee_contract');
        $data['title'] = 'Employee contracts';

        $this->load->view('human_resources/employees/employee_list', $data);
    }

    public function employee_contract_list($employee_id = 0)
    {
        $this->load->model('Employee_contract');
        $posted_params = dataTable_post_params();
        echo $this->Employee_contract->employee_contracts_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $employee_id);
    }
    public function employee_ssf_list($employee_id = 0)
    {
        $this->load->model('Employee_ssf');
        $posted_params = dataTable_post_params();
        echo $this->Employee_ssf->employee_ssf_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $employee_id);
    }
    public function employee_bank_list($employee_id = 0)
    {
        $this->load->model('Employee_bank');
        $posted_params = dataTable_post_params();
        echo $this->Employee_bank->employee_bank_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $employee_id);
    }

    public function save_employee_contract()
    {

        $this->load->model('Employee_contract');
        $employee_contract = new Employee_contract();
        $edit = $employee_contract->load($this->input->post('employee_contract_id'));
        $employee_contract->employee_id = $this->input->post('employee_id');
        $employee_contract->start_date = $this->input->post('start_date');
        $employee_contract->end_date = $this->input->post('end_date');

        if ($employee_contract->status == 'active') {
            $employee_contract->status = 'active';
        } else if ($employee_contract->status == 'inactive') {
            $employee_contract->status = 'inactive';
        } else {
            $employee_contract->status = 'active';
        }


        $employee_contract->created_by = $this->session->userdata('employee_id');
        if ($employee_contract->save()) {
            //save employee_salary
            $this->load->model('Employee_salary');
            $employee_salary = new Employee_salary();
            $edit = $employee_salary->load($this->input->post('employee_salary_id'));
            $employee_salary->employee_contract_id = $employee_contract->{$employee_contract::DB_TABLE_PK};
            $employee_salary->payroll_no = $this->input->post('payroll_no');
            $employee_salary->salary = $this->input->post('salary');
            $employee_salary->currency_id = $this->input->post('currency');
            $employee_salary->payment_mode = $this->input->post('payment_mode');
            $employee_salary->tax_details = $this->input->post('tax_details');
            $employee_salary->ssf_contribution = $this->input->post('ssf_contribution');
            $employee_salary->start_date = $this->input->post('start_date');
            $employee_salary->end_date = $this->input->post('end_date');
            $employee_salary->created_by = $this->session->userdata('employee_id');
            //save employee_designation
            if ($employee_salary->save()) {
                $this->load->model('Employee_designation');
                $Employee_designation = new Employee_designation();
                $edit = $Employee_designation->load($this->input->post('employee_designation_id'));
                $Employee_designation->employee_contract_id = $employee_contract->{$employee_contract::DB_TABLE_PK};
                $Employee_designation->department_id = $this->input->post('department_id');
                $Employee_designation->branch_id = $this->input->post('branch_id');
                $Employee_designation->job_position_id = $this->input->post('job_position_id');
                $Employee_designation->start_date = $this->input->post('start_date');
                $Employee_designation->end_date = $this->input->post('end_date');
                $Employee_designation->created_by = $this->session->userdata('employee_id');
                //update employee_position
                if ($Employee_designation->save()) {
                    $this->load->model('Employee');
                    $Employee_position = new Employee();
                    $edit = $Employee_position->load($this->input->post('employee_id'));
                    $Employee_position->department_id = $this->input->post('department_id');
                    $Employee_position->position_id = $this->input->post('job_position_id');
                    // saving employee allowances
                    if ($Employee_position->save()) {
                        $this->load->model('Employee_allowance');
                        $allowance_ids = $this->input->post('allowances_ids');
                        if (!empty($allowance_ids)) {
                            foreach ($allowance_ids as $index => $item) {
                                $allowance = new Employee_allowance();
                                $edit = $allowance->load($this->input->post('employee_allowance_ids')[$index]);
                                if ($edit) {
                                    $allowance->clear_items();
                                }
                                $allowance->allowance_id = $item;
                                $allowance->allowance_amount = $this->input->post('allowances_amounts')[$index];
                                $allowance->employee_id = $this->input->post('employee_id');
                                $allowance->created_by = $this->session->userdata('employee_id');
                                $allowance->save();
                            }
                        }
                    };
                }
            }

            $this->load->model(['account', 'employee', 'employee_account']);
            $sql = 'SELECT employee_accounts.account_id FROM employee_accounts
                    LEFT JOIN accounts ON employee_accounts.account_id = accounts.account_id
                    WHERE employee_id = ' . $employee_contract->employee_id . ' AND account_name LIKE "%salary%" LIMIT 1';
            $query = $this->db->query($sql);
            $account_id = $query->num_rows() > 0 ? $query->row()->account_id : false;

            if (!$account_id) {
                $employee = new Employee();
                $employee->load($this->input->post('employee_id'));
                $new_account = new Account();
                $new_account->account_name = strtoupper($employee->full_name()) . " SALARY ACCOUNT";
                $sql = 'SELECT account_group_id FROM account_groups WHERE group_name = "ACCOUNT PAYABLE" LIMIT 1';
                $query = $this->db->query($sql);
                $group = $query->row()->account_group_id;
                $new_account->account_group_id = $group;
                $new_account->opening_balance = 0;
                $new_account->description = strtoupper($employee->full_name()) . " SALARY ACCOUNT";
                if ($new_account->save()) {
                    $employee_account = new Employee_account();
                    $employee_account->account_id = $new_account->{$new_account::DB_TABLE_PK};
                    $employee_account->employee_id = $this->input->post('employee_id');
                    $employee_account->created_by = $this->session->userdata('employee_id');
                    $employee_account->save();
                };
            }
        }
    }


    public function clear_allowance()
    {
        $this->load->model('employee_allowance');
        $employee_allowance = new Employee_allowance();
        $employee_allowance->load($this->input->post('employee_allowance_id'));
        $employee_allowance->delete();
    }

    public function save_employee_salary()
    {

        $this->load->model('Employee_salary');
        $employee_salary = new Employee_salary;
        $edit = $employee_salary->load($this->input->post('employee_salary_id'));
        $employee_salary->employee_contract_id = $this->input->post('employee_contract_id');
        $employee_salary->payroll_no = $this->input->post('payroll_no');
        $employee_salary->salary = $this->input->post('salary');
        $employee_salary->subsistance = $this->input->post('subsistance');
        $employee_salary->responsibility = $this->input->post('responsibility');
        $employee_salary->currency_id = $this->input->post('currency_id');
        $employee_salary->payment_mode = $this->input->post('payment_mode');
        $employee_salary->tax_details = $this->input->post('tax_details');
        $employee_salary->ssf_contribution = $this->input->post('ssf_contribution');
        $employee_salary->start_date = $this->input->post('start_date');
        $employee_salary->end_date = $this->input->post('end_date');
        $employee_salary->created_by = $this->session->userdata('employee_id');
        $employee_salary->save();
    }
    public function delete_employee_salary()
    {
        $this->load->model('Employee_salary');
        $Employee_salary = new Employee_salary();
        if ($Employee_salary->load($this->input->post('employee_salary_id'))) {
            $Employee_salary->delete();
        }
    }

    public function save_employee_designation()
    {
        $this->load->model('Employee_designation');
        $Employee_designation = new Employee_designation();
        $edit = $Employee_designation->load($this->input->post('employee_designation_id'));
        $Employee_designation->employee_contract_id = $this->input->post('employee_contract_id');
        $Employee_designation->department_id = $this->input->post('department_id');
        $Employee_designation->branch_id = $this->input->post('branch_id');
        $Employee_designation->job_position_id = $this->input->post('job_position_id');
        $Employee_designation->start_date = $this->input->post('start_date');
        $Employee_designation->end_date = $this->input->post('end_date');
        $Employee_designation->created_by = $this->session->userdata('employee_id');

        if ($Employee_designation->save()) {

            $this->load->model('Employee');
            $this->load->model('Employee');
            $Employee = new Employee();
            $employee_contract = $Employee_designation->employee_contract();
            $edit = $Employee->load($employee_contract->employee_id);
            $Employee->department_id = $this->input->post('department_id');
            $Employee->position_id = $this->input->post('job_position_id');
            $Employee->save();
        }
    }

    public function delete_employee_designation()
    {
        $this->load->model('Employee_designation');
        $Employee_designation = new Employee_designation();
        if ($Employee_designation->load($this->input->post('employee_designation_id'))) {
            $Employee_designation->delete();
        }
    }

    public function delete_employee_contract()
    {
        $this->load->model('Employee_contract');
        $contract = new Employee_contract();
        if ($contract->load($this->input->post('contract_id'))) {
            $contract->delete();
        }
    }

    public function close_employee_contract()
    {
        $this->load->model('Employee_contract_close');
        $Employee_contract_close = new Employee_contract_close();
        $edit = $Employee_contract_close->load($this->input->post('employee_contract_close_id'));
        $Employee_contract_close->close_date = $this->input->post('close_date');
        $Employee_contract_close->reason = $this->input->post('reason');
        $Employee_contract_close->attachment = $this->input->post('attachment');
        $Employee_contract_close->created_by = $this->session->userdata('employee_id');
        $Employee_contract_close->employee_contract_id = $this->input->post('employee_contract_id');
        if ($Employee_contract_close->save()) {
            $this->load->model('Employee_contract');
            $Employee_contract = new Employee_contract();
            $edit = $Employee_contract->load($this->input->post('employee_contract_id'));
            $Employee_contract->status = 'inactive';
            $Employee_contract->save();
        }
    }


    public function activate_employee_contract()
    {
        $this->load->model('Employee_contract');
        $contract = new Employee_contract();   //update
        $edit = $contract->load($this->input->post('contract_id'));
        $contract->status = 'active';
        if ($contract->save()) {
            $this->load->model('Employee_contract_close');     //delete
            $closed_contract = $this->Employee_contract_close->get(1, 0, ['employee_contract_id' => $this->input->post('contract_id')], ' id desc');
            $Employee_contract_close = array_shift($closed_contract);
            $Employee_contract_close->delete();
        }
    }

    public function employee_contract_details()
    {
        $this->load->model('Employee_contract');
        $Employee_contract = new Employee_contract();
        $Employee_contract->load($this->input->post('employee_contract_id'));
        $this->load->model(['department', 'job_position', 'ssf', 'Bank', 'Branch']);
        $data['job_position_options'] = $this->job_position->job_position_options();
        $data['department_options'] = $this->department->department_options();
        $data['ssf_options'] = $this->ssf->ssf_options();
        $data['bank_options'] = $this->Bank->bank_dropdown_options();
        $data['branch_options'] = $this->Branch->branch_options();
        $data['employee_contract'] = $Employee_contract;
        $return['content'] = $this->load->view('contracts/employee_contract_details', $data, true);
        echo json_encode($return);
    }

    public function employee_contract_salary_list()
    {
        $this->load->model('Employee_contract');
        $Employee_contract = new Employee_contract();
        if ($Employee_contract->load($this->input->post('employee_contract_id'))) {
            $data['employee_salary_list'] = $Employee_contract->employee_contract_salary_list();
            $data['Employee_contract'] = $Employee_contract;
            $return['salary_table'] = $this->load->view('contracts/employee_contract_salary_list', $data, true);
            echo json_encode($return);
        }
    }

    public function employee_contract_designation_list()
    {
        $this->load->model('Employee_contract');
        $Employee_contract = new Employee_contract();
        $this->load->model(['department', 'job_position', 'ssf', 'Bank', 'Branch']);
        $data['job_position_options'] = $this->job_position->job_position_options();
        $data['department_options'] = $this->department->department_options();
        $data['ssf_options'] = $this->ssf->ssf_options();
        $data['bank_options'] = $this->Bank->bank_dropdown_options();
        $data['branch_options'] = $this->Branch->branch_options();
        if ($Employee_contract->load($this->input->post('employee_contract_id'))) {
            $data['employee_designation_list'] = $Employee_contract->employee_contract_designation_list();
            $data['Employee_contract'] = $Employee_contract;
            $return['designation_table'] = $this->load->view('contracts/employee_contract_designation_list', $data, true);
            echo json_encode($return);
        }
    }
    // EMPLOYEE SSF

    public function save_employee_ssf()
    {
        $this->load->model('Employee_ssf');
        $employee_ssf = new Employee_ssf();
        $edit = $employee_ssf->load($this->input->post('employee_ssf_id'));
        $employee_ssf->ssf_id = $this->input->post('ssf_id');
        $employee_ssf->ssf_no = $this->input->post('ssf_no');
        $employee_ssf->start_date = $this->input->post('start_date');
        $employee_ssf->employee_id = $this->input->post('employee_id');
        $employee_ssf->created_by = $this->session->userdata('employee_id');
        $employee_ssf->created_at = date('Y-m-d H:i:s');
        $employee_ssf->save();
    }

    public function delete_employee_ssf()
    {
        $this->load->model('Employee_ssf');
        $employee_ssf = new Employee_ssf();
        if ($employee_ssf->load($this->input->post('employee_ssf_id'))) {
            $employee_ssf->delete();
        }
    }

    // EMPLOYEE SBANK

    public function save_employee_bank()
    {
        $this->load->model('Employee_bank');
        $employee_bank = new Employee_bank();
        $edit = $employee_bank->load($this->input->post('employee_bank_id'));
        $employee_bank->bank_id = $this->input->post('bank_id');
        $employee_bank->account_no = $this->input->post('account_no');
        $employee_bank->branch = $this->input->post('branch');
        $employee_bank->swift_code = $this->input->post('swift_code');
        $employee_bank->start_date = $this->input->post('start_date');
        $employee_bank->employee_id = $this->input->post('employee_id');
        $employee_bank->created_by = $this->session->userdata('employee_id');
        $employee_bank->save();
    }

    public function delete_employee_bank()
    {
        $this->load->model('Employee_bank');
        $employee_bank = new Employee_bank();
        if ($employee_bank->load($this->input->post('employee_bank_id'))) {
            $employee_bank->delete();
        }
    }
}
