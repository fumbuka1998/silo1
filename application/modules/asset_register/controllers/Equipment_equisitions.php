<?php

class Equipment_equisitions extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
     }

    public function index(){
        $limit = $this->input->post('length');
        if ($limit != '') {
            $this->load->model('requisition');
            $posted_params = dataTable_post_params();
            echo $this->requisition->requisitions_list($this->input->post('location_id'),$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $data['title'] = 'Requisitions';
            $data['approval_module_options'] = approval_module_dropdown_options();
            $this->load->model('account');
            $data['expense_accounts_options'] = $this->account->dropdown_options(['DIRECT EXPENSES','INDIRECT EXPENSES']);
            $this->load->view('equipments_requisitions/index', $data);
        }
    }
    

    public function save_requisition(){
        $this->load->model('requisition');
        $requisition = new Requisition();
        $edit = $requisition->load($this->input->post('requisition_id'));
        $requisition->required_date = trim($this->input->post('required_date'));
        $requisition->approval_module_id = $this->input->post('approval_module_id');
        $requisition->required_date = $requisition->required_date != '' ? $requisition->required_date : null;
        $requisition->requesting_comments = $this->input->post('comments');
        $requisition->request_date = $this->input->post('request_date');
        $requisition->status = 'PENDING';
        $requisition->requester_id = $this->session->userdata('employee_id');
        if($requisition->save()){
            if ($edit) {
                $requisition->delete_items();
            } else {
                if ($requisition->approval_module_id == '2') {
                    $requisition->insert_project_requisition($this->input->post('requisition_cost_center_id'));
                } else {
                    $requisition->insert_cost_center_requisition($this->input->post('requisition_cost_center_id'));
                }
            }
            $this->load->model(['requisition_material_item', 'requisition_cash_item']);
            $item_types = $this->input->post('item_types');
            $item_ids = $this->input->post('item_ids');
            $quantities = $this->input->post('quantities');
            $rates = $this->input->post('rates');
            $expense_account_ids = $this->input->post('expense_account_ids');
            $currency_id = $this->input->post('currency_id');
            $vendor_or_unit_ids = $this->input->post('vendor_or_unit_ids');
            $cost_center_ids = $this->input->post('cost_center_ids');
            foreach ($item_types as $index => $item_type){
                if($quantities[$index] > 0){
                    $requisition_item = $item_type == 'material' ? new Requisition_material_item() : new Requisition_cash_item();
                    if($item_type == 'material'){
                        $requisition_item->material_item_id = $item_ids[$index];
                        $requisition_item->requested_vendor_id = $vendor_or_unit_ids[$index];
                        $requisition_item->requested_vendor_id = $requisition_item->requested_vendor_id != '' ? $requisition_item->requested_vendor_id : null;
                    } else {
                        $requisition_item->description = $item_ids[$index];
                        $requisition_item->measurement_unit_id = $vendor_or_unit_ids[$index];
                    }
                    $requisition_item->requisition_id = $requisition->{$requisition::DB_TABLE_PK};
                    $requisition_item->requested_quantity = $quantities[$index];
                    $requisition_item->requested_rate = $rates[$index];
                    $requisition_item->requested_currency_id = $currency_id;
                    $requisition_item->expense_account_id = $expense_account_ids[$index];
                    $requisition_item->expense_account_id = $requisition_item->expense_account_id != '' ? $requisition_item->expense_account_id : null;
                    if($requisition_item->save()){
                        if($cost_center_ids[$index] != ''){
                            $requisition_item->insert_task_junction($cost_center_ids[$index]);
                        }
                    }
                }
            }
        }
    }

    public function save_requisition_attachment()
    {
        $this->load->model('requisition_attachment');
        $attachment = new Requisition_attachment();
        $requisition_id = $this->input->post('requisition_id');
        $requisition_directory = "./uploads/requisition_attachments/" . $requisition_id . '/';
        if (!file_exists($requisition_directory)) {
            mkdir($requisition_directory);
        }

        inspect_object($_FILES);

        $config = [
            'upload_path' => $requisition_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];

        $this->load->library('upload', $config);
        if (!empty($_FILES['file'])) {
            if ($this->upload->do_upload('file')) {
                $attachment->attachment_name = $this->upload->data()['file_name'];
                $attachment->datetime_attached = datetime();
                $attachment->caption = $this->input->post('caption');
                $attachment->requisition_id = $requisition_id;
                $attachment->employee_id = $this->session->userdata('employee_id');
                if ($attachment->save()) {
                    $requisition = $attachment->requisition();
                    $action = 'Requisition Attachment Upload';
                    $description = 'A new attachment was uploaded to requisition number ' . $requisition->requisition_number();
                    system_log($action, $description);
                }
            }
        }
    }

    public function requisition_attachments()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($this->input->post('requisition_id'));
        $this->load->view('requisitions/requisition_attachments', ['requisition' => $requisition]);
    }

    public function delete_requisition_attachment()
    {
        $this->load->model('requisition_attachment');
        $attachment = new Requisition_attachment();
        if ($attachment->load($this->input->post('attachment_id'))) {
            $file_path = './uploads/requisition_attachments/' . $attachment->requisition_id . '/' . $attachment->attachment_name;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $attachment->delete();

            $requisition = $attachment->requisition();
            $action = 'Requisition Attachment Delete';
            $description = 'An attachment from requisition number ' . $requisition->requisition_number() . ' was deleted';
            system_log($action, $description);
        }
    }

    public function preview_requisition($requisition_id = 0)
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        if ($requisition->load($requisition_id)) {
            $data['requisition'] = $requisition;
            $html = $this->load->view('requisitions/requisition_sheet', $data, true);
            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage('L', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6); // margin footer
            if ($requisition->status == "DECLINED") {
                $pdf->SetWatermarkText("DECLINED");
                $pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.1;
                $pdf->SetDisplayMode('fullpage');
            }

            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('requisition_' . $requisition->requisition_number() . '.pdf', 'I'); // view in the explorer
        } else {
            redirect(base_url());
        }
    }

    public function preview_requisition_approved_chains($requisition_id = 0){
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($requisition_id);
        $data['requisition'] = $requisition;
        $html = $this->load->view('requisitions/approval_chain_sheet', $data, true);
        //this the PDF filename that user will get to download

        //load mPDF library
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();
        $pdf->AddPage('L', // L - landscape, P - portrait
            '', '', '', '',
            15, // margin_left
            15, // margin right
            15, // margin top
            15, // margin bottom
            9, // margin header
            6); // margin footer
        if ($requisition->status == "DECLINED") {
            $pdf->SetWatermarkText("DECLINED");
            $pdf->SetProtection(array('print'));
            $pdf->showWatermarkText = true;
            $pdf->watermark_font = 'DejaVuSansCondensed';
            $pdf->watermarkTextAlpha = 0.1;
            $pdf->SetDisplayMode('fullpage');
        }

        $pdf->WriteHTML($html);
        //$this->mpdf->Output($file_name, 'D'); // download force
        $pdf->Output('requisition_approval_' . $requisition->requisition_number() . '.pdf', 'I'); // view in the explorer

    }

    public function approve_requisition()
    {
        $this->load->model('requisition_approval');
        $approval = new Requisition_approval();
        $approval->approval_chain_level_id = $this->input->post('approval_chain_level_id');
        $approval->approved_date = $this->input->post('approve_date');
        $approval->requisition_id = $this->input->post('requisition_id');
        $approval->created_at = datetime();
        $approval->created_by = $this->session->userdata('employee_id');
        $approval->has_sources = $this->input->post('has_sources');
        $approval->has_sources = $approval->has_sources == 'true' ? true : false;
        $approval->is_final = 0;
        $approval->approving_comments = $this->input->post('comments');
        if($approval->save()){
            $this->load->model(['requisition_approval_material_item','requisition_approval_cash_item']);
            $item_types = $this->input->post('item_types');
            $item_ids = $this->input->post('item_ids');
            $quantities = $this->input->post('quantities');
            $rates = $this->input->post('rates');
            $currency_ids = $this->input->post('currency_ids');
            $expense_account_ids = $this->input->post('expense_account_ids');
            $sources = $this->input->post('sources');
            $sources_types = $this->input->post('source_types');

            foreach ($item_ids as $index => $item_id){
                //Save expense account
                $approval_item_type_model = $item_types[$index] == 'material' ? 'requisition_approval_material_item' : 'requisition_approval_cash_item';
                $expense_account_id = $expense_account_ids[$index];
                $expense_account_id = $expense_account_id != '' ? $expense_account_id : null;
                $this->$approval_item_type_model->save_approval_expense_account($approval->{$approval::DB_TABLE_PK},$item_id,$expense_account_id);

                if($approval->has_sources) {
                    $item_quantities = $quantities[$index];

                    if($item_types[$index] == 'material'){
                        foreach ($item_quantities as $item_index => $quantity) {
                            $approved_item = new Requisition_approval_material_item();
                            $approved_item->approved_quantity = $item_quantities[$item_index];
                            $approved_item->approved_rate = $rates[$index][$item_index];
                            $approved_item->currency_id = $currency_ids[$index][$item_index];
                            $approved_item->source_type = $sources_types[$index][$item_index];
                            $approved_item->requisition_material_item_id = $item_id;
                            $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                            $source_type = $sources_types[$index][$item_index];
                            if($source_type == 'cash'){
                                $approved_item->account_id = $sources[$index][$item_index];
                                $approved_item->source_type = $source_type;
                            } else if($source_type == 'store'){
                                $approved_item->location_id = $sources[$index][$item_index];
                                $approved_item->source_type = $source_type;
                            } else {
                                $approved_item->vendor_id = $sources[$index][$item_index];
                                $approved_item->source_type = $source_type;
                            }
                            $approved_item->save();
                        }
                    } else {
                        foreach ($item_quantities as $item_index => $quantity){
                            $approved_item = new Requisition_approval_cash_item();
                            $approved_item->account_id = $sources[$index][$item_index];
                            $approved_item->approved_quantity = $item_quantities[$item_index];
                            $approved_item->approved_rate = $rates[$index][$item_index];
                            $approved_item->currency_id = $currency_ids[$index][$item_index];
                            $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                            $approved_item->requisition_cash_item_id = $item_id;
                            $approved_item->save();
                        }
                    }
                } else {
                    if($item_types[$index] == 'material'){
                        $approved_item =  new Requisition_approval_material_item();
                        $approved_item->requisition_material_item_id = $item_id;
                    } else {
                        $approved_item =  new Requisition_approval_cash_item();
                        $approved_item->requisition_cash_item_id = $item_id;
                    }
                    $approved_item->approved_quantity = $quantities[$index];
                    $approved_item->approved_rate = $rates[$index];
                    $approved_item->currency_id = $currency_ids[$index];
                    $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                    $approved_item->save();
                }
            }

            $requisition = $approval->requisition();
            if($approval->has_sources && !$requisition->current_approval_level()){
                $approval->is_final = 1;
                $approval->save();
                $requisition->status = 'APPROVED';
                $requisition->finalized_date = $approval->approved_date;
                $requisition->finalizer_id = $approval->created_by;
                $requisition->save();
            }
        }
    }

    public function delete_requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        if ($requisition->load($this->input->post('requisition_id'))) {
            $requisition->delete();
        }
    }

    public function decline_requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        if ($requisition->load($this->input->post('requisition_id'))) {
            $requisition->status = 'DECLINED';
            $location = $requisition->location();
            if ($requisition->save()) {
                $description = 'A requisition from ' . $location->location_name . ' with number ' . $requisition->requisition_number() . ' was  declined';
                system_log('Requisition Declination', $description, $location->project_id);
            }
        }
    }
}

