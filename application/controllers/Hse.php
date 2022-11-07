<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 9:54 AM
 */
class Hse extends CI_Controller {
    public function __construct() {
        parent::__construct();
        check_login();
    }

    public function settings(){
        $this->load->model('hse_certificate');
        $data['title'] = 'HSE | Settings';
        $data['certificates_options'] = $this->hse_certificate->dropdown_options();
        $data['employees_options'] = employee_options();
        $this->load->view('hse/settings/index',$data);
    }

    public function save_category(){
        $this->load->model('category');
        $category = new Category();
        $category->load($this->input->post('category_id'));
        $category->name = $this->input->post('category_name');
        $category->description = $this->input->post('description');
        $category->created_by = $this->session->userdata('employee_id');
        $category->save();
    }

    public function delete_category(){
        $this->load->model('category');
        $category = new Category();
        if($category->load($this->input->post('category_id'))){
            $category->delete();
        } else {
            redirect(base_url());
        }
    }

    public function hse_categories_list(){
        $this->load->model('category');
        $posted_params = dataTable_post_params();
        echo $this->category->categories_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function settings_details($id = 0){
        $this->load->model('category');
        $category = new Category();
        if($category->load($id)){
            $data['title'] = 'HSE | '.$category->name;
            $data['category'] = $category;
            $this->load->view('hse/settings/profile/index',$data);
        } else {
            redirect(base_url());
        }
    }

    public function save_parameter(){
        $this->load->model('category_parameter');
        $parameter = new Category_parameter();
        $parameter->load($this->input->post('parameter_id'));
        $parameter->name = $this->input->post('parameter_name');
        $parameter->category_id = $this->input->post('category_id');
        $parameter->description = $this->input->post('description');
        $parameter->created_by = $this->session->userdata('employee_id');
        $parameter->save();
    }

    public function delete_parameter(){
        $this->load->model('category_parameter');
        $parameter = new Category_parameter();
        if($parameter->load($this->input->post('parameter_id'))){
            $parameter->delete();
        } else {
            redirect(base_url());
        }
    }

    public function category_parameters_list($category_id = 0){
        $this->load->model('category_parameter');
        $posted_params = dataTable_post_params();
        echo $this->category_parameter->category_parameters_list($category_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function inspection($category_id = null){
        $this->load->model(['category','category_parameter','employee','project']);
        $category = new Category();
        $category->load($category_id);
        switch ($category->name){
            case 'First Aid Kit Check':
                $index = 'hse/inspections/first_aid_kits/index';
                break;
            case 'Site':
                $index = 'hse/inspections/index';
                break;
        }
        $data['categories'] = $this->category->get();
        $data['category_id'] = $category_id;
        $data['category_parameters'] = $this->category_parameter->get(0,0,['category_id'=>$category_id]);
        $data['projects_options'] = $this->project->on_going_projects_dropdown();
        $data['categories_options'] = $this->category->dropdown_options();
        $data['parameters_options'] = $this->category_parameter->dropdown_options();
        $data['inspectors_options'] = employee_options();
        $data['title'] = 'HSE | Inspections | '.hse_inspection_categories($category_id)->name;
        $this->load->view($index,$data);
    }

    public function inspections_list(){
        $this->load->model('inspection');
        $posted_params = dataTable_post_params();
        echo $this->inspection->inspections_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_inspection(){
        $this->load->model(['inspection','inspection_category','inspection_category_parameter','inspection_category_parameter_type']);
        $this->db->trans_begin();
        $inspection = new Inspection();
        $edit = $inspection->load($this->input->post('inspection_id'));
        $inspection->inspection_date = $this->input->post('inspection_date');
        $inspection->site_id = $this->input->post('site_id');
        $inspection->status = $this->input->post('status');
        $inspection->inspector_id = $this->input->post('inspector_id');
        $inspection->inspection_type = $this->input->post('inspection_type');
        $inspection->description = $this->input->post('description');
        $inspection->location = $this->input->post('location');
        $inspection->created_by = $this->session->userdata('employee_id');

        if($inspection->save()) {
            if ($edit) {
                $inspection->delete_inspection_category();
            }
            $inspection_category = new Inspection_category();
            $inspection_category->category_id = $this->input->post('category_id');
            $inspection_category->inspection_id = $inspection->{$inspection::DB_TABLE_PK};
            $inspection_category->save();
            $category_parameter_ids = $this->input->post('category_parameter_ids');
            foreach ($category_parameter_ids as $parameter_index => $category_parameter_id) {
                $inspection_category_parameter = new Inspection_category_parameter();
                $inspection_category_parameter->category_parameter_id = $category_parameter_id;
                $inspection_category_parameter->inspection_category_id = $inspection_category->{$inspection_category::DB_TABLE_PK};
                $inspection_category_parameter->remarks = '';
                if($inspection_category_parameter->save()){
                    $parameter_type_ids = $this->input->post('parameter_type_ids');
                    foreach ($parameter_type_ids[$parameter_index] as $parameter_type_index=>$parameter_type_id) {
                        if($parameter_type_id != ''){
                            $inspection_category_parameter_type = new Inspection_category_parameter_type();
                            $inspection_category_parameter_type->parameter_type_id = $parameter_type_id;
                            $inspection_category_parameter_type->is_checked = $this->input->post('is_checkeds')[$parameter_index][$parameter_type_index];
                            $inspection_category_parameter_type->inspection_category_parameter_id = $inspection_category_parameter->{$inspection_category_parameter::DB_TABLE_PK};
                            $inspection_category_parameter_type->save();
                        }
                    }
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        }
        else {
            $this->db->trans_commit();
        }
    }

    public function delete_inspection(){
        $this->load->model('inspection');
        $inspection = new Inspection();
        $inspection->load($this->input->post('inspection_id'));
        $inspection->delete_inspection_category();
        $inspection->delete();
    }

    public function incident_details($id = 0){
        $this->load->model('incident');
        $incident = new Incident();
        if($incident->load($id)){
            $data['incident'] = $incident;
            $data['title'] = 'HSE | '.$incident->site()->project_name;
            $this->load->view('hse/incidents/profile/index',$data);
        } else {
            redirect(base_url());
        }
    }

    public function inspection_details($id = 0){
        $this->load->model('inspection');
        $inspection = new Inspection();
        if($inspection->load($id)){
            $data['inspection'] = $inspection;
            $data['title'] = 'HSE | '.$inspection->site()->project_name;
            $this->load->view('hse/inspections/profile/index',$data);
        } else {
            redirect(base_url());
        }
    }

    public function inspection_preview($id,$inp_type){
        $this->load->model('inspection');
        $inspection = new Inspection();
        if($inspection->load($id)){
            $this->load->library(['m_pdf']);
            $data['inspections'] = $inspection;
            switch ($inp_type){
                case 'Site_Inspection':
                    $html = $this->load->view('hse/inspections/printouts/site_inspection_sheet',$data,true);
                    break;
                default:
                    $html = $this->load->view('hse/inspections/printouts/fik_inspection_sheet',$data,true);
                    break;
            }

            $pdf = $this->m_pdf->load();
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
            $pdf->setFooter($footercontents);

            $pdf->WriteHTML($html);

            $pdf->Output('Inspection'.add_leading_zeros($inspection->{$inspection::DB_TABLE_PK}).'.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function inspection_job_cards_list($inspection_id = 0){
        $this->load->model('inspection_job_card');
        $posted_params = dataTable_post_params();
        echo $this->inspection_job_card->inspection_job_cards_list($inspection_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_job_card(){
        $this->db->trans_begin();
        $this->load->model(['job_card','inspection_job_card','incident_job_card']);
        $job_card = new Job_card();
       $edit = $job_card->load($this->input->post('job_card_id'));
        $job_card->priority = $this->input->post('priority');
        $job_card->date = $this->input->post('date');
        $job_card->is_closed = 0;
        $job_card->remarks = $this->input->post('remarks');
        $job_card->created_by = $this->session->userdata('employee_id');
        if($job_card->save()) {
            if ($edit) {
                if($this->input->post('job_type') == 'inspection'){
                    $job_card->delete_inspection_job_card();
                } else {
                    $job_card->delete_incident_job_card();
                }

            }
            $inspection_id = $this->input->post('inspection_id');
            $incident_id = $this->input->post('incident_id');
            if ($inspection_id) {
                $inspection_job_card = new Inspection_job_card();
                $inspection_job_card->inspection_id = $inspection_id;
                $inspection_job_card->job_card_id = $job_card->{$job_card::DB_TABLE_PK};
                $inspection_job_card->save();
            } else {
                $incident_job_card = new Incident_job_card();
                $incident_job_card->incident_id = $incident_id;
                $incident_job_card->job_card_id = $job_card->{$job_card::DB_TABLE_PK};
                $incident_job_card->save();
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
        }
        else {
            # Everything is Perfect.
            # Committing data to the database.
            $this->db->trans_commit();
        }
    }

    public function delete_job_card(){
        $this->load->model('job_card');
        $job_card = new Job_card();
        if($job_card->load($this->input->post('job_card_id'))){
            $job_card->delete_inspection_job_card();
            $job_card->delete();
        }
    }

    public function job_card(){
        $data['title'] = 'HSE | Job Cards';
        $this->load->view('hse/job_cards/index',$data);
    }

    public function all_job_cards_list(){
        $this->load->model('job_card');
        $posted_params = dataTable_post_params();
        echo $this->job_card->job_cards_list( $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function hse_incidents_list(){
        $this->load->model('incident');
        $posted_params = dataTable_post_params();
        echo $this->incident->incidents_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function incident(){
        $this->load->model('project');
        $data['title'] = 'HSE | Incidents';
        $data['projects_options'] = $this->project->on_going_projects_dropdown();
        $this->load->view('hse/incidents/index',$data);
    }

    public function save_incident(){
       $this->load->model('incident');
       $incident = new Incident();
       $incident->load($this->input->post('incident_id'));
       $incident->site_id = $this->input->post('site_id');
       $incident->incident_date = $this->input->post('incident_date');
       $incident->reference = $this->input->post('reference');
       $incident->is_reported = $this->input->post('is_reported');
       $incident->type = $this->input->post('type');
       $incident->causative_agent = $this->input->post('causative_agent');
       $incident->location = $this->input->post('location');
       $incident->description = $this->input->post('description');
       $incident->created_by = $this->session->userdata('employee_id');
       $incident->save();
    }

    public function delete_incident(){
        $this->load->model('incident');
        $incident = new Incident();
        $incident->load($this->input->post('incident_id'));
        $incident->delete();
    }

    public function job_card_profile($type, $id = 0){
        $this->load->model(['job_card','employee','activity']);
        $job_card = new Job_card();
        if($job_card->load($id)){
            if($type == 'Inspection'){
               $inspection =  $job_card->inspection_job_card()->inspection();
                $data['activities_options'] = $this->activity->dropdown_options($inspection->site_id);
            } else {
                $incident = $job_card->incident_job_card()->incident();

                $data['activities_options'] = $this->activity->dropdown_options($incident->site_id);
            }
            $data['labours_options'] = employee_options();
           $data['title'] = 'HSE | '.$job_card->job_card_number();
           $data['type'] = $type;
           $data['job_card'] = $job_card;
           $this->load->view('hse/job_cards/profile/index',$data);
        } else {
            redirect(base_url());
        }
    }

    public function incident_job_cards_list($incident_id = 0){
        $this->load->model('incident_job_card');
        $posted_params = dataTable_post_params();
        echo $this->incident_job_card->incident_job_cards_list($incident_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function job_card_labours_and_activities_list(){
        $this->load->model('job_card_labour');
        $posted_params = dataTable_post_params();
        echo $this->job_card_labour->job_card_labours_and_activities_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_job_card_labour_and_service(){
        $this->load->model(['job_card_labour','job_card_service']);
        $this->db->trans_begin();
        $job_card_labour = new Job_card_labour();
        $edit = $job_card_labour->load($this->input->post('job_card_labour_id'));
        $job_card_labour->employee_id = $this->input->post('employee_id');
        $job_card_labour->job_card_id = $this->input->post('job_card_id');
        $job_card_labour->save();
        if($edit){
            $job_card_labour->delete_job_card_service();
        }
        $activities = $this->input->post('activity_ids');
        foreach ($activities as $activity){
            if(!empty($activity)){
                $job_card_service = new Job_card_service();
                $job_card_service->load($this->input->post('job_card_service_id'));
                $job_card_service->activity_id = $activity;
                $job_card_service->job_card_labour_id = $job_card_labour->{$job_card_labour::DB_TABLE_PK};
                $job_card_service->save();
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
# Something went wrong.
            $this->db->trans_rollback();
        }
        else {
# Everything is Perfect.
# Committing data to the database.
            $this->db->trans_commit();
        }
    }

    public function delete_job_card_labour_and_service(){
        $this->load->model(['job_card_labour','job_card_service']);
        $this->db->trans_begin();
        $job_card_labour = new Job_card_labour();
        $job_card_labour->load($this->input->post('job_card_labour_id'));
        $job_card_labour->delete_job_card_service();
        $job_card_labour->delete();

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
# Something went wrong.
            $this->db->trans_rollback();
        }
        else {
# Everything is Perfect.
# Committing data to the database.
            $this->db->trans_commit();
        }
    }

    public function fetch_parameters()
    {
        $output = '';
        $query = '';
        $this->load->model('category_parameter');
        if($this->input->post('query'))
        {
            $query = $this->input->post('query');
        }
        $category_id = $this->input->post('category_id');
        $data = $this->category_parameter->fetch_data($query,$category_id);
        $output .= '
<div class="table-responsive">
<table class="table table-bordered table-striped">
<tr>
<th>S/n</th>
<th>Parameter</th>
<th>Check</th>
</tr>
';
        $sn = 1;
        if($data->num_rows() > 0)
        {
            foreach($data->result() as $row)
            {
                $output .= '
<tr>
<td>'.$sn++.'</td>
<td>'.$row->name.'</td>
<td>
<input type="checkbox" name="parameter_id" value="'.$row->id.'">
</td>
</tr>
';
            }
        }
        else
        {
            $output .= '<tr>
<td colspan="5">No Data Found</td>
</tr>';
        }
        $output .= '</table>';
        echo $output;
    }

    public function job_card_reports(){
        if($this->input->post('generate') != '' || $this->input->post('print') != ''){
            $data['job_card_type'] = $job_card_type = $this->input->post('job_card_type');
            $data['from'] = $from = $this->input->post('from');
            $data['to'] = $to = $this->input->post('to');
            $data['print'] = $this->input->post('print') != null;
            $this->load->model('job_card');
            $data['job_card_reports'] = $this->job_card->job_card_reports($from, $to, $job_card_type);

            if($data['print']){
                $html = $this->load->view('hse/job_cards/reports/job_card_sheet',$data,true);
                $mpdf = new \Mpdf\Mpdf();
                $mpdf->WriteHTML($html);
                $mpdf->Output('Job_card_sheet'.'.pdf', 'I');
            } else {
                $this->load->view('hse/job_cards/reports/job_card_table',$data);
            }
        } else {
            $data['title'] = 'Job Card Reports';
            $this->load->view('hse/job_cards/reports/index',$data);
        }
    }

    public function preview_labour_activity($id, $type){
        $this->load->model('job_card_labour');
        $job_card_labour = new Job_card_labour();
        if($job_card_labour->load($id)){
            if($type == 'Inspection'){
                $inspection = $job_card_labour->job_card()->inspection_job_card()->inspection();
                $project = $inspection->site();
            } else {
                $incident = $job_card_labour->job_card()->incident_job_card()->incident();
                $project = $incident->site();
            }
            $this->load->library(['m_pdf']);
            $data['project'] = $project;
            $data['labour'] = $job_card_labour->labour();
            $data['job_card_services'] = $job_card_labour->job_card_services();
            $html = $this->load->view('hse/job_cards/profile/labours_and_activities/preview_activity',$data,true);

            $pdf = $this->m_pdf->load();
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
            $pdf->setFooter($footercontents);

            $pdf->WriteHTML($html);

            $pdf->Output('job_card_labour'.add_leading_zeros($job_card_labour->{$job_card_labour::DB_TABLE_PK}).'.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function hse_certificates_list(){
        $this->load->model('hse_certificate');
        $posted_params = dataTable_post_params();
        echo $this->hse_certificate->hse_certificates_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_hse_certificate(){
        $this->load->model('hse_certificate');
        $hse_certificate = new Hse_certificate();
        $hse_certificate->load($this->input->post('hse_certificate__id'));
        $hse_certificate->name = $this->input->post('certificate_name');
        $hse_certificate->description = $this->input->post('description');
        $hse_certificate->created_by = $this->session->userdata('employee_id');
        $hse_certificate->type = $this->input->post('type');
        $hse_certificate->save();
    }

    public function delete_hse_certificate(){
        $this->load->model('hse_certificate');
        $hse_certificate = new Hse_certificate();
        $hse_certificate->load($this->input->post('hse_certificate_id'));
        $hse_certificate->delete();
    }

    public function registered_certificates_list(){
        $this->load->model('registered_certificate');
        $posted_params = dataTable_post_params();
        echo $this->registered_certificate->hse_registered_certificates_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_registered_hse_certificate()
    {
        $this->load->model('registered_certificate');
        $registered_certificate = new Registered_certificate();
        $registered_certificate->load($this->input->post('registered_certificate_id'));
        $registered_certificate->hse_certificate_id = $this->input->post('hse_certificate_id');
        $registered_certificate->company_id = $this->input->post('company_id');
        $registered_certificate->employee_id = $this->input->post('employee_id');
        $registered_certificate->created_by = $this->session->userdata('employee_id');
        $registered_certificate->save();
    }

    public function delete_registered_certificate(){
        $this->load->model('registered_certificate');
        $registered_certificate = new Registered_certificate();
        $registered_certificate->load($this->input->post('registered_certificate_id'));
        $registered_certificate->delete();
    }

    public function deployment(){
        $this->load->model('category_parameter');
//        $data['deployment_category_parameters'] = $this->category_parameter->deployment_category_parameters();
        $data['title'] = 'HSE | Inspection | Deployments';
        $data['index_title'] = 'HSE | Inspection |';
        $this->load->view('hse/deployments/index',$data);
    }

    public function deployment_form($id = 0){
        $this->load->model(array('deployment','category_parameter'));
        $deployment = new Deployment();
        $data['form_title'] = 'HSE | Inspection | Deployment Form';
        if($deployment->load($id)){
            $data['deployment'] = $deployment;
            $data['category_parameters'] = $this->category_parameter->deployment_category_parameters();
            $this->load->view('hse/deployments/deployment_form',$data);
        } else {
            $data['category_parameters'] = $this->category_parameter->deployment_category_parameters();
            $this->load->view('hse/deployments/deployment_form',$data);
        }

    }

    public function save_deployment(){
        $this->db->trans_begin();
        $this->load->model(['deployment','deployment_category_parameter','deployment_person']);
        $deployment = new Deployment();
        $edit = $deployment->load($this->input->post('deployment_id'));
        $deployment->name = $this->input->post('name');
        $deployment->departure_time = $this->input->post('departure');
        $deployment->arrival_time = $this->input->post('arrival_time');
        $deployment->relax_station = $this->input->post('relax_station');
        $deployment->registration_number = $this->input->post('registration_number');
        $deployment->driver = $this->input->post('driver');
        $deployment->created_by = $this->session->userdata('employee_id');
        if($deployment->save()){
            if($edit) {
                $deployment->delete_deployment_category_parameter();
                $deployment->delete_deployment_erson();
            }
            $category_parameter_ids = $this->input->post('category_parameter_ids');
            foreach ($category_parameter_ids as $index => $category_parameter_id){
                $category_paramenter = new Deployment_category_parameter();
                $category_paramenter->category_parameter_id = $category_parameter_id;
                $category_paramenter->answer = $this->input->post('answers')[$index];
                $category_paramenter->description = $this->input->post('descriptions')[$index];
                $category_paramenter->deployment_id = $deployment->{$deployment::DB_TABLE_PK};
                $category_paramenter->save();
            }
            $persengers = $this->input->post('persengers');
            foreach ($persengers as $key => $name){
                if($name !=''){
                    $deployment_person = new Deployment_person();
                    $deployment_person->name = $name;
                    $deployment_person->deployment_id = $deployment->{$deployment::DB_TABLE_PK};
                    $deployment_person->save();
                }

            }
        }

        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
        }
        else {
            # Everything is Perfect.
            # Committing data to the database.
            $this->db->trans_commit();
            redirect(base_url('hse/deployment'));
        }
    }

    public function deployments_list(){
        $this->load->model('deployment');
        $posted_params = dataTable_post_params();
        echo $this->deployment->deployments_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function delete_deployment(){
        $this->db->trans_begin();
        $this->load->model('deployment');
        $deployment = new Deployment();
        if($deployment->load($this->input->post('deployment_id'))){
            $deployment->delete_deployment_category_parameter();
            $deployment->deployment_person();
            $deployment->delete();
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        }
        else {
            $this->db->trans_commit();
        }

    }

    public function preview_deployment($id = 0){
        $this->load->model('deployment');
        $deployment = new Deployment();
        if($deployment->load($id)){

            $this->load->library(['m_pdf']);
            $data['deployment'] = $deployment;
            $html = $this->load->view('hse/deployments/preview_deployment',$data,true);

            $pdf = $this->m_pdf->load();
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
            $pdf->setFooter($footercontents);

            $pdf->WriteHTML($html);

            $pdf->Output('deployment_'.add_leading_zeros($deployment->{$deployment::DB_TABLE_PK}).'.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function category_parameter_types(){
        $this->load->model(array('category_parameter','parameter_type'));
        $parameter_id = $this->input->post('category_parameter_id');
        $parameter_id = $parameter_id != '' ? $parameter_id : null;
        $parameter = new Category_parameter();
        $parameter->load($parameter_id);
        $parameter_type_name = $this->input->post('parameter_type_name');;
        $parameter_type_name = trim($parameter_type_name) != '' ? trim($parameter_type_name) : null;
        if(!is_null($parameter_type_name)) {
            $parameter_type = new Parameter_type();
            $parameter_type->load($this->input->post('category_parameter_id'));
            $parameter_type->name = $parameter_type_name;
            $parameter_type->category_parameter_id = $parameter_id;
            $parameter_type->description = $this->input->post('description');
            $parameter_type->save();

        }
        $data['parameter'] = $parameter;
        $data['parameter_types'] = $parameter->parameter_types();
        $ret_val['table_view'] = $this->load->view('hse/settings/profile/parameters/parameters', $data, true);
        echo json_encode($ret_val);

    }

    public function delete_parameter_type(){
        $this->load->model(array('category_parameter','parameter_type'));
        $parameter_id = $this->input->post('category_parameter_id');
        $parameter_id = $parameter_id != '' ? $parameter_id : null;
        $parameter = new Category_parameter();
        $parameter->load($parameter_id);
        $parameter_type = new Parameter_type();
        if($parameter_type->load($this->input->post('parameter_type__id'))){
            $parameter_type->delete();
            $data['parameter'] = $parameter;
            $data['parameter_types'] = $parameter->parameter_types();
            $ret_val['table_view'] = $this->load->view('hse/settings/profile/parameters/parameters', $data, true);
            echo json_encode($ret_val);
        }
    }

    public function fik_inspections_list(){
        $this->load->model('inspection');
        $posted_params = dataTable_post_params();
        echo $this->inspection->fik_inspections_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function toolbox_talk_registers_list(){
        $this->load->model('toolbox_talk_register');
        $posted_params = dataTable_post_params();
        echo $this->toolbox_talk_register->toolbox_talk_registers_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function talk_register(){
        $this->load->model(['project','site_topic']);
        $data['projects_options'] = $this->project->on_going_projects_dropdown();
        $data['topics_options'] = $this->site_topic->dropdown_options();
        $this->load->view('hse/toolbox_talk_registers/index',$data);
    }

    public function load_activities_dropdown_options(){
        $this->load->model('activity');
        $project_id = $this->input->post('project_id');
        $data = $this->activity->dropdown_options($project_id);
        echo  stringfy_dropdown_options($data);
    }

    public function save_toolbox_talk_register(){
        $this->load->model(['toolbox_talk_register','toolbox_talk_register_topic','toolbox_talk_register_participant']);
        $this->db->trans_begin();
        $selected_items = $this->input->post('selected_items');
        if(!empty($selected_items)){
            $talk_register = new Toolbox_talk_register();
            $edit = $talk_register->load($this->input->post('toolbox_talk_register_id'));
            $talk_register->supervisor_id = $this->input->post('supervisor_id');
            $talk_register->activity_id = $this->input->post('activity_id');
            $talk_register->site_id = $this->input->post('site_id');
            $talk_register->date = $this->input->post('date');
            $talk_register->created_by = $this->session->userdata('employee_id');
            if($talk_register->save()){
                if($edit){
                    $talk_register->delete_talk_register_participants();
                    $talk_register->delete_talk_register_topics();
                }
                foreach ($selected_items as $index => $selected_item){
                    if(!empty($selected_item)){
                        if($selected_item == 'topic'){
                            $toolbox_topic = new Toolbox_talk_register_topic();
                            $toolbox_topic->toolbox_talk_register_id = $talk_register->{$talk_register::DB_TABLE_PK};
                            $toolbox_topic->topic_id = $this->input->post('topic_ids')[$index];
                            $toolbox_topic->save();
                        } else {
                            $toolbox_member = new Toolbox_talk_register_participant();
                            $toolbox_member->toolbox_talk_register_id = $talk_register->{$talk_register::DB_TABLE_PK};
                            $toolbox_member->name = $this->input->post('member_names')[$index];
                            $toolbox_member->save();
                        }
                    }
                }

            }
        }

        if($this->db->trans_status === FALSE){
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function preview_toolbox_talk_register($id = 0){
        $this->load->model('toolbox_talk_register');
        $talk_register = new Toolbox_talk_register();
        if($talk_register->load($id)){
            $data['talk_register'] = $talk_register;
            $this->load->library(['m_pdf']);
            $html = $this->load->view('hse/toolbox_talk_registers/preview_toolbox_talk_register',$data,true);

            $pdf = $this->m_pdf->load();
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
            $pdf->setFooter($footercontents);

            $pdf->WriteHTML($html);

            $pdf->Output('toolbox_talk_register_'.add_leading_zeros($talk_register->{$talk_register::DB_TABLE_PK}).'.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function delete_toolbox_talk_register(){
        $this->load->model('toolbox_talk_register');
        $talk_register = new Toolbox_talk_register();
        if($talk_register->load($this->input->post('toolbox_talk_register_id'))){
            $talk_register->delete();
        } else {
            redirect(base_url());
        }
    }

    public function site_diary_compliance(){
        $this->load->model('project');
        $data['projects_options'] = $this->project->on_going_projects_dropdown();
        $this->load->view('hse/site_diary_compliances/index',$data);
    }

    public function save_site_diary_compliance(){
        $this->db->trans_begin();
        $this->load->model(['site_diary_compliance','site_diary_compliance_status']);
        $site_diary_compliance = new Site_diary_compliance();
        $edit = $site_diary_compliance->load($this->input->post('site_diary_compliance_id'));
        $site_diary_compliance->date = $this->input->post('date');
        $site_diary_compliance->remarks = $this->input->post('remarks');
        $site_diary_compliance->supervisor_id = $this->input->post('supervisor_id');
        $site_diary_compliance->site_id = $this->input->post('site_id');
        $site_diary_compliance->created_by = $this->session->userdata('employee_id');
        if($site_diary_compliance->save()){
            if($edit){
                $site_diary_compliance->delete_diary_copliance_statuses();
            }
            $descriptions = $this->input->post('descriptions');
            foreach ($descriptions as $index => $description){
                $site_diary_compliance_status = new Site_diary_compliance_status();
                $site_diary_compliance_status->site_diary_id = $site_diary_compliance->{$site_diary_compliance::DB_TABLE_PK};
                $site_diary_compliance_status->description = $description;
                $site_diary_compliance_status->comments = $this->input->post('comments')[$index];
                $site_diary_compliance_status->save();
            }
        }
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function delete_site_diary_compliance(){
        $this->load->model('site_diary_compliance');
        $site_diary_compliance = new Site_diary_compliance();
        if($site_diary_compliance->load($this->input->post('site_diary_compliance_id'))){
            $site_diary_compliance->delete();
        } else {
            redirect(base_url());
        }
    }

    public function preview_site_diary_compliance($id = 0){
        $this->load->model('site_diary_compliance');
        $site_diary_compliance = new Site_diary_compliance();
        if($site_diary_compliance->load($id)){

            $this->load->library(['m_pdf']);
            $data['site_diary_compliance'] = $site_diary_compliance;
            $html = $this->load->view('hse/site_diary_compliances/preview_site_diary_compliance',$data,true);

            $pdf = $this->m_pdf->load();
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
            $pdf->setFooter($footercontents);

            $pdf->WriteHTML($html);

            $pdf->Output('site_diary_compliance_id_'.add_leading_zeros($site_diary_compliance->{$site_diary_compliance::DB_TABLE_PK}).'.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function site_diary_compliances_list(){
        $this->load->model('site_diary_compliance');
        $posted_params = dataTable_post_params();
        echo $this->site_diary_compliance->site_diary_compliances_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_topic(){
        $this->load->model('site_topic');
        $topic = new Site_topic();
        $topic->load($this->input->post('topic_id'));
        $topic->name = $this->input->post('topic_name');
        $topic->description = $this->input->post('description');
        $topic->save();

    }

    public function delete_topic(){
        $this->load->model('site_topic');
        $topic = new Site_topic();
        if($topic->load($this->input->post('topic_id'))){
            $topic->delete();
        } else {
            redirect(base_url());
        }
    }

    public function hse_topics_list(){
        $this->load->model('site_topic');
        $posted_params = dataTable_post_params();
        echo $this->site_topic->topics_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

}