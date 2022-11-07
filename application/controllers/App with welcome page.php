<?php

class App extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        check_login();
        $data['number_of_projects'] = $this->db->count_all('projects');
        $data['number_of_sub_contractors'] = $this->db->count_all('Contractors');
        $data['number_of_clients'] = $this->db->count_all('clients');
        $data['number_of_pre_orders'] = number_of_pre_orders();
        $data['number_of_purchase_orders_grns'] = 0;
        $data['number_of_orders'] = $this->db->count_all('purchase_orders');
        $data['number_of_vendors'] = $this->db->count_all('vendors');
        $data['number_of_requisitions'] = $this->db->count_all('requisitions');
        $data['number_of_locations'] = $this->db->count_all('inventory_locations');
        $data['number_of_material_items'] = $this->db->count_all('material_items');
        $data['number_of_employees'] = $this->db->count_all('employees');
        $data['number_of_departments'] = $this->db->count_all('departments');
        $data['number_of_material_items'] = $this->db->count_all('material_items');
        $this->load->view('dashboard', $data);
    }

    public function welcome_page(){

        $this->load->model('project_category');
        $data['project_categories'] = $this->project_category->get();
        $this->load->view('welcome_page',$data);
    }

    public function login()
    {
        $where = [
            'username' => $this->input->post('username'),
            'password' => md5($this->input->post('password'))
        ];

        $this->load->model('user');
        $users = $this->user->get(0, 0, $where);
        $user = array_shift($users);
        if (!empty($user)) {
            $employee = $user->employee();
            $permissions = $user->permission_names();
            $department = $employee->department();
            $userdata = [
                "employee_id" => $employee->employee_id,
                "employee_name" => $employee->full_name(),
                "department_name" => $department->department_name,
                'department_id' => $department->{$department::DB_TABLE_PK},
                'dp_path' => $employee->avatar_path(),
                'permissions' => $permissions,
                'job_position_id' => $employee->position_id,
                'has_project' => $employee->has_project()
            ];
            $this->session->set_userdata($userdata);
            system_log('Login');
            redirect(base_url());
        }

        $this->load->view('login_form');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        system_log('Logout');
        redirect(base_url('app/welcome_page'));
    }

    public function error_404()
    {
        redirect(base_url());
    }

}

