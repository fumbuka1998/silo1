<?php

class Administrative_actions extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_login();
    }

    public function index()
    {
        check_permission('Administrative Actions', true);
        $data['title'] = 'Administrative Actions';
        $this->load->view('administrative_actions/index', $data);
    }

    public function company_details()
    {
        check_permission('Administrative Actions', true);
        $this->load->view('administrative_actions/company_details/index');
    }

    public function save_company_details()
    {

        if (check_permission('Administrative Actions')) {
            if (!empty($_FILES['company_logo'])) {
                $config = [
                    'upload_path' => "./images/",
                    'allowed_types' => 'gif|jpg|png|jpeg',
                    'file_name' => 'company_logo.png',
                    'overwrite' => true
                ];

                $this->load->library('upload', $config);
                $this->upload->do_upload('company_logo');
            }

            $this->load->model('company_detail');
            $company_detail = new Company_detail();
            $company_detail->company_name = $this->input->post('company_name');
            $company_detail->telephone = $this->input->post('telephone');
            $company_detail->mobile = $this->input->post('mobile');
            $company_detail->fax = $this->input->post('fax');
            $company_detail->email = $this->input->post('email');
            $company_detail->website = $this->input->post('website');
            $company_detail->tin = $this->input->post('tin');
            $company_detail->vrn = $this->input->post('vrn');
            $company_detail->tagline = $this->input->post('tagline');
            $company_detail->address = $this->input->post('address');
            $company_detail->corporate_color= $this->input->post('corporate_color');
            $company_detail->created_by = $this->session->userdata('employee_id');

            $company_detail->save();
        }
    }

    public function audit_trail()
    {
        check_permission('Administrative Actions', true);
        $data['title'] = 'Audit Trail';
        $this->load->view('administrative_actions/audit_trail', $data);
    }

    public function audit_trail_report()
    {
        check_permission('Administrative Actions', true);
        $action_type = $this->input->post('action_type');
        $project_id = $this->input->post('project_id');
        $from = $this->input->post('from');
        $to = $this->input->post('to');

        $where = [];
        if ($action_type != '') {
            $where['action'] = $action_type;
        }
        if ($project_id != '') {
            $where['project_id'] = $project_id;
        } else {
            $project_id = null;
        }
        if ($from != '') {
            $where['datetime_logged >='] = $from;
        }
        if ($to != '') {
            $where['datetime_logged <='] = $to;
        }
        $this->load->model('system_log');
        $data['log_entries'] = $this->system_log->get(0, 0, $where, 'datetime_logged DESC');
        $print = $this->input->post('print');
        if ($print == 'true') {
            $data['print'] = $print;

            if ($project_id != '') {
                $this->load->model('project');
                $project = new Project();
                $project->load($project_id);
                $data['project'] = $project->project_name;
            } else {
                $data['project'] = 'ALL';
            }

            $data['action'] = $action_type != '' ? $action_type : 'ALL';
            $data['from'] = $from != '' ? standard_datetime($from) : '';
            $data['to'] = $to != '' ? standard_datetime($to) : '';

            $html = $this->load->view('administrative_actions/audit_trail_sheet', $data, true);

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                'L', // L - landscape, P - portrait
                '',
                '',
                '',
                '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6
            ); // margin footer
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('audit_report_' . strftime('%d/%m/%Y %H:%M:%S') . '.pdf', 'I'); // view in the explorer

            $action = 'Print Audit Report';
            $description = 'An audit trail from ' . standard_datetime($from) . ' to ' . standard_datetime($to) . ' was printed';
        } else {
            $this->load->view('administrative_actions/audit_trail_table', $data);

            $action = 'Generate Audit Report';
            $description = 'An audit trail from ' . standard_datetime($from) . ' to ' . standard_datetime($to) . ' was printed';
        }
        system_log($action, $description, $project_id);
    }

    public function approval_settings()
    {
        $this->load->model('Approval_module');
        $this->load->model('Approval_chain_level');
        $this->load->model('Job_position');
        $data['job_position_options'] = $this->Job_position->job_position_options();
        $data['approval_modules'] = $this->Approval_module->approval_modules();
        $data['title'] = 'Approval Settings';
        $this->load->view('administrative_actions/approval_settings/approval_settings', $data);
    }

    public function save_approval_settings()
    {

        $this->load->model('Approval_module');
        $Approval_module = new Approval_module();

        $this->load->model('Approval_chain_level');
        $Approval_chain_level = new Approval_chain_level();
        $Approval_chain_level->approval_module_id = $this->input->post('approval_module_id');
        $Approval_module->load($Approval_chain_level->approval_module_id);

        $level = $this->input->post('after_level');

        $chain_levels = $Approval_module->chain_levels($level);

        $Approval_chain_level->created_by = $this->session->userdata('employee_id');
        $Approval_chain_level->label = $this->input->post('label');
        $Approval_chain_level->change_source = $this->input->post('change_source');
        $Approval_chain_level->status = 'active';
        $Approval_chain_level->special_level = $this->input->post('is_special_level') == 'true' ? 1 : 0;

        $Approval_chain_level->level = $level + 1;

        $Approval_chain_level->level_name = $this->input->post('level_name');
        if ($Approval_chain_level->save()) {
            foreach ($chain_levels as $chain_level) {
                $chain_level->level = $chain_level->level + 1;
                $chain_level->save();
            }
        }
    }

    public function delete_chain_level()
    {

        $this->load->model('Approval_chain_level');
        $Approval_chain_level = new Approval_chain_level();
        if ($Approval_chain_level->load($this->input->post('approval_chain_level_id'))) {
            $Approval_chain_level->delete();
        }
    }

    public function disable_chain_level()
    {

        $this->load->model('Approval_chain_level');
        $Approval_chain_level = new Approval_chain_level();

        if ($Approval_chain_level->load($this->input->post('approval_chain_level_id'))) {

            $Approval_chain_level->status = 'inactive';

            $Approval_chain_level->save();
        }
    }

    public function enable_chain_level()
    {

        $this->load->model('Approval_chain_level');
        $Approval_chain_level = new Approval_chain_level();

        if ($Approval_chain_level->load($this->input->post('approval_chain_level_id'))) {

            $Approval_chain_level->status = 'active';

            $Approval_chain_level->save();
        }
    }

    public function load_approval_chain_levels()
    {
        $this->load->model('approval_module');
        $approval_module = new Approval_module();
        if ($approval_module->load($this->input->post('approval_module_id'))) {
            $data['module_chain_levels'] = $approval_module->chain_levels();
            if ($this->input->post('without_table') != 'true') {
                $return['table'] = $this->load->view('administrative_actions/approval_settings/chain_levels_table', $data, true);
            }
            $return['chain_levels_options'] = $approval_module->approval_chain_level_options();
            echo json_encode($return);
        }
    }

    public function approval_chain_level_dropdown_options($specific_level)
    {
        $this->load->model('approval_module');
        $module = new Approval_module();
        $module->load($this->input->post('approval_module_id'));
        $levels = $module->chain_levels(0, null, 'active');
        $options = ['' => '&nbsp;'];
        foreach ($levels as $level) {
            $options[$level->{$level::DB_TABLE_PK}] = $level->level_name.($level->special_level ? ' (Special)' : '');
        }
        echo stringfy_dropdown_options($options);
    }
}
