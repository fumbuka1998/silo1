<?php

class Requisition extends MY_Model
{

    const DB_TABLE = 'requisitions';
    const DB_TABLE_PK = 'requisition_id';
    const REQUISITION_TYPES = ['project', 'cost_center'];

    public $approval_module_id;
    public $request_date;
    public $currency_id;
    public $required_date;
    public $finalized_date;
    public $requesting_comments;
    public $freight;
    public $inspection_and_other_charges;
    public $vat_inclusive;
    public $vat_percentage;
    public $confidentiality_chain_position;
    public $requester_id;
    public $foward_to;
    public $finalizer_id;
    public $status;


    public function requisition_number()
    {
        return 'RQ/' . add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function project_requisition()
    {
        $this->load->model('project_requisition');
        $junction_items = $this->project_requisition->get(1, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junction_items) ? array_shift($junction_items) : false;
    }

    public function cost_center_requisition()
    {
        $this->load->model('cost_center_requisition');
        $junction_items = $this->cost_center_requisition->get(1, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junction_items) ? array_shift($junction_items) : false;
    }

    public function project()
    {
        return $this->project_requisition()->project();
    }

    public function cost_center()
    {
        return $this->cost_center_requisition()->cost_center();
    }

    public function requested_for()
    {
        foreach ($this::REQUISITION_TYPES as $type) {
            $model = $type . '_requisition';
            $this->load->model($model);
            $junctions = $this->$model->get(1, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
            if (!empty($junctions)) {
                return $type;
            }
        }
    }

    public function cost_center_name()
    {
        $for_project = $this->project_requisition();
        if ($for_project) {
            $source = $for_project->project()->project_name;
        } else {
            $source = $this->cost_center_requisition()->cost_center()->cost_center_name;
        }
        return $source;
    }

    public function department()
    {
        $for_project = $this->project_requisition();
        if ($for_project) {
            $source = $for_project->project()->category()->category_name;
        } else {
            $source = 'General';
        }
        return $source;
    }

    public function requester()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->requester_id);
        return $employee;
    }

