<?php

class Sub_contract_certificate extends MY_Model
{

    const DB_TABLE = 'sub_contract_certificates';
    const DB_TABLE_PK = 'id';

    public $sub_contract_id;
    public $certificate_number;
    public $certified_amount;
    public $vat_inclusive;
    public $vat_percentage;
    public $certificate_date;
    public $remarks;
    public $created_by;


    public function contract_items()
    {
        $sub_contract_items = $this->get();
        return $sub_contract_items;
    }

    public function sub_contract()
    {
        $this->load->model('Sub_contract');
        $sub_contract = new Sub_contract();
        $sub_contract->load($this->sub_contract_id);
        return $sub_contract;
    }

    public function sub_contracts_certificate_list_table($sub_contract_id, $limit, $start, $keyword, $order)
    {

        $this->load->model('sub_contract');
        $sub_contract = new Sub_contract();
        $sub_contract->load($sub_contract_id);

        $order_string = dataTable_order_string(['certificate_number', 'certificate_date', 'certified_amount', 'remarks'], $order, 'certificate_number');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $where = 'sub_contract_id = "' . $sub_contract_id . '"';

        $sql = 'SELECT COUNT(sub_contract_certificates.id) AS record_total FROM sub_contract_certificates
                WHERE ' . $where;

        $query = $this->db->query($sql);
        $records_total = $query->row()->record_total;

        if ($keyword != '') {
            $where .= 'AND (certificate_number LIKE "%' . $keyword . '%"OR certificate_date LIKE "%' . $keyword . '%" OR certified_amount LIKE "%' . $keyword . '%" OR remarks LIKE "%' . $keyword . '%")';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS sub_contract_certificates.id, certificate_number,certificate_date,certified_amount,remarks FROM sub_contract_certificates
                WHERE ' . $where . $order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];

        foreach ($results as $row) {
            $sub_contract_certificate = new self();
            $sub_contract_certificate->load($row->id);
            $data['sub_contract_certificate'] = $sub_contract_certificate;
            $data['sub_contract_certificate_id'] = $sub_contract_certificate->{$sub_contract_certificate::DB_TABLE_PK};
            $rows[] = [
                '<span class="pull-left">' . $row->certificate_number . '</span>',
                custom_standard_date($row->certificate_date),
                '<span class="pull-left">' . wordwrap($row->remarks, 50, '<br>') . '</span>',
                '<span class="pull-right">' . number_format($row->certified_amount) . ' ' . $sub_contract_certificate->is_vat_iclusive() . '</span>',
                $this->load->view('projects/sub_contracts/sub_contract_items/sub_contract_certificate_action', $data, true)
            ];
        }
        $json = [
            "certified_amount" => $sub_contract->certified_amount(),
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function drop_down_options($all = true)
    {
        if (!$all) {
            $certificates = $this->get(0, 0, ['sub_contract_id' => $this->sub_contract_id], 'id ASC');
        } else {
            $certificates = $this->get(0, 0, '', 'id ASC');
        }
        $options[] = '&nbsp;';
        foreach ($certificates as $certificate) {
            $options[$certificate->{$certificate::DB_TABLE_PK}] = $certificate->certificate_number;
        }
        return $options;
    }

    public function certificate_tasks()
    {
        $this->load->model('sub_contract_certificate_task');
        return $this->sub_contract_certificate_task->get(0, 0, ['sub_contract_certificate_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function paid_certificate()
    {
        $this->load->model('sub_contract_certificate_payment_voucher');
        $paid_certificates = $this->sub_contract_certificate_payment_voucher->get(0, 0, ['sub_contract_certificate_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($paid_certificates) ? array_shift($paid_certificates) : false;
    }

    public function paid_amount()
    {
        $certificate_id = $this->{$this::DB_TABLE_PK};
        $sql = 'SELECT (
                    (
                    SELECT COALESCE(SUM(amount),0) 
                    )  + (
                    SELECT COALESCE(SUM(withheld_amount),0) FROM withholding_taxes
                    WHERE withholding_taxes.payment_voucher_item_id = main_table.payment_voucher_item_id
                    ) 
                ) AS paid_amount 
                FROM payment_voucher_items AS main_table
                LEFT JOIN payment_voucher_item_approved_sub_contract_requisition_items ON main_table.payment_voucher_item_id = payment_voucher_item_approved_sub_contract_requisition_items.payment_voucher_item_id
                LEFT JOIN sub_contract_payment_requisition_approval_items ON payment_voucher_item_approved_sub_contract_requisition_items.sub_contract_payment_requisition_approval_item_id = sub_contract_payment_requisition_approval_items.id
                LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id
                LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                WHERE is_final = 1 AND status = "APPROVED" AND certificate_id = ' . $certificate_id . '';
        $query = $this->db->query($sql);
        return $query->row()->paid_amount;
    }

    public function approved_amount()
    {
        $certificate_id = $this->{$this::DB_TABLE_PK};
        $sql = 'SELECT COALESCE(SUM(approved_amount*1.18),0) AS approved_amount 
                FROM sub_contract_payment_requisition_approval_items
                LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_item_id = sub_contract_payment_requisition_items.id
                LEFT JOIN sub_contract_payment_requisition_approvals ON sub_contract_payment_requisition_approval_items.sub_contract_payment_requisition_approval_id = sub_contract_payment_requisition_approvals.id
                LEFT JOIN sub_contract_payment_requisitions ON sub_contract_payment_requisition_approvals.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
                WHERE is_final = 1 AND status = "APPROVED" AND certificate_id = ' . $certificate_id . '';
        $query = $this->db->query($sql);
        return $query->row()->approved_amount;
    }

    public function load_sub_contactor_description()
    {
        $sub_contract = $this->sub_contract();
        $contractor = $sub_contract->stakeholder();
        return strtoupper($sub_contract->contract_name . '-' . $contractor->stakeholder_name);
    }

    public function is_vat_iclusive()
    {
        return $this->vat_inclusive == 1 ? "VAT+" : "";
    }

    public function has_requisition()
    {
        $this->load->model('sub_contract_payment_requisition_item');
        $requisition_items = $this->sub_contract_payment_requisition_item->get(0, 0, ['certificate_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($requisition_items) ? array_shift($requisition_items) : false;
    }
}
