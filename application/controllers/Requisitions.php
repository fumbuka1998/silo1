<?php

class Requisitions extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_login();
    }

    public function index()
    {
        $data['title'] = 'Requisitions';
        $data['material_options'] = material_item_dropdown_options('all');
        $this->load->view('requisitions/index', $data);
    }

    public function requisitions_list()
    {
        $limit = $this->input->post('length');
        $this->load->model(['requisition', 'approval_module', 'approval_chain_level']);
        if ($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->requisition->requisitions_list($this->input->post('project_id'), $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $approval_levels = $this->approval_chain_level->get(0, 0, ['status' => 'active'], 'level ASC');
            $approver_employees = [];
            foreach ($approval_levels as $approval_level) {
                $approver_employees[$approval_level->{$approval_level::DB_TABLE_PK}] = $approval_level->level_name;
            }

            $data['approver_employees'] = $approver_employees;
            $data['approval_modules'] = $this->approval_module->approval_module_options(2);
            $data['title'] = 'Requisitions';
            $data['approval_module_options'] = [
                '' => '&nbsp;',
                1 => 'General Requisition',
                2 => 'Project Requisition'
            ];
            $data['material_options'] = material_item_dropdown_options('all');
            $data['vat_options'] = $this->requisition->vat_enum_values('vat_inclusive');
            $this->load->model('account');
            $data['expense_accounts_options'] = $this->account->dropdown_options(['DIRECT EXPENSES', 'INDIRECT EXPENSES']);
            $this->load->view('requisitions/requisitions_list/index', $data);
        }
    }

    public function edit_requisition_form($requisition_id)
    {
        $this->load->model(['requisition', 'approval_module', 'stakeholder', 'account']);
        $requisition = new Requisition();
        $requisition->load($requisition_id);
        $approval_module = new Approval_module();
        $approval_module->load($requisition->approval_module_id);
        $data['requisition'] = $requisition;
        $data['currency_options'] = currency_dropdown_options();
        $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
        //$data['approval_module_options'] = approval_module_dropdown_options();
        $data['main_location_options'] = locations_options('main');
        $data['account_options'] = ['' => '&nbsp;'];
        $data['asset_items_options'] = asset_item_dropdown_options();
        $data['expense_accounts_options'] = $this->account->dropdown_options(['DIRECT EXPENSES', 'INDIRECT EXPENSES']);
        $data['payment_voucher_print_out_link'] = 'Finance/preview_payment_voucher/';
        $data['journal_voucher_print_out_link'] = 'Finance/preview_journal_voucher/';
        $data['imprest_voucher_print_out_link'] = 'Finance/preview_imprest_voucher/';
        $data['last_approval'] = $last_approval = $requisition->last_approval();
        $data['payment_voucher'] = false;
        $data['journal_voucher'] = false;
        $data['imprest_voucher'] = false;
        if ($last_approval) {
            $data['payment_voucher'] = $payment_voucher = $last_approval->payment_voucher();
            $data['imprest_voucher'] = $imprest_voucher = $last_approval ? $last_approval->imprest_voucher($last_approval->{$last_approval::DB_TABLE_PK}) : false;
        }
        if ($data['payment_voucher']) {
            $data['paid_items'] = $paid_items = $last_approval->payment_vouchers();
        }
        $data['forward_to_dropdown'] = $approval_module->forwarding_to_employee_options();
        $data['forwarded_to_employee'] = $requisition->forwarded_to_employee_approval();
        $data['can_override_prev'] = $can_override_prev = $this->approval_module->employee_power($requisition->approval_module_id, 'requisition', $requisition_id);
        $data['employees_with_special_approval'] = $requisition->special_level_approval(true);
        $data['approval_module'] = $requisition->approval_module();
        $data['requisition_approval'] = $requisition->requisition_approval();
        $data['attachments'] = $requisition->attachments();
        $data['approved_print_out_link'] = 'requisitions/preview_requisition/';
        $data['requisition_number'] =  $requisition->requisition_number();
        return $this->load->view('requisitions/requisitions_list/edit_requisition_modal_body', $data);
    }

    public function approve_requisition_form($requisition_id)
    {
        $this->load->model(['requisition', 'approval_module', 'stakeholder', 'account']);
        $requisition = new Requisition();
        $requisition->load($requisition_id);
        $approval_module = new Approval_module();
        $approval_module->load($requisition->approval_module_id);

        $data['requisition'] = $requisition;
        $data['currency_options'] = currency_dropdown_options();
        $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
        //$data['approval_module_options'] = approval_module_dropdown_options();
        $data['main_location_options'] = locations_options('main');
        $data['account_options'] = ['' => '&nbsp;'];
        $data['asset_items_options'] = asset_item_dropdown_options();
        $data['expense_accounts_options'] = $this->account->dropdown_options(['DIRECT EXPENSES', 'INDIRECT EXPENSES']);
        $data['payment_voucher_print_out_link'] = 'Finance/preview_payment_voucher/';
        $data['journal_voucher_print_out_link'] = 'Finance/preview_journal_voucher/';
        $data['imprest_voucher_print_out_link'] = 'Finance/preview_imprest_voucher/';
        $data['last_approval'] = $last_approval = $requisition->last_approval();
        $data['payment_voucher'] = false;
        $data['journal_voucher'] = false;
        $data['imprest_voucher'] = false;
        if ($last_approval) {
            $data['payment_voucher'] = $payment_voucher = $last_approval->payment_voucher();
            $data['imprest_voucher'] = $imprest_voucher = $last_approval ? $last_approval->imprest_voucher($last_approval->{$last_approval::DB_TABLE_PK}) : false;
        }
        if ($data['payment_voucher']) {
            $data['paid_items'] = $paid_items = $last_approval->payment_vouchers();
        }
        $data['forward_to_dropdown'] = $this->employees_next_in_chain($requisition_id);
        $data['forwarded_to_employee'] = $requisition->forwarded_to_employee_approval();
        $data['can_override_prev'] = $can_override_prev = $this->approval_module->employee_power($requisition->approval_module_id, 'requisition', $requisition_id);
        $data['employees_with_special_approval'] = $requisition->special_level_approval(true);
        $data['approval_module'] = $requisition->approval_module();
        $data['requisition_approval'] = $requisition->requisition_approval();
        $data['attachments'] = $requisition->attachments();
        $data['approved_print_out_link'] = 'requisitions/preview_requisition/';
        $data['requisition_number'] =  $requisition->requisition_number();
        return $this->load->view('requisitions/requisitions_list/approve_requisition_modal_body', $data);
    }

    public function enquiries_list()
    {
        $limit = $this->input->post('length');
        $this->load->model(['enquiry', 'approval_module', 'stakeholder', 'asset_item']);
        if ($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->enquiry->enquiries_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $data['title'] = 'Requisitions | Enquiries';
            $data['material_options'] = material_item_dropdown_options('all');
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['asset_options'] = $this->asset_item->dropdown_options();
            $this->load->view('requisitions/enquiries/index', $data);
        }
    }

    public function save_requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $currency_id = $this->input->post('currency_id');
        $foward_to = $this->input->post('foward_to');
        $edit = $requisition->load($this->input->post('requisition_id'));
        $requisition->required_date = trim($this->input->post('required_date'));
        $requisition->approval_module_id = $this->input->post('approval_module_id');
        $requisition->required_date = $requisition->required_date != '' ? $requisition->required_date : null;
        $requisition->freight = $this->input->post('freight');
        $requisition->inspection_and_other_charges = $this->input->post('inspection_and_other_charges');
        $requisition->vat_inclusive = $this->input->post('vat_inclusive') == 'NULL' ? Null : $this->input->post('vat_inclusive');
        $requisition->vat_percentage = $this->input->post('vat_percentage');
        $requisition->requesting_comments = $this->input->post('comments');
        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $requisition->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
        $requisition->foward_to = $foward_to != '' ? $foward_to : null;
        $requisition->request_date = $this->input->post('request_date');
        if ($requisition->status != 'APPROVED') {
            $requisition->status = $this->input->post('status');
        }
        $requisition->requester_id = $this->session->userdata('employee_id');
        $requisition->currency_id = $currency_id;
        if ($requisition->status != 'APPROVED') {
            if (!$edit) {
                if ($requisition->save()) {
                    if ($requisition->approval_module_id == '2') {
                        $requisition->insert_project_requisition($this->input->post('requisition_cost_center_id'));
                    } else {
                        $requisition->insert_cost_center_requisition($this->input->post('requisition_cost_center_id'));
                    }
                    $this->load->model(['requisition_material_item', 'requisition_cash_item', 'requisition_asset_item', 'requisition_service_item']);
                    $item_types = $this->input->post('item_types');
                    $item_ids = $this->input->post('item_ids');
                    $quantities = $this->input->post('quantities');
                    $rates = $this->input->post('rates');
                    $source_or_unit_ids = $this->input->post('source_or_unit_ids');
                    $unit_ids = $this->input->post('unit_ids');
                    $cost_center_ids = $this->input->post('cost_center_ids');
                    $source_types = $this->input->post('source_types');
                    foreach ($item_types as $index => $item_type) {
                        if ($quantities[$index] > 0) {

                            if ($item_type == 'material') {
                                $requisition_item = new Requisition_material_item();
                                $requisition_item->material_item_id = $item_ids[$index];
                                $requisition_item->source_type = $source_types[$index];
                                $requisition_item->requested_currency_id = $currency_id;
                                if ($requisition_item->source_type == 'vendor') {
                                    $requisition_item->requested_vendor_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_vendor_id = $requisition_item->requested_vendor_id != '' ? $requisition_item->requested_vendor_id : null;
                                } else if ($requisition_item->source_type == 'store') {
                                    $requisition_item->requested_location_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_location_id = $requisition_item->requested_location_id != '' ? $requisition_item->requested_location_id : null;
                                } else if ($requisition_item->source_type == 'imprest') {
                                    $requisition_item->requested_account_id = $source_or_unit_ids[$index];
                                } else if ($requisition_item->source_type == 'cash') {
                                    $requisition_item->payee = $source_or_unit_ids[$index];
                                    $requisition_item->requested_account_id = $requisition_item->requested_account_id != '' ? $requisition_item->requested_account_id : null;
                                } else {
                                    $requisition_item->requested_location_id = $requisition_item->requested_vendor_id = $requisition_item->source_type = null;
                                }
                            } else if ($item_type == 'asset') {
                                $requisition_item = new Requisition_asset_item();
                                $requisition_item->asset_item_id = $item_ids[$index];
                                $requisition_item->source_type = $source_types[$index];
                                $requisition_item->requested_currency_id = $currency_id;
                                if ($requisition_item->source_type == 'vendor') {
                                    $requisition_item->requested_vendor_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_vendor_id = $requisition_item->requested_vendor_id != '' ? $requisition_item->requested_vendor_id : null;
                                } else if ($requisition_item->source_type == 'store') {
                                    $requisition_item->requested_location_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_location_id = $requisition_item->requested_location_id != '' ? $requisition_item->requested_location_id : null;
                                } else if ($requisition_item->source_type == 'imprest') {
                                    $requisition_item->requested_account_id = $source_or_unit_ids[$index];
                                } else if ($requisition_item->source_type == 'cash') {
                                    $requisition_item->payee = $source_or_unit_ids[$index];;
                                } else {
                                    $requisition_item->requested_location_id = $requisition_item->requested_vendor_id = $requisition_item->source_type = null;
                                }
                            } else if ($item_type == 'service') {
                                $requisition_item = new Requisition_service_item();
                                $requisition_item->description = $item_ids[$index];
                                $requisition_item->source_type = $source_types[$index];
                                if ($requisition_item->source_type == 'vendor') {
                                    $requisition_item->requested_vendor_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_vendor_id = $requisition_item->requested_vendor_id != '' ? $requisition_item->requested_vendor_id : null;
                                } else if ($requisition_item->source_type == 'imprest') {
                                    $requisition_item->requested_account_id = $source_or_unit_ids[$index];
                                } else {
                                    $requisition_item->payee = $source_or_unit_ids[$index];
                                }
                                $requisition_item->measurement_unit_id = $unit_ids[$index];
                            } else {
                                $requisition_item = new Requisition_cash_item();
                                $requisition_item->description = $item_ids[$index];
                                $requisition_item->payee = $source_or_unit_ids[$index];
                                $requisition_item->measurement_unit_id = $unit_ids[$index];
                                $requisition_item->requested_currency_id = $currency_id;
                            }

                            $requisition_item->requisition_id = $requisition->{$requisition::DB_TABLE_PK};
                            $requisition_item->requested_quantity = $quantities[$index];
                            $requisition_item->requested_rate = $rates[$index];
                            if ($requisition_item->save()) {
                                if ($cost_center_ids[$index] != '') {
                                    $requisition_item->insert_task_junction($cost_center_ids[$index]);
                                }
                            }
                        }
                    }

                    if ($requisition->status == 'PENDING') {
                        $requisition->log_requisiton('Submission', 'submit');
                        $current_level = $requisition->current_approval_level();
                        $employees_to_approve = $current_level->employees();

                        $addresses = $recipients = [];
                        foreach ($employees_to_approve as $employee) {
                            if ($employee->email != '') {
                                $addresses[] = $employee->email;
                                $recipients[] = $employee->phone;
                            }
                        }

                        $this->load->library('email');
                        $config = array(
                            'protocol' => 'smtp',
                            'smtp_host' => 'ssl://chir101.websitehostserver.net',
                            'smtp_port' => 465,
                            'smtp_user' => 'noreply@epmtz.com', // change it to yours
                            'smtp_pass' => 'stunnamadeit@123', // change it to yours
                            'mailtype' => 'html',
                            'smtp_timeout' => 60,
                            'charset' => 'iso-8859-1',
                            'wordwrap' => TRUE
                        );

                        $this->email->initialize($config);

                        $this->email->initialize($config);
                        $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                        $subject = 'REQUISITION NO: ' . $requisition->{$requisition::DB_TABLE_PK} . ' FOR ' . $requisition->cost_center_name();
                        $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $requisition->requester()->full_name() . ' submitted a requisition that is waiting for your approval
                            in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for requisition no ' . $requisition->{$requisition::DB_TABLE_PK} . '<hr/></div><br/>';
                        $content .= $this->preview_requisition($requisition->{$requisition::DB_TABLE_PK}, 'true');

                        $message = $this->load->view('includes/email', ['content' => $content], true);

                        $this->email->to($addresses);
                        $this->email->subject($subject);
                        $this->email->set_mailtype("html");
                        $this->email->message($message);
                        $this->email->send();


                        $sms_message = 'Greetings,
                        ' . $requisition->requester()->full_name() . ' submitted a requisition that is waiting for your approval in the system.
                        Please go to ' . base_url() . ' and search for requisition no ' . $requisition->{$requisition::DB_TABLE_PK} . '.
                        
                        Received at ' . standard_datetime();
                        //inspect_object($sms_message);
                        //exit;
                        //send_sms($recipients, $sms_message);
                    }
                }
            } else {

                if ($requisition->status != 'APPROVED' && $requisition->save()) {
                    $requisition->delete_items();
                    $this->load->model(['requisition_material_item', 'requisition_cash_item', 'requisition_asset_item', 'requisition_service_item']);
                    $item_types = $this->input->post('item_types');
                    $item_ids = $this->input->post('item_ids');
                    $quantities = $this->input->post('quantities');
                    $rates = $this->input->post('rates');
                    $source_or_unit_ids = $this->input->post('source_or_unit_ids');
                    $unit_ids = $this->input->post('unit_ids');
                    $cost_center_ids = $this->input->post('cost_center_ids');
                    $source_types = $this->input->post('source_types');
                    foreach ($item_types as $index => $item_type) {
                        if ($quantities[$index] > 0) {

                            if ($item_type == 'material') {
                                $requisition_item = new Requisition_material_item();
                                $requisition_item->material_item_id = $item_ids[$index];
                                $requisition_item->source_type = $source_types[$index];
                                $requisition_item->requested_currency_id = $currency_id;
                                if ($requisition_item->source_type == 'vendor') {
                                    $requisition_item->requested_vendor_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_vendor_id = $requisition_item->requested_vendor_id != '' ? $requisition_item->requested_vendor_id : null;
                                } else if ($requisition_item->source_type == 'store') {
                                    $requisition_item->requested_location_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_location_id = $requisition_item->requested_location_id != '' ? $requisition_item->requested_location_id : null;
                                } else if ($requisition_item->source_type == 'imprest') {
                                    $requisition_item->requested_account_id = $source_or_unit_ids[$index];
                                } else if ($requisition_item->source_type == 'cash') {
                                    $requisition_item->payee = $source_or_unit_ids[$index];
                                    $requisition_item->requested_account_id = $requisition_item->requested_account_id != '' ? $requisition_item->requested_account_id : null;
                                } else {
                                    $requisition_item->requested_location_id = $requisition_item->requested_vendor_id = $requisition_item->source_type = null;
                                }
                            } else if ($item_type == 'asset') {
                                $requisition_item = new Requisition_asset_item();
                                $requisition_item->asset_item_id = $item_ids[$index];
                                $requisition_item->source_type = $source_types[$index];
                                $requisition_item->requested_currency_id = $currency_id;
                                if ($requisition_item->source_type == 'vendor') {
                                    $requisition_item->requested_vendor_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_vendor_id = $requisition_item->requested_vendor_id != '' ? $requisition_item->requested_vendor_id : null;
                                } else if ($requisition_item->source_type == 'store') {
                                    $requisition_item->requested_location_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_location_id = $requisition_item->requested_location_id != '' ? $requisition_item->requested_location_id : null;
                                } else if ($requisition_item->source_type == 'imprest') {
                                    $requisition_item->requested_account_id = $source_or_unit_ids[$index];
                                } else if ($requisition_item->source_type == 'cash') {
                                    $requisition_item->payee = $source_or_unit_ids[$index];
                                } else {
                                    $requisition_item->requested_location_id = $requisition_item->requested_vendor_id = $requisition_item->source_type = null;
                                }
                            } else if ($item_type == 'service') {
                                $requisition_item = new Requisition_service_item();
                                $requisition_item->description = $item_ids[$index];
                                $requisition_item->source_type = $source_types[$index];
                                if ($requisition_item->source_type == 'vendor') {
                                    $requisition_item->requested_vendor_id = $source_or_unit_ids[$index];
                                    $requisition_item->requested_vendor_id = $requisition_item->requested_vendor_id != '' ? $requisition_item->requested_vendor_id : null;
                                } else if ($requisition_item->source_type == 'imprest') {
                                    $requisition_item->requested_account_id = $source_or_unit_ids[$index];
                                } else {
                                    $requisition_item->payee = $source_or_unit_ids[$index];
                                }
                                $requisition_item->measurement_unit_id = $unit_ids[$index];
                            } else {
                                $requisition_item = new Requisition_cash_item();
                                $requisition_item->description = $item_ids[$index];
                                $requisition_item->measurement_unit_id = $unit_ids[$index];
                                $requisition_item->payee = $source_or_unit_ids[$index];
                                $requisition_item->requested_currency_id = $currency_id;
                            }

                            $requisition_item->requisition_id = $requisition->{$requisition::DB_TABLE_PK};
                            $requisition_item->requested_quantity = $quantities[$index];
                            $requisition_item->requested_rate = $rates[$index];
                            if ($requisition_item->save()) {
                                $requisition->log_requisiton('Update', 'updat');
                                if ($cost_center_ids[$index] != '') {
                                    $requisition_item->insert_task_junction($cost_center_ids[$index]);
                                }
                            }
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
        $this->load->view('requisitions/requisitions_list/requisition_attachments', ['requisition' => $requisition]);
    }

    public function preview_requisition($requisition_id = 0, $string_for_email = false)
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        if ($requisition->load($requisition_id)) {
            $data['requisition'] = $requisition;
            $data['current_approval_level'] = $requisition->current_approval_level();
            $requisition_approvals = $requisition->requisition_approvals();
            $data['requisition_approvals'] = [];
            foreach ($requisition_approvals as $approval) {
                $data['requisition_approvals'][$approval->approval_chain_level_id] = $approval;
            }
            $data['chain_levels'] = $requisition->approval_module()->chain_levels(0, null, 'active', 'all');
            $data['material_items'] = $requisition->material_items();
            $data['asset_items'] = $requisition->asset_items();
            $data['cash_items'] = $requisition->cash_items();
            $html = $this->load->view('requisitions/requisitions_list/requisition_sheet', $data, true);

            if ($string_for_email) {
                return $html;
            }

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            if ($requisition->status == "REJECTED") {
                $pdf->SetWatermarkText("REJECTED");
                $pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            } else if ($requisition->status == "INCOMPLETE") {
                $pdf->SetWatermarkText("INCOMPLETE REQUISITION");
                //$pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            } else if ($requisition->is_printed != null) {
                //$pdf->SetWatermarkText("COPY");
                //$pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            }
            foreach ($requisition_approvals as $approval) {
                if ($approval->is_final == 1 && $requisition->is_printed == null) {
                    $requisition->is_printed = $this->session->userdata('employee_id');
                    //$requisition->save();
                }
            }
            $footercontents = '
                <div>
                    <div style="text-align: left; float: left; width: 50%">
                        <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . "-" . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                    </div>
                    <div>
                        <span>' . $requisition->requisition_number() . '</span>
                    </div>
                    <div style="text-align: center">
                    Page {PAGENO} of {nb}
                    </div>
                </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('requisition_' . $requisition->requisition_number() . '.pdf', 'I'); // view in the explorer
        } else {
            redirect(base_url());
        }
    }

    public function preview_requisition_approved_chains($requisition_id = 0)
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        $requisition->load($requisition_id);
        $data['requisition'] = $requisition;
        echo $html = $this->load->view('requisitions/requisitions_list/approval_chain_sheet', $data, true);
        exit();
        //this the PDF filename that user will get to download

        //load mPDF library
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
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
        if ($requisition->status == "DECLINED") {
            $pdf->SetWatermarkText("DECLINED");
            $pdf->SetProtection(array('print'), 'stunnamadeot@123', 'stunnamadeot@123');
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
        $this->load->model(['requisition', 'requisition_approval', 'approval_chain_level']);
        $approval = new Requisition_approval();
        $approval->approval_chain_level_id = $this->input->post('approval_chain_level_id');
        $approval->returned_chain_level_id = $this->input->post('returned_chain_level_id') != 0 ? $this->input->post('returned_chain_level_id') : null;

        $approval->approved_date = $this->input->post('approve_date');
        $approval->requisition_id = $this->input->post('requisition_id');
        $approval->created_at = datetime();
        $approval->created_by = $this->session->userdata('employee_id');
        $approval->has_sources = $this->input->post('has_sources');
        $approval->has_sources = $approval->has_sources == 'true' ? true : false;
        $currency_id = $this->input->post('currency_id');
        $approval->is_final = 0;
        $forward_to = $this->input->post('forward_to');
        $approval->forward_to = $forward_to != '' ? $forward_to : null;
        $approval->freight = $this->input->post('freight');
        $approval->inspection_and_other_charges = $this->input->post('inspection_and_other_charges');
        $approval->vat_inclusive = $this->input->post('vat_inclusive') == 'NULL' ? Null : $this->input->post('vat_inclusive');
        $approval->vat_percentage = $this->input->post('vat_percentage');
        $approval->approving_comments = $this->input->post('comments');

        if ($approval->save()) {
            $this->load->model(['requisition_approval_material_item', 'requisition_approval_asset_item', 'requisition_approval_service_item', 'requisition_approval_cash_item']);
            $item_types = $this->input->post('item_types');
            $item_ids = $this->input->post('item_ids');
            $quantities = $this->input->post('quantities');
            $rates = $this->input->post('rates');
            $sources = $this->input->post('sources');
            $sources_types = $this->input->post('source_types');

            foreach ($item_ids as $index => $item_id) {
                if ($approval->has_sources) {
                    $item_quantities = $quantities[$index];
                    if ($item_types[$index] == 'material') {
                        foreach ($item_quantities as $item_index => $quantity) {
                            if ($quantity > 0) {
                                $approved_item = new Requisition_approval_material_item();
                                $approved_item->approved_quantity = $item_quantities[$item_index];
                                $approved_item->approved_rate = $rates[$index][$item_index];
                                $approved_item->currency_id = $currency_id;
                                $approved_item->source_type = $sources_types[$index][$item_index];
                                $approved_item->requisition_material_item_id = $item_id;
                                $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                                $source_type = $sources_types[$index][$item_index];
                                if ($source_type == 'cash') {
                                    $approved_item->payee = $sources[$index][$item_index];
                                    $approved_item->account_id = $approved_item->account_id != '' ? $approved_item->account_id : null;
                                    $approved_item->source_type = $source_type;
                                } else if ($source_type == 'imprest') {
                                    $approved_item->account_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                } else if ($source_type == 'store') {
                                    $approved_item->location_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                } else {
                                    $approved_item->vendor_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                }
                                $approved_item->save();
                            }
                        }
                    } else if ($item_types[$index] == 'asset') {
                        foreach ($item_quantities as $item_index => $quantity) {
                            if ($quantity > 0) {
                                $approved_item = new Requisition_approval_asset_item();
                                $approved_item->approved_quantity = $item_quantities[$item_index];
                                $approved_item->approved_rate = $rates[$index][$item_index];
                                $approved_item->currency_id = $currency_id;
                                $approved_item->source_type = $sources_types[$index][$item_index];
                                $approved_item->requisition_asset_item_id = $item_id;
                                $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                                $source_type = $sources_types[$index][$item_index];
                                if ($source_type == 'cash') {
                                    $approved_item->source_type = $source_type;
                                    $approved_item->payee = $sources[$index][$item_index];
                                } else if ($source_type == 'imprest') {
                                    $approved_item->account_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                } else if ($source_type == 'store') {
                                    $approved_item->location_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                } else {
                                    $approved_item->vendor_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                }
                                $approved_item->save();
                            }
                        }
                    } else if ($item_types[$index] == 'service') {
                        foreach ($item_quantities as $item_index => $quantity) {
                            if ($quantity > 0) {
                                $approved_item = new Requisition_approval_service_item();
                                $approved_item->approved_quantity = $item_quantities[$item_index];
                                $approved_item->approved_rate = $rates[$index][$item_index];
                                $approved_item->source_type = $sources_types[$index][$item_index];
                                $approved_item->requisition_service_item_id = $item_id;
                                $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                                $source_type = $sources_types[$index][$item_index];
                                if ($source_type == 'cash') {
                                    $approved_item->source_type = $source_type;
                                    $approved_item->payee = $sources[$index][$item_index];
                                } else if ($source_type == 'imprest') {
                                    $approved_item->account_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                } else {
                                    $approved_item->vendor_id = $sources[$index][$item_index];
                                    $approved_item->source_type = $source_type;
                                }
                                $approved_item->save();
                            }
                        }
                    } else {
                        foreach ($item_quantities as $item_index => $quantity) {
                            if ($quantity > 0) {
                                $approved_item = new Requisition_approval_cash_item();
                                $approved_item->approved_quantity = $item_quantities[$item_index];
                                $approved_item->approved_rate = $rates[$index][$item_index];
                                $approved_item->payee = $sources[$index][$item_index];
                                $approved_item->currency_id = $currency_id;
                                $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                                $approved_item->requisition_cash_item_id = $item_id;
                                $approved_item->save();
                            }
                        }
                    }
                } else {
                    if ($item_types[$index] == 'material') {
                        $approved_item = new Requisition_approval_material_item();
                        $approved_item->requisition_material_item_id = $item_id;
                    } else {
                        $approved_item = new Requisition_approval_cash_item();
                        $approved_item->requisition_cash_item_id = $item_id;
                    }
                    $approved_item->approved_quantity = $quantities[$index];
                    $approved_item->approved_rate = $rates[$index];
                    $approved_item->currency_id = $currency_id;
                    $approved_item->requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                    $approved_item->save();
                }
            }

            $requisition = $approval->requisition();
            $approval_status = $this->input->post('status');
            $approval_status = $approval_status != '' ? $approval_status : null;
            $set_final = $this->input->post('set_final');
            $to_set_final = $set_final != '' ? $set_final : null;
            $employee_id = $this->session->userdata('employee_id');

            /***Sending emails to the approvers on the next level on the chain***/
            if ($requisition->status == 'PENDING' && is_null($approval_status) && $approval_status != "REJECTED") {
                $addresses = $recipients = [];
                if (!is_null($forward_to) && $approval->forward_to()->email != '') {

                    $addresses[] = $approval->forward_to()->email;
                    $recipients[] = $approval->forward_to()->phone;
                } else {
                    if ($requisition->current_approval_level() && $requisition->foward_to != $this->session->userdata('employee_id')) {

                        $approval_chain_level_id = $approval->approval_chain_level_id;
                        $approval_chain_level = new Approval_chain_level();
                        $approval_chain_level->load($approval_chain_level_id);
                        $levels_to_approve = $approval_chain_level->next_level();
                        $employees_to_approve = $levels_to_approve->employees();
                        foreach ($employees_to_approve as $employee) {
                            if ($employee->email != '') {
                                $addresses[] = $employee->email;
                                $recipients[] = $employee->phone;
                            }
                        }
                    }
                }

                if (!empty($addresses)) {
                    $this->load->library('email');
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://chir101.websitehostserver.net',
                        'smtp_port' => 465,
                        'smtp_user' => 'noreply@epmtz.com', // change it to yours
                        'smtp_pass' => 'stunnamadeit@123', // change it to yours
                        'mailtype' => 'html',
                        'smtp_timeout' => 60,
                        'charset' => 'iso-8859-1',
                        'wordwrap' => TRUE
                    );

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'REQUISITION NO: ' . $requisition->{$requisition::DB_TABLE_PK} . ' FOR ' . $requisition->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $requisition->requester()->full_name() . ' submitted a requisition that is waiting for your approval
                            in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for requisition no ' . $requisition->{$requisition::DB_TABLE_PK} . '<hr/></div><br/>';
                    $content .= $this->preview_requisition($requisition->{$requisition::DB_TABLE_PK}, 'true');

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            }

            /***Sending the email after the requisition has been approved ***/
            if ($approval_status == 'REJECTED') {
                $approval->is_final = 1;
                $approval->save();
                $requisition->status = 'REJECTED';
                $requisition->finalized_date = $approval->approved_date;
                $requisition->finalizer_id = $approval->created_by;
                if ($requisition->save()) {
                    $requisition->log_requisiton('Declination', 'decline');
                    $requester_id = $requisition->requester_id;
                    $this->load->model('employee');
                    $employee = new Employee();
                    $addresses = [];
                    if ($employee->load($requester_id)) {
                        $addresses[] = $employee->email;
                    }

                    $this->load->library('email');
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://chir101.websitehostserver.net',
                        'smtp_port' => 465,
                        'smtp_user' => 'noreply@epmtz.com', // change it to yours
                        'smtp_pass' => 'stunnamadeit@123', // change it to yours
                        'mailtype' => 'html',
                        'smtp_timeout' => 60,
                        'charset' => 'iso-8859-1',
                        'wordwrap' => TRUE
                    );

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'REQUISITION NO: ' . $requisition->{$requisition::DB_TABLE_PK} . ' FOR ' . $requisition->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $requisition->requester()->full_name() . ' the submitted requisition has been rejected by
                    ' . $approval->created_by()->full_name() . ' in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for requisition no ' . $requisition->{$requisition::DB_TABLE_PK} . '<hr/></div><br/>';
                    $content .= $this->preview_requisition($requisition->{$requisition::DB_TABLE_PK}, 'true');

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            } else if (
                ($approval->has_sources && !$requisition->current_approval_level()) ||
                $requisition->foward_to == $employee_id  ||
                !is_null($to_set_final)
            ) {

                $approval->is_final = 1;
                if ($requisition->foward_to == $employee_id && $requisition->forwarded_to_employee_special_level()) {
                    $approval->approval_chain_level_id = $requisition->forwarded_to_employee_special_level()->approval_chain_level_id;
                }
                $approval->save();
                $requisition->status = 'APPROVED';
                $requisition->finalized_date = $approval->approved_date;
                $requisition->finalizer_id = $approval->created_by;
                if ($requisition->save()) {
                    $requester_id = $requisition->requester_id;
                    $this->load->model('employee');
                    $employee = new Employee();
                    $addresses = [];
                    if ($employee->load($requester_id)) {
                        $addresses[] = $employee->email;
                    }

                    $this->load->library('email');
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://chir101.websitehostserver.net',
                        'smtp_port' => 465,
                        'smtp_user' => 'noreply@epmtz.com', // change it to yours
                        'smtp_pass' => 'stunnamadeit@123', // change it to yours
                        'mailtype' => 'html',
                        'smtp_timeout' => 60,
                        'charset' => 'iso-8859-1',
                        'wordwrap' => TRUE
                    );

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'REQUISITION NO: ' . $requisition->{$requisition::DB_TABLE_PK} . ' FOR ' . $requisition->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $requisition->requester()->full_name() . ' the requisition you submitted has been approved by
                    ' . $approval->created_by()->full_name() . ' in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for requisition no ' . $requisition->{$requisition::DB_TABLE_PK} . '<hr/></div><br/>';
                    $content .= $this->preview_requisition($requisition->{$requisition::DB_TABLE_PK}, 'true');

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            }
        }
    }

    public function delete_requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        if ($requisition->load($this->input->post('requisition_id'))) {
            $requisition->log_requisiton('Delete', 'delet');
            $requisition->delete();
        }
    }

    public function decline_requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        if ($requisition->load($this->input->post('requisition_id'))) {
            $requisition->status = 'DECLINED';
            if ($requisition->save()) {
            }
        }
    }

    public function preview_approved_cash_requisition($requisition_approval_id, $account_id = null)
    {
        $this->load->model('Requisition_approval');
        $requisition_approval = new Requisition_approval();
        if ($requisition_approval->load($requisition_approval_id)) {
            $requisition = $requisition_approval->requisition();
            $data['requisition_approval'] = $requisition_approval;
            $data['account_id'] = $account_id;
            $data['cost_center_name'] = $requisition->cost_center_name();
            $data['current_approval_level'] = $requisition->current_approval_level();
            $requisition_approvals = $requisition->requisition_approvals();
            $data['requisition_approvals'] = [];
            foreach ($requisition_approvals as $approval) {
                $data['requisition_approvals'][$approval->approval_chain_level_id] = $approval;
            }
            $data['chain_levels'] = $requisition->approval_module()->chain_levels(0, null, 'active', 'all');
            $html = $this->load->view('finance/transactions/approved_cash_requests/approved_cash_sheet', $data, true);
            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            if ($requisition_approval->is_cancelled()) {
                $pdf->SetWatermarkText("REVOKED");
                $pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            }

            if ($requisition_approval->is_final == 1 && $requisition_approval->is_printed == null) {
                $requisition_approval->is_printed = $this->session->userdata('employee_id');
                $requisition_approval->save();
            }
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
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('requisition.pdf', 'I'); // view in the explorer
        } else {
            redirect(base_url());
        }
    }

    public function load_level_to_approve_requisition($not_forwarding_to = null)
    {
        $this->load->model('approval_module');
        $approval_module_id = $this->input->post('approval_module_id');
        $approval_module = new Approval_module();
        if ($approval_module->load($approval_module_id)) {
            if (is_null($not_forwarding_to)) {
                echo stringfy_dropdown_options($approval_module->forwarding_to_employee_options());
            } else {
                echo $approval_module->approval_chain_level_to_approve_options();
            }
        }
    }

    public function employees_next_in_chain($requisition_id)
    {
        $requisition = new Requisition();
        $requisition->load($requisition_id);
        $options[''] = '&nbsp;';
        $next_level = $requisition->current_approval_level()->next_level();
        if ($next_level) {
            $employee_options = $next_level->employee_options();
            $no = 0;
            foreach ($employee_options as $index => $employee_option) {
                $no++;
                if ($no > 1) $options[$index] = $employee_option . ' - ' . strtoupper($next_level->level_name);
            }
        }
        return $options;
    }

    public function save_sub_contract_requisition()
    {
        $this->load->model('sub_contract_payment_requisition');
        $sub_contract_requisition = new Sub_contract_payment_requisition();
        $currency_id = $this->input->post('currency_id');
        $foward_to = $this->input->post('foward_to');
        $edit = $sub_contract_requisition->load($this->input->post('sub_contract_requisition_id'));
        $sub_contract_requisition->required_date = trim($this->input->post('required_date'));
        $sub_contract_requisition->approval_module_id = $this->input->post('approval_module_id');
        $sub_contract_requisition->required_date = $sub_contract_requisition->required_date != '' ? $sub_contract_requisition->required_date : null;
        $sub_contract_requisition->requesting_comments = $this->input->post('comments');
        $sub_contract_requisition->foward_to = $foward_to != '' ? $foward_to : null;
        $sub_contract_requisition->vat_inclusive = $this->input->post('vat_inclusive');
        $sub_contract_requisition->vat_percentage = 18;
        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $sub_contract_requisition->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
        $sub_contract_requisition->request_date = $this->input->post('request_date');
        if ($sub_contract_requisition->status != 'APPROVED') {
            $sub_contract_requisition->status = $this->input->post('status');
        }
        $sub_contract_requisition->requester_id = $this->session->userdata('employee_id');
        $sub_contract_requisition->currency_id = $currency_id;
        if ($sub_contract_requisition->save()) {
            if ($edit) {
                $sub_contract_requisition->delete_items();
            }

            $this->load->model('sub_contract_payment_requisition_item');
            $requested_amounts = $this->input->post('requested_amounts');
            if (!empty($requested_amounts)) {
                foreach ($requested_amounts as $index => $requested_amount) {
                    $sub_contract_requisition_item = new Sub_contract_payment_requisition_item();
                    $sub_contract_requisition_item->sub_contract_requisition_id = $sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK};
                    $sub_contract_requisition_item->certificate_id = $this->input->post('certificate_ids')[$index];
                    $sub_contract_requisition_item->requested_amount = $requested_amount;
                    $sub_contract_requisition_item->save();
                }
            }
        }
    }

    public function delete_sub_contract_requisition()
    {
        $this->load->model('sub_contract_payment_requisition');
        $sub_contract_requisition = new Sub_contract_payment_requisition();
        if ($sub_contract_requisition->load($this->input->post('sub_contract_requisition_id'))) {
            $sub_contract_requisition->delete();
        }
    }

    public function preview_sub_contract_requisition($sub_contract_requisition_id = 0, $string_for_email = false)
    {
        $this->load->model('sub_contract_payment_requisition');
        $sub_contract_requisition = new Sub_contract_payment_requisition();
        if ($sub_contract_requisition->load($sub_contract_requisition_id)) {
            $data['sub_contract_requisition'] = $sub_contract_requisition;
            $data['current_approval_level'] = $sub_contract_requisition->current_approval_level();
            $sub_contract_requisition_approvals = $sub_contract_requisition->sub_contract_requisition_approvals();
            $data['sub_contract_requisition_approvals'] = [];
            foreach ($sub_contract_requisition_approvals as $approval) {
                $data['sub_contract_requisition_approvals'][$approval->approval_chain_level_id] = $approval;
            }

            $data['chain_levels'] = $sub_contract_requisition->approval_module()->chain_levels(0, null, 'active', 'all');
            $data['sub_contract_requisition_items'] = $sub_contract_requisition->sub_contract_requisition_items();
            $html = $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_sheet', $data, true);

            if ($string_for_email) {
                return $html;
            }

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            if ($sub_contract_requisition->status == "REJECTED") {
                $pdf->SetWatermarkText("REJECTED");
                $pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            }

            $footercontents = '
                <div>
                    <div style="text-align: left; float: left; width: 50%">
                        <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . "-" . strftime('%d/%m/%Y %H:%M:%S')  . '</span>
                    </div>
                    <div>
                        <span>' . $sub_contract_requisition->sub_contract_requisition_number() . '</span>
                    </div>
                    <div style="text-align: center">
                    Page {PAGENO} of {nb}
                    </div>
                </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('Sub Contract Payment Requisition_' . $sub_contract_requisition->sub_contract_requisition_number() . '.pdf', 'I'); // view in the explorer
        } else {
            redirect(base_url());
        }
    }

    public function approve_sub_contract_requisition()
    {
        $this->load->model(['sub_contract_payment_requisition_approval', 'approval_chain_level']);
        $approval = new Sub_contract_payment_requisition_approval();
        $approval->approval_chain_level_id = $this->input->post('approval_chain_level_id');
        $approval->approval_date = $this->input->post('approve_date');
        $approval->sub_contract_requisition_id = $this->input->post('sub_contract_requisition_id');
        $approval->created_by = $this->session->userdata('employee_id');
        $approval->currency_id = $this->input->post('currency_id');;
        $forward_to = $this->input->post('forward_to');
        $approval->forward_to = $forward_to != '' ? $forward_to : null;
        $approval->vat_inclusive = $this->input->post('vat_inclusive');
        $approval->vat_percentage = 18;
        $approval->is_final = 0;
        $approval->approving_comments = $this->input->post('comments');
        if ($approval->save()) {
            $this->load->model('sub_contract_payment_requisition_approval_item');
            $amounts = $this->input->post('amounts');
            $sub_contract_payment_requisition_item_ids = $this->input->post('sub_contract_payment_requisition_item_ids');
            if (!empty($sub_contract_payment_requisition_item_ids)) {
                foreach ($sub_contract_payment_requisition_item_ids as $index => $sub_contract_payment_requisition_item_id) {
                    $approved_item = new Sub_contract_payment_requisition_approval_item();
                    $approved_item->sub_contract_payment_requisition_approval_id = $approval->{$approval::DB_TABLE_PK};
                    $approved_item->sub_contract_payment_requisition_item_id = $sub_contract_payment_requisition_item_id;
                    $approved_item->approved_amount = $amounts[$index];
                    $approved_item->save();
                }
            }

            $sub_contract_requisition = $approval->sub_contract_requisition();
            $approval_status = $this->input->post('status');
            $employee_id = $this->session->userdata('employee_id');

            /***Sending emails to the approvers on the next level on the chain*/
            if ($sub_contract_requisition->status == 'PENDING') {

                $addresses = $recipients = [];
                if (!is_null($forward_to) && $approval->forward_to()->email != '') {

                    $addresses[] = $approval->forward_to()->email;
                    $recipients[] = $approval->forward_to()->phone;
                } else {
                    if ($sub_contract_requisition->current_approval_level() && $sub_contract_requisition->forward_to != $this->session->userdata('employee_id')) {

                        $approval_chain_level = new Approval_chain_level();
                        $approval_chain_level->load($approval->approval_chain_level_id);
                        $levels_to_approve = $approval_chain_level->next_level();
                        $employees_to_approve = $levels_to_approve->employees();
                        foreach ($employees_to_approve as $employee) {
                            if ($employee->email != '') {
                                $addresses[] = $employee->email;
                                $recipients[] = $employee->phone;
                            }
                        }
                    }
                }

                if (!empty($addresses)) {
                    $this->load->library('email');
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://chir101.websitehostserver.net',
                        'smtp_port' => 465,
                        'smtp_user' => 'noreply@epmtz.com', // change it to yours
                        'smtp_pass' => 'stunnamadeit@123', // change it to yours
                        'mailtype' => 'html',
                        'smtp_timeout' => 60,
                        'charset' => 'iso-8859-1',
                        'wordwrap' => TRUE
                    );

                    $this->email->initialize($config);

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'SUB CONTRACT PAYMENT REQUISITION NO: ' . $sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK} . ' FOR ' . $sub_contract_requisition->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $sub_contract_requisition->requester()->full_name() . ' submitted a sub contract payment requisition that is waiting for your approval
                            in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for sub contract payment requisition no ' . $sub_contract_requisition->sub_contract_requisition_number() . '<hr/></div><br/>';
                    $content .= $this->preview_sub_contract_requisition($sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK}, 'true');

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            }

            /***Sending the email after the requisition has been approved ***/
            if ($approval_status == 'REJECTED') {
                $approval->is_final = 1;
                $approval->save();
                $sub_contract_requisition->status = 'REJECTED';
                $sub_contract_requisition->finalized_date = $approval->approved_date;
                $sub_contract_requisition->finalizer_id = $approval->created_by;
                if ($sub_contract_requisition->save()) {
                    $this->load->model('employee');
                    $employee = new Employee();
                    $addresses = [];
                    if ($employee->load($sub_contract_requisition->requester_id)) {
                        $addresses[] = $employee->email;
                    }

                    $this->load->library('email');
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://chir101.websitehostserver.net',
                        'smtp_port' => 465,
                        'smtp_user' => 'noreply@epmtz.com',
                        'smtp_pass' => 'stunnamadeit@123',
                        'mailtype' => 'html',
                        'smtp_timeout' => 60,
                        'charset' => 'iso-8859-1',
                        'wordwrap' => TRUE
                    );

                    $this->email->initialize($config);

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'SUB CONTRACT PAYMENT REQUISITION NO: ' . $sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK} . ' FOR ' . $sub_contract_requisition->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $sub_contract_requisition->requester()->full_name() . ' the submitted sub contract payment requisition has been rejected by
                    ' . $approval->created_by()->full_name() . ' in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for sub contract payment requisition no ' . $sub_contract_requisition->sub_contract_requisition_number() . '<hr/></div><br/>';
                    $content .= $this->preview_sub_contract_requisition($sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK}, 'true');

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            } else if (
                !$sub_contract_requisition->current_approval_level() ||
                $sub_contract_requisition->foward_to == $employee_id
            ) {
                $approval->is_final = 1;
                if ($sub_contract_requisition->foward_to == $employee_id && $sub_contract_requisition->forwarded_to_employee_special_level()) {
                    $approval->approval_chain_level_id = $sub_contract_requisition->forwarded_to_employee_special_level()->approval_chain_level_id;
                }
                $approval->save();
                $sub_contract_requisition->status = 'APPROVED';
                $sub_contract_requisition->finalized_date = $approval->approved_date;
                $sub_contract_requisition->finalizer_id = $approval->created_by;
                if ($sub_contract_requisition->save()) {
                    $this->load->model('employee');
                    $employee = new Employee();
                    $addresses = [];
                    if ($employee->load($sub_contract_requisition->requester_id)) {
                        $addresses[] = $employee->email;
                    }

                    $this->load->library('email');
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://chir101.websitehostserver.net',
                        'smtp_port' => 465,
                        'smtp_user' => 'noreply@epmtz.com', // change it to yours
                        'smtp_pass' => 'stunnamadeit@123', // change it to yours
                        'mailtype' => 'html',
                        'smtp_timeout' => 60,
                        'charset' => 'iso-8859-1',
                        'wordwrap' => TRUE
                    );

                    $this->email->initialize($config);

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'SUB CONTRACT PAYMENT REQUISITION NO: ' . $sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK} . ' FOR ' . $sub_contract_requisition->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $sub_contract_requisition->requester()->full_name() . ' the sub contract payment requisition you submitted has been approved by
                    ' . $approval->created_by()->full_name() . ' in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for sub contract payment requisition no ' . $sub_contract_requisition->sub_contract_requisition_number() . '<hr/></div><br/>';
                    $content .= $this->preview_sub_contract_requisition($sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK}, 'true');

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            }
        }
    }

    public function preview_sub_contract_requisition_approved_chains($sub_contract_requisition_id = 0)
    {
        $this->load->model('sub_contract_payment_requisition');
        $sub_contract_requisition = new Sub_contract_payment_requisition();
        if ($sub_contract_requisition->load($sub_contract_requisition_id)) {
            $data['sub_contract_requisition'] = $sub_contract_requisition;
            $data['last_approval'] = $sub_contract_requisition->last_approval();
            $html = $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_approval_chain', $data, true);
            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
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
            if ($sub_contract_requisition->status == "REJECTED") {
                $pdf->SetWatermarkText("REJECTED");
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.1;
                $pdf->SetDisplayMode('fullpage');
            }

            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('sub_contract_requisition_approval_' . $sub_contract_requisition->sub_contract_requisition_number() . '.pdf', 'I'); // view in the explorer
        }
    }

    public function preview_approved_sub_contract_payment_requsition($sub_contract_requisition_aprroval_id)
    {
        $this->load->model('sub_contract_payment_requisition_approval');
        $sub_contract_requisition_approval = new Sub_contract_payment_requisition_approval();
        if ($sub_contract_requisition_approval->load($sub_contract_requisition_aprroval_id)) {
            $sub_contract_requisition = $sub_contract_requisition_approval->sub_contract_requisition();
            $data['sub_contract_requisition_approval'] = $sub_contract_requisition_approval;
            $data['cost_center_name'] = $sub_contract_requisition_approval->sub_contract_requisition()->cost_center_name();
            $data['current_approval_level'] = $sub_contract_requisition->current_approval_level();
            $sub_contract_requisition_approvals = $sub_contract_requisition->sub_contract_requisition_approvals();
            $data['sub_contract_requisition_approvals'] = [];
            foreach ($sub_contract_requisition_approvals as $approval) {
                $data['sub_contract_requisition_approvals'][$approval->approval_chain_level_id] = $approval;
            }

            $data['chain_levels'] = $sub_contract_requisition->approval_module()->chain_levels(0, null, 'active', 'all');
            $html = $this->load->view('finance/sub_contract_payments/approved_sub_contract_payment_sheet', $data, true);
            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            if ($sub_contract_requisition_approval->is_cancelled()) {
                $pdf->SetWatermarkText("REVOKED");
                $pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            }
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
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('sub_contract_requisition.pdf', 'I'); // view in the explorer
        } else {
            redirect(base_url());
        }
    }

    public function preview_enquiry($enquiry_id)
    {
        $this->load->model('enquiry');
        $enquiry = new Enquiry();
        if ($enquiry->load($enquiry_id)) {
            $data['enquiry'] = $enquiry;
            $html = $this->load->view('requisitions/enquiries/enquiry_sheet', $data, true);

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $footercontents = '<div>
                                    <div style="text-align: left; float: left; width: 50%">
                                        <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . "-" . strftime('%d/%m/%Y %H:%M:%S')  . '</span>
                                    </div>
                                    <div>
                                        <span>' . $enquiry->enquiry_number() . '</span>
                                    </div>
                                    <div style="text-align: center">
                                    Page {PAGENO} of {nb}
                                    </div>
                                </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output('requisition_' . $enquiry->enquiry_number() . '.pdf', 'I'); // view in the explorer
        } else {
            redirect(base_url());
        }
    }

    public function save_enquiry()
    {
        $this->load->model('enquiry');
        $enquiry = new Enquiry();
        $edit = $enquiry->load($this->input->post('enquiry_id'));
        $enquiry->enquiry_date = $this->input->post('enquiry_date');
        $enquiry->required_date = trim($this->input->post('required_date'));
        $enquiry->enquiry_for = '';
        $enquiry->enquiry_to = $this->input->post('vendor_id');
        $enquiry->required_date = $enquiry->required_date != '' ? $enquiry->required_date : null;
        $enquiry->comments = $this->input->post('comments');
        $enquiry->status = "PENDING";
        $enquiry->created_by = $this->session->userdata('employee_id');
        if ($enquiry->save()) {
            if ($edit) {
                $enquiry->delete_items();
            }
            $this->load->model([
                'enquiry_material_item',
                'enquiry_asset_item',
                'enquiry_service_item'
            ]);

            $item_types = $this->input->post('item_types');
            $items = $this->input->post('items');
            $quantities = $this->input->post('quantities');
            $remarks = $this->input->post('remarks');
            $measurement_unit_ids = $this->input->post('measurement_unit_ids');
            foreach ($item_types as $index => $item_type) {
                if ($item_type == 'material') {
                    $enquiry_item = new Enquiry_material_item();
                    $enquiry_item->material_item_id = $items[$index];
                } else if ($item_type == 'asset') {
                    $enquiry_item = new Enquiry_asset_item();
                    $enquiry_item->asset_item_id = $items[$index];
                } else if ($item_type == 'service') {
                    $enquiry_item = new Enquiry_service_item();
                    $enquiry_item->description = $items[$index];
                    $enquiry_item->measurement_unit_id = $measurement_unit_ids[$index];
                }
                $enquiry_item->enquiry_id = $enquiry->{$enquiry::DB_TABLE_PK};
                $enquiry_item->quantity = $quantities[$index];
                $enquiry_item->remarks = $remarks[$index];
                $enquiry_item->save();
            }
        }
    }

    public function delete_enquiry()
    {
        $this->load->model('enquiry');
        $enquiry = new Enquiry();
        if ($enquiry->load($this->input->post('enquiry_id'))) {
            $enquiry->delete();
        }
    }
}
