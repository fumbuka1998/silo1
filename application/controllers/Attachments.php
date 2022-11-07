<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 27/04/2018
 * Time: 09:22
 */

class Attachments extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_login();
    }

    public function save_requisition_attachment()
    {
        $this->load->model(['requisition', 'requisition_attachment', 'attachment']);
        $attachment = new Attachment();
        $requisition_id = $this->input->post('requisition_id');
        $requisition_directory = "./uploads/attachments/requisitions/" . $requisition_id . '/';
        if (!file_exists($requisition_directory)) {
            mkdir($requisition_directory, 0777, true);
        }

        $config = [
            'upload_path' => $requisition_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];

        $this->load->library('upload', $config);
        if (!empty($_FILES['file'])) {
            if ($this->upload->do_upload('file')) {
                $attachment->attachment_name = $this->upload->data()['file_name'];
                $attachment->caption = $this->input->post('caption');
                $attachment->created_by = $this->session->userdata('employee_id');
                if ($attachment->save()) {
                    $requisition_attachment = new Requisition_attachment();
                    $requisition_attachment->attachment_id = $attachment->{$attachment::DB_TABLE_PK};
                    $requisition_attachment->requisition_id = $requisition_id;
                    $requisition_attachment->save();
                    $requisition = $requisition_attachment->requisition();
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
        $attachments = $requisition->attachments();
        $this->load->view('requisitions/requisitions_list/requisition_attachments', ['attachments' => $attachments]);
    }

    public function delete_attachment()
    {
        $this->load->model('attachment');
        $attachment = new Attachment();
        $attachment->load($this->input->post('attachment_id'));
        $attachment->delete();
    }

    public function save_tender_attachment()
    {
        $this->load->model(['attachment', 'tender_attachment']);
        $this->load->library('upload');
        $attach = new Attachment();
        $tender_id = $this->input->post('tender_id');
        $attachment_directory = "./uploads/attachments/tenders/" . $tender_id . '/';
        if (!file_exists($attachment_directory)) {
            mkdir($attachment_directory, 0777, true);
        }


        $config = [
            'upload_path' => $attachment_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];


        foreach ($_FILES['files'] as $key => $array) {
            $i = 1;
            foreach ($array as $value) {
                $field_name = 'file_' . $i;
                $_FILES[$field_name][$key] = $value;
                $i++;
            }
        }

        unset($_FILES['files']);
        $i = 0;
        $this->upload->initialize($config);
        foreach ($_FILES as $field_name => $file) {
            if ($this->upload->do_upload($field_name)) {
                $upload_data = $this->upload->data();
                $attach = new Attachment();
                $attach->attachment_name = $upload_data['file_name'];
                $attach->caption = $this->input->post('captions')[$i];
                $attach->created_by = $this->session->userdata('employee_id');
                if ($attach->save()) {
                    $tender_attachment = new Tender_attachment();
                    $tender_attachment->tender_id = $tender_id;
                    $tender_attachment->attachment_id = $attach->{$attach::DB_TABLE_PK};
                    $tender_attachment->save();
                }
            }
            $i++;
        }
    }

    public function tender_attachments_list()
    {
        $this->load->model('tender_attachment');
        $params = dataTable_post_params();
        echo $this->tender_attachment->tender_attachments_list($params['limit'], $params['start'], $params['keyword'], $params['order']);
    }

    public function save_project_attachment()
    {
        $this->load->model(['attachment', 'project_attachment']);
        $this->load->library('upload');
        $attach = new Attachment();
        $project_id = $this->input->post('project_id');
        $attachment_directory = "./uploads/attachments/projects/" . $project_id . '/';
        if (!file_exists($attachment_directory)) {
            mkdir($attachment_directory, 0777, true);
        }


        $config = [
            'upload_path' => $attachment_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];


        foreach ($_FILES['files'] as $key => $array) {
            $i = 1;
            foreach ($array as $value) {
                $field_name = 'file_' . $i;
                $_FILES[$field_name][$key] = $value;
                $i++;
            }
        }

        unset($_FILES['files']);
        $i = 0;
        $this->upload->initialize($config);
        foreach ($_FILES as $field_name => $file) {
            if ($this->upload->do_upload($field_name)) {
                $upload_data = $this->upload->data();
                $attach = new Attachment();
                $attach->attachment_name = $upload_data['file_name'];
                $attach->caption = $this->input->post('captions')[$i];
                $attach->created_by = $this->session->userdata('employee_id');
                if ($attach->save()) {
                    $project_attachment = new Project_attachment();
                    $project_attachment->project_id = $project_id;
                    $project_attachment->attachment_id = $attach->{$attach::DB_TABLE_PK};
                    $project_attachment->save();
                }
            }
            $i++;
        }
    }

    public function project_attachments_list()
    {
        $this->load->model('project_attachment');
        $params = dataTable_post_params();
        echo $this->project_attachment->project_attachments_list($params['limit'], $params['start'], $params['keyword'], $params['order']);
    }

    public function save_company_attachment()
    {
        $this->load->model(['attachment', 'company_document']);
        $this->load->library('upload');
        $company_detail_id = $this->input->post('company_detail_id');
        $attachment_directory = "./uploads/attachments/company_details/";
        if (!file_exists($attachment_directory)) {
            mkdir($attachment_directory, 0777, true);
        }


        $config = [
            'upload_path' => $attachment_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];


        foreach ($_FILES['files'] as $key => $array) {
            $i = 1;
            foreach ($array as $value) {
                $field_name = 'file_' . $i;
                $_FILES[$field_name][$key] = $value;
                $i++;
            }
        }

        unset($_FILES['files']);
        $i = 0;
        $this->upload->initialize($config);
        foreach ($_FILES as $field_name => $file) {
            if ($this->upload->do_upload($field_name)) {
                $upload_data = $this->upload->data();
                $attach = new Attachment();
                $attach->attachment_name = $upload_data['file_name'];
                $attach->caption = $this->input->post('captions')[$i];
                $attach->created_by = $this->session->userdata('employee_id');
                if ($attach->save()) {
                    $company_attachment = new Company_document();
                    $company_attachment->attachment_id = $attach->{$attach::DB_TABLE_PK};
                    $company_attachment->save();
                }
            }
            $i++;
        }
    }

    public function company_attachments_list()
    {
        $this->load->model('company_document');
        $params = dataTable_post_params();
        echo $this->company_document->company_attachments_list($params['limit'], $params['start'], $params['keyword'], $params['order']);
    }

    public function save_payment_request_attachment()
    {
        $this->load->model([
            'purchase_order_payment_request',
            'purchase_order_payment_request_attachment',
            'attachment'
        ]);
        $attachment = new Attachment();
        $purchase_order_payment_request_id = $this->input->post('purchase_order_payment_request_id');
        $purchase_order_payment_request_directory = "./uploads/attachments/purchase_order_payment_requests/" . $purchase_order_payment_request_id . '/';
        if (!file_exists($purchase_order_payment_request_directory)) {
            mkdir($purchase_order_payment_request_directory, 0777, true);
        }

        $config = [
            'upload_path' => $purchase_order_payment_request_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];

        $this->load->library('upload', $config);
        if (!empty($_FILES['file'])) {
            if ($this->upload->do_upload('file')) {
                $attachment->attachment_name = $this->upload->data()['file_name'];
                $attachment->caption = $this->input->post('caption');
                $attachment->created_by = $this->session->userdata('employee_id');
                if ($attachment->save()) {
                    $purchase_order_payment_request_attachment = new Purchase_order_payment_request_attachment();
                    $purchase_order_payment_request_attachment->attachment_id = $attachment->{$attachment::DB_TABLE_PK};
                    $purchase_order_payment_request_attachment->purchase_order_payment_request_id = $purchase_order_payment_request_id;
                    $purchase_order_payment_request_attachment->save();
                    $purchase_order_payment_request = $purchase_order_payment_request_attachment->purchase_order_payment_request();
                    $action = 'Purchase Order Payment Request Attachment Upload';
                    $description = 'A new attachment was uploaded to payment request number ' . $purchase_order_payment_request->request_number();
                    system_log($action, $description);
                }
            }
        }
    }

    public function procurements_attachments()
    {
        switch ($this->input->post('reffering_to')) {
            case 'ORDER':
                $model = 'purchase_order';
                break;
            case 'P-INV':
                $model = 'invoice';
                break;
            case 'O-INV':
                $model = 'outgoing_invoice';
                break;
            case 'GRN':
                $model = 'goods_received_note';
                break;
        }
        $this->load->model($model);
        $model_class = ucfirst($model);
        $reffering_object = new $model_class();
        $reffering_object->load($this->input->post('reffering_id'));
        $attachments = $reffering_object->attachments();
        $this->load->view('attachments/purchase_order_related/attachment_table', ['attachments' => $attachments]);
    }

    public function save_procurements_attachments()
    {
        $this->load->model(['goods_received_note', 'invoice', 'outgoing_invoice', 'purchase_order', 'procurement_attachment', 'attachment']);
        $attachment = new Attachment();
        $reffering_id = $this->input->post('reffering_id');
        $reffering_to = $this->input->post('reffering_to');
        switch ($reffering_to) {
            case 'ORDER':
                $model = 'purchase_order';
                $object_no = 'order_number';
                $name = 'purchase order';
                break;
            case 'P-INV':
                $model = 'invoice';
                $object_no = 'reference';
                $name = 'invoice(Bill)';
                break;
            case 'O-INV':
                $model = 'outgoing_invoice';
                $object_no = 'outgoing_inv_number';
                $name = 'invoice(Tax Invoice)';
                break;
            case 'GRN':
                $model = 'goods_received_note';
                $object_no = 'grn_number';
                $name = 'GRN';
                break;
        }
        $reffered_directory = "./uploads/attachments/" . $model . "s/" . $reffering_id . '/';
        if (!file_exists($reffered_directory)) {
            mkdir($reffered_directory, 0777, true);
        }

        $config = [
            'upload_path' => $reffered_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx|csv'
        ];

        $this->load->library('upload', $config);
        if (!empty($_FILES['file'])) {
            if ($this->upload->do_upload('file')) {
                $attachment->attachment_name = $this->upload->data()['file_name'];
                $attachment->caption = $this->input->post('caption');
                $attachment->created_by = $this->session->userdata('employee_id');
                if ($attachment->save()) {
                    $procurement_attachment = new Procurement_attachment();
                    $procurement_attachment->attachment_id = $attachment->{$attachment::DB_TABLE_PK};
                    $procurement_attachment->reffering_id = $reffering_id;
                    $procurement_attachment->reffering_to = $reffering_to;
                    $procurement_attachment->save();
                    $reffering_object = $procurement_attachment->reffering_object();
                    $action = 'Sub Contract Payment Requisition Attachment Upload';
                    $description = 'A new attachment was uploaded to ' . $name . ' number/reference ' . $reffering_object->$object_no();
                    system_log($action, $description);
                }
            }
        }
    }

    public function purchase_order_payment_request_attachments()
    {
        $this->load->model('purchase_order_payment_request');
        $purchase_order_payment_request = new Purchase_order_payment_request();
        $purchase_order_payment_request->load($this->input->post('purchase_order_payment_request_id'));
        $attachments = $purchase_order_payment_request->attachments();
        $this->load->view('procurements/order_payment_requests/purchase_order_payment_request_attachments_table', ['attachments' => $attachments]);
    }

    public function save_sub_contract_requisition_attachment()
    {
        $this->load->model(['requisition', 'sub_contract_payment_requisition_attachment', 'attachment']);
        $attachment = new Attachment();
        $sub_contract_requisition_id = $this->input->post('sub_contract_requisition_id');
        $requisition_directory = "./uploads/attachments/sub_contract_payment_requisitions/" . $sub_contract_requisition_id . '/';
        if (!file_exists($requisition_directory)) {
            mkdir($requisition_directory, 0777, true);
        }

        $config = [
            'upload_path' => $requisition_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];

        $this->load->library('upload', $config);
        if (!empty($_FILES['file'])) {
            if ($this->upload->do_upload('file')) {
                $attachment->attachment_name = $this->upload->data()['file_name'];
                $attachment->caption = $this->input->post('caption');
                $attachment->created_by = $this->session->userdata('employee_id');
                if ($attachment->save()) {
                    $requisition_attachment = new Sub_contract_payment_requisition_attachment();
                    $requisition_attachment->attachment_id = $attachment->{$attachment::DB_TABLE_PK};
                    $requisition_attachment->sub_contract_payment_requisition_id = $sub_contract_requisition_id;
                    $requisition_attachment->save();
                    $requisition = $requisition_attachment->sub_contract_payment_requisition();
                    $action = 'Sub Contract Payment Requisition Attachment Upload';
                    $description = 'A new attachment was uploaded to requisition number ' . $requisition->sub_contract_requisition_number();
                    system_log($action, $description);
                }
            }
        }
    }

    public function sub_contract_payment_requisition_attachments()
    {
        $this->load->model('sub_contract_payment_requisition');
        $sub_contract_requisition = new Sub_contract_payment_requisition();
        $sub_contract_requisition->load($this->input->post('sub_contract_requisition_id'));
        $attachments = $sub_contract_requisition->attachments();
        $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_attachments', ['attachments' => $attachments]);
    }

    public function save_journal_voucher_attachment()
    {
        $this->load->model(['journal_voucher', 'journal_voucher_attachment', 'attachment']);
        $attachment = new Attachment();
        $journal_voucher_id = $this->input->post('journal_voucher_id');
        $jv_directory = "./uploads/attachments/journal_vouchers/" . $journal_voucher_id . '/';
        if (!file_exists($jv_directory)) {
            mkdir($jv_directory, 0777, true);
        }

        $config = [
            'upload_path' => $jv_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];

        $this->load->library('upload', $config);
        if (!empty($_FILES['file'])) {
            if ($this->upload->do_upload('file')) {
                $attachment->attachment_name = $this->upload->data()['file_name'];
                $attachment->caption = $this->input->post('caption');
                $attachment->created_by = $this->session->userdata('employee_id');
                if ($attachment->save()) {
                    $jv_attachment = new Journal_voucher_attachment();
                    $jv_attachment->attachment_id = $attachment->{$attachment::DB_TABLE_PK};
                    $jv_attachment->journal_voucher_id = $journal_voucher_id;
                    $jv_attachment->save();
                    $journal_voucher = $jv_attachment->journal_voucher();
                    $action = 'Journal Voucher Attachment Upload';
                    $description = 'A new attachment was uploaded to journal voucher number ' . $journal_voucher->jv_number();
                    system_log($action, $description);
                }
            }
        }
    }

    public function journal_voucher_attachments()
    {
        $this->load->model('journal_voucher');
        $jv = new Journal_voucher();
        $jv->load($this->input->post('journal_voucher_id'));
        $attachments = $jv->attachments();
        $this->load->view('finance/transactions/journals/journal_voucher_attachments', ['attachments' => $attachments]);
    }

    public function save_deployment_attachment()
    {
        $this->load->model(['attachment', 'deployment_attachment']);
        $this->load->library('upload');
        $deployment_id = $this->input->post('deployment_id');
        $attachment_directory = "./uploads/attachments/deployments/" . $deployment_id . '/';
        if (!file_exists($attachment_directory)) {
            mkdir($attachment_directory, 0777, true);
        }


        $config = [
            'upload_path' => $attachment_directory,
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|docx|doc|dot|xls|xlsx'
        ];


        foreach ($_FILES['files'] as $key => $array) {
            $i = 1;
            foreach ($array as $value) {
                $field_name = 'file_' . $i;
                $_FILES[$field_name][$key] = $value;
                $i++;
            }
        }

        unset($_FILES['files']);
        $i = 0;
        $this->upload->initialize($config);
        foreach ($_FILES as $field_name => $file) {
            if ($this->upload->do_upload($field_name)) {
                $upload_data = $this->upload->data();
                $attach = new Attachment();
                $attach->attachment_name = $upload_data['file_name'];
                $attach->caption = $this->input->post('captions')[$i];
                $attach->created_by = $this->session->userdata('employee_id');
                if ($attach->save()) {
                    $deployment_attachment = new Deployment_attachment();
                    $deployment_attachment->attachment_id = $attach->{$attach::DB_TABLE_PK};
                    $deployment_attachment->deployment_id = $deployment_id;
                    $deployment_attachment->save();
                }
            }
            $i++;
        }
    }
}