    public function finalizer()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->finalizer_id);
        return $employee;
    }

    public function material_items($approved_vendor_id = 'all')
    {
        $this->load->model('requisition_material_item');
        $where['requisition_id'] = $this->{$this::DB_TABLE_PK};
        if ($approved_vendor_id != 'all') {
            $where['approved_vendor_id'] = $approved_vendor_id;
        }
        return $this->requisition_material_item->get(0, 0, $where);
    }

    public function asset_items()
    {
        $this->load->model('requisition_asset_item');
        return $this->requisition_asset_item->get(0, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function cash_items()
    {
        $this->load->model('requisition_cash_item');
        $where['requisition_id'] = $this->{$this::DB_TABLE_PK};
        return $this->requisition_cash_item->get(0, 0, $where);
    }

    public function service_items()
    {
        $this->load->model('requisition_service_item');
        $where['requisition_id'] = $this->{$this::DB_TABLE_PK};
        return $this->requisition_service_item->get(0, 0, $where);
    }

    public function delete_items()
    {
        $this->db->delete('requisition_material_items', ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('requisition_cash_items', ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('requisition_asset_items', ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('requisition_service_items', ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function delete_junctions()
    {
        $this->db->delete('project_requisitions', ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('cost_center_requisitions', ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function vendor_options()
    {
        $sql = 'SELECT * FROM (
                    SELECT stakeholder_id, stakeholders.stakeholder_name FROM requisition_material_items
                    LEFT JOIN stakeholders ON requisition_material_items.approved_vendor_id = stakeholders.stakeholder_id
                    WHERE requisition_material_items.requisition_id = "' . $this->{$this::DB_TABLE_PK} . '"
                    AND requisition_material_items.approved_vendor_id IS NOT NULL

                    UNION

                    SELECT stakeholder_id, stakeholders.stakeholder_name FROM requisition_tools_items
                    LEFT JOIN stakeholders ON requisition_tools_items.approved_vendor_id = stakeholders.stakeholder_id
                    WHERE requisition_tools_items.requisition_id = "' . $this->{$this::DB_TABLE_PK} . '"
                    AND requisition_tools_items.approved_vendor_id IS NOT NULL
                ) AS vendors_options
                GROUP BY vendors_options.stakeholder_id;
                ';
        $query = $this->db->query($sql);
        $vendors = $query->result();
        $options = '<option value="">&nbsp;</option>';
        $approved_vendors = [];
        if (!empty($vendors)) {
            $options .= '<optgroup label="Approved Vendors"></optgroup>';
            foreach ($vendors as $vendor) {
                $approved_vendors[] = $vendor->stakeholder_id;
                $options .= '<option value="' . $vendor->stakeholder_id . '">' . $vendor->stakeholder_name . '</option>';
            }
        }

        $sql_other_vendors = 'SELECT stakeholder_id, stakeholder_name FROM stakeholders
                ';
        $query = $this->db->query($sql_other_vendors);
        $other_vendors = $query->result();
        if (!empty($other_vendors)) {
            $options .= '<optgroup label="Other Vendors"></optgroup>';
            foreach ($other_vendors as $vendor) {
                if (!in_array($vendor->stakeholder_id, $approved_vendors)) {
                    $options .= '<option value="' . $vendor->stakeholder_id . '">' . $vendor->stakeholder_name . '</option>';
                }
            }
        }
        return $options;
    }

    public function is_project_related()
    {
        return $this->project_requisition();
    }

    public function requester_title()
    {
        if ($this->is_project_related()) {
            $sql = 'SELECT position_name AS title FROM project_team_members
                  LEFT JOIN projects ON project_team_members.project_id = projects.project_id
                  LEFT JOIN job_positions ON project_team_members.job_position_id = job_positions.job_position_id
                  LEFT JOIN project_requisitions ON projects.project_id = project_requisitions.project_id
                  WHERE employee_id = "' . $this->requester_id . '" AND project_requisitions.requisition_id = "' . $this->{$this::DB_TABLE_PK} . '"
                  ';
            $query = $this->db->query($sql);
            return $query->row()->title;
        } else {
            return $this->requester()->position()->position_name;
        }
    }

    public function attachments()
    {
        $this->load->model('requisition_attachment');
        $junctions = $this->requisition_attachment->get(0, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        $attachments = [];
        foreach ($junctions as $junction) {
            $attachments[] = $junction->attachment();
        }
        return $attachments;
    }

    public function requisitions_list($project_id, $limit, $start, $keyword, $order)
    {
        $this->load->model([
            'currency',
            'stakeholder',
            'account',
            'inventory_location',
            'sub_contract_certificate',
            'Approval_module',
            'sub_contract_payment_requisition'
        ]);

        $employee_id = $this->session->userdata('employee_id');
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
        if (!is_null($project_id)) {
            $this->load->model('project');
            $project = new Project();
            $project->load($project_id);
            $data['material_options'] = material_item_dropdown_options($project->category_id);
        }

        $order_columns = !is_null($project_id) ? ['request_date', 'requisition_id', 'required_date', '', 'status'] : ['request_date', 'requisition_id', '', 'required_date', '', 'status'];
        $order_string = dataTable_order_string($order_columns, $order, 'request_date');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $status = $this->input->post('status');
        $approval_module_id = $this->input->post('approval_module_id');
        $approval_module_id = $approval_module_id != '' ? $approval_module_id : null;
        $approval_level = $this->input->post('approval_level');
        $approval_level = $approval_level != '' ? $approval_level : null;
        if ($status != 'all' && $status != 'mine' && $status != '') {
            $where = ' status = "' . $status . '" ' . ($confidentiality_position != '' ? ' AND confidentiality_chain_position <=' . $confidentiality_position : '') . '';
        } else {
            $where = $confidentiality_position != '' ? ' confidentiality_chain_position <=' . $confidentiality_position : '';
        }

        if ($status == 'mine') {
            $where .= '' . ($where != '' ? ' AND' : '') . ' status = "PENDING"';
        }

        if (!is_null($approval_module_id)) {
            $where .= '' . ($where != '' ? ' AND' : '') . ' approval_module_id = ' . $approval_module_id . '';
        }

        $rq_level_query = '
            SELECT 
            CASE
            WHEN requisition_approvals.id IS NOT NULL THEN level
            ELSE 0
            END
            FROM requisitions AS req 
            LEFT JOIN requisition_approvals ON req.requisition_id = requisition_approvals.requisition_id
            LEFT JOIN approval_chain_levels ON requisition_approvals.approval_chain_level_id = approval_chain_levels.id
            WHERE req.status = "PENDING" AND req.requisition_id = requisitions.requisition_id
           ORDER BY level DESC LIMIT 1';

        $scrq_level_query = '
           SELECT 
           CASE
             WHEN sub_contract_payment_requisition_approvals.id IS NOT NULL THEN level
             ELSE 0
           END
           FROM sub_contract_payment_requisitions AS req
           LEFT JOIN sub_contract_payment_requisition_approvals ON req.sub_contract_requisition_id = sub_contract_payment_requisition_approvals.sub_contract_requisition_id
           LEFT JOIN approval_chain_levels ON sub_contract_payment_requisition_approvals.approval_chain_level_id = approval_chain_levels.id
           WHERE req.status = "PENDING" AND req.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
           ORDER BY level DESC LIMIT 1';

        $approved_reqs_query = '
                    SELECT ( 
                        CASE 
                        WHEN requisition_type = "normal_requisition" THEN (
                            SELECT requisition_id FROM requisition_approvals
                            LEFT JOIN approval_chain_levels ON requisition_approvals.approval_chain_level_id = approval_chain_levels.id
                            WHERE requisition_approvals.requisition_id = all_types_requisitions.requisition_id 
                            AND approval_chain_levels.level > all_types_requisitions.level
                            )
                        ELSE (
                            SELECT sub_contract_requisition_id FROM sub_contract_payment_requisition_approvals
                            LEFT JOIN approval_chain_levels ON sub_contract_payment_requisition_approvals.approval_chain_level_id = approval_chain_levels.id
                            WHERE sub_contract_payment_requisition_approvals.sub_contract_requisition_id = all_types_requisitions.requisition_id 
                            AND approval_chain_levels.level > all_types_requisitions.level 
                            )
                        END
                    ) AS approved_requisition_ids';
        $last_approval_forward_to = 'SELECT ( 
                        CASE 
                        WHEN requisition_type = "normal_requisition" THEN (
                            SELECT forward_to FROM requisition_approvals
                            WHERE requisition_id = all_types_requisitions.requisition_id ORDER BY requisition_approvals.id DESC LIMIT 1 
                            )
                        ELSE (
                            SELECT forward_to FROM sub_contract_payment_requisition_approvals
                            WHERE sub_contract_requisition_id = all_types_requisitions.requisition_id ORDER BY sub_contract_payment_requisition_approvals.id DESC LIMIT 1 
                            )
                        END
                    ) AS approved_requisition_forward_to';

        $this_employee_level_query = '
                    SELECT level FROM employee_approval_chain_levels
                    LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                    WHERE status = "active" AND approval_chain_levels.approval_module_id = all_types_requisitions.approval_module_id AND employee_id = ' . $employee_id . ' LIMIT 1';

        if (!$project_id) {
            $sql = 'SELECT * FROM (
                        SELECT "sub_contract_requisition" AS requisition_type, sub_contract_payment_requisitions.sub_contract_requisition_id AS requisition_id, (' . $scrq_level_query . ') AS level, request_date, requester_id, foward_to, approval_module_id  
                        FROM sub_contract_payment_requisitions
                        ' . ($where != '' ? ' WHERE ' . $where : '') . '
 
                        UNION
                        
                        SELECT "normal_requisition" AS requisition_type, requisition_id, (' . $rq_level_query . ') AS level ,request_date, requester_id, foward_to, approval_module_id  
                        FROM requisitions
                        ' . ($where != '' ? ' WHERE ' . $where : '') . '
                    ) AS all_types_requisitions';
        } else {
            $sql = 'SELECT * FROM (
                        SELECT "sub_contract_requisition" AS requisition_type, sub_contract_payment_requisitions.sub_contract_requisition_id AS requisition_id, (' . $scrq_level_query . ') AS level , request_date, requester_id,foward_to, approval_module_id  
                        FROM sub_contract_payment_requisitions
                        LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisitions.sub_contract_requisition_id = sub_contract_payment_requisition_items.sub_contract_requisition_id
                        LEFT JOIN sub_contract_certificates ON sub_contract_payment_requisition_items.certificate_id = sub_contract_certificates.id
                        LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                        WHERE project_id = ' . $project_id . ($where != '' ? ' AND ' . $where : '') . '
 
                        UNION
                        
                        SELECT "normal_requisition" AS requisition_type, requisitions.requisition_id AS requisition_id, (' . $rq_level_query . ') AS level ,request_date, requester_id,foward_to, approval_module_id  
                        FROM requisitions
                        LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                        WHERE project_id = ' . $project_id . ($where != '' ? ' AND ' . $where : '') . '
                    ) AS all_types_requisitions';
        }
        if (!is_null($approval_level)) {
            $sql .= ' WHERE level =' . $this->previous_approval_level($approval_level, $approval_module_id) . '';
        }

        if ($status == 'mine') {
            $sql .= ' WHERE (
                (
                    foward_to IS NULL AND (' . $last_approval_forward_to . ') IS NULL AND level + 1 = (' . $this_employee_level_query . ')
                ) OR requester_id = ' . $employee_id . '
            ) OR (
                foward_to = ' . $employee_id . ' OR (' . $last_approval_forward_to . ') = ' . $employee_id . '
            )';
        }

        $sql .= ' GROUP BY request_date, requisition_id';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        $p_req_where_clause = $sc_req_where_clause = $where;
        if ($keyword != '') {
            $p_req_where_clause .= ($p_req_where_clause != '' ? ' AND ' : '') . ' (request_date LIKE "%' . $keyword . '%" OR status LIKE "%' . $keyword . '%"  OR projects.project_name LIKE "%' . $keyword . '%" OR requisitions.requisition_id LIKE "%' . $keyword . '%" OR required_date LIKE "%' . $keyword . '%")';
            $sc_req_where_clause .= ($sc_req_where_clause != '' ? ' AND ' : '') . ' (request_date LIKE "%' . $keyword . '%" OR status LIKE "%' . $keyword . '%"  OR projects.project_name LIKE "%' . $keyword . '%" OR sub_contract_payment_requisitions.sub_contract_requisition_id LIKE "%' . $keyword . '%" OR required_date LIKE "%' . $keyword . '%")';
        }

        $p_req_where_clause = $p_req_where_clause != '' ? ' WHERE ' . $p_req_where_clause : '';
        $sc_req_where_clause = $sc_req_where_clause != '' ? ' WHERE ' . $sc_req_where_clause : '';
        if ($project_id) {
            $p_req_where_clause  != '' ? $p_req_where_clause .= ' AND project_requisitions.project_id = ' . $project_id . '  ' : $p_req_where_clause .= ' WHERE project_requisitions.project_id = ' . $project_id;
            $sc_req_where_clause  != '' ? $sc_req_where_clause .= ' AND sub_contracts.project_id = ' . $project_id . '  ' : $sc_req_where_clause .= ' WHERE sub_contracts.project_id = ' . $project_id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM (

                SELECT "normal_requisition" AS requisition_type, requisitions.requisition_id AS requisition_id, (' . $rq_level_query . ') AS level , request_date, requisitions.currency_id, required_date,status, requester_id, foward_to, approval_module_id
                FROM requisitions
                LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
                ' . $p_req_where_clause . '
                
                UNION 
                
                SELECT "sub_contract_requisition" AS requisition_type, sub_contract_payment_requisitions.sub_contract_requisition_id AS requisition_id, (' . $scrq_level_query . ') AS level , request_date, sub_contract_payment_requisitions.currency_id, required_date,status, requester_id, foward_to, approval_module_id
                FROM sub_contract_payment_requisitions
                LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisitions.sub_contract_requisition_id = sub_contract_payment_requisition_items.sub_contract_requisition_id
                LEFT JOIN sub_contract_certificates ON sub_contract_payment_requisition_items.certificate_id = sub_contract_certificates.id
                LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                LEFT JOIN projects ON sub_contracts.project_id = projects.project_id
                ' . $sc_req_where_clause . '
                
                ) AS all_types_requisitions';
        if (!is_null($approval_level)) {
            $sql .= ' WHERE level = ' . $this->previous_approval_level($approval_level, $approval_module_id) . '';
        }

        if ($status == 'mine') {
            $sql .= ' WHERE (
                (
                    foward_to IS NULL AND (' . $last_approval_forward_to . ') IS NULL AND level + 1 = (' . $this_employee_level_query . ')
                ) OR requester_id = ' . $employee_id . '
            ) OR (
                foward_to = ' . $employee_id . ' OR (' . $last_approval_forward_to . ') = ' . $employee_id . '
            )';
        }
        $sql .= ' GROUP BY request_date, requisition_id ' . $order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        foreach ($results as $row) {
            $currency = new Currency();
            $currency->load($row->currency_id);
            $data['currency'] = $currency;
            $data['requisition_id'] = $requisition_id = $row->requisition_id;
            if ($row->requisition_type == 'sub_contract_requisition') {
                $requisition =  new Sub_contract_payment_requisition();
                $requisition->load($requisition_id);


                $approval_module = new Approval_module();
                $approval_module->load($requisition->approval_module_id);
                $data['last_approval'] = $last_approval = $requisition->last_approval();
                $data['payment_voucher'] = false;
                $data['journal_voucher'] = false;
                $data['imprest_voucher'] = false;
                if ($last_approval) {
                    $data['payment_voucher'] = $payment_voucher = $last_approval->payment_voucher();
                    $data['journal_voucher'] = $journal_voucher = $last_approval->journal_voucher();
                }
                if ($data['payment_voucher']) {
                    $data['paid_items'] = $paid_items = $last_approval->payment_vouchers();
                }
                $data['requisition'] = $requisition;
                $data['forward_to_dropdown'] = $approval_module->forwarding_to_employee_options();
                $data['forwarded_to_employee'] = $forwarded_to_employee = $requisition->forwarded_to_employee_approval();
                $data['can_override_prev'] = false; //$data['can_override_prev'] = $can_override_prev = $this->approval_module->employee_power($requisition->approval_module_id, 'sub_contract_payment_requisition', $requisition_id);
                $data['emp_special_approval'] = []; //$emp_special_approval = $requisition->special_level_approval(true);
                $data['approval_module'] = $requisition->approval_module();
                $data['requisition_approval'] = $requisition->sub_contract_requisition_approval();
                $data['project'] = $project = $requisition->project();
                $data['sub_contract_options'] = $this->stakeholder->sub_contract_options($project->project_id);
                $data['certificate_options'] = $this->sub_contract_certificate->drop_down_options();
                $data['approved_print_out_link'] = 'requisitions/preview_sub_contract_requisition/';
                $data['requisition_number'] =  $requisition->sub_contract_requisition_number();
                $data['special_level_approval'] = false; // $special_level_approval = $requisition->special_level_approval();
                $data['current_approval_level'] = $current_approval_level = $requisition->current_approval_level();


                if ($forwarded_to_employee) {
                    $data['can_approve'] = $can_approve = $requisition->status != 'INCOMPLETE' && $current_approval_level && $employee_id == $requisition->forwarded_to_employee_approval(true);
                } else {
                    $data['can_approve'] = $can_approve = $requisition->status != 'INCOMPLETE' && $current_approval_level && in_array($employee_id, $current_approval_level->can_approve_positions()) && ($last_approval ? $last_approval->forward_to == null : $requisition->foward_to == null);
                }


                if (($status == 'mine' && ($can_approve || $row->requester_id == $employee_id))|| $status != 'mine') {
                    $row = [
                        $last_approval ? standard_datetime($last_approval->created_at, true) : standard_datetime($row->request_date),
                        $requisition->sub_contract_requisition_number(),
                        $requisition->cost_center_name(),
                        $row->required_date != null ? set_date($row->required_date) : 'N/A',
                        $requisition->currency()->symbol . '<span class="pull-right">' . number_format($requisition->requisition_amount(), 2) . '</span>',
                        $requisition->progress_status_label(),
                        $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_list_actions', $data, true)
                    ];
                }
            } else {
                $requisition = new self();
                $requisition->load($requisition_id);


                $approval_module = new Approval_module();
                $approval_module->load($requisition->approval_module_id);
                $data['requisition'] = $requisition;
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
                $data['forwarded_to_employee'] = $forwarded_to_employee = $requisition->forwarded_to_employee_approval();
                $data['can_override_prev'] = false; //$data['can_override_prev'] = $can_override_prev = $this->approval_module->employee_power($requisition->approval_module_id, 'requisition', $requisition_id);
                $data['emp_special_approval'] = []; // $emp_special_approval = $requisition->special_level_approval(true);
                $data['approval_module'] = $requisition->approval_module();
                $data['requisition_approval'] = $requisition->requisition_approval();
                $data['attachments'] = $requisition->attachments();
                $data['approved_print_out_link'] = 'requisitions/preview_requisition/';
                $data['requisition_number'] =  $requisition->requisition_number();
                $data['special_level_approval'] = false; // $special_level_approval = $requisition->special_level_approval();
                $data['current_approval_level'] = $current_approval_level = $requisition->current_approval_level();

                if ($forwarded_to_employee) {
                    $data['can_approve'] = $can_approve = $requisition->status != 'INCOMPLETE' && $current_approval_level && $employee_id == $requisition->forwarded_to_employee_approval(true);
                } else {
                    $data['can_approve'] = $can_approve = $requisition->status != 'INCOMPLETE' && $current_approval_level && in_array($employee_id, $current_approval_level->can_approve_positions()) && ($last_approval ? $last_approval->forward_to == null : $requisition->foward_to == null);
                }

                if (($status == 'mine' && ($can_approve ||
                        $row->requester_id == $employee_id))
                    || $status != 'mine'
                ) {
                    $row = [
                        $last_approval ? standard_datetime($last_approval->created_at, true) : standard_datetime($row->request_date),
                        $requisition->requisition_number(),
                        $requisition->cost_center_name(),
                        $row->required_date != null ? set_date($row->required_date) : 'N/A',
                        $requisition->currency()->symbol . '<span class="pull-right">' . number_format($requisition->requisition_amount(), 2) . '</span>',
                        $requisition->progress_status_label(),
                        $this->load->view('requisitions/requisitions_list/requisition_list_actions', $data, true)
                    ];
                }
            }

            if ($project_id) {
                array_splice($row, 2, 1);
            }
            $rows[] = $row;
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function insert_project_requisition($project_id)
    {
        $this->load->model('project_requisition');
        $junction_item = new Project_requisition();
        $junction_item->project_id = $project_id;
        $junction_item->requisition_id = $this->{$this::DB_TABLE_PK};
        $junction_item->save();
    }

    public function insert_cost_center_requisition($cost_center_id)
    {
        $this->load->model('Cost_center_requisition');
        $junction_item = new Cost_center_requisition();
        $junction_item->cost_center_id = $cost_center_id;
        $junction_item->requisition_id = $this->{$this::DB_TABLE_PK};
        $junction_item->save();
    }

    public function current_users_levels()
    {
        $this->load->model('human_resource/employee_approval_chain_level');
        $junctions = $this->employee_approval_chain_level->get(0, 0, ['employee_id' => $this->session->userdata('employee_id')]);
        $levels = '';
        foreach ($junctions as $junction) {
            $levels .= ',' . $junction->approval_chain_level()->level;
        }
        $levels = substr($levels, 1);
        return $levels;
    }

    public function approval_module()
    {
        $this->load->model('approval_module');
        $approval_module = new Approval_module();
        $approval_module->load($this->approval_module_id);
        return $approval_module;
    }

    public function last_approval()
    {
        $this->load->model('requisition_approval');
        $approvals = $this->requisition_approval->get(1, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}], ' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function final_approval()
    {
        $this->load->model('requisition_approval');
        $approvals = $this->requisition_approval->get(1, 0, [
            'requisition_id' => $this->{$this::DB_TABLE_PK},
            'is_final' => 1
        ], ' id DESC');
        return !empty($approvals) ? array_shift($approvals) : false;
    }

    public function previous_approval_level($level, $approval_module_id)
    {
        $this->load->model(['approval_chain_level', 'approval_module']);
        $approval_module = new Approval_module();
        $approval_module->load($approval_module_id);
        $approval_chain_levels = $this->approval_chain_level->get(1, 0, ['approval_module_id' => $approval_module_id, 'status' => 'active', 'level < ' => $level], ' level DESC');
        if (!empty($approval_chain_levels)) {
            $approval_chain_level = array_shift($approval_chain_levels);
            return $approval_chain_level->level;
        } else {
            return 0;
        }
    }

    public function current_approval_level()
    {
        $last_approval = $this->last_approval();
        $this->load->model('approval_module');
        if ($last_approval) {
            if (!is_null($last_approval->returned_chain_level_id)) {
                $this->load->model('approval_chain_level');
                $current_level = new Approval_chain_level();
                $current_level->load($last_approval->returned_chain_level_id);
            } else {
                $current_level = $last_approval->approval_chain_level()->next_level();
            }
        } else {
            $current_level = $this->approval_module->chain_levels(0, $this->approval_module_id, 'active');
        }

        return !empty($current_level) ? (is_array($current_level) ? array_shift($current_level) : $current_level) : false;
    }

    public function next_level_employees_options($next_level_id)
    {

        $sql = 'SELECT employee_approval_chain_levels.employee_id AS employee_id, CONCAT(employees.first_name," ",employees.last_name) AS employee_name FROM employee_approval_chain_levels
                LEFT JOIN employees ON employee_approval_chain_levels.created_by = employees.employee_id
                LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                WHERE approval_chain_levels.approval_module_id IN (1,2) 
                AND approval_chain_levels.id = ' . $next_level_id . '
                AND status = "active"
                AND special_level = 0';

        $query = $this->db->query($sql);
        $options[''] = '&nbsp;';
        $results = $query->result();

        foreach ($results as $row) {
            $options[$row->employee_id . '_employee'] = $row->employee_name;
        }
        return $options;
    }

    public function next_approval_employees_options()
    {
        $current_level = $this->current_approval_level();
        if ($current_level) {
            $next_level = $current_level->next_level();
            $next_level = $next_level != false ? $next_level : $current_level;
            return $this->next_level_employees_options($next_level->{$next_level::DB_TABLE_PK});
        }
    }

    public function progress_status_label()
    {
        $current_level  = $this->current_approval_level();
        $last_approval = $this->last_approval();
        $payment_voucher = $last_approval ? $last_approval->payment_voucher() : false;
        $imprest_voucher = $last_approval ? $last_approval->imprest_voucher($last_approval->{$last_approval::DB_TABLE_PK}) : false;
        if ($this->status == 'REJECTED') {
            $label = '<span style="font-size: 12px" class="label label-danger">Rejected By ' . $this->finalizer()->full_name() . '</span>';
        } else if ($this->status == 'INCOMPLETE') {
            $label = '<span style="font-size: 12px" class="label label-warning">' . $this->status . '</span>';
        } else if ($this->status != 'APPROVED') {
            if ($last_approval && $last_approval->forward_to) {
                $label = '<span style="font-size: 12px" class="label label-info">Waiting For ' . $last_approval->forward_to()->full_name() . '</span>';
            } else if ($this->foward_to && !$last_approval) {
                $label = '<span style="font-size: 12px" class="label label-info">Waiting For ' . $this->foward_to()->full_name() . '</span>';
            } else if ($current_level) {
                $label = '<span style="font-size: 12px" class="label label-info">Waiting For ' . $current_level->level_name . '</span>';
            } else {
                $label = '<span style="font-size: 12px" title="The Level being waited to approve has either been removed or disabled" class="label label-danger">Level Not Found!</span>';
            }
        } else {
            if ($payment_voucher or $imprest_voucher) {
                $approved_amount = $last_approval->total_approved_amount(false, 'cash');
                $imprest_amount = $last_approval->imprest_voucher($last_approval->{$last_approval::DB_TABLE_PK}) ? $last_approval->imprest_voucher_object()->total_amount_vat_inclusive() : 0;
                $paid_amount = $last_approval->total_paid_amount();
                if (($approved_amount - ($paid_amount  + $imprest_amount)) <= 0) {
                    $label = '<span style="font-size: 12px" class="label label-success">Paid</span>';
                } else {
                    $label = '<span class="label" style="background-color: #00e765; font-size: 12px;">Partial Payment(s)</span>';
                }
            } else {
                $label = '<span style="font-size: 12px" class="label label-success">Approval Completed</span>';
            }
        }
        return $label;
    }

    public function requisition_approvals($requisition_id = null)
    {
        $this->load->model('requisition_approval');
        $requisition_id = !is_null($requisition_id)  ? $requisition_id : $this->{$this::DB_TABLE_PK};
        return $this->requisition_approval->get(0, 0, ['requisition_id' => $requisition_id]);
    }

    public function requisition_approval()
    {
        $this->load->model('requisition_approval');
        $requisition_approvals = $this->requisition_approval->get(0, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($requisition_approvals) ? array_shift($requisition_approvals) : false;
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function requested_material_amount($sql_string = false)
    {
        $sql = 'SELECT COALESCE(SUM(requested_quantity*requested_rate),0) AS material_amount FROM requisition_material_items
                WHERE requisition_id = ' . $this->{$this::DB_TABLE_PK};

        return $sql_string ? $sql : $this->db->query($sql)->row()->material_amount;
    }

    public function requested_asset_amount($sql_string = false)
    {
        $sql = 'SELECT COALESCE(SUM(requested_quantity*requested_rate),0) AS asset_amount FROM requisition_asset_items
                WHERE requisition_id = ' . $this->{$this::DB_TABLE_PK};

        return $sql_string ? $sql : $this->db->query($sql)->row()->asset_amount;
    }

    public function requested_service_amount($sql_string = false)
    {
        $sql = 'SELECT COALESCE(SUM(requested_quantity*requested_rate),0) AS service_amount FROM requisition_service_items
                WHERE requisition_id = ' . $this->{$this::DB_TABLE_PK};

        return $sql_string ? $sql : $this->db->query($sql)->row()->service_amount;
    }

    public function requested_cash_amount($sql_string = false)
    {
        $sql = 'SELECT COALESCE(SUM(requested_quantity*requested_rate),0) AS cash_amount FROM requisition_cash_items
                WHERE requisition_id = ' . $this->{$this::DB_TABLE_PK};

        return $sql_string ? $sql : $this->db->query($sql)->row()->cash_amount;
    }

    public function requested_items_amount()
    {
        $sql = 'SELECT (
            (' . $this->requested_cash_amount(true) . ') + (' . $this->requested_material_amount(true) . ') + (' . $this->requested_asset_amount(true) . ') + (' . $this->requested_service_amount(true) . ')
        ) AS items_amount';
        return $this->db->query($sql)->row()->items_amount;
    }

    public function total_requested_amount()
    {
        return $this->requested_items_amount() + $this->freight + $this->inspection_and_other_charges;
    }

    public function requisition_amount()
    {
        $this->load->model(['requisition_approval']);
        $approvals = $this->requisition_approval->get(0, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}], 'id DESC');
        $last_approval = !empty($approvals) ? array_shift($approvals) : false;
        if ($last_approval) {
            $requisition_amount = $last_approval->total_approved_amount();
        } else {
            $requisition_amount = $this->total_requested_amount();
        }

        return $requisition_amount;
    }

    public function total_amount_in_base_currency()
    {
        if ($this->currency_id == 1) {
            return $this->total_requested_amount();
        } else {
            $currency = $this->currency();
            return $this->total_requested_amount() * $currency->rate_to_native($this->request_date);
        }
    }

    public function notify_approver()
    {
        $current_approval_level = $this->current_approval_level();
        if ($current_approval_level) {
        }
    }

    public function purchase_orders()
    {
        $this->load->model('requisition_purchase_order');
        $junctions = $this->requisition_purchase_order->get(0, 0, ['requisition_id' => $this->{$this::DB_TABLE_PK}]);
        $purchase_orders = [];
        foreach ($junctions as $junction) {
            $purchase_orders[] = $junction->purchase_order();
        }
        return $purchase_orders;
    }

    public function external_transfers()
    {
        $sql = 'SELECT DISTINCT transfer_id FROM transferred_transfer_orders
                LEFT JOIN requisition_approvals ON transferred_transfer_orders.requisition_approval_id = requisition_approvals.id
                WHERE requisition_id =  ' . $this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $results = $query->result();

        $this->load->model('external_material_transfer');
        $transfers = [];
        foreach ($results as $result) {
            $transfer = new External_material_transfer();
            $transfer->load($result->transfer_id);
            $transfers[] = $transfer;
        }

        return $transfers;
    }

    public function special_level_approval($employees = false)
    {
        $this->load->model(['approval_module']);
        $approval_module = new Approval_module();
        $approval_module->load($this->approval_module_id);

        if ($employees) {
            $emp_with_special_approval = [];
            foreach ($approval_module->chain_levels(0, $approval_module->id, 'active', true) as $chain_level) {
                $employee_options = $chain_level->employee_options();
                $no = 0;
                foreach ($employee_options as $index => $employee_option) {
                    $no++;
                    if ($no > 1) $emp_with_special_approval[] = $index;
                }
            }
            return $emp_with_special_approval;
        } else {
            return $approval_module->has_special_level();
        }
    }

    public function foward_to()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->foward_to);
        return $employee;
    }

    public function forwarded_to_employee_special_level()
    {
        $sql = 'SELECT approval_chain_level_id FROM employee_approval_chain_levels
                LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                WHERE approval_chain_levels.approval_module_id = ' . $this->approval_module_id . '
                AND employee_id = ' . $this->foward_to . '
                AND status = "active"
                AND special_level = 1';

        $query = $this->db->query($sql);
        return !empty($query->row()) > 0 ? $query->row() : false;
    }

    public function vat_enum_values($field = false)
    {
        $options['NULL'] = 'NONE';
        if ($field == false) {
            $options[0] = 'No';
            $options[1] = 'Yes';
        } else {
            $type = $this->db->query("SHOW COLUMNS FROM requisitions WHERE Field = '" . $field . "'")->row(0)->Type;
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $enum = explode("','", $matches[1]);
            $count = 0;
            foreach ($enum as $item) {
                $options[$item] = $item == 'VAT PRICED' ? $item : 'CALCULATE VAT';
            }
        }
        return $options;
    }

    public function requisiton_lists_on_dashboard($limit, $start, $keyword, $order)
    {
        $employee_id = $this->session->userdata('employee_id');
        $order_columns = ['requisition_id', 'required_date', 'status'];
        //order string
        $order_string = dataTable_order_string($order_columns, $order, 'requisition_id DESC');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $rq_level_query = '
            SELECT 
            CASE
            WHEN requisition_approvals.id IS NOT NULL THEN level
            ELSE 0
            END
            FROM requisitions AS req 
            LEFT JOIN requisition_approvals ON req.requisition_id = requisition_approvals.requisition_id
            LEFT JOIN approval_chain_levels ON requisition_approvals.approval_chain_level_id = approval_chain_levels.id
            WHERE req.status = "PENDING" AND req.requisition_id = requisitions.requisition_id
           ORDER BY level DESC LIMIT 1';

        $scrq_level_query = '
           SELECT 
           CASE
             WHEN sub_contract_payment_requisition_approvals.id IS NOT NULL THEN level
             ELSE 0
           END
           FROM sub_contract_payment_requisitions AS req
           LEFT JOIN sub_contract_payment_requisition_approvals ON req.sub_contract_requisition_id = sub_contract_payment_requisition_approvals.sub_contract_requisition_id
           LEFT JOIN approval_chain_levels ON sub_contract_payment_requisition_approvals.approval_chain_level_id = approval_chain_levels.id
           WHERE req.status = "PENDING" AND req.sub_contract_requisition_id = sub_contract_payment_requisitions.sub_contract_requisition_id
           ORDER BY level DESC LIMIT 1';

        $approved_reqs_query = '
                    SELECT ( 
                        CASE 
                        WHEN requisition_type = "normal_requisition" THEN (
                            SELECT requisition_id FROM requisition_approvals
                            LEFT JOIN approval_chain_levels ON requisition_approvals.approval_chain_level_id = approval_chain_levels.id
                            WHERE requisition_approvals.requisition_id = all_types_requisitions.requisition_id 
                            AND approval_chain_levels.level > all_types_requisitions.level
                            )
                        ELSE (
                            SELECT sub_contract_requisition_id FROM sub_contract_payment_requisition_approvals
                            LEFT JOIN approval_chain_levels ON sub_contract_payment_requisition_approvals.approval_chain_level_id = approval_chain_levels.id
                            WHERE sub_contract_payment_requisition_approvals.sub_contract_requisition_id = all_types_requisitions.requisition_id 
                            AND approval_chain_levels.level > all_types_requisitions.level 
                            )
                        END
                    ) AS approved_requisition_ids';
        $last_approval_forward_to = 'SELECT ( 
                        CASE 
                        WHEN requisition_type = "normal_requisition" THEN (
                            SELECT forward_to FROM requisition_approvals
                            WHERE requisition_id = all_types_requisitions.requisition_id ORDER BY requisition_approvals.id DESC LIMIT 1 
                            )
                        ELSE (
                            SELECT forward_to FROM sub_contract_payment_requisition_approvals
                            WHERE sub_contract_requisition_id = all_types_requisitions.requisition_id ORDER BY sub_contract_payment_requisition_approvals.id DESC LIMIT 1 
                            )
                        END
                    ) AS approved_requisition_forward_to';

        $this_employee_level_query = '
                    SELECT level FROM employee_approval_chain_levels
                    LEFT JOIN approval_chain_levels ON employee_approval_chain_levels.approval_chain_level_id = approval_chain_levels.id
                    WHERE status = "active" AND approval_chain_levels.approval_module_id = all_types_requisitions.approval_module_id AND employee_id = ' . $employee_id . ' LIMIT 1';

        $where = ' status = "PENDING"';

        $sql = 'SELECT * FROM (
                    SELECT "sub_contract_requisition" AS requisition_type, sub_contract_payment_requisitions.sub_contract_requisition_id AS requisition_id, (' . $scrq_level_query . ') AS level, request_date, requester_id, foward_to, approval_module_id  
                    FROM sub_contract_payment_requisitions
                    ' . ($where != '' ? ' WHERE ' . $where : '') . '

                    UNION
                    
                    SELECT "normal_requisition" AS requisition_type, requisition_id, (' . $rq_level_query . ') AS level ,request_date, requester_id, foward_to, approval_module_id  
                    FROM requisitions
                    ' . ($where != '' ? ' WHERE ' . $where : '') . '
                ) AS all_types_requisitions
                WHERE (
                    (
                        foward_to IS NULL AND (' . $last_approval_forward_to . ') IS NULL AND level + 1 = (' . $this_employee_level_query . ')
                    ) OR requester_id = ' . $employee_id . '
                ) OR (
                    foward_to = ' . $employee_id . ' OR (' . $last_approval_forward_to . ') = ' . $employee_id . '
                )
                GROUP BY request_date, requisition_id';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        $p_req_where_clause = $sc_req_where_clause = $where;
        if ($keyword != '') {
            $p_req_where_clause .= ($p_req_where_clause != '' ? ' AND ' : '') . ' (request_date LIKE "%' . $keyword . '%" OR status LIKE "%' . $keyword . '%"  OR projects.project_name LIKE "%' . $keyword . '%" OR requisitions.requisition_id LIKE "%' . $keyword . '%" OR required_date LIKE "%' . $keyword . '%")';
            $sc_req_where_clause .= ($sc_req_where_clause != '' ? ' AND ' : '') . ' (request_date LIKE "%' . $keyword . '%" OR status LIKE "%' . $keyword . '%"  OR projects.project_name LIKE "%' . $keyword . '%" OR sub_contract_payment_requisitions.sub_contract_requisition_id LIKE "%' . $keyword . '%" OR required_date LIKE "%' . $keyword . '%")';
        }

        $p_req_where_clause = $p_req_where_clause != '' ? ' WHERE ' . $p_req_where_clause : '';
        $sc_req_where_clause = $sc_req_where_clause != '' ? ' WHERE ' . $sc_req_where_clause : '';


        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM (

                SELECT "normal_requisition" AS requisition_type, requisitions.requisition_id AS requisition_id, (' . $rq_level_query . ') AS level , request_date, requisitions.currency_id, required_date,status, requester_id, foward_to, approval_module_id
                FROM requisitions
                LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
                ' . $p_req_where_clause . '
                
                UNION 
                
                SELECT "sub_contract_requisition" AS requisition_type, sub_contract_payment_requisitions.sub_contract_requisition_id AS requisition_id, (' . $scrq_level_query . ') AS level , request_date, sub_contract_payment_requisitions.currency_id, required_date,status, requester_id, foward_to, approval_module_id
                FROM sub_contract_payment_requisitions
                LEFT JOIN sub_contract_payment_requisition_items ON sub_contract_payment_requisitions.sub_contract_requisition_id = sub_contract_payment_requisition_items.sub_contract_requisition_id
                LEFT JOIN sub_contract_certificates ON sub_contract_payment_requisition_items.certificate_id = sub_contract_certificates.id
                LEFT JOIN sub_contracts ON sub_contract_certificates.sub_contract_id = sub_contracts.id
                LEFT JOIN projects ON sub_contracts.project_id = projects.project_id
                ' . $sc_req_where_clause . '
                
                ) AS all_types_requisitions
                WHERE (
                    (
                        foward_to IS NULL AND (' . $last_approval_forward_to . ') IS NULL AND level + 1 = (' . $this_employee_level_query . ')
                    ) OR requester_id = ' . $employee_id . '
                ) OR (
                    foward_to = ' . $employee_id . ' OR (' . $last_approval_forward_to . ') = ' . $employee_id . '
                )
                GROUP BY request_date, requisition_id ' . $order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;
        $this->load->model(['sub_contract_payment_requisition', 'sub_contract_certificate']);

        $rows = [];
        foreach ($results as $row) {
            $requisition_id = $row->requisition_id;
            if ($row->requisition_type == 'sub_contract_requisition') {
                $sub_contract_requisition =  new Sub_contract_payment_requisition();
                $sub_contract_requisition->load($requisition_id);
                $cost_center_name = $sub_contract_requisition->cost_center_name();
                $forwarded_to_employee = $sub_contract_requisition->forwarded_to_employee_approval();
                $special_level_approval = false; //$sub_contract_requisition->special_level_approval();
                $current_approval_level = $sub_contract_requisition->current_approval_level();
                $emp_special_approval = []; //$sub_contract_requisition->special_level_approval(true);
                $last_approval = $sub_contract_requisition->last_approval();

                if ($forwarded_to_employee) {
                    $data['can_approve'] = $can_approve = $sub_contract_requisition->status != 'INCOMPLETE' && $current_approval_level && $employee_id == $sub_contract_requisition->forwarded_to_employee_approval(true);
                } else {
                    $data['can_approve'] = $can_approve = $sub_contract_requisition->status != 'INCOMPLETE' && $current_approval_level && in_array($employee_id, $current_approval_level->can_approve_positions()) && ($last_approval ? $last_approval->forward_to == null : $sub_contract_requisition->foward_to == null);
                }

                if ($can_approve || $row->requester_id == $employee_id) {
                    $row = [
                        anchor(base_url('requisitions/preview_sub_contract_requisition/' . $sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK}), $sub_contract_requisition->sub_contract_requisition_number(), 'target="_blank"'),
                        strlen($cost_center_name) < 10 ? $cost_center_name : '<span style="cursor: pointer;" title="' . $cost_center_name . '">' . substr($cost_center_name, 0, 9) . ' ...</span>',
                        $sub_contract_requisition->currency()->symbol . '<span class="pull-right">' . number_format($sub_contract_requisition->requisition_amount(), 2) . '</span>',
                        $sub_contract_requisition->progress_status_label(),
                    ];
                }
            } else {
                $requisition = new self();
                $requisition->load($requisition_id);
                $cost_center_name = $requisition->cost_center_name();
                $forwarded_to_employee = $requisition->forwarded_to_employee_approval();
                $current_approval_level = $requisition->current_approval_level();
                $special_level_approval = false; //$requisition->special_level_approval();
                $emp_special_approval = []; //$requisition->special_level_approval(true);
                $last_approval = $requisition->last_approval();

                if ($forwarded_to_employee) {
                    $data['can_approve'] = $can_approve = $requisition->status != 'INCOMPLETE' && $current_approval_level && $employee_id == $requisition->forwarded_to_employee_approval(true);
                } else {
                    $data['can_approve'] = $can_approve = $requisition->status != 'INCOMPLETE' && $current_approval_level && in_array($employee_id, $current_approval_level->can_approve_positions()) && ($last_approval ? $last_approval->forward_to == null : $requisition->foward_to == null);
                }

                if ($can_approve || $row->requester_id == $employee_id) {
                    $row = [
                        anchor(base_url('requisitions/preview_requisition/' . $requisition->{$requisition::DB_TABLE_PK}), $requisition->requisition_number(), 'target="_blank"'),
                        strlen($cost_center_name) < 10 ? $cost_center_name : '<span style="cursor: pointer;" title="' . $cost_center_name . '">' . substr($cost_center_name, 0, 9) . ' ...</span>',
                        $requisition->currency()->symbol . '<span class="pull-right">' . number_format($requisition->requisition_amount(), 2) . '</span>',
                        $requisition->progress_status_label(),
                    ];
                }
            }

            $rows[] = $row;
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function all_requisitions_on_dashboard()
    {
        $sql = 'SELECT 
                (

                    (
                    SELECT COUNT(sub_contract_payment_requisitions.sub_contract_requisition_id) FROM sub_contract_payment_requisitions
                    WHERE status = "PENDING"
                    ) + (
                    SELECT COUNT(requisition_id) FROM requisitions
                    WHERE status = "PENDING"
                    )
                        
                ) AS "PENDING",
                
                (

                    (
                    SELECT COUNT(sub_contract_payment_requisitions.sub_contract_requisition_id) FROM sub_contract_payment_requisitions
                    WHERE status = "APPROVED"
                    ) + (
                    SELECT COUNT(requisition_id) FROM requisitions
                    WHERE status = "APPROVED"
                    )
                    
                ) AS "APPROVED",
                
                (

                    (
                    SELECT COUNT(sub_contract_payment_requisitions.sub_contract_requisition_id) FROM sub_contract_payment_requisitions
                    WHERE status = "INCOMPLETE"
                    ) + (
                    SELECT COUNT(requisition_id) FROM requisitions
                    WHERE status = "INCOMPLETE"
                    )
                    
                ) AS "INCOMPLETE",
                
                (
                    (
                    SELECT COUNT(sub_contract_payment_requisitions.sub_contract_requisition_id) FROM sub_contract_payment_requisitions
                    WHERE status = "REJECTED"
                    ) + (
                    SELECT COUNT(requisition_id) FROM requisitions
                    WHERE status = "REJECTED"
                    )
                    
                ) AS "REJECTED",
                
                (
                    (
                    SELECT COUNT(sub_contract_payment_requisitions.sub_contract_requisition_id) FROM sub_contract_payment_requisitions
                    ) + (
                    SELECT COUNT(requisition_id) FROM requisitions
                    )
                    
                ) AS "ALL_TYPES"
                ';

        $query = $this->db->query($sql);
        $pending_requisitions = $query->row()->PENDING;
        $approved_requisitions = $query->row()->APPROVED;
        $incomplete_requisitions = $query->row()->INCOMPLETE;
        $rejected_requisitions = $query->row()->REJECTED;
        $all_requisitions = $query->row()->ALL_TYPES > 0 ? $query->row()->ALL_TYPES : 0.0000000000000000000000000000001;

        $data['pending_requisitions'] = number_format($pending_requisitions);
        $data['pending_requisitions_percent'] = round(($pending_requisitions / $all_requisitions) * 100);

        $data['approved_requisitions'] = number_format($approved_requisitions);
        $data['approved_requisitions_percent'] = round(($approved_requisitions / $all_requisitions) * 100);

        $data['incomplete_requisitions'] = number_format($incomplete_requisitions);
        $data['incomplete_requisitions_percent'] = round(($incomplete_requisitions / $all_requisitions) * 100);

        $data['rejected_requisitions'] = number_format($rejected_requisitions);
        $data['rejected_requisitions_percent'] = round(($rejected_requisitions / $all_requisitions) * 100);

        $data['all_requisitions'] = number_format($all_requisitions);

        return $data;
    }

    public function forwarded_to_employee_approval($ret_id = false)
    {
        $this->load->model('requisition_approval');
        $last_approval = $this->last_approval();
        if(!$ret_id){
            return $last_approval ? !is_null($last_approval->forward_to) : !is_null($this->foward_to);
        }
        if ($last_approval) {
            $forwarded_to_employee = $last_approval->forward_to;
            if ($forwarded_to_employee == $this->session->userdata('employee_id')) {
                return $ret_id ? $forwarded_to_employee : true;
            }
        } else {
            $forwarded_to_employee = $this->foward_to;
            if ($forwarded_to_employee == $this->session->userdata('employee_id')) {
                return $ret_id ? $forwarded_to_employee : true;
            }
        }
    }

    public function log_requisiton($action_noun, $action_verb)
    {
        $action = 'Requisition ' . $action_noun;
        $description = 'Requisition number ' . $this->requisition_number() . ' for ' . $this->cost_center_name() . ' was ' . $action_verb . 'ed';
        system_log($action, $description);
    }
}
