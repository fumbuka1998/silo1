<?php

class Contractors extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->model('contractor');
     }

    public function index(){
        $this->load->model('contractor');
        $data['title'] = 'Contractors';
        $data['number_of_contractors'] = $this->db->count_all('contractors');
        $this->load->view('contractors/index',$data);
    }

    public function contractors_list(){
        $posted_params = dataTable_post_params();
        if($posted_params['limit'] == null){
            $data['title'] = 'Contractors';
            $data['currency_options'] = currency_dropdown_options();
            $this->load->view('contractors/list',$data);
        } else {
            echo $this->contractor->contractors_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        }
    }

    public function save_contractor(){
        $contractor = new Contractor();
        $edit = $contractor->load($this->input->post('contractor_id'));
        $contractor->contractor_name = $this->input->post('contractor_name');
        $contractor->email = $this->input->post('email');
        $contractor->phone = $this->input->post('phone');
        $contractor->alternative_phone = $this->input->post('alternative_phone');
        $contractor->address = $this->input->post('address');

        if($contractor->save()){
            if (!$edit) {
                $this->load->model('account');
                $account = new Account();
                $account->account_name = $contractor->contractor_name;
                $account->account_group_id = $contractor->contractor_acount_group_id();
                $account->description = ' Account Payable For ' . $contractor->contractor_name;
                $account->opening_balance = $this->input->post('account_opening_balance');
                if ($account->save()) {
                    $this->load->model('contractor_account');
                    $contractor_account = new Contractor_account();
                    $contractor_account->account_id = $account->{$account::DB_TABLE_PK};
                    $contractor_account->contractor_id = $contractor->{$contractor::DB_TABLE_PK};
                    $contractor_account->save();
                }
            }
            redirect(base_url('contractors/profile/'.$contractor->{$contractor::DB_TABLE_PK}));
        }
    }

    public function profile($id = 0){
        $contractor = new Contractor();
        if($contractor->load($id)){
            $this->load->model('suppliers_evaluation_factor');
            $options = new Suppliers_evaluation_factor();
            $data['title'] = $contractor->contractor_name;
            $data['contractor'] = $contractor;
            $data['currency_options'] = currency_dropdown_options();
            $data['enum_options'] = $options;
            $data['contractor_evaluation_factors'] = $this->suppliers_evaluation_factor->load_contractor_factor_and_points($id);
            $this->load->view('contractors/profile',$data);
        }
    }

    public function sub_contracts_list($sub_contractor_id=0){

        $this->load->model('Sub_contract');
        $posted_params = dataTable_post_params();
        if($posted_params['limit'] == null){
            $data['title'] = 'Sub-Contracts';
            $this->load->view('sub_contractors/sub_contracts_tab',$data);
        } else {
            echo $this->Sub_contract->sub_contracts_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$sub_contractor_id);
        }
    }

    public function load_contractor_dropdown_options()
    {
        echo stringfy_dropdown_options($this->contractor->contractor_options());
    }

    public function save_sub_contract_certificate(){
        $this->load->model('Sub_contract_certificate');
        $sub_contract_certificate = new Sub_contract_certificate();
        $sub_contract_certificate->sub_contract_id= $this->input->post('sub_contract_id');
        $sub_contract_certificate->certificate_number= $this->input->post('certificate_number');
        $sub_contract_certificate->certificate_date= $this->input->post('certificate_date');
        $sub_contract_certificate->certified_amount= $this->input->post('certified_amount');
        $sub_contract_certificate->vat_inclusive = $this->input->post('vat_inclusive');
        $sub_contract_certificate->vat_percentage = 18;
        $sub_contract_certificate->remarks= $this->input->post('remarks');
        $sub_contract_certificate->created_by = $this->session->userdata('employee_id');
        $sub_contract_certificate->save();

    }

    public function delete_sub_contract_certificate(){
        $this->load->model('Sub_contract_certificate');
        $sub_contract_certificate = new Sub_contract_certificate();
        $sub_contract_certificate->load($this->input->post('sub_contract_certificate_id'));
        $sub_contract_certificate->delete();
    }

    public function sub_contracts_certificate_list_table($sub_contract_id){

        $this->load->model('sub_contract_certificate');
        $posted_params = dataTable_post_params();
        echo $this->sub_contract_certificate->sub_contracts_certificate_list_table($sub_contract_id,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function sub_contracts_list_table($sub_contract_id){

        $this->load->model('sub_contract_item');
        $posted_params = dataTable_post_params();
        echo $this->sub_contract_item->sub_contracts_list_table($sub_contract_id,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function load_sub_contract_certificates(){
        $this->load->model('sub_contract');
        $sub_contract_id = $this->input->post('sub_contract_id');
        $sub_contract =  new Sub_contract();
        $sub_contract->load($sub_contract_id);
        echo stringfy_dropdown_options($sub_contract->certificates(true));
    }

    public function load_certificate_amount(){
        $this->load->model('sub_contract_certificate');
        $certificate_id = $this->input->post('certificate_id');
        $sub_contract_certificate = new Sub_contract_certificate();
        $sub_contract_certificate->load($certificate_id);
        $balance = $sub_contract_certificate->certified_amount - $sub_contract_certificate->approved_amount();
        if($balance > 0) {
            if ($sub_contract_certificate->vat_inclusive == 1) {
                $vat_amount = 0.01 * $sub_contract_certificate->vat_percentage * ($balance / 1.18);
                $ret_val['vat_amount'] = $vat_amount;
                $ret_val['cert_amount'] = $balance / 1.18;
            } else {
                $ret_val['vat_amount'] = 0;
                $ret_val['cert_amount'] = $balance;
            }
            echo json_encode($ret_val);
        }
    }

    // Contractor Evaluation

    public function save_subcontractor_evaluation()
    {
        $this->load->model(['suppliers_evaluation_factor','contractor_evaluation_score']);
        $contractor = $this->contractor_evaluation_score->get(1,0,['contractor_id' => $this->input->post('contractor_id')]);


        $supplier_evaluation = new Suppliers_evaluation_factor();
        $contractor_evaluation = new Contractor_evaluation_score();

        if ( $contractor) {
            $contractor_junction = array_shift($contractor);
            $evaluation_id = $contractor_junction->supplier_evaluation_factors_id;
            $junction_id = $contractor_junction->id;

            $supplier_evaluation->id = $evaluation_id;
            $contractor_evaluation->id = $junction_id;
        }

        $supplier_evaluation->general_experience = $this->input->post('general_experience');
        $supplier_evaluation->certificate_of_completion = $this->input->post('certificates_of_comletion');
        $supplier_evaluation->two_team_supervisors_with_atleast_a_bachelor_degree = $this->input->post('team_supervisors');
        $supplier_evaluation->financial_capacity_of_at_least_payment_of_workers_for_one_month = $this->input->post('financial_capacity');
        $supplier_evaluation->proof_of_training_of_casual_laborers = $this->input->post('casual_laborers');

        $supplier_evaluation->save();

        $last_supplier_evaluation = $this->suppliers_evaluation_factor->get(1,0, '', 'id DESC');
        $last_supplier_evaluation_id = array_shift($last_supplier_evaluation);

        $contractor_evaluation->contractor_id = $this->input->post('contractor_id');
        $contractor_evaluation->supplier_evaluation_factors_id = $last_supplier_evaluation_id->id;
        $contractor_evaluation->save();


    }

    public function check_points($loaded_choice = false)
    {
        $this->load->model('suppliers_evaluation_factor');
        $choice = $this->input->post('selector_value');

        echo $this->suppliers_evaluation_factor->factor_to_points($choice);
    }

    public function contractors_evaluation()
    {
        $this->load->model(['contractor_evaluation_score', 'suppliers_evaluation_factor', 'project']);
        $data['contractor_options'] = $this->contractor_evaluation_score->evaluated_contractors_options();
        $data['project_options'] = $this->project->project_dropdown_options();

        $contrator_ids = $this->input->post('contractors_ids');
        $project_id = $this->input->post('project_id');

        $contrator_ids = is_array($contrator_ids) ? array_filter($contrator_ids) : [];

        $contractors_data = [];

        if (!empty($contrator_ids)) {

            $triggered = $this->input->post('triggered') == 'true';

            $project = new Project();
            $project->load($project_id);
            $data['project'] = $project;


            foreach ($contrator_ids as $id){
                $contractor = new Contractor();
                $contractor->load($id);
                $evaluation_id = $this->contractor_evaluation_score->get(1,0, ['contractor_id' => $id]);
                if ($evaluation_id) {
                    $found_factor = array_shift($evaluation_id);
                    $found_id = $found_factor->supplier_evaluation_factors_id;

                    $data2 = [];

                    $evaluation_factor = new Suppliers_evaluation_factor();
                    $evaluation_factor->load($found_id);
                    $total = 0;

                    $count = 1;
                    foreach ($evaluation_factor as $factor){

                        if($count > 1){
                            if ($this->suppliers_evaluation_factor->factor_to_points($factor) != '') {
                                $data2[] = $this->suppliers_evaluation_factor->factor_to_points($factor);
                            }else{
                                $data2[] = 0;
                            }

                            $total += $this->suppliers_evaluation_factor->factor_to_points($factor);
                        }
                        $count++;
                    }
                    $data2[] = $total;
                    $data2[] = $contractor->contractor_name;
                    $contractors_data[] = $data2;

                }
            }

            $data['contractors_data'] = $contractors_data;

            if ($triggered) {
                $data['print'] = isset($print);

                $html = $this->load->view('contractors/contractor_evaluation_sheet', $data, true);

                $this->load->library('m_pdf');
                $pdf = $this->m_pdf->load();

                $pdf->AddPage(
                    '', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6,'','','','','','','','','','A4-L'
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

                $pdf->Output('Contractor Evaluations.pdf', 'I');

            }else{
                echo $this->load->view('contractors/contractor_evaluation_table', $data, true);
            }
        } else{
            $data['triggered'] = false;
            $this->load->view('contractors/contractor_evaluation', $data);
        }

    }

    public function get_en()
    {
        $this->load->model('suppliers_evaluation_factor');
        inspect_object($this->suppliers_evaluation_factor->get_enum_values());
    }


}
