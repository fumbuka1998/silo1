<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 27/04/2018
 * Time: 08:15
 */
class Attachment extends MY_Model
{

    const DB_TABLE = 'attachments';
    const DB_TABLE_PK = 'id';

    public $attachment_name;
    public $caption;
    public $created_by;

    private function attachment_types()
    {
        return ['requisition_attachment', 'project_attachment', 'tender_attachment', 'company_document', 'purchase_order_payment_request_attachment', 'sub_contract_payment_requisition_attachment', 'journal_voucher_attachment', 'deployment_attachment', 'procurement_attachment'];
    }

    public function type_junction()
    {
        $attachment_types = $this->attachment_types();
        foreach ($attachment_types as $attachment_type) {
            $this->load->model($attachment_type);
            $junctions = $this->$attachment_type->get(1, 0, ['attachment_id' => $this->{$this::DB_TABLE_PK}]);
            if (!empty($junctions)) {
                return array_shift($junctions);
                break;
            }
        }
    }

    public function full_path()
    {
        $type_junction = $this->type_junction();
        $type = get_class($type_junction);
        if ($type == 'Procurement_attachment') {
            $reffering_object = $type_junction->reffering_object();
            $directory = $reffering_object::DB_TABLE;
            $sub_directory = $type_junction->reffering_id . '/';
        } else 
        if ($type == 'Company_document') {
            $sub_directory = '';
            $directory = 'company_details';
        } else 
        if (
            $type == 'Purchase_order_payment_request_attachment'
            || $type == 'Sub_contract_payment_requisition_attachment'
            || $type == 'Journal_voucher_attachement'
            || $type == 'Deployment_attachment'
        ) {
            $first_word = explode('_a', strtolower($type))[0];
            $required_id = $first_word . '_id';
            $directory = $first_word . 's';
            $sub_directory = $type_junction->$required_id . '/';
        } else {
            $first_word = explode('_', strtolower($type))[0];
            $required_id = $first_word . '_id';
            $directory = $first_word . 's';
            $sub_directory = $type_junction->$required_id . '/';
        }
        return 'uploads/attachments/' . $directory . '/' . $sub_directory . $this->attachment_name . '';
    }

    public function link()
    {
        $extension = strtolower(pathinfo($this->attachment_name, PATHINFO_EXTENSION));

        $supported_image = array('gif', 'jpg', 'jpeg', 'png');

        if ($extension == 'xls' || $extension == 'xlsx') {
            $link_display = '<i class="fa fa-file-excel-o"></i>';
        } else if ($extension == 'doc' || $extension == 'docx') {
            $link_display = '<i class="fa fa-file-word-o"></i>';
        } else if ($extension == 'pdf') {
            $link_display = '<i class="fa fa-file-pdf-o"></i>';
        } else if (in_array($extension, $supported_image)) {
            $link_display = '<i class="fa fa-image"></i>';
        } else {
            $link_display = '<i class="fa fa-file"></i>';
        }

        return anchor(base_url($this->full_path()), $link_display, ' target="_blank" class="btn btn-primary btn-xs" title="Open" ');
    }

    public function delete()
    {
        unlink(realpath($this->full_path()));
        parent::delete();
    }

    public function delete_button()
    {
        if ($this->session->userdata('employee_id') == $this->created_by || check_permission('Administrative Actions')) {
            $delete_button = '<button type="button" title="Delete Attachment" class="btn btn-xs btn-danger delete_attachment" attachment_id="' . $this->{$this::DB_TABLE_PK} . '" ><i class="fa fa-trash-o"></i></button>';
        } else {
            $delete_button = '';
        }
        return $delete_button;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function action_buttons()
    {
        return '<span class="pull-right">' . $this->link() . ' ' . $this->delete_button() . '</span>';
    }
}
