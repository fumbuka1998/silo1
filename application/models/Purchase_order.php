<?php

class Purchase_order extends MY_Model
{

    const DB_TABLE = 'purchase_orders';
    const DB_TABLE_PK = 'order_id';

    public $location_id;
    public $currency_id;
    public $stakeholder_id;
    public $issue_date;
    public $comments;
    public $vat_inclusive;
    public $vat_percentage;
    public $freight;
    public $inspection_and_other_charges;
    public $reference;
    public $delivery_date;
    public $status;
    public $employee_id;
    public $is_printed;
    public $handler_id;

    public function order_number()
    {
        return 'P.O/' . add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function creator()
    {
        return 'handler_id';
    }

    public function purchase_orders_list($limit, $start, $keyword, $order, $orders_for = null, $holder_id = null, $display_on_dashboard = false)
    {
        if ($orders_for == 'stakeholder') {
            $order_columns = ['issue_date', 'order_id', 'reference', 'location_name', 'project_name', 'status'];
            $where = '  purchase_orders.stakeholder_id = ' . $holder_id;
        } else if ($orders_for == 'location') {
            $order_columns = ['issue_date', 'order_id', 'stakeholder_name', 'project_name', 'status'];
            $where = ' purchase_orders.location_id = ' . $holder_id;
        } else {
            $order_columns = ['issue_date', 'order_id', 'stakeholder_name', 'location_name', 'project_name', 'status'];
            $where = '';
        }
        $order_string = dataTable_order_string($order_columns, $order, 'issue_date');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $status = $this->input->post('status');
        if ($status != 'all' && $status != '') {
            $where .= ($where != '' ? ' AND ' : '') . ' status = "' . $status . '" ';
        }

        $records_total = $this->count_rows($where);
        if ($keyword != '') {
            $where .= ($where == '' ? ' ' : ' AND ') . ' (
             stakeholder_name LIKE "%' . $keyword . '%" OR reference LIKE "%' . $keyword . '%" OR location_name LIKE "%' . $keyword . '%"
             OR issue_date LIKE "%' . $keyword . '%"  OR status LIKE "%' . $keyword . '%"
               OR order_id LIKE "%' . $keyword . '%" OR pcc.project_name LIKE "%' . $keyword . '%" OR cc.cost_center_name LIKE "%' . $keyword . '%"
               ) ';
        }

        $where = $where != '' ? ' WHERE ' . $where : '';

        $sql = 'SELECT SQL_CALC_FOUND_ROWS issue_date,purchase_orders.order_id,stakeholders.stakeholder_name, purchase_orders.stakeholder_id,projects.project_id,
                    inventory_locations.location_name, purchase_orders.location_id, status
                    FROM purchase_orders
                    LEFT JOIN inventory_locations ON purchase_orders.location_id = inventory_locations.location_id
                    LEFT JOIN stakeholders ON purchase_orders.stakeholder_id = stakeholders.stakeholder_id
                    LEFT JOIN projects ON inventory_locations.project_id = projects.project_id
                    LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                    LEFT JOIN projects AS pcc ON project_purchase_orders.project_id = pcc.project_id
                    LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
                    LEFT JOIN cost_centers cc ON cost_center_purchase_orders.cost_center_id = cc.id
                ' . $where . $order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        if (!empty($results)) {
            $data['material_options'] = material_item_dropdown_options('all');
            $this->load->model(['stakeholder', 'currency', 'cost_center', 'inventory_location']);
            $data['projects_options'] = projects_dropdown_options();
            $data['cost_center_options'] = $this->cost_center->dropdown_options();
            $data['stakeholders_options'] = $this->stakeholder->dropdown_options();
            $data['locations_options'] = $this->inventory_location->dropdown_options();
            $data['procurement_members_options'] = $this->purchase_order->procurement_members_options();
            $data['currency_options'] = $this->currency->dropdown_options();
            $data['measurement_unit_options'] = measurement_unit_dropdown_options();
        }

