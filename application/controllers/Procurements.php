<?php

class Procurements extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_login();
    }

    public function pre_orders()
    {
        $limit = $this->input->post('length');
        if ($limit != '') {
            $this->load->model('purchase_order');
            $posted_params = dataTable_post_params();
            echo $this->purchase_order->pre_orders_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $data['title'] = 'To Be Ordered';
            $this->load->view('procurements/purchase_orders/pre_orders', $data);
        }
    }


    
    public function purchase_orders($orders_for = null, $holder_id = null)
    {
        $limit = $this->input->post('length');
        $this->load->model(['purchase_order']);
        if ($limit != '') {
            $params = dataTable_post_params();
            echo $this->purchase_order->purchase_orders_list($params['limit'], $params['start'], $params['keyword'], $params['order'], $orders_for, $holder_id);
        } else {
            check_permission('Procurements', true);
            $this->load->model(['stakeholder', 'currency', 'cost_center']);
            $data['projects_options'] = projects_dropdown_options();
            $data['cost_center_options'] = $this->cost_center->dropdown_options();
            $data['stakeholders_options'] = $this->stakeholder->dropdown_options();
            $data['procurement_members_options'] = $this->purchase_order->procurement_members_options();
            $data['vat_options'] = $this->purchase_order->vat_enum_values('vat_inclusive');
            $data['currency_options'] = $this->currency->dropdown_options();
            $data['measurement_unit_options'] = measurement_unit_dropdown_options();
            $data['locations_options'] = locations_options();
            $data['title'] = 'Purchase Orders';
            $this->load->view('procurements/purchase_orders/index', $data);
        }
    }


    public function save_purchase_order()
    {
        if (check_permission('Procurements')) {
            $this->load->model('purchase_order');
            $order = new Purchase_order();
            $edit = $order->load($this->input->post('order_id'));
            $order->employee_id = $this->session->userdata('employee_id');
            $order->handler_id = $this->input->post('handler_id');
            $order->stakeholder_id = $this->input->post('vendor_id');
            $requisition_id = $this->input->post('requisition_id');
            $project_id = $this->input->post('project_id');
            $order->location_id = $this->input->post('location_id');
            $order->currency_id = $this->input->post('currency_id');
            $order->issue_date = $this->input->post('issue_date');
            $order->delivery_date = $this->input->post('delivery_date');
            $order->reference = $this->input->post('reference');
            $order->vat_inclusive =  $this->input->post('vat_inclusive') == 'NULL' ? Null : $this->input->post('vat_inclusive');
            $order->vat_percentage = $this->input->post('vat_percentage');
            $order->freight = $this->input->post('freight');
            $order->inspection_and_other_charges = $this->input->post('inspection_and_other_charges');
            $order->comments = $this->input->post('comments');
            $order->status = 'PENDING';
            if ($order->save()) {


                $order->delete_items();

                if ($requisition_id != null) {
                    $this->load->model('requisition_purchase_order');
                    $requisition_purchase_order = new Requisition_purchase_order();
                    $requisition_purchase_order->purchase_order_id = $order->{$order::DB_TABLE_PK};
                    $requisition_purchase_order->requisition_id = $requisition_id;
                    $requisition_purchase_order->save();
                }

                if ($project_id != null) {
                    $this->load->model('project_purchase_order');
                    $project_purchase_order = new Project_purchase_order();
                    $project_purchase_order->project_id = $project_id;
                    $project_purchase_order->purchase_order_id = $order->{$order::DB_TABLE_PK};
                    $project_purchase_order->save();
                } else {
                    $cost_center_id = $this->input->post('cost_center_id');
                    $cost_center_id = $cost_center_id != '' ? $cost_center_id : null;
                    if (!is_null($cost_center_id)) {
                        $this->load->model('cost_center_purchase_order');
                        $cost_center_purchase_order = new Cost_center_purchase_order();
                        $cost_center_purchase_order->cost_center_id = $cost_center_id;
                        $cost_center_purchase_order->purchase_order_id = $order->{$order::DB_TABLE_PK};
                        $cost_center_purchase_order->save();
                    }
                }

                $this->load->model(['purchase_order_material_item', 'purchase_order_service_item', 'purchase_order_asset_item']);

                $unit_ids = $this->input->post('unit_ids');
                $item_ids = $this->input->post('item_ids');
                if (!empty($item_ids)) {

                    foreach ($item_ids as $index => $item_id) {
                        $item_type = $this->input->post('item_types')[$index];
                        if ($item_type == 'material') {
                            $item = new Purchase_order_material_item();
                            $item->material_item_id = $item_id;
                        } else if ($item_type == 'asset') {
                            $item = new Purchase_order_asset_item();
                            $item->asset_item_id = $item_id;
                        } else {
                            $item = new Purchase_order_service_item();
                            $item->description = $item_id;
                            $item->measurement_unit_id = $unit_ids[$index];
                        }
                        $item->order_id = $order->{$order::DB_TABLE_PK};
                        $item->quantity = $this->input->post('quantities')[$index];
                        $item->price = $this->input->post('prices')[$index];
                        $item->remarks = $this->input->post('remarks')[$index];
                        $item->save();
                    }
                }

                if($edit){
                    $action = 'Purchase Order Update';
                    $description = $order->order_number().' was updated';
                } else {
                    $action = 'Purchase Order Creation';
                    $description = $order->order_number().' was created';
                }
                system_log($action,$description, !is_null($project_id) ? $project_id : null);

            }
        }
    }

    public function delete_purchase_order()
    {
        $this->load->model('purchase_order');
        $order = new Purchase_order();
        if ($order->load($this->input->post('order_id'))) {
            $project = $order->project();
            system_log('Purchase Delete',$order->order_number().' was deleted', $project ? $project->{$project::DB_TABLE_PK} : null);
            $order->delete();
        }
    }

    public function location_purchase_orders($location_id = 0)
    {
        $limit = $this->input->post('length');
        $this->load->model(['purchase_order']);
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'issue_date';
                break;
            case 1;
                $order_column = 'order_id';
                break;
            case 2;
                $order_column = 'vendor_name';
                break;
            case 4;
                $order_column = 'status';
                break;
            default:
                $order_column = 'issue_date';
        }

        $order_string = $order_column . ' ' . $order_dir;

        $sql = 'SELECT issue_date,purchase_orders.order_id,vendors.vendor_name, purchase_orders.vendor_id,
                purchase_orders.location_id, status
                FROM purchase_orders
                LEFT JOIN vendors ON purchase_orders.vendor_id = vendors.vendor_id
                WHERE location_id = "' . $location_id . '"
            ';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $sql .= ' AND (vendor_name LIKE "%' . $keyword . '%" OR issue_date LIKE "%' . $keyword . '%"  OR status LIKE "%' . $keyword . '%"  OR order_id = "' . $keyword . '") ';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $query = $this->db->query($sql);

        $results = $query->result();
        $rows = [];
        if (!empty($results)) {
            $data['material_options'] = material_item_dropdown_options();
            $this->load->model(['inventory_location', 'vendor']);
            $location = new Inventory_location();
            $location->load($location_id);
            $data['location'] = $location;
            $data['sub_location_options'] = $location->sub_location_options();

            $data['vendors_options'] = $this->vendor->vendor_options();
            $data['projects_options'] = projects_dropdown_options();
            $data['procurement_members_options'] = $this->purchase_order->procurement_members_options();
        }
        foreach ($results as $row) {
            $order = new Purchase_order();
            $order->load($row->order_id);
            $data['order'] = $order;
            $status_label_class = 'label label-';

            $data['receivable'] = $order->receivable();

            if ($row->status == 'PENDING') {
                $status_label_class .= 'info';
            } else if ($row->status == 'RECEIVED') {
                if ($data['receivable'] > 0) {
                    $order->status = 'PARTIAL RECEIVED';
                }

                $status_label_class .= 'success';
            } else {
                $status_label_class .= 'danger';
            }
            $rows[] = [
                custom_standard_date($row->issue_date),
                $order->order_number(),
                check_permission('Procurements') ? anchor(base_url('procurements/vendor_profile/' . $row->vendor_id), $row->vendor_name) : $row->vendor_name,
                '<span class="' . $status_label_class . '">' . $order->status . '</span>',
                $this->load->view('procurements/purchase_orders/list_actions', $data, true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function preview_purchase_order($order_id = 0, $print = false)
    {
        $this->load->model('purchase_order');
        $order = new Purchase_order();
        if ($order->load($order_id)) {
            $data['order'] = $order;
            $data['vendor'] = $order->stakeholder();
            $html = $this->load->view('procurements/purchase_orders/purchase_order_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            if ($order->status == "CANCELLED") {
                $pdf->SetWatermarkText("CANCELLED");
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.3;
                $pdf->SetDisplayMode('fullpage');
            }

            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . "-" . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div>
                            <strong>P.O Handler: </strong> <span>' . $order->handler()->full_name() . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output($order->order_number() . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function view_purchase_order_summary($order_id = 0)
    {
        $this->load->model('purchase_order');
        $order = new Purchase_order();
        if ($order->load($order_id)) {
            $returned_invoices = [];
            if ($order->general_invoices()) {
                $returned_invoices = $order->general_invoices();
            }

            if ($order->grn_invoices()) {
                $returned_invoices = $order->grn_invoices();
            }

            $data['purchace_order_invoices'] =  multdimational_array_sort($returned_invoices, 'invoice_date', SORT_DESC);
            $data['order'] = $order;
            $data['vendor'] = $order->stakeholder();
            $data['grns'] = $order->grns();
            $html = $this->load->view('procurements/purchase_orders/purchase_order_summary', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            //generate the PDF!
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
            if ($order->status == "CANCELLED") {
                $pdf->SetWatermarkText("CANCELLED");
                $pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.3;
                $pdf->SetDisplayMode('fullpage');
            }
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Order Summary ' . $order->order_number() . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function preview_goods_received_report($order_id = 0, $unreceived_only = false)
    {
        $this->load->model('purchase_order');
        $order = new Purchase_order();
        if ($order->load($order_id)) {
            $data['order'] = $order;
            $data['unreceived_only'] = $unreceived_only;
            $project_junction = $order->project_purchase_order();
            if ($project_junction) {
                $data['project'] = $project_junction->project();
            }
            $data['project_junction'] = $project_junction;
            $html = $this->load->view('procurements/purchase_orders/goods_received_report', $data, true);

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
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Goods Received Report - ' . add_leading_zeros($order->order_number()) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function receive_purchase_order()
    {
        $item_ids = $this->input->post('item_ids');
        $order_item_ids = $this->input->post('order_item_ids');
        $date_received = $this->input->post('receive_date');
        $sub_location_id = $this->input->post('receiving_sub_location_id');
        $item_types = $this->input->post('item_types');
        $rejected_quantities = $this->input->post('rejected_quantities');
        $quantities = $this->input->post('quantities');
        $prices = $this->input->post('prices');
        $btn_status = $this->input->post('status');

        $this->load->model([
            'material_stock',
            'goods_received_note',
            'goods_received_note_material_stock_item',
            'purchase_order',
            'purchase_order_grn'
        ]);

        $order = new Purchase_order();
        $order->load($this->input->post('order_id'));

        $grn = new Goods_received_note();
        $edit = $grn->load($this->input->post('grn_id'));
        $grn->receiver_id = $this->session->userdata('employee_id');
        $grn->comments = $this->input->post('comments');
        $grn->location_id = $order->location_id;
        $grn->receive_date = $date_received;
        $grn->save();


        $order->status = 'RECEIVED';
        $order->save();

        if ($edit) {
            $grn->delete_junctions();
        }

        $project_junction = $order->project_purchase_order();

        $order_grn = new Purchase_order_grn();
        $order_grn->purchase_order_id = $order->{$order::DB_TABLE_PK};
        $order_grn->goods_received_note_id = $grn->{$grn::DB_TABLE_PK};
        $order_grn->factor = $this->input->post('factor');
        $order_grn->freight = $this->input->post('freight');
        $order_grn->insurance = $this->input->post('insurance');
        $order_grn->other_charges = $this->input->post('other_charges');
        $order_grn->clearance_charges = $this->input->post('clearance_charges');
        $order_grn->clearance_vat = $this->input->post('clearance_vat');
        $order_grn->import_duty = $this->input->post('import_duty');
        $order_grn->wharfage = $this->input->post('wharfage');
        $order_grn->service_fee = $this->input->post('service_fee');
        $order_grn->clearance_currency_id = 1;
        $order_grn->vat = $this->input->post('vat');
        $order_grn->cpf = $this->input->post('cpf');
        $order_grn->rdl = $this->input->post('rdl');
        $order_grn->exchange_rate = $this->input->post('exchange_rate');
        $order_grn->save();

        $this->load->model([
            'purchase_order_material_item_grn_item',
            'asset', 'asset_sub_location_history',
            'grn_asset_sub_location_history', 'goods_received_note_asset_item_reject', 'grn_received_service'
        ]);
        foreach ($item_ids as $index => $item_id) {
            $quantities[$index] = !is_null($quantities[$index]) ? $quantities[$index] : 0;
            if (($quantities[$index] == 0 && $rejected_quantities[$index] > 0) || ($quantities[$index] > 0)) {
                if ($item_types[$index] == 'material') {
                    $stock = new Material_stock();
                    $stock->date_received = $date_received;
                    $stock->sub_location_id = $sub_location_id;
                    $stock->project_id = $project_junction ? $project_junction->project_id : null;
                    $stock->receiver_id = $this->session->userdata('employee_id');
                    $stock->item_id = $item_id;
                    $stock->quantity = $quantities[$index];
                    $quantities[$index] = $quantities[$index] != null ? $quantities[$index] : 0;
                    $stock->price = $prices[$index] * $order_grn->exchange_rate;
                    $stock->description = '';
                    if ($stock->save()) {
                        if ($quantities[$index] > 0) {
                            $stock->update_average_price();
                        }
                        $grn_item = new Goods_received_note_material_stock_item();
                        $grn_item->stock_id = $stock->{$stock::DB_TABLE_PK};
                        $grn_item->rejected_quantity = floatval($rejected_quantities[$index]);
                        $grn_item->remarks = $stock->description;
                        $grn_item->grn_id = $grn->{$grn::DB_TABLE_PK};
                        $grn_item->save();

                        $order_jrn_junction = new Purchase_order_material_item_grn_item();
                        $order_jrn_junction->goods_received_note_item_id = $grn_item->{$grn_item::DB_TABLE_PK};
                        $order_jrn_junction->purchase_order_material_item_id = $order_item_ids[$index];
                        $order_jrn_junction->save();
                    }
                } else if ($item_types[$index] == 'asset') {

                    $quantity = $quantities[$index];
                    if ($quantity > 0) {
                        $rejected_quantity = floatval($rejected_quantities[$index]);
                        if ($rejected_quantity > 0) {
                            $rejected_assets = new Goods_received_note_asset_item_reject();
                            $rejected_assets->rejected_quantity = $rejected_quantity;
                            $rejected_assets->grn_id = $grn->{$grn::DB_TABLE_PK};
                            $rejected_assets->purchase_order_asset_item_id = $order_item_ids[$index];
                            $rejected_assets->save();
                        }

                        for ($i = 0; $i < $quantity; $i++) {
                            $asset = new Asset();
                            $asset->asset_item_id = $item_id;
                            $asset->book_value = $prices[$index] * $order_grn->exchange_rate;
                            $asset->salvage_value = 0;
                            $asset->created_by = $this->session->userdata('employee_id');
                            $asset->description = '';
                            $asset->status = 'ACTIVE';
                            if ($asset->save()) {
                                $history = new Asset_sub_location_history();
                                $history->asset_id = $asset->{$asset::DB_TABLE_PK};
                                $history->book_value = $asset->book_value;
                                $history->sub_location_id = $sub_location_id;
                                $history->description = '';
                                $history->project_id = $project_junction ? $project_junction->project_id : null;
                                $history->received_date = $date_received;
                                $history->created_by = $this->session->userdata('employee_id');
                                if ($history->save()) {
                                    $grn_item = new Grn_asset_sub_location_history();
                                    $grn_item->grn_id = $grn->{$grn::DB_TABLE_PK};
                                    $grn_item->asset_sub_location_history_id = $history->{$history::DB_TABLE_PK};
                                    $grn_item->save();
                                }
                            }
                        }
                    }
                } else {

                    $rejected_quantity = floatval($rejected_quantities[$index]);
                    $received_service = new Grn_received_service();
                    $received_service->purchase_order_service_item_id = $order_item_ids[$index];
                    $received_service->grn_id = $grn->{$grn::DB_TABLE_PK};
                    $received_service->received_quantity = $quantities[$index];
                    $received_service->rejected_quantity = $rejected_quantity;
                    $received_service->sub_location_id = $sub_location_id;
                    $received_service->rate = $prices[$index] * $order_grn->exchange_rate;
                    $received_service->remarks = $grn->comments;
                    $received_service->created_by = $this->session->userdata('employee_id');
                    $received_service->save();
                }
            }
        }
        $ret_val['success'] = 'success';
        if ($btn_status == 'PREVIEW') {
            $ret_val['grn_id'] = $grn->grn_id;
        }
        echo json_encode($ret_val);
    }

    public function preview_grn($grn_id = 0)
    {
        $this->load->model('goods_received_note');
        $grn = new Goods_received_note();
        if ($grn->load($grn_id)) {
            $data['grn'] = $grn;
            $data['order_grn'] = $grn->purchase_order_grn();
            $data['imprest_voucher_grn'] = $grn->imprest_voucher_retirement_grn();
            $data['is_site_grn'] = $grn->is_site_grn();
            $data['transfer_grn'] = $grn->transfer_grn();
            $data['imprest_grn'] = $grn->imprest_grn();
            $data['unprocured_grn'] = $grn->unprocured_grn();

            $this->load->view('procurements/preview_goods_received_note', $data);
        } else {
            redirect(base_url());
        }
    }

    public function receive_unordered_items()
    {
        $item_ids = $this->input->post('item_ids');
        $date_received = $this->input->post('receive_date');
        $sub_location_id = $this->input->post('receiving_sub_location_id');
        $item_types = $this->input->post('item_types');
        $rejected_quantities = $this->input->post('rejected_quantities');
        $quantities = $this->input->post('quantities');
        $prices = $this->input->post('prices');
        $location_id = $this->input->post('location_id');
        $exchange_rate = $this->input->post('exchange_rate');
        $exchange_rate = $exchange_rate != '' ? $exchange_rate : 1;
        $project_id = $this->input->post('project_id');
        $project_id = $project_id != '' ? $project_id : null;

        $this->load->model([
            'project',
            'material_stock',
            'goods_received_note',
            'goods_received_note_material_stock_item',
            'unprocured_delivery',
            'unprocured_delivery_grn'
        ]);
        if (!is_null($project_id)) {
            $project = new Project();
            $project->load($project_id);
        }

        $delivery = new Unprocured_delivery();
        $delivery->location_id = $location_id;
        $delivery->client_id = $project->client_id;
        $delivery->delivery_date = $date_received;
        $delivery->delivery_for = $project_id;
        $delivery->currency_id = 1;
        $delivery->comments = $this->input->post('comments');
        $delivery->receiver_id = $this->session->userdata('employee_id');
        $delivery->save();

        $grn = new Goods_received_note();
        $grn->receiver_id = $this->session->userdata('employee_id');
        $grn->comments = $this->input->post('comments');
        $grn->location_id = $location_id;
        $grn->receive_date = $date_received;
        if ($grn->save()) {
            $delivery_grn = new Unprocured_delivery_grn();
            $delivery_grn->delivery_id = $delivery->{$delivery::DB_TABLE_PK};
            $delivery_grn->grn_id = $grn->{$grn::DB_TABLE_PK};
            $delivery_grn->save();
        }

        $this->load->model([
            'unprocured_delivery_material_item_grn_item',
            'asset', 'asset_sub_location_history',
            'grn_asset_sub_location_history', 'goods_received_note_asset_item_reject', 'unprocured_delivery_material_item',
            'unprocured_delivery_asset_item'
        ]);
        foreach ($item_ids as $index => $item_id) {
            $quantities[$index] = $quantities[$index] != null ? $quantities[$index] : 0;
            if (($quantities[$index] == 0 && $rejected_quantities[$index] > 0) || ($quantities[$index] > 0 && $rejected_quantities[$index] >= 0)) {
                if ($item_types[$index] == 'material') {
                    $delivery_material_item = new Unprocured_delivery_material_item();
                    $delivery_material_item->delivery_id = $delivery->{$delivery::DB_TABLE_PK};
                    $delivery_material_item->material_item_id = $item_id;
                    $delivery_material_item->quantity = $quantities[$index];
                    $delivery_material_item->price = $prices[$index];
                    $delivery_material_item->remarks = '';
                    if ($delivery_material_item->save()) {

                        $stock = new Material_stock();
                        $stock->date_received = $date_received;
                        $stock->sub_location_id = $sub_location_id;
                        $stock->project_id = $project_id;
                        $stock->receiver_id = $this->session->userdata('employee_id');
                        $stock->item_id = $item_id;
                        $stock->quantity = $quantities[$index];
                        $quantities[$index] = $quantities[$index] != null ? $quantities[$index] : 0;
                        $stock->price = $prices[$index] * $exchange_rate;
                        $stock->description = '';
                        if ($stock->save()) {
                            if ($prices[$index] > 0) {
                                $stock->update_average_price();
                            }
                            $grn_item = new Goods_received_note_material_stock_item();
                            $grn_item->stock_id = $stock->{$stock::DB_TABLE_PK};
                            $grn_item->rejected_quantity = floatval($rejected_quantities[$index]);
                            $grn_item->remarks = $stock->description;
                            $grn_item->grn_id = $grn->{$grn::DB_TABLE_PK};
                            $grn_item->save();

                            $unprocured_item_grn_item = new Unprocured_delivery_material_item_grn_item();
                            $unprocured_item_grn_item->unprocured_delivery_material_item_id = $delivery_material_item->{$delivery_material_item::DB_TABLE_PK};
                            $unprocured_item_grn_item->grn_item_id = $grn_item->{$grn_item::DB_TABLE_PK};
                            $unprocured_item_grn_item->save();
                        }
                    }
                } else if ($item_types[$index] == 'asset') {

                    $quantity = $quantities[$index];
                    if ($quantity > 0) {
                        $delivery_asset_item = new Unprocured_delivery_asset_item();
                        $delivery_asset_item->delivery_id = $delivery->{$delivery::DB_TABLE_PK};
                        $delivery_asset_item->asset_item_id = $item_id;
                        $delivery_asset_item->quantity = $quantities[$index];
                        $delivery_asset_item->price = $prices[$index];
                        $delivery_asset_item->remarks = '';
                        $delivery_asset_item->save();

                        $rejected_quantity = floatval($rejected_quantities[$index]);
                        if ($rejected_quantity > 0) {
                            $rejected_assets = new Goods_received_note_asset_item_reject();
                            $rejected_assets->rejected_quantity = $rejected_quantity;
                            $rejected_assets->grn_id = $grn->{$grn::DB_TABLE_PK};
                            $rejected_assets->delivery_asset_item_id = $delivery_asset_item->{$delivery_asset_item::DB_TABLE_PK};
                            $rejected_assets->save();
                        }

                        for ($i = 0; $i < $quantity; $i++) {
                            $asset = new Asset();
                            $asset->asset_item_id = $item_id;
                            $asset->book_value = $prices[$index] * $exchange_rate;
                            $asset->salvage_value = 0;
                            $asset->created_by = $this->session->userdata('employee_id');
                            $asset->description = '';
                            $asset->status = 'ACTIVE';
                            if ($asset->save()) {
                                $history = new Asset_sub_location_history();
                                $history->asset_id = $asset->{$asset::DB_TABLE_PK};
                                $history->book_value = $asset->book_value;
                                $history->sub_location_id = $sub_location_id;
                                $history->description = '';
                                $history->project_id = $project_id;
                                $history->received_date = $date_received;
                                $history->created_by = $this->session->userdata('employee_id');
                                if ($history->save()) {
                                    $grn_item = new Grn_asset_sub_location_history();
                                    $grn_item->grn_id = $grn->{$grn::DB_TABLE_PK};
                                    $grn_item->asset_sub_location_history_id = $history->{$history::DB_TABLE_PK};
                                    $grn_item->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function purchase_orders_grns()
    {
        $limit = $this->input->post('length');
        if ($limit != '') {
            $this->load->model('goods_received_note');
            $params = dataTable_post_params();
            echo $this->goods_received_note->purchase_order_grns($params['limit'], $params['start'], $params['keyword'], $params['order']);
        } else {
            $this->load->view('procurements/purchase_order_grns', ['title', 'Purchase Order GRNs']);
        }
    }

    public function close_requisition()
    {
        $this->load->model('requisition');
        $requisition = new Requisition();
        if ($requisition->load($this->input->post('requisition_id'))) {
            $requisition->status = 'CLOSED';
            $requisition->save();
        }
    }

    public function cancel_purchase_order()
    {
        $this->load->model('cancelled_purchase_order');
        $cancellation = new Cancelled_purchase_order();
        $cancellation->purchase_order_id = $this->input->post('order_id');
        $cancellation->created_by = $this->session->userdata('employee_id');
        $cancellation->cancellation_date = $this->input->post('cancellation_date');
        $cancellation->reason = $this->input->post('reason');
        if ($cancellation->save()) {
            $purchase_order = $cancellation->purchase_order();
            $purchase_order->status = 'CANCELLED';
            $purchase_order->save();
        }
    }

    public function close_purchase_order()
    {
        $this->load->model('closed_purchase_order');
        $closure = new Closed_purchase_order();
        $closure->purchase_order_id = $this->input->post('order_id');
        $closure->created_by = $this->session->userdata('employee_id');
        $closure->closing_date = $this->input->post('closing_date');
        $closure->closing_remarks = $this->input->post('remarks');
        if ($closure->save()) {
            $purchase_order = $closure->purchase_order();
            $purchase_order->status = 'CLOSED';
            $purchase_order->save();
        }
    }

    public function stakeholder_reports()
    {
        $this->load->model(['stakeholder', 'currency']);
        $stakeholder = new Stakeholder();
        $stakeholder->load($this->input->post('stakeholder_id'));
        $data['from'] = $from = $this->input->post('from');
        $to = $this->input->post('to');
        $report_type = $this->input->post('report_type');
        $report_category = $this->input->post('report_category');
        $data['report_category'] = $report_category != '' ? $report_category : null;
        $opening_balance_date = new DateTime($from);
        $opening_balance_date->modify(' - 1 day');
        $opening_balance_date = $opening_balance_date->format('Y-m-d');
        $currency_id = $this->input->post('currency_id');

        $currency = new Currency();
        $currency->load($currency_id);
        $data['currency'] = $currency;
        $data['print'] = $this->input->post('print');
        $data['last_balance'] = $stakeholder->balance($to);


        if ($report_type == 'orders_statement') {
            $this->load->model('purchase_order');
            $data['orders'] = $this->purchase_order->get(0, 0, [
                'stakeholder_id' => $stakeholder->{$stakeholder::DB_TABLE_PK},
                'issue_date >= ' =>  $from,
                'issue_date <=' => $to,
                'currency_id' => $currency_id
            ], ' issue_date ASC ');
            $view_path = 'stakeholders/reports/' . ($data['print'] ? 'orders_statement_sheet' : 'orders_statement_table');
        } else if ($report_type == 'statement') {
            $data['transactions'] = $stakeholder->statement_transactions($currency_id, $from, $to);
            $data['opening_balance'] = $stakeholder->balance($currency_id, $opening_balance_date);
            $view_path = 'stakeholders/reports/' . ($data['print'] ? 'statement_sheet' : 'statement_transactions_table');
        } else {
            $items = (!is_null($data['report_category']) && $data['report_category'] != 'in_bulk') ? $stakeholder->supplied_items($from, $to) : $stakeholder->supplied_items_in_bulk($from, $to);
            $data['items'] = $items;
            $view_path = 'stakeholders/reports/' . ($data['print'] ? 'supplied_items_report_sheet' : 'supplied_items_report_table');
        }


        if ($data['print']) {
            $data['stakeholder'] = $stakeholder;
            $data['from'] = $from;
            $data['to'] = $to;

            $html = $this->load->view($view_path, $data, true);
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            //generate the PDF!
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
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output($report_type . ' - ' . $stakeholder->stakeholder_name . '.pdf', 'I');
        } else {
            $this->load->view($view_path, $data);
        }
    }

    public function get_grn_cif()
    {
        $this->load->model(['goods_received_note', 'purchase_order']);
        $grn = new Goods_received_note();
        $grn->load($this->input->post('grn_id'));
        $order = new Purchase_order();
        $order->load($this->input->post('order_id'));

        $ret_val['order_cif'] = $order->cif();
        $ret_val['grn_maximum_amount'] = round($grn->uninvoiced_amount(), 2);
        echo json_encode($ret_val);
    }

    public function purchase_order_status()
    {
        if ($this->input->post('triggered') == 'true') {
            $this->load->model('purchase_order');
            $where = '';
            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $vendor_id = $this->input->post('vendor_id');
            $json['sub_title'] = '';
            $vendor_selected = !is_null($vendor_id) && $vendor_id != '';

            if ($vendor_selected) {
                $where['vendor_id ='] = $vendor_id;
            }

            if (!is_null($from) && $from != '') {
                $where['issue_date >='] = $from;
                $json['sub_title'] .= ' FROM ' . custom_standard_date($from);
            }

            if (!is_null($to) && $to != '') {
                $where['issue_date <='] = $to;
                $json['sub_title'] .= ' TO ' . custom_standard_date($to);
            }

            $purchase_orders = $this->purchase_order->get(0, 0, $where);
            $total_orders_value = 0;
            foreach ($purchase_orders as $order) {
                $order_value = $order->total_order_in_base_currency();
                $orders[] = [
                    '<a href="' . base_url('procurements/preview_purchase_order/' . $order->{$order::DB_TABLE_PK}) . '" target="_blank">' . $order->order_number() . '</a>',
                    $order_value
                ];
                $total_orders_value += $order_value;
            }

            if ($vendor_selected) {
                $this->load->model('vendor');
                $vendor = new Vendor();
                $vendor->load($vendor_id);
                $json['sub_title'] = $vendor->vendor_name . ' : ' . $json['sub_title'];
                $json['drilldown'][] = [
                    'name' => 'Orders',
                    'id' => 'ordered_drilldown',
                    'data' => $orders
                ];
            } else {
                $this->load->model('vendor');
                $vendors = $this->vendor->vendors_with_orders($from, $to);
                $vendors_drilldown = [];
                foreach ($vendors as $vendor) {
                    $vendor_order_value = 0;
                    $orders = [];
                    foreach ($purchase_orders as $order) {
                        if ($order->vendor_id == $vendor->vendor_id) {
                            $vendor_order_value += $order_value = $order->total_order_in_base_currency();
                            $orders[] = [
                                '<a href="' . base_url('procurements/preview_purchase_order/' . $order->{$order::DB_TABLE_PK}) . '" target="_blank">' . $order->order_number() . '</a>',
                                $order_value
                            ];
                        }
                    }

                    $drilldown_id = 'vendor_' . $vendor->vendor_id;
                    $json['drilldown'][] = [
                        'name' => $vendor->vendor_name,
                        'id' => $drilldown_id,
                        'data' => $orders
                    ];

                    $vendors_drilldown[] = [
                        'name' => $vendor->vendor_name,
                        'y' => $vendor_order_value,
                        'drilldown' => $drilldown_id
                    ];
                }
                $json['drilldown'][] = [
                    'name' => 'Vendors',
                    'id' => 'ordered_drilldown',
                    'data' => $vendors_drilldown
                ];
            }

            $json['total_orders_value'] = $total_orders_value;

            echo json_encode($json);
        } else {
            $data['vendor_dropdown_options'] = vendor_dropdown_options();
            $this->load->view('procurements/reports/purchase_order_status', $data);
        }
    }

    public function save_grn_invoice()
    {
        $this->load->model(['invoice', 'grn_invoice', 'stakeholder_invoice', 'purchase_order']);
        $invoice = new Invoice();
        $invoice->invoice_date = $this->input->post('invoice_date');
        $invoice->currency_id = $this->input->post('currency_id');
        $invoice->amount = $this->input->post('invoice_amount');
        $invoice->description = $this->input->post('description');
        $invoice->reference = $this->input->post('reference');
        $invoice->created_by = $this->session->userdata('employee_id');
        $ret_val = [];
        if ($invoice->save()) {
            $grn_invoice = new Grn_invoice();
            $grn_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
            $grn_invoice->grn_id = $this->input->post('grn_id');
            $grn_invoice->save();

            $order = new Purchase_order();
            $order->load($this->input->post('order_id'));

            $stakeholder_invoice = new Vendor_invoice();
            $stakeholder_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
            $stakeholder_invoice->stakeholder_id = $order->stakeholder_id;
            $stakeholder_invoice->save();

            $ret_val['invoices_tbody'] = $this->load->view('procurements/purchase_orders/purchase_order_grn_invoices_tbody', [
                'invoices' => $order->grn_invoices(),
                'order' => $order
            ], true);
        }
        echo json_encode($ret_val);
    }

    public function save_order_invoice()
    {
        $this->load->model(['invoice', 'purchase_order_invoice', 'stakeholder_invoice', 'purchase_order']);
        $invoice = new Invoice();
        $invoice->invoice_date = $this->input->post('invoice_date');
        $invoice->currency_id = $this->input->post('currency_id');
        $invoice->amount = $this->input->post('invoice_amount');
        $invoice->description = $this->input->post('description');
        $invoice->reference = $this->input->post('reference');
        $invoice->created_by = $this->session->userdata('employee_id');
        $ret_val = [];
        if ($invoice->save()) {
            $order_invoice = new Purchase_order_invoice();
            $order_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
            $order_invoice->purchase_order_id = $this->input->post('order_id');
            $order_invoice->save();

            $order = new Purchase_order();
            $order->load($this->input->post('order_id'));

            $stakeholder_id = $this->input->post('stakeholder_id');
            if ($stakeholder_id != '') {
                $stakeholder_invoice = new Stakeholder_invoice();
                $stakeholder_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                $stakeholder_invoice->stakeholder_id = $stakeholder_id;
                $stakeholder_invoice->save();
            }

            $ret_val['invoices_tbody'] = $this->load->view('procurements/purchase_orders/purchase_order_general_invoices_tbody', [
                'invoices' => $order->general_invoices(),
                'order' => $order
            ], true);
        }
        echo json_encode($ret_val);
    }

    public function delete_grn_invoice()
    {
        $this->load->model(['invoice', 'purchase_order']);
        $invoice = new Invoice();
        if ($invoice->load($this->input->post('invoice_id'))) {
            $invoice->delete();
            $order = new Purchase_order();
            $order->load($this->input->post('order_id'));

            $invoices_tbody = $this->load->view('procurements/purchase_orders/purchase_order_grn_invoices_tbody', [
                'invoices' => $order->grn_invoices(),
                'order' => $order
            ], true);
            echo json_encode(['invoices_tbody' => $invoices_tbody]);
        }
    }

    public function delete_order_invoice()
    {
        $this->load->model(['invoice', 'purchase_order']);
        $invoice = new Invoice();
        if ($invoice->load($this->input->post('invoice_id'))) {
            $invoice->delete();
            $order = new Purchase_order();
            $order->load($this->input->post('order_id'));

            $invoices_tbody = $this->load->view('procurements/purchase_orders/purchase_order_general_invoices_tbody', [
                'invoices' => $order->general_invoices(),
                'order' => $order
            ], true);
            echo json_encode(['invoices_tbody' => $invoices_tbody]);
        }
    }

    public function order_payment_requests()
    {
        if (check_permission('Procurements', true)) {
            $limit = $this->input->post('length');
            if ($limit != '') {
                $this->load->model(['purchase_order_payment_request']);
                $posted_params = dataTable_post_params();
                echo $this->purchase_order_payment_request->order_payment_request_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
            } else {
                $this->load->model(['purchase_order', 'approval_chain_level','approval_module']);
                $data['title'] = 'Procurements | Order Payment Request';
                $approval_module = $this->approval_module->get(1, 0, ['id' => 3]);
                $data['first_approver_options'] = array_shift($approval_module)->forwarding_to_employee_options();
                $data['order_dropdown_options'] = $this->purchase_order->dropdown_options(['RECEIVED', 'PENDING']);
                $data['currency_dropdown_options'] = currency_dropdown_options();
                $this->load->view('procurements/order_payment_requests/index', $data);
            }
        } else {
            redirect(base_url());
        }
    }

    public function save_order_payment_request()
    {
        $this->load->model('purchase_order_payment_request');
        $payment_request = new Purchase_order_payment_request();
        $edit = $payment_request->load($this->input->post('purchase_order_payment_request_id'));
        $payment_request->purchase_order_id = $this->input->post('order_id');
        $payment_request->approval_module_id = 3;
        $payment_request->currency_id = $this->input->post('currency_id');
        $payment_request->request_date = $this->input->post('request_date');
        $payment_request->forward_to = $this->input->post('forward_to');
        $payment_request->forward_to = $payment_request->forward_to != '' ? $payment_request->forward_to : null;
        $payment_request->remarks = $this->input->post('remarks');
        $payment_request->requester_id = $this->session->userdata('employee_id');
        if ($payment_request->status != 'APPROVED') {
            $payment_request->status = 'PENDING';
        }

        if ($payment_request->status != 'APPROVED' && $payment_request->save()) {
            $this->load->model(['purchase_order_payment_request_invoice_item', 'purchase_order_payment_request_cash_item']);
            if ($edit) {
                $payment_request->delete_payment_request_items();
            }
            $item_types = $this->input->post('item_types');

            foreach ($item_types as $index => $item_type) {
                if ($item_type == 'invoice') {
                    $payment_request_item = new Purchase_order_payment_request_invoice_item();
                    $payment_request_item->purchase_order_payment_request_id = $payment_request->{$payment_request::DB_TABLE_PK};
                    $payment_request_item->invoice_id = $this->input->post('invoice_ids')[$index];
                } else {
                    $payment_request_item =  new Purchase_order_payment_request_cash_item();
                    $payment_request_item->purchase_order_payment_request_id = $payment_request->{$payment_request::DB_TABLE_PK};
                    $payment_request_item->reference = $this->input->post('references')[$index];
                    $payment_request_item->claimed_by = $this->input->post('claimers')[$index];
                }

                $payment_request_item->description = $this->input->post('descriptions')[$index];
                $payment_request_item->requested_amount = remove_commas($this->input->post('amounts')[$index]);
                $payment_request_item->save();
            }

            //Send a notification email

            if (is_null($payment_request->forward_to)) {
                $current_level = $payment_request->current_approval_level();
                $employees_to_approve = $current_level->employees();

                $addresses = [];
                foreach ($employees_to_approve as $employee) {
                    if ($employee->email != '') {
                        $addresses[] = $employee->email;
                    }
                }
            } else {
                $addresses[] = $payment_request->forwarded_to()->email;
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
            $subject = 'PAYMENT REQUEST NO: ' . $payment_request->{$payment_request::DB_TABLE_PK} . ' FOR ' . $payment_request->cost_center_name();
            $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $payment_request->requester()->full_name() . ' submitted a Payment Request that is waiting for your approval
                                in the system,<br/> Please <a href="' . base_url() . '">login</a>, go to payment requests under procurements menu and search for Payment Request No. ' . $payment_request->{$payment_request::DB_TABLE_PK} . '<hr/></div><br/>';
            $content .= $this->preview_purchase_order_payment_request($payment_request->{$payment_request::DB_TABLE_PK}, 'true');

            $message = $this->load->view('includes/email', ['content' => $content], true);

            $this->email->to($addresses);
            $this->email->subject($subject);
            $this->email->set_mailtype("html");
            $this->email->message($message);
            $this->email->send();
        }
    }

    public function delete_payment_request()
    {
        $this->load->model('purchase_order_payment_request');
        $payment_request = new Purchase_order_payment_request();
        $payment_request->load($this->input->post('payment_request_id'));
        $description = 'Payment request No.' . $payment_request->{$payment_request::DB_TABLE_PK} . ' was deleted!';
        system_log($description);
        $payment_request->delete();
    }

    public function preview_purchase_order_payment_request($payment_request_id = 0, $string_for_email = false)
    {
        $this->load->model('purchase_order_payment_request');
        $payment_request = new Purchase_order_payment_request();
        if ($payment_request->load($payment_request_id)) {
            $data['payment_request'] = $payment_request;
            $data['chain_levels'] = $payment_request->approval_module()->chain_levels();
            $payment_request_approvals = $payment_request->purchase_order_payment_request_approvals();
            foreach ($payment_request_approvals as $approval) {
                $data['payment_request_approvals'][$approval->approval_chain_level_id] = $approval;
            }
            $html = $this->load->view('procurements/order_payment_requests/purchase_order_payment_request_preview', $data, true);

            if ($string_for_email) {
                return $html;
            }

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            if ($payment_request->status == "REJECTED") {
                $pdf->SetWatermarkText("REJECTED");
                $pdf->SetProtection(array('print'));
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            }
            //generate the PDF!
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
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Purchase Order payment request' . add_leading_zeros($payment_request->request_number()) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function get_invoice_details()
    {
        $this->load->model('invoice');
        $invoice = new Invoice();
        $invoice_id = $this->input->post('invoice_id');
        if ($invoice->load($invoice_id)) {
            $ret_val['amount'] = $invoice->amount - $invoice->amount_approved_to_be_paid();
            $ret_val['description'] = $invoice->description;
            $ret_val['stakeholder_name'] = $invoice->stakeholder()->stakeholder_name;
            echo json_encode($ret_val);
        }
    }

    public function get_purchase_order_invoices()
    {
        $this->load->model('purchase_order');
        $currency_id = $this->input->post('currency_id');
        $purchase_order = new Purchase_order();
        if ($purchase_order->load($this->input->post('order_id'))) {
            $ret_val['invoice_options'] = stringfy_dropdown_options($purchase_order->invoice_options($currency_id));
            $ret_val['requested_invoices'] = $this->load->view('procurements/order_payment_requests/requested_orders_table', [
                'invoices' => $purchase_order->requested_order_item_payments($currency_id)
            ], true);
            echo json_encode($ret_val);
        }
    }

    public function save_order_payment_requests_approval()
    {
        $this->load->model('purchase_order_payment_request_approval');
        $payment_request_approval = new Purchase_order_payment_request_approval();
        $payment_request_approval->purchase_order_payment_request_id = $this->input->post('purchase_order_payment_request_id');
        $payment_request_approval->approval_chain_level_id = $this->input->post('approval_chain_level_id');
        $payment_request_approval->approval_date = $this->input->post('approval_date');
        $payment_request_approval->is_final = 0;
        $payment_request_approval->forward_to = $this->input->post('forward_to');
        $payment_request_approval->forward_to = $payment_request_approval->forward_to != '' ? $payment_request_approval->forward_to : null;
        $payment_request_approval->comments = $this->input->post('comments');
        $payment_request_approval->created_by = $this->session->userdata('employee_id');


        if ($payment_request_approval->save()) {
            $this->load->model(['purchase_order_payment_request_approval_invoice_item', 'purchase_order_payment_request_approval_cash_item', 'approval_chain_level']);
            $item_types = $this->input->post('item_types');
            if (!empty($item_types)) {
                foreach ($item_types as $index => $item_type) {
                    if ($item_type == 'invoice') {
                        $payment_request_item_approval = new Purchase_order_payment_request_approval_invoice_item();
                        $payment_request_item_approval->purchase_order_payment_request_approval_id = $payment_request_approval->{$payment_request_approval::DB_TABLE_PK};
                        $payment_request_item_approval->purchase_order_payment_request_invoice_item_id = $this->input->post('purchase_order_payment_request_invoice_item_ids')[$index];
                    } else {
                        $payment_request_item_approval =  new Purchase_order_payment_request_approval_cash_item();
                        $payment_request_item_approval->purchase_order_payment_request_approval_id = $payment_request_approval->{$payment_request_approval::DB_TABLE_PK};
                        $payment_request_item_approval->purchase_order_payment_request_cash_item_id = $this->input->post('purchase_order_payment_request_cash_item_ids')[$index];
                        $payment_request_item_approval->claimed_by = $this->input->post('claimed_by')[$index];
                    }

                    $payment_request_item_approval->approved_amount = remove_commas($this->input->post('amounts')[$index]);
                    $payment_request_item_approval->save();
                }
            }

            $payment_request = $payment_request_approval->purchase_order_payment_request();
            $status = $this->input->post('status');
            $status = $status != '' ? $status : null;
            if (($payment_request_approval->forward_to == '' && !$payment_request->current_approval_level() && is_null($status)) || $payment_request->forward_to == $this->session->userdata('employee_id')) {
                $payment_request_approval->is_final = 1;
                $payment_request_approval->save();
                $payment_request->status = 'APPROVED';
                $payment_request->finalized_date = $payment_request_approval->approval_date;
                $payment_request->finalizer_id = $payment_request_approval->created_by;
                $payment_request->save();

                if ($payment_request->save() && $payment_request->status == 'APPROVED') {
                    $requester_id = $payment_request->requester_id;
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

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'PURCHASE ORDER PAYMENT REQUEST NO: ' . $payment_request->{$payment_request::DB_TABLE_PK} . ' FOR ' . $payment_request->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $payment_request->requester()->full_name() . ' the payment you requested has been approved,<br/>
                                Please <a href="' . base_url() . '">login</a> and search for purchase order payment request no ' . $payment_request->{$payment_request::DB_TABLE_PK} . '<hr/></div><br/>';
                    $content .= $this->preview_approved_purchase_order_payments($payment_request_approval->{$payment_request_approval::DB_TABLE_PK}, false, true);

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            } else if (($payment_request_approval->forward_to == '' && !$payment_request->current_approval_level()) || !is_null($status)) {
                $payment_request_approval->is_final = 1;
                $payment_request_approval->save();
                $payment_request->status = $status;
                $payment_request->finalized_date = $payment_request_approval->approval_date;
                $payment_request->finalizer_id = $payment_request_approval->created_by;
                $payment_request->save();

                if ($payment_request->status == 'REJECTED') {
                    $requester_id = $payment_request->requester_id;
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

                    $this->email->initialize($config);
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'PURCHASE ORDER PAYMENT REQUEST NO: ' . $payment_request->{$payment_request::DB_TABLE_PK} . ' FOR ' . $payment_request->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $payment_request->requester()->full_name() . ' the payment you requested has been rejected by ' . $payment_request_approval->employee()->full_name() . ',<br/>
                                Please <a href="' . base_url() . '">login</a> and search for purchase order payment request no ' . $payment_request->{$payment_request::DB_TABLE_PK} . '<hr/></div><br/>';
                    $content .= $this->preview_approved_purchase_order_payments($payment_request_approval->{$payment_request_approval::DB_TABLE_PK}, false, true);

                    $message = $this->load->view('includes/email', ['content' => $content], true);

                    $this->email->to($addresses);
                    $this->email->subject($subject);
                    $this->email->set_mailtype("html");
                    $this->email->message($message);
                    $this->email->send();
                }
            }

            if ($payment_request->status == 'PENDING' && $payment_request_approval->is_final != 1 && is_null($status)) {
                $approval_chain_level_id = $payment_request_approval->approval_chain_level_id;
                $approval_chain_level = new Approval_chain_level();
                $approval_chain_level->load($approval_chain_level_id);
                $levels_to_approve = $approval_chain_level->next_level();

                if ($levels_to_approve) {
                    $employees_to_approve = $levels_to_approve->employees();
                    $addresses = [];
                    foreach ($employees_to_approve as $employee) {
                        if ($employee->email != '') {
                            $addresses[] = $employee->email;
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
                    $this->email->from('noreply@epmtz.com', 'Electronic Project Manager');
                    $subject = 'PURCHASE ORDER PAYMENT REQUEST NO: ' . $payment_request->{$payment_request::DB_TABLE_PK} . ' FOR ' . $payment_request->cost_center_name();
                    $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $payment_request->requester()->full_name() . ' submitted the purchase order payment request that is waiting for your approval,<br/>
                                Please <a href="' . base_url() . '">login</a> and search for purchase order payment request no ' . $payment_request->{$payment_request::DB_TABLE_PK} . '<hr/></div><br/>';
                    $content .= $this->preview_approved_purchase_order_payments($payment_request_approval->{$payment_request_approval::DB_TABLE_PK}, false, true);

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

    public function preview_approved_purchase_order_payments($approved_payment_request_id = 0, $print = false, $string_for_email = false)
    {
        $this->load->model(['purchase_order_payment_request_approval', 'purchase_order_payment_request']);
        $payment_request_approval = new Purchase_order_payment_request_approval();
        if ($payment_request_approval->load($approved_payment_request_id)) {
            $payment_request = $payment_request_approval->purchase_order_payment_request();
            $data['payment_request'] = $payment_request;
            $data['payment_request_approval'] = $payment_request_approval;
            $html = $this->load->view('procurements/order_payment_requests/purchase_order_approved_payment_preview', $data, true);
            if ($string_for_email) {
                return $html;
            }

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            //generate the PDF!

            if ($payment_request->status == 'REJECTED') {
                $pdf->SetProtection(array('print'));
                $pdf->SetWatermarkText("REJECTED");
                $pdf->showWatermarkText = true;
                $pdf->watermark_font = 'DejaVuSansCondensed';
                $pdf->watermarkTextAlpha = 0.5;
                $pdf->SetDisplayMode('fullpage');
            } else if ($payment_request_approval->is_cancelled()) {
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
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Purchase Order approved payment' . add_leading_zeros($payment_request_approval->{$payment_request_approval::DB_TABLE_PK}) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function orders_report()
    {
        if ($this->input->post('triggered') != null) {

            $this->load->model('purchase_order');
            $report_type = $this->input->post('report_type');
            $vendor_id = $this->input->post('vendor_id');
            $vendor_id = $vendor_id != '' ? $vendor_id : null;
            $data['from'] = $from = $this->input->post('from');
            $data['to'] = $to = $this->input->post('to');
            $data['print'] = $print = $this->input->post('print');
            $vendor_selected = !is_null($vendor_id) && $vendor_id != '';
            $report_type_selected = !is_null($report_type) && $report_type != '';
            $where = '';
            if ($vendor_selected) {
                $where['vendor_id ='] = $vendor_id;
            }
            if (!is_null($from) && $from != '') {
                $where['issue_date >='] = $from;
            }

            if (!is_null($to) && $to != '') {
                $where['issue_date <='] = $to;
            }

            if ($report_type_selected) {
                $where['status ='] = $report_type;
            }
            $purchase_orders = $this->purchase_order->get(0, 0, $where);
            $data['report_type'] = $report_type;
            $data['purchase_orders'] = $purchase_orders;
            if ($vendor_selected) {
                if ($print) {

                    $html = $this->load->view('procurements/reports/purschase_order_sheet', $data, true);

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
                    $pdf->SetFooter($footercontents);
                    $pdf->WriteHTML($html);
                    //$this->mpdf->Output($file_name, 'D'); // download force

                    $pdf->Output('purchase order.pdf', 'I'); // view in the explorer

                } else {
                    $this->load->view('procurements/reports/purchase_order_table', $data);
                }
            } else {
                if ($print) {

                    $html = $this->load->view('procurements/reports/orders_report_sheet', $data, true);

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
                    $pdf->SetFooter($footercontents);
                    $pdf->WriteHTML($html);
                    //$this->mpdf->Output($file_name, 'D'); // download force

                    $pdf->Output('Orders Report ' . $from . ' - ' . $to . '.pdf', 'I'); // view in the explorer

                } else {
                    $this->load->view('procurements/reports/orders_report_table', $data);
                }
            }
        } else {
            $data['vendor_options'] = vendor_dropdown_options();
            $this->load->view('procurements/reports/orders_report', $data);
        }
    }

    public function stakeholder_invoices_list($stakeholder_id)
    {
        $this->load->model('invoice');
        $datatable_params = dataTable_post_params();
        echo $this->invoice->list($datatable_params["limit"], $datatable_params["start"], $datatable_params["keyword"], $datatable_params["order"], $stakeholder_id);
    }

    public function reports()
    {
        $report_type = $this->input->post('report_type');
        $report_type = $report_type != '' ? $report_type : null;
        $data['from'] = $from = $this->input->post('from');
        $vendor_id = $this->input->post('vendor_id');
        $data['to'] = $to = $this->input->post('to');
        $data['print'] = $print = $this->input->post('print');
        $data['title'] = 'Procurements | Reports';

        if (!is_null($report_type)) {

            if ($report_type == 'requested_items') {
                ini_set("pcre.backtrack_limit", "5000000");
                set_time_limit(86400);
                $sql = 'SELECT requisition_material_items.requisition_id,item_name AS description,approved_quantity,approved_rate,requisition_approval_material_items.source_type,vendor_id,location_id,currency_name,requisitions.currency_id,required_date,request_date FROM requisition_approval_material_items
                        LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                        LEFT JOIN material_items ON requisition_material_items.material_item_id = material_items.item_id
                        LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                        LEFT JOIN currencies ON requisition_approval_material_items.currency_id = currencies.currency_id
                        WHERE status = "APPROVED"
                        AND required_date >= "' . $from . '"
                        AND required_date <= "' . $to . '"
                        
                        UNION
                        
                        SELECT requisition_cash_items.requisition_id,description,approved_quantity,approved_rate,"CASH" AS source_type, "" AS vendor_id,"" AS location_id,currency_name,requisitions.currency_id,required_date,request_date FROM requisition_approval_cash_items
                        LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                        LEFT JOIN requisitions ON requisition_cash_items.requisition_id = requisitions.requisition_id
                        LEFT JOIN currencies ON requisition_approval_cash_items.currency_id = currencies.currency_id
                        WHERE status = "APPROVED"
                        AND required_date >= "' . $from . '"
                        AND required_date <= "' . $to . '"
                        
                        UNION
                        
                        SELECT requisition_asset_items.requisition_id,asset_name,approved_quantity,approved_rate,requisition_approval_asset_items.source_type,requisition_approval_asset_items.vendor_id,requisition_approval_asset_items.location_id,currency_name,requisitions.currency_id,required_date,request_date FROM requisition_approval_asset_items
                        LEFT JOIN requisition_asset_items ON requisition_approval_asset_items.requisition_asset_item_id = requisition_asset_items.id
                        LEFT JOIN asset_items ON requisition_asset_items.asset_item_id = asset_items.id
                        LEFT JOIN requisitions ON requisition_asset_items.requisition_id = requisitions.requisition_id
                        LEFT JOIN currencies ON requisition_approval_asset_items.currency_id = currencies.currency_id
                        WHERE status = "APPROVED"
                        AND required_date >= "' . $from . '"
                        AND required_date <= "' . $to . '"
                        
                        UNION
                        
                        SELECT requisition_service_items.requisition_id,description, approved_quantity,approved_rate,requisition_approval_service_items.source_type,requisition_approval_service_items.vendor_id, "" AS location_id,currency_name,requisitions.currency_id,required_date,request_date FROM requisition_approval_service_items
                        LEFT JOIN requisition_service_items ON requisition_approval_service_items.requisition_service_item_id = requisition_service_items.id
                        LEFT JOIN requisitions ON requisition_service_items.requisition_id = requisitions.requisition_id
                        LEFT JOIN currencies ON requisitions.currency_id = currencies.currency_id
                        WHERE status = "APPROVED"
                        AND required_date >= "' . $from . '"
                        AND required_date <= "' . $to . '" ';

                $query = $this->db->query($sql);
                $requested_items = $query->result();

                $this->load->model(['vendor', 'inventory_location', 'currency', 'requisition']);
                $table_items = [];
                foreach ($requested_items as $requested_item) {
                    $currency =  new Currency();
                    $currency->load($requested_item->currency_id);
                    $data['currency'] = $currency;

                    $requisition =  new Requisition();
                    $requisition->load($requested_item->requisition_id);
                    $cost_center = $requisition->cost_center_name();

                    if ($requested_item->source_type == 'vendor') {
                        $vendor = new Vendor();
                        $vendor->load($requested_item->vendor_id);
                        $requested_from = $vendor->vendor_name;
                        $source_type = "Vendor";
                    } else if ($requested_item->source_type == 'store') {
                        $location = new Inventory_location();
                        $location->load($requested_item->location_id);
                        $requested_from = $location->location_name;
                        $source_type = "Store";
                    } else if ($requested_item->source_type == 'imprest') {
                        $requested_from = '';
                        $source_type = "Imprest";
                    } else {
                        $requested_from = '';
                        $source_type = "Cash";
                    }
                    $table_items[] = [
                        'required_date' => $requested_item->required_date,
                        'requisition' => $requisition,
                        'request_date' => $requested_item->request_date,
                        'description' => $requested_item->description,
                        'cost_center' => $cost_center,
                        'approved_quantity' => $requested_item->approved_quantity,
                        'approved_rate' => $requested_item->approved_rate,
                        'currency' => $currency->symbol,
                        'amount' => $requested_item->approved_rate * $requested_item->approved_quantity,
                        'source_type' => $source_type,
                        'requested_from' => $requested_from
                    ];
                }

                $data['table_items'] = $table_items;

                if ($print) {

                    $html = $this->load->view('procurements/reports/requested_items_sheet', $data, true);
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
                    $pdf->SetFooter($footercontents);
                    $pdf->WriteHTML($html);
                    //$this->mpdf->Output($file_name, 'D'); // download force

                    $pdf->Output('Requested Items.pdf', 'I'); // view in the explorer
                } else {
                    echo $this->load->view('procurements/reports/requested_items_table', $data, true);
                }
            }
        } else {
            $this->load->view('procurements/reports/index', $data);
        }
    }

    public function load_vendor_account_options()
    {
        $this->load->model('vendor');
        $vendor = new Vendor();
        $vendor_string = $this->input->post('vendor_id');
        $vendor_id = $vendor_string != '' ? explode("_", $vendor_string)[1] : null;
        if ($vendor->load($vendor_id)) {
            echo stringfy_dropdown_options($vendor->accounts_dropdown_options());
        }
    }

    public function validate_invoice_amount_against_order()
    {
        $this->load->model('purchase_order');
        $order_id = $this->input->post('order_id');
        $vendor_id = $this->input->post('vendor_id');

        $purchase_order = new Purchase_order();
        $purchase_order->load($order_id);
        if ($vendor_id ==  $purchase_order->stakeholder_id) {
            $ret_val['flag'] = 1;
            $amount = $purchase_order->uninvoiced_amount();
            $ret_val['amount'] = round($amount, 2);
            echo json_encode($ret_val);
        } else {
            $ret_val['flag'] = 0;
            $ret_val['amount'] = 0;
            echo json_encode($ret_val);
        }
    }
}