        foreach ($results as $row) {
            $order = new Purchase_order();
            $order->load($row->order_id);
            $data['reffering_object'] = $data['order'] = $order;
            $data['receivable'] = $order->receivable();
            $data['currency'] = $order->currency();
            $data['vat_options'] = $this->purchase_order->vat_enum_values('vat_inclusive');
            $data['attachments'] = $order->attachments();
            $purchase_order_value = $order->order_items_value();
            $received_value = $order->total_received_value();
            $status_label_class = 'label label-';
            if ($row->status == 'PENDING') {
                $status_label_class .= 'info';
            } else if ($row->status == 'RECEIVED') {
                if ($data['receivable'] > 0) {
                    $order->status = 'PARTIAL RECEIVED';
                }
                $status_label_class .= 'success';
            } else if ($row->status == 'CLOSED') {
                $status_label_class .= 'success';
            } else {
                $status_label_class .= 'danger';
            }
            $currency_symbol = $order->currency()->symbol;

            if ($display_on_dashboard) {
                $stakeholder = $row->stakeholder_name;
                $cost_center_name = $order->cost_center_name();

                $rows[] = [
                    anchor(base_url('procurements/preview_purchase_order/' . $order->{$order::DB_TABLE_PK}), $order->order_number(), 'target="_blank"'),
                    anchor(base_url('stakeholders/stakeholder_profile/' . $row->stakeholder_id), strlen($stakeholder) < 10 ? $stakeholder : '<span style="cursor: pointer;" title="' . $stakeholder . '">' . substr($stakeholder, 0, 9) . ' ...</span>'),
                    check_permission('Inventory') ? anchor(base_url('inventory/location_profile/' . $row->location_id), $row->location_name) : $row->location_name,
                    strlen($cost_center_name) < 10 ? $cost_center_name : '<span style="cursor: pointer;" title="' . $cost_center_name . '">' . substr($cost_center_name, 0, 9) . ' ...</span>',
                    $currency_symbol . ' ' . number_format($purchase_order_value, 2),
                    '<span class="' . $status_label_class . '">' . $order->status . '</span>',
                ];
            } else {

                $data['stakeholder'] = $order->Stakeholder();
                $data['location'] = $order->location();
                $data['currency'] = $order->currency();

                if ($orders_for == 'stakeholder') {
                    $rows[] = [
                        custom_standard_date($row->issue_date),
                        $order->order_number(),
                        check_permission('Inventory') ? anchor(base_url('inventory/location_profile/' . $row->location_id), $row->location_name) : $row->location_name,
                        $order->cost_center_name(),
                        $currency_symbol . ' ' . number_format($purchase_order_value, 2),
                        $currency_symbol . ' ' . number_format($received_value, 2),
                        $currency_symbol . ' ' . number_format(($purchase_order_value - $received_value), 2),
                        '<span class="' . $status_label_class . '">' . $order->status . '</span>',
                        $this->load->view('procurements/purchase_orders/list_actions', $data, true)
                    ];
                } else if ($orders_for == 'location') {
                    $rows[] = [
                        custom_standard_date($row->issue_date),
                        $order->order_number(),
                        anchor(base_url('stakeholders/stakeholder_profile/' . $row->stakeholder_id), wordwrap($row->stakeholder_name, 30, '<br/>')),
                        $order->cost_center_name(),
                        $currency_symbol . ' ' . number_format($purchase_order_value, 2),
                        $currency_symbol . ' ' . number_format($received_value, 2),
                        $currency_symbol . ' ' . number_format(($purchase_order_value - $received_value), 2),
                        '<span class="' . $status_label_class . '">' . $order->status . '</span>',
                        $this->load->view('procurements/purchase_orders/list_actions', $data, true)
                    ];
                } else {
                    $rows[] = [
                        custom_standard_date($row->issue_date),
                        $order->order_number(),
                        anchor(base_url('stakeholders/stakeholder_profile/' . $row->stakeholder_id), wordwrap($row->stakeholder_name, 30, '<br/>')),
                        check_permission('Inventory') ? anchor(base_url('inventory/location_profile/' . $row->location_id), $row->location_name) : $row->location_name,
                        $order->cost_center_name(),
                        $currency_symbol . ' ' . number_format($purchase_order_value, 2),
                        $currency_symbol . ' ' . number_format($received_value, 2),
                        $currency_symbol . ' ' . number_format(($purchase_order_value - $received_value), 2),
                        '<span class="' . $status_label_class . '">' . $order->status . '</span>',
                        $this->load->view('procurements/purchase_orders/list_actions', $data, true)
                    ];
                }
            }
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function handler()
    {
        $this->load->model('employee');
        $handler = new Employee();
        $handler->load($this->handler_id);
        return $handler;
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function purchase_order_nature()
    {
        $nature = ($this->project_purchase_order() && !$this->cost_center_purchase_order()) ? 'project' : 'cost_center';
        switch ($nature) {
            case 'project':
                $project = $this->project();
                $cost_center_id = $project->{$project::DB_TABLE_PK};
                break;
            case 'cost_center':
                $cost_center = $this->cost_center();
                $cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
                break;
        }
        return [$nature, $cost_center_id];
    }

    public function project_purchase_order()
    {
        $this->load->model('project_purchase_order');
        $junction = $this->project_purchase_order->get(1, 0, ['purchase_order_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junction) ? array_shift($junction) : false;
    }

    public function cost_center_purchase_order()
    {
        $this->load->model('cost_center_purchase_order');
        $junction = $this->cost_center_purchase_order->get(1, 0, ['purchase_order_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junction) ? array_shift($junction) : false;
    }

    public function project()
    {
        $junction = $this->project_purchase_order();
        return $junction ? $junction->project() : false;
    }

    public function cost_center()
    {
        $junction = $this->cost_center_purchase_order();
        return $junction ? $junction->cost_center() : false;
    }

    public function cost_center_name()
    {
        $project = $this->project();
        return $project ? $project->project_name : (($cost_center = $this->cost_center()) ? $cost_center->cost_center_name : false);
    }

    public function requisition_purchase_order()
    {
        $this->load->model('requisition_purchase_order');
        $junction = $this->requisition_purchase_order->get(1, 0, ['purchase_order_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junction) ? array_shift($junction) : false;
    }

    public function requisition()
    {
        $junction = $this->requisition_purchase_order();
        return $junction ? $junction->requisition() : false;
    }

    public function Stakeholder()
    {
        $this->load->model('stakeholder');
        $stakeholder = new Stakeholder();
        $stakeholder->load($this->stakeholder_id);
        return $stakeholder;
    }

    public function grns($from = null, $to = null)
    {
        $sql = 'SELECT goods_received_note_id FROM purchase_order_grns
                LEFT JOIN goods_received_notes g ON purchase_order_grns.goods_received_note_id = g.grn_id 
                WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK};

        if (!is_null($from)) {
            $sql .= ' AND  g.receive_date >= "' . $from . '" ';
        }

        if (!is_null($to)) {
            $sql .= ' AND  g.receive_date <= "' . $to . '" ';
        }

        $query = $this->db->query($sql);
        $results = $query->result();
        $this->load->model('goods_received_note');
        $grns = [];
        foreach ($results as $row) {
            $grn = new Goods_received_note();
            $grn->load($row->goods_received_note_id);
            $grns[] = $grn;
        }
        return $grns;
    }

    public function total_grn_extra_charges()
    {
        $sql = 'SELECT COALESCE(SUM(freight + insurance + other_charges),0) AS extra_charges FROM purchase_order_grns
            WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return $query->row()->extra_charges;
    }

    public function material_items()
    {
        $this->load->model('purchase_order_material_item');
        $where['order_id'] = $this->{$this::DB_TABLE_PK};
        return $this->purchase_order_material_item->get(0, 0, $where);
    }

    public function asset_items()
    {
        $this->load->model('purchase_order_asset_item');
        $where['order_id'] = $this->{$this::DB_TABLE_PK};
        return $this->purchase_order_asset_item->get(0, 0, $where);
    }

    public function service_items()
    {
        $this->load->model('purchase_order_service_item');
        $where['order_id'] = $this->{$this::DB_TABLE_PK};
        return $this->purchase_order_service_item->get(0, 0, $where);
    }

    public function delete_items()
    {
        $where = ['order_id' => $this->{$this::DB_TABLE_PK}];
        $this->db->where($where)->delete('purchase_order_material_items');
        $this->db->where($where)->delete('purchase_order_asset_items');
        $this->db->where($where)->delete('purchase_order_service_items');

        $where = ['purchase_order_id' => $this->{$this::DB_TABLE_PK}];

        $this->db->where($where)->where('purchase_order_id', $this->{$this::DB_TABLE_PK});
        $this->db->where($where)->delete('project_purchase_orders');
        $this->db->where($where)->delete('cost_center_purchase_orders');
    }

    public function receivable()
    {
        $receivable = false;
        $material_items = $this->material_items();
        foreach ($material_items as $item) {
            if ($item->unreceived_quantity() > 0) {
                $receivable = true;
                break;
            }
        }

        $asset_items = $this->asset_items();
        foreach ($asset_items as $item) {
            if (($item->quantity - $item->received_quantity()) > 0) {
                $receivable = true;
                break;
            }
        }

        $service_items = $this->service_items();
        foreach ($service_items as $item) {
            if (($item->quantity - $item->received_quantity()) > 0) {
                $receivable = true;
                break;
            }
        }

        return $receivable;
    }

    private function check_ordered_pre_order($requisition_id, $stakeholder_id, $currency_id = 1)
    {

        $sql = 'SELECT order_id FROM requisition_purchase_orders
                LEFT JOIN purchase_orders ON requisition_purchase_orders.purchase_order_id = purchase_orders.order_id
                WHERE requisition_id = ' . $requisition_id . ' AND purchase_orders.currency_id = ' . $currency_id . ' AND stakeholder_id = ' . $stakeholder_id . '
                LIMIT 1
                ';

        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    private function pre_order_approved_material_items($requisition_id, $stakeholder_id, $currency_id)
    {
        $this->load->model('requisition_approval_material_item');
        $sql = 'SELECT requisition_approval_material_items.id FROM requisition_approval_material_items
                LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                WHERE requisition_id = "' . $requisition_id . '"
                AND is_final = "1"
                AND vendor_id = "' . $stakeholder_id . '"
                AND currency_id = "' . $currency_id . '"
                ';

        $query = $this->db->query($sql);
        $results = $query->result();

        $sources_items = [];
        foreach ($results as $row) {
            $source = new Requisition_approval_material_item();
            $source->load($row->id);
            $sources_items[] = $source;
        }

        return $sources_items;
    }

    private function pre_order_approved_asset_items($requisition_id, $stakeholder_id, $currency_id)
    {
        $this->load->model('requisition_approval_asset_item');
        $sql = 'SELECT requisition_approval_asset_items.id FROM requisition_approval_asset_items
                LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                WHERE requisition_id = "' . $requisition_id . '"
                AND is_final = "1"
                AND vendor_id = "' . $stakeholder_id . '"
                AND currency_id = "' . $currency_id . '"
                ';

        $query = $this->db->query($sql);
        $results = $query->result();

        $sources_items = [];
        foreach ($results as $row) {
            $source = new Requisition_approval_asset_item();
            $source->load($row->id);
            $sources_items[] = $source;
        }

        return $sources_items;
    }

    private function pre_order_approved_service_items($requisition_id, $stakeholder_id)
    {
        $this->load->model('requisition_approval_service_item');
        $sql = 'SELECT requisition_approval_service_items.id FROM requisition_approval_service_items
                LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                WHERE requisition_id = "' . $requisition_id . '"
                AND is_final = "1"
                AND vendor_id = "' . $stakeholder_id . '"
                ';

        $query = $this->db->query($sql);
        $results = $query->result();

        $sources_items = [];
        foreach ($results as $row) {
            $source = new Requisition_approval_service_item();
            $source->load($row->id);
            $sources_items[] = $source;
        }

        return $sources_items;
    }

    public function procurement_members_options()
    {
        $options[''] = "&nbsp;";
        $sql = 'SELECT CONCAT(first_name," ",middle_name," ",last_name) AS employee_name, employees.employee_id FROM employees
                LEFT JOIN users ON employees.employee_id = users.employee_id
                LEFT JOIN users_permissions ON users.user_id = users_permissions.user_id
                LEFT JOIN permissions ON users_permissions.permission_id = permissions.permission_id
                WHERE permissions.name = "PROCUREMENTS"
                ';
        $query = $this->db->query($sql);
        $employees = $query->result();
        foreach ($employees as $employee) {
            $options[$employee->employee_id] = $employee->employee_name;
        }
        return $options;
    }

    public function pre_orders_list($limit, $start, $keyword, $order)
    {
        $order_string = dataTable_order_string(['approved_date', 'stakeholder_name', 'requisition_id', 'finalizer_name'], $order, 'approved_date');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $material_where_clause = ' WHERE  is_final = 1 AND requisitions.status = "APPROVED" AND requisition_approval_material_items.source_type = "vendor" AND approved_quantity > 0  AND requisition_approval_material_items.vendor_id IS NOT NULL AND (
                      SELECT COUNT(purchase_order_id) FROM requisition_purchase_orders
                      LEFT JOIN purchase_orders ON requisition_purchase_orders.purchase_order_id = purchase_orders.order_id
                      WHERE requisition_approvals.requisition_id = requisition_purchase_orders.requisition_id
                      AND purchase_orders.stakeholder_id = requisition_approval_material_items.vendor_id
                    ) = 0 ';

        $asset_where_clause = ' WHERE is_final = 1 AND requisitions.status = "APPROVED" AND  requisition_approval_asset_items.source_type = "vendor" AND approved_quantity > 0 AND requisition_approval_asset_items.vendor_id IS NOT NULL AND (
                      SELECT COUNT(purchase_order_id) FROM requisition_purchase_orders
                      LEFT JOIN purchase_orders ON requisition_purchase_orders.purchase_order_id = purchase_orders.order_id
                      WHERE requisition_approvals.requisition_id = requisition_purchase_orders.requisition_id
                      AND purchase_orders.stakeholder_id = requisition_approval_asset_items.vendor_id
                    ) = 0 ';

        $service_where_clause = ' WHERE is_final = 1 AND requisitions.status = "APPROVED" AND requisition_approval_service_items.source_type = "vendor" AND approved_quantity > 0 AND requisition_approval_service_items.vendor_id IS NOT NULL AND (
                      SELECT COUNT(purchase_order_id) FROM requisition_purchase_orders
                      LEFT JOIN purchase_orders ON requisition_purchase_orders.purchase_order_id = purchase_orders.order_id
                      WHERE requisition_approvals.requisition_id = requisition_purchase_orders.requisition_id
                      AND purchase_orders.stakeholder_id = requisition_approval_service_items.vendor_id
                    ) = 0 ';

        $sql = 'SELECT * FROM (
                    SELECT requisition_approval_service_items.requisition_approval_id,vendor_id,requisitions.currency_id,requisitions.requisition_id FROM requisition_approval_service_items
                    LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                    ' . $service_where_clause . '
                    
                    UNION
                    
                    SELECT requisition_approval_material_items.requisition_approval_id,vendor_id,requisitions.currency_id,requisitions.requisition_id FROM requisition_approval_material_items
                    LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                    ' . $material_where_clause . '
                    
                    UNION
                    
                    SELECT requisition_approval_asset_items.requisition_approval_id,vendor_id,requisitions.currency_id,requisitions.requisition_id FROM requisition_approval_asset_items
                    LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                    LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                    ' . $asset_where_clause . '
                    
                ) AS artificial_table GROUP BY requisition_id, vendor_id,currency_id
        ';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        $general_where_clause = '';

        if ($keyword != '') {
            $general_where_clause = ' WHERE requisition_id LIKE "%' . $keyword . '%" OR approved_date LIKE "%' . $keyword . '%"   OR stakeholder_name LIKE "%' . $keyword . '%"  ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM (
				SELECT requisition_approval_service_items.requisition_approval_id,stakeholders.stakeholder_id,approved_date,stakeholder_name,requisitions.currency_id,requisitions.requisition_id,
				CONCAT(first_name," ",COALESCE(middle_name,"")," ",last_name) AS finalizer_name
				FROM requisition_approval_service_items
				LEFT JOIN stakeholders ON requisition_approval_service_items.vendor_id = stakeholders.stakeholder_id
				LEFT JOIN requisition_approvals ON requisition_approval_service_items.requisition_approval_id = requisition_approvals.id
				LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
				LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
				' . $service_where_clause . '
				
				UNION ALL
	
				SELECT requisition_approval_material_items.requisition_approval_id,stakeholders.stakeholder_id,approved_date, stakeholder_name,requisitions.currency_id,requisitions.requisition_id,
				CONCAT(first_name," ",COALESCE(middle_name,"")," ",last_name) AS finalizer_name
				FROM requisition_approval_material_items
				LEFT JOIN stakeholders ON requisition_approval_material_items.vendor_id = stakeholders.stakeholder_id
				LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
				LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
				LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
				' . $material_where_clause . '
				
				UNION ALL
				
				SELECT requisition_approval_asset_items.requisition_approval_id,stakeholders.stakeholder_id,approved_date,stakeholder_name,requisitions.currency_id,requisitions.requisition_id,
				CONCAT(first_name," ",COALESCE(middle_name,"")," ",last_name) AS finalizer_name
				FROM requisition_approval_asset_items
				LEFT JOIN stakeholders ON requisition_approval_asset_items.vendor_id = stakeholders.stakeholder_id
				LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
				LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
				LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
				' . $asset_where_clause . '
			) AS artificial_table ' . $general_where_clause . ' GROUP BY requisition_id, stakeholder_id,currency_id
        ' . $order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;


        $this->load->model(['inventory_location', 'requisition', 'currency']);
        $data['location_options'] = $this->inventory_location->dropdown_options();
        $data['currency_options'] = $this->currency->dropdown_options();
        $data['procurement_members_options'] = $this->procurement_members_options();
        $data['stakeholder_options'] = stakeholder_dropdown_options();

        $rows = [];
        foreach ($results as $row) {
            $requisition = new Requisition();
            $requisition->load($row->requisition_id);
            $project_requisition = $requisition->project_requisition();
            if ($project_requisition) {
                $data['project'] = $project_requisition->project();
                $data['cost_center'] = false;
            } else {
                $data['cost_center'] = $requisition->cost_center_requisition()->cost_center();
                $data['project'] = false;
            }

            $data['requisition_approval'] = $requisition->last_approval();

            $data['requisition_id'] = $row->requisition_id;
            $data['stakeholder_id'] = $row->stakeholder_id;
            $data['stakeholder_name'] = $row->stakeholder_name;
            $data['currency_id'] = $row->currency_id;
            $data['ordered'] = $this->check_ordered_pre_order($row->requisition_id, $row->stakeholder_id, $row->currency_id);
            $data['approved_material_items'] = $this->pre_order_approved_material_items($row->requisition_id, $row->stakeholder_id, $row->currency_id);
            $data['approved_asset_items'] = $this->pre_order_approved_asset_items($row->requisition_id, $row->stakeholder_id, $row->currency_id);
            $data['approved_service_items'] = $this->pre_order_approved_service_items($row->requisition_id, $row->stakeholder_id, $row->currency_id);

            $rows[] = [
                custom_standard_date($row->approved_date),
                $row->stakeholder_name != null ? $row->stakeholder_name : 'Cash Purchase',
                $project_requisition ? $data['project']->project_name : $data['cost_center']->cost_center_name,
                add_leading_zeros($row->requisition_id),
                $row->finalizer_name,
                $this->load->view('procurements/purchase_orders/pre_orders_list_actions', $data, true)
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function ordered_material_value()
    {
        $sql = 'SELECT COALESCE(SUM((price*quantity)),0) AS order_value FROM purchase_order_material_items
                WHERE order_id = "' . $this->{$this::DB_TABLE_PK} . '"';

        $query = $this->db->query($sql);
        $results = $query->result();
        return array_shift($results)->order_value;
    }

    public function ordered_asset_value()
    {
        $sql = 'SELECT COALESCE(SUM((price*quantity)),0) AS order_value FROM purchase_order_asset_items
                WHERE order_id = "' . $this->{$this::DB_TABLE_PK} . '"';

        $query = $this->db->query($sql);
        $results = $query->result();
        return array_shift($results)->order_value;
    }

    public function ordered_service_value()
    {
        $sql = 'SELECT COALESCE(SUM(price*quantity),0) AS order_value FROM purchase_order_service_items
                WHERE order_id = "' . $this->{$this::DB_TABLE_PK} . '"';

        $query = $this->db->query($sql);
        $results = $query->result();
        return array_shift($results)->order_value;
    }

    public function order_items_value()
    {
        $order_items_value =  $this->ordered_material_value() + $this->ordered_asset_value() + $this->ordered_service_value();
        $vat_amount = 0.01 * $this->vat_percentage * $order_items_value;
        if ($this->vat_inclusive == "VAT COMPONENT") {
            return $order_items_value + $vat_amount;
        } else {
            return $order_items_value;
        }
    }

    public function cif()
    {
        $order_items_value = $this->order_items_value();
        $freight_inspection_charges = $this->freight + $this->inspection_and_other_charges;
        if ($this->vat_inclusive == "VAT COMPONENT") {
            $vat_amount = 0.01 * $this->vat_percentage * $freight_inspection_charges;
            return $order_items_value + $freight_inspection_charges + $vat_amount;
        } else {
            return $order_items_value + $freight_inspection_charges;
        }
    }

    public function invoiced_amount()
    {
        $sql1 = 'SELECT COALESCE(SUM(amount),0) AS invoiced_amount FROM invoices
                LEFT JOIN grn_invoices ON invoices.id = grn_invoices.invoice_id
                LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK};

        $sql2 = 'SELECT COALESCE(SUM(amount),0) AS invoiced_amount FROM invoices
                LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                WHERE currency_id = ' . $this->currency_id . ' AND stakeholder_id = ' . $this->stakeholder_id . ' AND  purchase_order_id = ' . $this->{$this::DB_TABLE_PK};

        $sql = 'SELECT ((' . $sql1 . ') + (' . $sql2 . ')) AS invoiced_amount ';

        $query = $this->db->query($sql);
        return $query->row()->invoiced_amount;
    }

    public function uninvoiced_amount()
    {
        return $this->cif() - $this->invoiced_amount();
    }

    public function material_received_value()
    {
        $sql = 'SELECT (
                    (
                        COALESCE(SUM((material_stocks.price*material_stocks.quantity)/(exchange_rate*purchase_order_grns.factor)),0)
                    ) * (
                        CASE 
                            WHEN purchase_orders.vat_inclusive = "VAT PRICED" OR purchase_orders.vat_inclusive IS NULL 
                            THEN 1
                            ELSE 1.18
                            END
                    )
                ) AS value_received FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_material_item_grn_items ON goods_received_note_material_stock_items.item_id = purchase_order_material_item_grn_items.goods_received_note_item_id
                LEFT JOIN purchase_order_material_items ON purchase_order_material_item_grn_items.purchase_order_material_item_id = purchase_order_material_items.item_id
                LEFT JOIN purchase_orders ON purchase_order_material_items.order_id = purchase_orders.order_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE purchase_order_material_items.order_id = ' . $this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $results = $query->result();
        return array_shift($results)->value_received;
    }

    public function asset_received_value()
    {
        $sql = 'SELECT (
                    (
                        COALESCE(SUM(book_value/(purchase_order_grns.exchange_rate*purchase_order_grns.factor)),0)
                    ) * (
                        CASE 
                            WHEN purchase_orders.vat_inclusive = "VAT PRICED" OR purchase_orders.vat_inclusive IS NULL 
                            THEN 1
                            ELSE 1.18
                            END
                    )
                ) AS received_value FROM asset_sub_location_histories
                LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                WHERE purchase_order_grns.purchase_order_id = ' . $this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->received_value;
    }

    public function service_received_value()
    {
        $sql = 'SELECT (
                    (
                        COALESCE(SUM((grn_received_services.rate*grn_received_services.received_quantity)/(exchange_rate*purchase_order_grns.factor)),0)
                    ) * (
                        CASE 
                            WHEN purchase_orders.vat_inclusive = "VAT PRICED" OR purchase_orders.vat_inclusive IS NULL 
                            THEN 1
                            ELSE 1.18
                            END
                    )
                ) AS value_received FROM grn_received_services
                LEFT JOIN goods_received_notes ON grn_received_services.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                WHERE purchase_order_grns.purchase_order_id = ' . $this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->value_received;
    }

    public function total_received_value()
    {
        return $this->material_received_value() + $this->asset_received_value() + $this->service_received_value();
    }

    public function total_grns_value()
    {
        return $this->total_received_value() + $this->total_grn_extra_charges();
    }

    public function material_received_value_with_duties()
    {
        $sql = 'SELECT COALESCE(SUM(price*quantity),0) AS value_received FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE purchase_order_grns.purchase_order_id = ' . $this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $results = $query->result();
        return array_shift($results)->value_received;
    }

    public function total_order_value()
    {
        return $this->order_items_value() + $this->freight + $this->inspection_and_other_charges;
    }

    public function total_order_in_base_currency()
    {
        if ($this->currency_id == 1) {
            return $this->order_items_value();
        } else {
            $currency = $this->currency();
            return $this->order_items_value() * $currency->rate_to_native($this->issue_date);
        }
    }

    public function delete_received_orders($order_ids)
    {
        $sql = '
                DELETE FROM goods_received_note_material_stock_items WHERE grn_id IN (
                SELECT goods_received_note_id FROM purchase_order_grns
                    WHERE purchase_order_id IN (14,16,18,20,25,28,30,3,32)
              );
              
              DELETE FROM material_stocks WHERE stock_id IN (
                SELECT stock_id FROM goods_received_note_material_stock_items
                WHERE grn_id IN (
                    SELECT goods_received_note_id FROM purchase_order_grns
                    WHERE purchase_order_id IN (14,16,18,20,25,28,30,3,32)
                )
              );
              
              DELETE FROM goods_received_notes WHERE grn_id IN (
                SELECT goods_received_note_id FROM purchase_order_grns
                    WHERE purchase_order_id IN (14,16,18,20,25,28,30,3,32)
              );
              
              
              DELETE FROM purchase_orders WHERE order_id IN (14,16,18,20,25,28,30,3,32);
              
              DELETE FROM ordered_pre_orders WHERE purchase_order_id IN (14,16,18,20,25,28,30,3,32);
              
              DELETE FROM requisition_purchase_orders WHERE purchase_order_id IN (14,16,18,20,25,28,30,3,32);
              
              
              ';
    }

    public function grn_options()
    {
        $grns = $this->grns();
        $options[''] = '&nbsp;';
        foreach ($grns as $grn) {
            $options[$grn->{$grn::DB_TABLE_PK}] = $grn->grn_number();
        }
        return $options;
    }

    public function grn_invoices()
    {
        $sql = 'SELECT invoice_id FROM grn_invoices
                LEFT JOIN invoices ON grn_invoices.invoice_id = invoices.id
                LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . ' ORDER BY invoice_date';

        $query = $this->db->query($sql);
        $rows = $query->result();
        $this->load->model('invoice');
        $invoices = [];
        foreach ($rows as $row) {
            $invoice = new Invoice();
            $invoice->load($row->invoice_id);
            $invoices[] = $invoice;
        }
        return $invoices;
    }

    public function general_invoices($suppliers_only = false)
    {
        if ($suppliers_only) {
            $sql = 'SELECT vendor_invoices.invoice_id FROM vendor_invoices
                    LEFT JOIN invoices ON vendor_invoices.invoice_id = invoices.id
                    LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                    LEFT JOIN purchase_orders ON purchase_order_invoices.purchase_order_id = purchase_orders.order_id
                    WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . ' AND stakeholder_invoices.stakeholder_id = ' . $this->stakeholder_id . '
                    AND purchase_orders.stakeholder_id = stakeholder_invoices.stakeholder_id';
        } else {
            $sql = 'SELECT invoice_id FROM purchase_order_invoices WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK};
        }
        $query = $this->db->query($sql);
        $rows = $query->result();
        $this->load->model('invoice');
        $invoices = [];
        foreach ($rows as $row) {
            $invoice = new Invoice();
            $invoice->load($row->invoice_id);
            $invoices[] = $invoice;
        }
        return $invoices;
    }

    public function other_charges()
    {
        $sql = 'SELECT 1 AS currency_id, COALESCE(SUM(purchase_order_payment_request_approval_invoice_items.approved_amount*exchange_rate),0) AS other_charges_cost FROM purchase_order_payment_request_approval_invoice_items
                LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                LEFT JOIN currencies ON purchase_order_payment_requests.currency_id = currencies.currency_id
                LEFT JOIN exchange_rate_updates AS latest_update ON currencies.currency_id = latest_update.currency_id AND latest_update.id = (
                 SELECT id FROM exchange_rate_updates AS general
                 WHERE general.currency_id = latest_update.currency_id
                 ORDER BY id ASC LIMIT 1
                )
                WHERE purchase_order_payment_request_invoice_items.invoice_id NOT IN (
                  SELECT purchase_order_invoices.invoice_id FROM stakeholder_invoices 
                  LEFT JOIN invoices ON stakeholder_invoices.invoice_id = invoices.id
                  LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                  WHERE stakeholder_id = ' . $this->stakeholder_id . ' AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                )
                 AND purchase_order_payment_request_invoice_items.invoice_id IN (
                  SELECT purchase_order_invoices.invoice_id FROM purchase_order_invoices
                  LEFT JOIN purchase_orders ON purchase_order_invoices.purchase_order_id = purchase_orders.order_id
                  WHERE stakeholder_id = ' . $this->stakeholder_id . ' AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                )                
                AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                AND is_final = 1
                AND status = "APPROVED"
                ';
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function cancellation()
    {
        $this->load->model('cancelled_purchase_order');
        $cancellations = $this->cancelled_purchase_order->get(1, 0, ['purchase_order_id' => $this->{$this::DB_TABLE_PK}]);
        return array_shift($cancellations);
    }

    public function invoice_options($currency_id = null)
    {
        $this->load->model(['invoice', 'purchase_order_payment_request']);
        $currency_id = is_null($currency_id) ? $this->currency_id : $currency_id;

        $sql = 'SELECT stakeholder_name, invoices.id AS invoice_id, invoices.reference FROM invoices
            LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
            LEFT JOIN stakeholders ON stakeholder_invoices.stakeholder_id = stakeholders.stakeholder_id
            LEFT JOIN grn_invoices ON invoices.id = grn_invoices.invoice_id
            LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
            LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
            WHERE currency_id = ' . $currency_id . ' AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
            
            UNION ALL
            
            SELECT stakeholder_name, invoices.id AS invoice_id, invoices.reference FROM invoices
            LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
            LEFT JOIN stakeholders ON stakeholder_invoices.stakeholder_id = stakeholders.stakeholder_id
            LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
            WHERE currency_id = ' . $currency_id . ' AND  purchase_order_id = ' . $this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        $results = $query->result();
        $options[''] = '&nbsp;';
        foreach ($results as $row) {
            $invoice = new Invoice();
            $invoice->load($row->invoice_id);
            $options[$row->stakeholder_name][$row->invoice_id] = $invoice->reference();
        }
        return $options;
    }

    public function requested_order_item_payments($currency_id = null)
    {
        $this->load->model(['invoice', 'purchase_order_payment_request']);
        $currency_id = is_null($currency_id) ? $this->currency_id : $currency_id;
        $sql = 'SELECT stakeholder_name, invoices.id AS invoice_id, invoices.reference, "invoice" AS type, symbol, requested_amount,  purchase_order_payment_request_invoice_items.purchase_order_payment_request_id AS payment_request_id
                FROM purchase_order_payment_request_invoice_items
                LEFT JOIN invoices ON purchase_order_payment_request_invoice_items.invoice_id = invoices.id
                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_invoice_items.purchase_order_payment_request_id = purchase_order_payment_requests.id
                LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                LEFT JOIN stakeholders ON purchase_orders.stakeholder_id = stakeholders.stakeholder_id
                LEFT JOIN currencies ON purchase_order_payment_requests.currency_id = currencies.currency_id
                WHERE purchase_order_payment_requests.currency_id = ' . $currency_id . ' 
                AND purchase_order_payment_requests.purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                
                UNION ALL
                
                SELECT claimed_by AS stakeholder_name, purchase_order_payment_request_cash_items.id AS invoice_id, reference, "cash" AS type, symbol, requested_amount, purchase_order_payment_request_cash_items.purchase_order_payment_request_id AS payment_request_id
                FROM purchase_order_payment_request_cash_items
                LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_cash_items.purchase_order_payment_request_id = purchase_order_payment_requests.id
                LEFT JOIN currencies ON purchase_order_payment_requests.currency_id = currencies.currency_id 
                WHERE purchase_order_payment_requests.currency_id = ' . $currency_id . ' 
                AND purchase_order_payment_requests.purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '';

        $query = $this->db->query($sql);
        $results = $query->result();
        $options = [];
        foreach ($results as $row) {
            $payment_request = new Purchase_order_payment_request();
            $payment_request->load($row->payment_request_id);
            $status = $payment_request->progress_status_label();
            $approval = $payment_request->purchase_order_payment_request_approval();
            if ($approval) {
                $amount_to_be_paid = ($approval->total_approved_amount() - $approval->total_paid_amount());
                $payment_voucher = $approval->payment_voucher();
                if ($payment_voucher) {
                    if ($payment_voucher && round($amount_to_be_paid, 2) <= 0) {
                        $status = '<span style="font-size: 12px" class="label label-success">Paid</span>';
                    } else if ($payment_voucher && round($amount_to_be_paid, 2) > 0) {
                        $status = '<span class="label" style="background-color: #00e765; font-size: 12px;">Partial Payment(s)</span>';
                    } else if (in_array($approval->{$approval::DB_TABLE_PK}, $approval->cancelled_approved_payment())) {
                        $status = '<span style="font-size: 12px" class="label label-danger">Revoked</span>';
                    }
                }
            }

            $options[] = [
                'request_date' => $payment_request->request_date,
                'reference' => $row->reference,
                'payment_request_no' => $payment_request->request_number(),
                'currency_symbol' => $row->symbol,
                'amount' => $row->requested_amount,
                'status' => $status,
                'payment_request_id' => $payment_request->{$payment_request::DB_TABLE_PK}
            ];
        }
        return $options;
    }

    public function dropdown_options($statuses = [])
    {
        $this->load->model('stakeholder');
        $vendors = $this->stakeholder->get();
        $options = ['' => '&nbsp;'];
        foreach ($vendors as $vendor) {
            $where = ' stakeholder_id = ' . $vendor->stakeholder_id;
            if (!empty($statuses)) {
                $selected_status = '';
                foreach ($statuses as $status) {
                    $selected_status .= '"' . $status . '",';
                }
                $where .= ' AND status IN(' . rtrim($selected_status, ',') . ')';
            }
            $orders = $this->purchase_order->get(0, 0, $where);
            foreach ($orders as $order) {
                $options[$vendor->stakeholder_name][$order->{$order::DB_TABLE_PK}] = 'P.O/' . add_leading_zeros($order->{$order::DB_TABLE_PK});
            }
        }
        return $options;
    }

    public function payment_requests()
    {
        $this->load->model('purchase_order_payment_request');
        $purchase_order_payment_requests = $this->purchase_order_payment_request->get(0, 0, ['purchase_order_id' => $this->{$this::DB_TABLE_PK}], 'purchase_order_id ASC');
        return $purchase_order_payment_requests;
    }

    public function unreceived_amount($vat_exclusive = false)
    {
        $unreceived_value = 0;
        $material_items = $this->material_items();
        foreach ($material_items as $material_item) {
            $unreceived_value += $material_item->unreceived_quantity() * $material_item->price;
        }
        $asset_items = $this->asset_items();
        foreach ($asset_items as $asset_item) {
            $unreceived_value += $asset_item->unreceived_quantity() * $asset_item->price;
        }
        $service_items = $this->service_items();
        foreach ($service_items as $service_item) {
            $unreceived_value += $service_item->unreceived_quantity() * $service_item->price;
        }
        if ($vat_exclusive) {
            return $unreceived_value;
        } else {
            if ($this->vat_inclusive == "VAT COMPONENT") {
                $vat_amount = 0.01 * $this->vat_percentage * $unreceived_value;
                return $unreceived_value + $vat_amount;
            } else {
                return $unreceived_value;
            }
        }
    }

    public function amount_paid($base_currency = false, $with_othercharges = false, $from = null, $to = null)
    {
        if ($base_currency) {
            $sql = 'SELECT (
                    (
                        SELECT COALESCE(SUM(journal_voucher_items.amount* main.exchange_rate),0) FROM journal_voucher_items
                        LEFT JOIN journal_vouchers ON journal_voucher_items.journal_voucher_id = journal_vouchers.journal_id
                        LEFT JOIN payment_request_approval_journal_vouchers ON journal_vouchers.journal_id = payment_request_approval_journal_vouchers.journal_voucher_id
                        LEFT JOIN purchase_order_payment_request_approvals ON payment_request_approval_journal_vouchers.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
                        LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                        LEFT JOIN exchange_rate_updates AS main ON journal_vouchers.currency_id = main.currency_id AND main.id = (
                          SELECT MAX(id) FROM exchange_rate_updates AS major
                          WHERE major.currency_id = main.currency_id
                        )
                        LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                        WHERE purchase_orders.stakeholder_id = ' . $this->stakeholder_id . ' 
                        AND journal_vouchers.currency_id = ' . $this->currency_id . ' 
                        AND purchase_order_payment_requests.purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '';
            if (!is_null($from)) {
                $sql .= ' AND journal_vouchers.transaction_date >= "' . $from . '" ';
            }
            if (!is_null($to)) {
                $sql .= ' AND journal_vouchers.taransaction_date <= "' . $to . '" ';
            }
            $sql .= '
                    ) + (
                        SELECT COALESCE(SUM(payment_voucher_items.amount * payment_vouchers.exchange_rate),0) FROM payment_voucher_items
                        LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                        LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                        LEFT JOIN invoices ON invoice_payment_vouchers.invoice_id = invoices.id
                        LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                        LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                        WHERE (
                          (
                            SELECT invoice_id FROM purchase_order_payment_request_invoice_items
                            LEFT JOIN purchase_order_payment_request_approval_invoice_items ON purchase_order_payment_request_invoice_items.id = purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id
                            LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                            WHERE payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id LIMIT 1
                          ) = invoice_payment_vouchers.invoice_id
                          OR 
                          (
                            SELECT invoice_id FROM purchase_order_payment_request_invoice_items
                            LEFT JOIN purchase_order_payment_request_approval_invoice_items ON purchase_order_payment_request_invoice_items.id = purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id
                            LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                            WHERE payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id LIMIT 1
                          ) IS NULL
                          
                        ) AND stakeholder_invoices.stakeholder_id = ' . $this->stakeholder_id . ' 
                        AND invoices.currency_id = ' . $this->currency_id . ' 
                        AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '';
            if (!is_null($from)) {
                $sql .= ' AND payment_vouchers.payment_date >= "' . $from . '" ';
            }
            if (!is_null($to)) {
                $sql .= ' AND payment_vouchers.payment_date <= "' . $to . '" ';
            }
            $sql .= '
                    ) + (
                        SELECT COALESCE(SUM(payment_voucher_items.amount * payment_vouchers.exchange_rate),0) AS amount_paid FROM payment_voucher_items
                        LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                        LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                        LEFT JOIN invoices ON invoice_payment_vouchers.invoice_id = invoices.id
                        LEFT JOIN grn_invoices ON invoices.id = grn_invoices.invoice_id
                        LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                        LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                        WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '';
            if (!is_null($from)) {
                $sql .= ' AND payment_vouchers.payment_date >= "' . $from . '" ';
            }
            if (!is_null($to)) {
                $sql .= ' AND payment_vouchers.payment_date <= "' . $to . '" ';
            }
            $sql .= '
                    )
                )  AS amount_paid';
        } else {
            $sql = 'SELECT (
                (
                    SELECT COALESCE(SUM(journal_voucher_items.amount),0) FROM journal_voucher_items
                    LEFT JOIN journal_vouchers ON journal_voucher_items.journal_voucher_id = journal_vouchers.journal_id
                    LEFT JOIN payment_request_approval_journal_vouchers ON journal_vouchers.journal_id = payment_request_approval_journal_vouchers.journal_voucher_id
                    LEFT JOIN purchase_order_payment_request_approvals ON payment_request_approval_journal_vouchers.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id 
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                    WHERE purchase_orders.stakeholder_id = ' . $this->stakeholder_id . ' 
                    AND journal_vouchers.currency_id = ' . $this->currency_id . ' 
                    AND purchase_order_payment_requests.purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '';
            if (!is_null($from)) {
                $sql .= ' AND journal_vouchers.transaction_date >= "' . $from . '" ';
            }
            if (!is_null($to)) {
                $sql .= ' AND journal_vouchers.transaction_date <= "' . $to . '" ';
            }
            $sql .= '
                ) + (
                    SELECT COALESCE(SUM(payment_voucher_items.amount),0) FROM payment_voucher_items
                    LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                    LEFT JOIN invoices ON invoice_payment_vouchers.invoice_id = invoices.id
                    LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                    LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                    WHERE (
                      (
                        SELECT invoice_id FROM purchase_order_payment_request_invoice_items
                        LEFT JOIN purchase_order_payment_request_approval_invoice_items ON purchase_order_payment_request_invoice_items.id = purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id
                        LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                        WHERE payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id LIMIT 1
                      ) = invoice_payment_vouchers.invoice_id
                      OR 
                      (
                        SELECT invoice_id FROM purchase_order_payment_request_invoice_items
                        LEFT JOIN purchase_order_payment_request_approval_invoice_items ON purchase_order_payment_request_invoice_items.id = purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id
                        LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                        WHERE payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id LIMIT 1
                      ) IS NULL
                      
                    ) AND stakeholder_invoices.stakeholder_id = ' . $this->stakeholder_id . ' 
                    AND invoices.currency_id = ' . $this->currency_id . ' 
                    AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '';
            if (!is_null($from)) {
                $sql .= ' AND payment_vouchers.payment_date >= "' . $from . '" ';
            }
            if (!is_null($to)) {
                $sql .= ' AND payment_vouchers.payment_date <= "' . $to . '" ';
            }
            $sql .= '
                ) + (

                    SELECT COALESCE(SUM(payment_voucher_items.amount),0) AS amount_paid FROM payment_voucher_items
                    LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                    LEFT JOIN invoices ON invoice_payment_vouchers.invoice_id = invoices.id
                    LEFT JOIN grn_invoices ON invoices.id = grn_invoices.invoice_id
                    LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                    LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                    WHERE purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '';
            if (!is_null($from)) {
                $sql .= ' AND payment_vouchers.payment_date >= "' . $from . '" ';
            }
            if (!is_null($to)) {
                $sql .= ' AND payment_vouchers.payment_date <= "' . $to . '" ';
            }
            $sql .= '
                )
            )  AS amount_paid';
        }

        if ($with_othercharges) {
            $query = $this->db->query($sql);
            $order_amount_paid = $query->row()->amount_paid;

            if ($base_currency) {
                $sql = 'SELECT COALESCE(SUM(amount/(
                          CASE WHEN payment_vouchers.currency_id = ' . $this->currency_id . '
                            THEN 1
                            ELSE (
                                SELECT major.exchange_rate FROM exchange_rate_updates AS major
                                WHERE major.currency_id = ' . $this->currency_id . ' ORDER BY major.id DESC LIMIT 1
                            )
                          END
                          )*exchange_rate),0) AS charges_amount_paid FROM purchase_order_payment_request_approval_invoice_items
                    LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                    LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                    LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_invoice_items.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    WHERE invoice_id NOT IN (
                      SELECT purchase_order_invoices.invoice_id FROM stakeholder_invoices 
                      LEFT JOIN invoices ON stakeholder_invoices.invoice_id = invoices.id
                      LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                      WHERE stakeholder_invoices.stakeholder_id = ' . $this->stakeholder_id . ' 
                      AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                    )
                    AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                    AND status = "APPROVED"';
                if (!is_null($from)) {
                    $sql .= ' AND payment_vouchers.payment_date >= "' . $from . '" ';
                }
                if (!is_null($to)) {
                    $sql .= ' AND payment_vouchers.payment_date <= "' . $to . '" ';
                }
            } else {
                $sql = 'SELECT COALESCE(SUM(amount/(
                          CASE WHEN payment_vouchers.currency_id = ' . $this->currency_id . '
                            THEN 1
                            ELSE (
                                SELECT major.exchange_rate FROM exchange_rate_updates AS major
                                WHERE major.currency_id = ' . $this->currency_id . ' ORDER BY major.id DESC LIMIT 1
                            )
                          END
                          )),0) AS charges_amount_paid FROM purchase_order_payment_request_approval_invoice_items
                    LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                    LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                    LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                    LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                    LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_invoice_items.purchase_order_payment_request_id = purchase_order_payment_requests.id
                    WHERE invoice_id NOT IN (
                      SELECT purchase_order_invoices.invoice_id FROM stakeholder_invoices 
                      LEFT JOIN invoices ON stakeholder_invoices.invoice_id = invoices.id
                      LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                      WHERE stakeholder_invoices.stakeholder_id = ' . $this->stakeholder_id . ' 
                      AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                    )
                    AND purchase_order_id = ' . $this->{$this::DB_TABLE_PK} . '
                    AND status = "APPROVED"';
                if (!is_null($from)) {
                    $sql .= ' AND payment_vouchers.payment_date >= "' . $from . '" ';
                }
                if (!is_null($to)) {
                    $sql .= ' AND payment_vouchers.payment_date <= "' . $to . '" ';
                }
            }

            $query = $this->db->query($sql);
            $charges_amount_paid = $query->row()->charges_amount_paid;
            return $order_amount_paid + $charges_amount_paid;
        } else {

            $query = $this->db->query($sql);
            return $query->row()->amount_paid;
        }
    }

    public function amount_due()
    {
        return $this->cif() - $this->unreceived_amount() - $this->amount_paid();
    }

    public function vat_enum_values($field = false)
    {
        $options['NULL'] = 'NONE';
        if ($field == false) {
            $options[0] = 'No';
            $options[1] = 'Yes';
        } else {
            $type = $this->db->query("SHOW COLUMNS FROM purchase_orders WHERE Field = '" . $field . "'")->row(0)->Type;
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $enum = explode("','", $matches[1]);
            $count = 0;
            foreach ($enum as $item) {
                $options[$item] = $item == 'VAT PRICED' ? $item : 'CALCULATE VAT';
            }
        }
        return $options;
    }

    public function purchase_orders_on_dashboard()
    {
        $sql = 'SELECT (
                        SELECT COUNT(order_id) FROM purchase_orders WHERE status = "PENDING"
                        ) AS "PENDING", 
                        (
                        SELECT COUNT(order_id) FROM purchase_orders WHERE status = "RECEIVED"
                        ) AS "RECEIVED",
                        (
                        SELECT COUNT(order_id) FROM purchase_orders WHERE status = "CLOSED"
                        ) AS "CLOSED",
                        (
                        SELECT COUNT(order_id) FROM purchase_orders WHERE status = "CANCELLED"
                        ) AS "CANCELLED",
                        (
                        SELECT COUNT(order_id) FROM purchase_orders
                        ) AS "ALL_OF_THEM"
                        
                        ';

        $query = $this->db->query($sql);
        $pending_purchase_orders = $query->row()->PENDING;
        $received_purchase_orders = $query->row()->RECEIVED;
        $closed_purchase_orders = $query->row()->CLOSED;
        $cancelled_purchase_orders = $query->row()->CANCELLED;
        $all_purchase_orders = $query->row()->ALL_OF_THEM > 0 ? $query->row()->ALL_OF_THEM : 0.0000000000000000000000000000001;

        $data['pending_purchase_orders'] = number_format($pending_purchase_orders);
        $data['pending_purchase_orders_percent'] = round(($pending_purchase_orders / $all_purchase_orders) * 100);

        $data['received_purchase_orders'] = number_format($received_purchase_orders);
        $data['received_purchase_orders_percent'] = round(($received_purchase_orders / $all_purchase_orders) * 100);

        $data['closed_purchase_orders'] = number_format($closed_purchase_orders);
        $data['closed_purchase_orders_percent'] = round(($closed_purchase_orders / $all_purchase_orders) * 100);

        $data['cancelled_purchase_orders'] = number_format($cancelled_purchase_orders);
        $data['cancelled_purchase_orders_percent'] = round(($cancelled_purchase_orders / $all_purchase_orders) * 100);

        $data['all_purchase_orders'] = number_format($all_purchase_orders);

        return $data;
    }

    public function generate_order_particulars($order_id, $feedback_type = 'echo')
    {
        $order = new self();
        $order->load($order_id);
        $ret_val['item_particulars'] = $this->load->view('finance/invoices/purchase_order_particulars', ['order' => $order], true);
        $ret_val['item_amount'] = $order->cif();
        $ret_val['item_object'] = $order;
        $ret_val['item_id'] = 'Purchase_' . $order_id . '_order';
        $ret_val['item_options'] = stringfy_dropdown_options(['Purchase_' . $order_id . '_order' => $order->order_number()]);
        if ($feedback_type != 'echo') {
            return json_encode($ret_val);
        } else {
            echo json_encode($ret_val);
        }
    }

    public function attachments()
    {
        $this->load->model('procurement_attachment');
        $where = ' (reffering_id =' . $this->{$this::DB_TABLE_PK} . ' AND  reffering_to="ORDER")';
        $junctions = $this->procurement_attachment->get(0, 0, $where);
        $attachments = [];
        foreach ($junctions as $junction) {
            $attachments[] = $junction->attachment();
        }
        return $attachments;
    }
}
