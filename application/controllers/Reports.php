<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 17/11/2017
 * Time: 08:39
 */

class Reports extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_login();
        check_permission('Executive Reports', true);
    }

    public function index()
    {
        $data['title'] = 'Reports';
        $this->load->view('reports/index', $data);
    }

    public function project_inventory_position()
    {
        set_time_limit(86400);
        ini_set('memory_limit', -1);
        $this->load->model('project');
        $data['title'] = 'Reports | Project Inventory Position';
        $data['project_options'] = projects_dropdown_options();
        if ($this->input->post('from') || $this->input->post('to')) {
            $project_id = $this->input->post('project_id');
            $project_id = !is_null($project_id) ? $project_id : null;
            $from = $this->input->post('from');
            $to = $this->input->post('to');

            if ($project_id != null) {
                $project = new Project();
                $project->load($project_id);
                $site_location = $project->location();

                //Requisitions
                $requisitions = $project->requisitions($from, $to);
                $goods_budget = $total_approved_amount = $goods_ordered_value = $order_received_value = $site_received_value = $material_used_value = 0;
                $json['requisitions'] = [];

                $total_store_sourced_amount = 0;
                $transfer_orders = [];
                foreach ($requisitions as $requisition) {
                    $final_approval = $requisition->final_approval();
                    if ($final_approval) {
                        $total_approved_amount += $amount = $final_approval->total_approved_amount(true);
                        $total_store_sourced_amount += $store_sourced_amount = $final_approval->material_items_approved_amount('store', true);
                        if ($store_sourced_amount > 0) {
                            $transfer_orders[] = [
                                '<a href="' . base_url('requisitions/preview_requisition_approved_chains/' . $requisition->{$requisition::DB_TABLE_PK}) . '" target="_blank">' . $requisition->requisition_number() . '</a>',
                                floatval($store_sourced_amount)
                            ];
                        }
                        $json['requisitions'][] = [
                            '<a href="' . base_url('requisitions/preview_requisition_approved_chains/' . $requisition->{$requisition::DB_TABLE_PK}) . '" target="_blank">' . $requisition->requisition_number() . '</a>',
                            $amount
                        ];
                    }
                }

                //Ordered Goods

                $orders = $project->purchase_orders($from, $to);
                $total_supplier_sourced_amount = $total_supplier_sourced_received_value = 0;
                $order_grns = $purchase_orders = [];
                foreach ($orders as $order) {
                    $total_supplier_sourced_amount += $supplier_sourced_amount = $order->total_order_in_base_currency();
                    $purchase_orders[] = [
                        '<a href="' . base_url('procurements/preview_purchase_order/' . $order->{$order::DB_TABLE_PK}) . '" target="_blank">' . $order->order_number() . '</a>',
                        floatval($supplier_sourced_amount)
                    ];
                }

                //Order Grns
                $sql = 'SELECT grn_id FROM purchase_order_grns
                    LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                    LEFT JOIN goods_received_notes ON purchase_order_grns.goods_received_note_id = goods_received_notes.grn_id
                    WHERE goods_received_notes.receive_date >= "' . $from . '" AND goods_received_notes.receive_date <= "' . $to . '" AND project_id = ' . $project_id . '
                    ';
                $query = $this->db->query($sql);
                $results = $query->result();
                $this->load->model('goods_received_note');
                foreach ($results as $result) {
                    $grn = new Goods_received_note();
                    $grn->load($result->grn_id);
                    $total_supplier_sourced_received_value += $amount = $grn->material_value();
                    $order_grns[] = [
                        '<a href="' . base_url('inventory/preview_grn/' . $grn->{$grn::DB_TABLE_PK}) . '" target="_blank">' . $grn->grn_number() . '</a>',
                        $amount
                    ];
                }

                $order_received_value += $total_supplier_sourced_received_value;
                $json['received_materials']['supplier_sourced_amount'] = floatval($total_supplier_sourced_received_value);
                $json['received_materials']['orders_grns'] = $order_grns;

                $goods_ordered_value += $total_supplier_sourced_amount;
                $goods_ordered_value += $total_store_sourced_amount;
                $json['ordered_goods']['supplier_sourced_amount'] = $total_supplier_sourced_amount;
                $json['ordered_goods']['store_sourced_amount'] = $total_store_sourced_amount;
                $json['ordered_goods']['purchase_orders'] = $purchase_orders;
                $json['ordered_goods']['transfer_orders'] = $transfer_orders;


                //Cost Assignments
                $material_cost_center_assignments = $project->material_cost_center_assignments('IN', $from, $to);
                $store_sourced_received_material_value = $opening_stock_value = $project->material_opening_stock_value();
                $mcas[] = ['Opening', floatval($opening_stock_value)];
                foreach ($material_cost_center_assignments as $assignment) {
                    $store_sourced_received_material_value += $value = $assignment->value();
                    $mcas[] = [
                        '<a href="' . base_url('inventory/preview_material_cost_center_assignment/' . $assignment->{$assignment::DB_TABLE_PK}) . '" target="_blank">' . $assignment->assignment_number() . '</a>',
                        floatval($value)
                    ];
                }
                $order_received_value += $store_sourced_received_material_value;


                $json['received_materials']['store_sourced_amount'] = floatval($store_sourced_received_material_value);
                $json['received_materials']['mcas'] = $mcas;


                //Site GRNS
                $site_grns = $site_location->grns($from, $to);
                $json['site_grns'] = [];
                foreach ($site_grns as $grn) {
                    $site_received_value += $amount = $grn->material_value();
                    $json['site_grns'][] = [
                        '<a href="' . base_url('inventory/preview_grn/' . $grn->{$grn::DB_TABLE_PK}) . '" target="_blank">' . $grn->grn_number() . '</a>',
                        $amount
                    ];
                }

                //Material Used
                $activities = $project->activities();
                $json['cost_activities'][] = [
                    'Project Shared',
                    $project->actual_cost(['material'], $from, $to, true)
                ];

                $material_used_value += $project->actual_cost(['material'], $from, $to, true);
                foreach ($activities as $activity) {
                    $amount = $activity->actual_cost(['material'], $from, $to);
                    if ($amount > 0) {
                        $material_used_value += $amount;
                        $json['cost_activities'][] = [
                            '<a href="' . base_url('costs/material_costs_list/activity/' . $activity->{$activity::DB_TABLE_PK}) . '/' . $from . '/' . $to . '" target="_blank">' . $activity->activity_name . '</a>',
                            $amount
                        ];
                    }
                }

                //Budget Figure
                $json['budget_activities'][] = [
                    'Project Shared',
                    $project->budget_figure(['material'], true)
                ];

                $goods_budget += $project->budget_figure(['material'], true);
                foreach ($activities as $activity) {
                    $amount = $activity->budget_figure(['material']);
                    if ($amount > 1) {
                        $goods_budget += $amount;
                        $json['budget_activities'][] = [
                            '<a href="' . base_url('budgets/material_budget_list/' . $activity->{$activity::DB_TABLE_PK}) . '" target="_blank">' . $activity->activity_name . '</a>',
                            $amount
                        ];
                    }
                }


                $data = [
                    'material_used_value' => $material_used_value,
                    'total_approved_amount' => $total_approved_amount,
                    'order_amount' => $goods_ordered_value,
                    'ordered_received_value' => $order_received_value,
                    'site_goods_received_value' => $site_received_value,
                    'material_balance_value' => $project->material_balance_value($to),
                    'site_material_balance_value' => $site_location->total_material_balance_value($project_id, $to)
                ];

                if ($this->input->post('print') == 'true') {
                    $data['from'] = $from;
                    $data['to'] = $to;
                    $data['project'] = $project;
                    $data['project_id'] = $project->{$project::DB_TABLE_PK};
                    $data['print'] = true;
                    $html = $this->load->view('reports/project_inventory_position_sheet', $data, true);

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
                    $pdf->Output('Project Summary Report - ' . $project->project_name . '.pdf', 'I');
                } else {

                    $json['project_name'] = $project->project_name;
                    $json['report_category'] = 'Project Inventory Position';
                    $json['table_view'] = $this->load->view('reports/project_inventory_position_table', $data, 'true');
                    $json['material_used_value'] = $data['material_used_value'];
                    $json['total_approved_amount'] = $data['total_approved_amount'];
                    $json['order_amount'] = $data['order_amount'];
                    $json['ordered_received_value'] = $data['ordered_received_value'];
                    $json['from'] = $from;
                    $json['to'] = $to;
                    $json['site_goods_received_value'] = $data['site_goods_received_value'];
                    $json['material_balance_value'] = $data['material_balance_value'];
                    $json['site_material_balance_value'] = $data['site_material_balance_value'];
                    $json['goods_budget'] = $goods_budget;
                    echo json_encode($json);
                }
            } else {
                $json['requisitions'] = $json['site_grns'] = [];

                $transfer_orders = $order_grns = $purchase_orders = [];

                $site_material_balance_value = $material_balance_value = $goods_budget = $total_approved_amount = $goods_ordered_value = $order_received_value = $site_received_value = $material_used_value = $total_store_sourced_amount = $total_supplier_sourced_amount = $total_supplier_sourced_received_value = 0;

                $projects = $this->project->get();
                foreach ($projects as $project) {
                    $site_location = $project->location();

                    //Requisitions
                    $requisitions = $project->requisitions($from, $to);
                    foreach ($requisitions as $requisition) {
                        $final_approval = $requisition->final_approval();
                        if ($final_approval) {
                            $total_approved_amount += $amount = $final_approval->total_approved_amount(true);
                            $total_store_sourced_amount += $store_sourced_amount = $final_approval->material_items_approved_amount('store', true);
                            if ($store_sourced_amount > 0) {
                                $transfer_orders[] = [
                                    '<a href="' . base_url('requisitions/preview_requisition_approved_chains/' . $requisition->{$requisition::DB_TABLE_PK}) . '" target="_blank">' . $requisition->requisition_number() . '</a>',
                                    floatval($store_sourced_amount)
                                ];
                            }
                            $json['requisitions'][] = [
                                '<a href="' . base_url('requisitions/preview_requisition_approved_chains/' . $requisition->{$requisition::DB_TABLE_PK}) . '" target="_blank">' . $requisition->requisition_number() . '</a>',
                                $amount
                            ];
                        }
                    }

                    //Ordered Goods
                    $orders = $project->purchase_orders($from, $to);
                    foreach ($orders as $order) {
                        $total_supplier_sourced_amount += $supplier_sourced_amount = $order->total_order_in_base_currency();
                        $purchase_orders[] = [
                            '<a href="' . base_url('procurements/preview_purchase_order/' . $order->{$order::DB_TABLE_PK}) . '" target="_blank">' . $order->order_number() . '</a>',
                            floatval($supplier_sourced_amount)
                        ];
                    }

                    //Order Grns
                    $sql = 'SELECT grn_id FROM purchase_order_grns
                    LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                    LEFT JOIN goods_received_notes ON purchase_order_grns.goods_received_note_id = goods_received_notes.grn_id
                    WHERE goods_received_notes.receive_date >= "' . $from . '" AND goods_received_notes.receive_date <= "' . $to . '"
                    ';
                    $query = $this->db->query($sql);
                    $results = $query->result();
                    $this->load->model('goods_received_note');
                    foreach ($results as $result) {
                        $grn = new Goods_received_note();
                        $grn->load($result->grn_id);
                        $total_supplier_sourced_received_value += $amount = $grn->material_value();
                        $order_grns[] = [
                            '<a href="' . base_url('inventory/preview_grn/' . $grn->{$grn::DB_TABLE_PK}) . '" target="_blank">' . $grn->grn_number() . '</a>',
                            $amount
                        ];
                    }

                    $order_received_value += $total_supplier_sourced_received_value;
                    $json['received_materials']['supplier_sourced_amount'] = floatval($total_supplier_sourced_received_value);
                    $json['received_materials']['orders_grns'] = $order_grns;

                    $goods_ordered_value += $total_supplier_sourced_amount;
                    $goods_ordered_value += $total_store_sourced_amount;
                    $json['ordered_goods']['supplier_sourced_amount'] = $total_supplier_sourced_amount;
                    $json['ordered_goods']['store_sourced_amount'] = $total_store_sourced_amount;
                    $json['ordered_goods']['purchase_orders'] = $purchase_orders;
                    $json['ordered_goods']['transfer_orders'] = $transfer_orders;

                    //Cost Assignments
                    $material_cost_center_assignments = $project->material_cost_center_assignments('IN', $from, $to);
                    $store_sourced_received_material_value = $opening_stock_value = $project->material_opening_stock_value();
                    $mcas[] = ['Opening', floatval($opening_stock_value)];
                    foreach ($material_cost_center_assignments as $assignment) {
                        $store_sourced_received_material_value += $value = $assignment->value();
                        $mcas[] = [
                            '<a href="' . base_url('inventory/preview_material_cost_center_assignment/' . $assignment->{$assignment::DB_TABLE_PK}) . '" target="_blank">' . $assignment->assignment_number() . '</a>',
                            floatval($value)
                        ];
                    }
                    $order_received_value += $store_sourced_received_material_value;

                    $json['received_materials']['store_sourced_amount'] = floatval($store_sourced_received_material_value);
                    $json['received_materials']['mcas'] = $mcas;

                    //Site GRNS
                    $site_grns = $site_location->grns($from, $to);
                    foreach ($site_grns as $grn) {
                        $site_received_value += $amount = $grn->material_value();
                        $json['site_grns'][] = [
                            '<a href="' . base_url('inventory/preview_grn/' . $grn->{$grn::DB_TABLE_PK}) . '" target="_blank">' . $grn->grn_number() . '</a>',
                            $amount
                        ];
                    }

                    //Material Used
                    $activities = $project->activities();
                    $json['cost_activities'][] = [
                        'Project Shared',
                        $project->actual_cost(['material'], $from, $to, true)
                    ];

                    $material_used_value += $project->actual_cost(['material'], $from, $to, true);
                    foreach ($activities as $activity) {
                        $amount = $activity->actual_cost(['material'], $from, $to);
                        if ($amount > 0) {
                            $material_used_value += $amount;
                            $json['cost_activities'][] = [
                                '<a href="' . base_url('costs/material_costs_list/activity/' . $activity->{$activity::DB_TABLE_PK}) . '/' . $from . '/' . $to . '" target="_blank">' . $activity->activity_name . '</a>',
                                $amount
                            ];
                        }
                    }

                    //Budget Figure
                    $json['budget_activities'][] = [
                        'Project Shared',
                        $project->budget_figure(['material'], true)
                    ];
                    $goods_budget += $project->budget_figure(['material'], true);
                    foreach ($activities as $activity) {
                        $amount = $activity->budget_figure(['material']);
                        if ($amount > 1) {
                            $goods_budget += $amount;
                            $json['budget_activities'][] = [
                                '<a href="' . base_url('budgets/material_budget_list/' . $activity->{$activity::DB_TABLE_PK}) . '" target="_blank">' . $activity->activity_name . '</a>',
                                $amount
                            ];
                        }
                    }


                    $material_balance_value += $project->scraped_material_balance_value($to);
                    $site_material_balance_value += $site_location->new_total_material_balance_value('all', $to);
                }

                $data = [
                    'material_used_value' => $material_used_value,
                    'total_approved_amount' => $total_approved_amount,
                    'order_amount' => $goods_ordered_value,
                    'ordered_received_value' => $order_received_value,
                    'site_goods_received_value' => $site_received_value,
                    'material_balance_value' => $material_balance_value,
                    'site_material_balance_value' => $site_material_balance_value
                ];

                if ($this->input->post('print') == 'true') {
                    $data['from'] = $from;
                    $data['to'] = $to;
                    $data['project_id'] = $project_id;
                    $data['print'] = true;
                    $html = $this->load->view('reports/project_inventory_position_sheet', $data, true);

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
                    $pdf->Output('All Project Summary Report .pdf', 'I');
                } else {

                    $json['project_name'] = 'All Projects From ' . custom_standard_date($from) . ' To ' . custom_standard_date($to) . '';
                    $json['report_category'] = 'All Projects Inventory Position';
                    $json['table_view'] = $this->load->view('reports/project_inventory_position_table', $data, 'true');
                    $json['material_used_value'] = $data['material_used_value'];
                    $json['total_approved_amount'] = $data['total_approved_amount'];
                    $json['order_amount'] = $data['order_amount'];
                    $json['from'] = $from;
                    $json['to'] = $to;
                    $json['ordered_received_value'] = $data['ordered_received_value'];
                    $json['site_goods_received_value'] = $data['site_goods_received_value'];
                    $json['material_balance_value'] = $data['material_balance_value'];
                    $json['site_material_balance_value'] = $data['site_material_balance_value'];
                    $json['goods_budget'] = $goods_budget;
                    echo json_encode($json);
                }
            }
        } else {
            $this->load->view('reports/project_inventory_position', $data);
        }
    }

//    public function project_inventory_movement()
//    {
//        $project_id = $this->input->post('project_id');
//        if ($project_id != null) {
//            $this->load->model(['project', 'material_item']);
//            $as_of = $this->input->post('as_of');
//            $project = new Project();
//            $project->load($project_id);
//            $site_location = $project->location();
//            $site_sub_locations = $site_location->sub_location_options();
//            unset($site_sub_locations['']);
//            $main_store_sub_locations_query = 'SELECT sub_location_id FROM sub_locations WHERE location_id = 1';
//
//            $material_items = $this->material_item->location_material_items(1, null, false, $project_id);
//            $rows = [];
//            foreach ($material_items as $material_item) {
//                $has_transaction = 0;
//                $site_sub_location_balances = [];
//                foreach ($site_sub_locations as $sub_location_id => $sub_location_name) {
//                    $has_transaction += $site_sub_location_balances[$sub_location_id] = $material_item->sub_location_balance($sub_location_id, $project_id, $as_of, 'all');
//                }
//                $has_transaction += $main_store_balance = $material_item->sub_location_balance($main_store_sub_locations_query, $project_id, $as_of, 'external');
//                $has_transaction += $used = $material_item->used_quantity_from_site_store_for_project($project_id, $as_of);
//                $has_transaction += $received_from_orders = $material_item->received_quantity_from_grns($project_id, $as_of);
//                $has_transaction += $on_transit = $material_item->sub_location_transferred_out_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of,'external', false, true);
//                $has_transaction += $assigned_out = $material_item->sub_location_assigned_out_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of);
//                $has_transaction += $sold = $material_item->sub_location_sold_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of);
//                $has_transaction += $assigned_in = $material_item->sub_location_assigned_in_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of);
//                $has_transaction += $opening_stock = $material_item->sub_location_opening_quantity('all', $project_id);
//                if ($has_transaction > 0) {
//                    $rows[] = [
//                        'item_name' => $material_item->item_name,
//                        'unit' => $material_item->unit()->symbol,
//                        'opening_stock' => $opening_stock,
//                        'assigned_in' => $assigned_in,
//                        'received_from_orders' => $received_from_orders,
//                        'sold' => $sold,
//                        'on_transit' => $on_transit,
//                        'used' => $used,
//                        'assigned_out' => $assigned_out,
//                        'main_store_balance' => $main_store_balance,
//                        'site_sub_location_balances' => $site_sub_location_balances,
//                        'average_price' => $material_item->last_average_price($project_id)
//                    ];
//                }
//            }
//            $data['site_sub_locations'] = $site_sub_locations;
//            $data['rows'] = $rows;
//            if ($this->input->post('print') == 'true') {
//                $data['as_of'] = $as_of;
//                $data['project'] = $project;
//                $data['print'] = true;
//                $html = $this->load->view('reports/project_inventory_movement_report_sheet', $data, true);
//
//                $this->load->library('m_pdf');
//                //actually, you can pass mPDF parameter on this load() function
//                $pdf = $this->m_pdf->load();
//
//
//                //generate the PDF!
//                $footercontents = '
//                    <div>
//                        <div style="text-align: left; float: left; width: 50%">
//                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
//                        </div>
//                        <div>
//                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
//                        </div>
//                        <div style="text-align: center">
//                        {PAGENO}
//                        </div>
//                    </div>';
//                $pdf->setFooter($footercontents);
//                $pdf->WriteHTML($html);
//                //offer it to user via browser download! (The PDF won't be saved on your server HDD)
//                $pdf->Output('Project Material Status Report - ' . $project->project_name . '.pdf', 'I');
//            } else {
//                $this->load->view('reports/project_inventory_movement_table', $data);
//            }
//
//        } else {
//            $data['title'] = 'Reports | Project Inventory Movement';
//            $data['project_options'] = projects_dropdown_options();
//            $this->load->view('reports/project_inventory_movement', $data);
//        }
//    }

    public function project_inventory_movement()
    {
        $project_id = $this->input->post('project_id');
        if ($project_id != null) {
            $this->load->model(['project', 'material_item']);
            $as_of = $this->input->post('as_of');
            $project = new Project();
            $project->load($project_id);
            $site_location = $project->location();
            $site_sub_locations = $site_location->sub_location_options();
            unset($site_sub_locations['']);
            $main_store_sub_locations_query = 'SELECT sub_location_id FROM sub_locations WHERE location_id = 1';

            //$material_items = $this->material_item->location_material_items(1, null, false, $project_id);
            $material_items = $this->material_item->project_material_items($project_id);
            $rows = [];
            foreach ($material_items as $material_item) {
                $has_transaction = 0;
                $site_sub_location_balances = [];
                foreach ($site_sub_locations as $sub_location_id => $sub_location_name) {
                    $has_transaction += $site_sub_location_balances[$sub_location_id] = $material_item->sub_location_balance($sub_location_id, $project_id, $as_of, 'all');
                }
                $has_transaction += $main_store_balance = $material_item->sub_location_balance($main_store_sub_locations_query, $project_id, $as_of, 'external');
                $has_transaction += $used = $material_item->used_quantity_from_site_store_for_project($project_id, $as_of);
                $has_transaction += $received_from_orders = $material_item->received_quantity_from_grns($project_id, $as_of);
                $has_transaction += $on_transit = $material_item->sub_location_transferred_out_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of,'external', false, true);
                $has_transaction += $assigned_out = $material_item->sub_location_assigned_out_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of);
                $has_transaction += $sold = $material_item->sub_location_sold_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of);
                $has_transaction += $assigned_in = $material_item->sub_location_assigned_in_quantity('SELECT sub_location_id FROM sub_locations', $project_id, null, $as_of);
                $has_transaction += $opening_stock = $material_item->sub_location_opening_quantity('all', $project_id);
                $has_transaction += $ordered = $material_item->ordered_quantity_for_project($project_id,$as_of);
                if ($has_transaction > 0) {
                    $rows[] = [
                        'item_name' => $material_item->item_name,
                        'unit' => $material_item->unit()->symbol,
                        'opening_stock' => $opening_stock,
                        'ordered' => $ordered,
                        'assigned_in' => $assigned_in,
                        'received_from_orders' => $received_from_orders,
                        'sold' => $sold,
                        'on_transit' => $on_transit,
                        'used' => $used,
                        'assigned_out' => $assigned_out,
                        'main_store_balance' => $main_store_balance,
                        'site_sub_location_balances' => $site_sub_location_balances,
                        'average_price' => $has_transaction == $ordered ? $material_item->average_ordered_price($project_id) : $material_item->last_average_price($project_id)
                    ];
                }
            }
            $data['site_sub_locations'] = $site_sub_locations;
            $data['rows'] = $rows;
            if ($this->input->post('print') == 'true') {
                $data['as_of'] = $as_of;
                $data['project'] = $project;
                $data['print'] = true;
                $html = $this->load->view('reports/project_inventory_movement_report_sheet', $data, true);

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
                $pdf->Output('Project Material Status Report - ' . $project->project_name . '.pdf', 'I');
            } else {
                $this->load->view('reports/project_inventory_movement_table', $data);
            }

        } else {
            $data['title'] = 'Reports | Project Inventory Movement';
            $data['project_options'] = projects_dropdown_options();
            $this->load->view('reports/project_inventory_movement', $data);
        }
    }

    public function project_performance_report()
    {
        $triggered = $this->input->post('triggered');
        if ($triggered != null) {
            $this->load->model('project');
            $project_id = $this->input->post('project_id');
            $where = [];

            $project_selected = $project_id != '' && !is_null($project_id);
            if ($project_selected) {
                $where['project_id'] = $project_id;
            }
            $projects = $this->project->get(0, 0, $where);
            $total_contract_sum = $total_budgeted_amount = $total_certified_amount = $total_actual_cost = $total_paid_amount = 0;
            $project_budgets = $project_contract_sums = $project_costs = $certified_projects = $paid_projects = $activity_budgets = $activity_costs = [];
            $task_budgets = $task_costs = [];
            foreach ($projects as $project) {
                $project_id = $project->{$project::DB_TABLE_PK};
                $project_contract_sum = $project_certified_amount = $project_paid_amount = 0;
                $project_budget = 0;
                $project_cost = 0;

                $activities = $project->activities();
                //Categorise Costs for Budgets and Actual Costs
                $cost_types = $project::COST_TYPES;
                foreach ($cost_types as $cost_type) {
                    $cost_type_budget_figure = $project->budget_figure([$cost_type], true);
                    $cost_type_cost_figure = $project->actual_cost([$cost_type], null, null, true);

                    //Generate Display Name
                    $exploded_words = explode('_', $cost_type);
                    $display_name = '';
                    foreach ($exploded_words as $word) {
                        $display_name .= ucfirst($word) . ' ';
                    }

                    $activity_budgets[$project_id][$cost_type][] = [
                        'name' => 'Project Shared',
                        'y' => $project->budget_figure([$cost_type], true)
                    ];

                    $activity_costs[$project_id][$cost_type][] = [
                        'name' => 'Project Shared',
                        'y' => $project->actual_cost([$cost_type], null, null, true)
                    ];


                    //Loop Through activities to get specific budgets and costs
                    foreach ($activities as $activity) {
                        $tasks = $activity->tasks();
                        $activity_id = $activity->{$activity::DB_TABLE_PK};


                        foreach ($tasks as $task) {
                            $cost_type_budget_figure += $task_budget_figure = $task->budget_figure([$cost_type]);
                            $cost_type_cost_figure += $task_cost_figure = $task->actual_cost([$cost_type]);
                            $task_budgets[$activity_id][$cost_type][] = [
                                'name' => $task->task_name,
                                'y' => $task_budget_figure
                            ];

                            $task_costs[$activity_id][$cost_type][] = [
                                'name' => $task->task_name,
                                'y' => $task_cost_figure
                            ];

                            $json['drilldown'][] = [
                                'name' => 'Task Wise',
                                'id' => 'activity_cost_type_budget_drilldown_' . $cost_type . $activity_id . $project_id,
                                'data' => $task_budgets[$activity_id][$cost_type]
                            ];

                            $json['drilldown'][] = [
                                'name' => 'Task Wise',
                                'id' => 'activity_cost_type_cost_drilldown_' . $cost_type . $activity_id . $project_id,
                                'data' => $task_costs[$activity_id][$cost_type]
                            ];

                        }

                        $activity_budgets[$project_id][$cost_type][] = [
                            'name' => $activity->activity_name,
                            'y' => $activity->budget_figure([$cost_type]),
                            'drilldown' => 'activity_cost_type_budget_drilldown_' . $cost_type . $activity_id . $project_id,
                        ];

                        $activity_costs[$project_id][$cost_type][] = [
                            'name' => $activity->activity_name,
                            'y' => $activity->actual_cost([$cost_type]),
                            'drilldown' => 'activity_cost_type_cost_drilldown_' . $cost_type . $activity_id . $project_id,
                        ];
                    }


                    $json['drilldown'][] = [
                        'name' => 'Activity Wise',
                        'id' => 'cost_type_budget_drilldown_' . $cost_type . $project_id,
                        'data' => $activity_budgets[$project_id][$cost_type]
                    ];

                    $json['drilldown'][] = [
                        'name' => 'Activity Wise',
                        'id' => 'cost_type_cost_drilldown_' . $cost_type . $project_id,
                        'data' => $activity_costs[$project_id][$cost_type]
                    ];

                    $cost_type_budgets[$project_id][] = [
                        'name' => $display_name,
                        'y' => doubleval($cost_type_budget_figure),
                        'drilldown' => 'cost_type_budget_drilldown_' . $cost_type . $project_id
                    ];

                    $cost_type_costs[$project_id][] = [
                        'name' => $display_name,
                        'y' => doubleval($cost_type_cost_figure),
                        'drilldown' => 'cost_type_cost_drilldown_' . $cost_type . $project_id
                    ];

                    $project_budget += $cost_type_budget_figure;
                    $project_cost += $cost_type_cost_figure;
                }

                //Activities for Contract Sums
                $activity_contract_sums = [];
                foreach ($activities as $activity) {
                    $activity_id = $activity->{$activity::DB_TABLE_PK};
                    $tasks = $activity->tasks();

                    $activity_contract_sum = $activity_cost = 0;
                    $task_contract_sums = $task_budgets = $task_costs = [];
                    foreach ($tasks as $task) {

                        //Contract Sum
                        $project_contract_sum += $task->contract_sum();
                        $activity_contract_sum += $task->contract_sum();

                        //Tasks Data
                        $task_contract_sums[] = [
                            'name' => $task->task_name,
                            'y' => floatval($task->contract_sum())
                        ];

                    }

                    //Activity Drilldowns
                    $json['drilldown'][] = [
                        'name' => 'Task Contract Sums',
                        'id' => 'activity_contract_sum_drilldown_' . $activity_id,
                        'data' => $task_contract_sums
                    ];


                    $activity_contract_sums[] = [
                        'name' => $activity->activity_name,
                        'y' => $activity_contract_sum,
                        'drilldown' => 'activity_contract_sum_drilldown_' . $activity_id
                    ];

                }

                $total_contract_sum += $project_contract_sum;
                $total_budgeted_amount += $project_budget;
                $total_actual_cost += $project_cost;

                //Certificates For Certified and Paid amounts
                $certificates = $project->certificates();
                $project_certificates = $project_receipts = [];
                foreach ($certificates as $certificate) {
                    $certificate_id = $certificate->{$certificate::DB_TABLE_PK};
                    $certificate_paid_amount = 0;
                    $receipts = $certificate->receipts();
                    $project_certified_amount += $certificate->certified_amount;
                    $certificate_receipts = [];
                    foreach ($receipts as $receipt) {
                        $paid_amount = $receipt->amount();
                        $certificate_paid_amount += $paid_amount;

                        $certificate_receipts[] = [
                            'name' => '<a target="_blank" href="' . base_url('Finance/preview_receipt/' . $receipt->{$receipt::DB_TABLE_PK}) . '">' . $receipt->receipt_number() . '</a>',
                            'y' => floatval($receipt->amount())
                        ];
                    }

                    $json['drilldown'][] = [
                        'name' => 'Receipts',
                        'id' => 'certificate_payments_drilldown_' . $certificate_id,
                        'data' => $certificate_receipts
                    ];

                    $project_paid_amount += $certificate_paid_amount;
                    $project_certificates[] = [
                        'name' => $certificate->certificate_number,
                        'y' => floatval($certificate->certified_amount)
                    ];

                    $project_receipts[] = [
                        'name' => $certificate->certificate_number,
                        'y' => floatval($certificate_paid_amount),
                        'drilldown' => 'certificate_payments_drilldown_' . $certificate_id
                    ];
                }

                $json['drilldown'][] = [
                    'name' => 'Certificates',
                    'id' => 'project_certified_drilldown_' . $project_id,
                    'data' => $project_certificates
                ];

                $json['drilldown'][] = [
                    'name' => 'Certificate Payments',
                    'id' => 'project_paid_drilldown_' . $project_id,
                    'data' => $project_receipts
                ];

                $certified_projects[] = [
                    'name' => $project->project_name,
                    'y' => doubleval($project_certified_amount),
                    'drilldown' => 'project_certified_drilldown_' . $project_id
                ];

                $paid_projects[] = [
                    'name' => $project->project_name,
                    'y' => $project_paid_amount,
                    'drilldown' => 'project_paid_drilldown_' . $project_id
                ];
                $total_certified_amount += $project_certified_amount;
                $total_paid_amount += $project_paid_amount;


                //Project Drilldowns
                $json['drilldown'][] = [
                    'name' => 'Activity Contract Sums',
                    'id' => 'project_contract_sum_drilldown_' . $project_id,
                    'data' => $activity_contract_sums
                ];


                $json['drilldown'][] = [
                    'name' => 'Activity Costs',
                    'id' => 'project_cost_drilldown_' . $project_id,
                    'data' => $cost_type_costs[$project_id]
                ];

                $json['drilldown'][] = [
                    'name' => 'Activity Budgets',
                    'id' => 'project_budget_drilldown_' . $project_id,
                    'data' => $cost_type_budgets[$project_id]
                ];

                $project_contract_sums[] = [
                    'name' => $project->project_name,
                    'y' => $project_contract_sum,
                    'drilldown' => 'project_contract_sum_drilldown_' . $project_id
                ];

                $project_budgets[] = [
                    'name' => $project->project_name,
                    'y' => $project_budget,
                    'drilldown' => 'project_budget_drilldown_' . $project_id
                ];

                $project_costs[] = [
                    'name' => $project->project_name,
                    'y' => doubleval($project_cost),
                    'drilldown' => 'project_cost_drilldown_' . $project_id
                ];

            }

            if ($project_selected) {
                $json['project_name'] = $project->project_name;
                $json['drilldown'][] = [
                    'name' => 'Activity Contract Sums',
                    'id' => 'contract_sum_drilldown',
                    'data' => $activity_contract_sums
                ];

                $json['drilldown'][] = [
                    'name' => 'Activity Budgets',
                    'id' => 'budget_drilldown',
                    'data' => $cost_type_budgets[$project_id]
                ];

                $json['drilldown'][] = [
                    'name' => 'Activity Costs',
                    'id' => 'actual_cost_drilldown',
                    'data' => $cost_type_costs[$project_id]
                ];
                $json['drilldown'][] = [
                    'name' => 'Certificates',
                    'id' => 'certified_amount_drilldown',
                    'data' => $project_certificates
                ];

                $json['drilldown'][] = [
                    'name' => 'Certificate Payments',
                    'id' => 'paid_amount_drilldown',
                    'data' => $project_receipts
                ];

            } else {
                $json['project_name'] = 'ALL PROJECTS';
                $json['drilldown'][] = [
                    'name' => 'Project Contract Sums',
                    'id' => 'contract_sum_drilldown',
                    'data' => $project_contract_sums
                ];

                $json['drilldown'][] = [
                    'name' => 'Project Budgets',
                    'id' => 'budget_drilldown',
                    'data' => $project_budgets
                ];

                $json['drilldown'][] = [
                    'name' => 'Project Costs',
                    'id' => 'actual_cost_drilldown',
                    'data' => $project_costs
                ];

                $json['drilldown'][] = [
                    'name' => 'Projects Certified',
                    'id' => 'certified_amount_drilldown',
                    'data' => $certified_projects
                ];

                $json['drilldown'][] = [
                    'name' => 'Project Payments',
                    'id' => 'paid_amount_drilldown',
                    'data' => $paid_projects
                ];
            }

            $json['contract_sum'] = $total_contract_sum;
            $json['budget_amount'] = $total_budgeted_amount;
            $json['actual_cost'] = $total_actual_cost;
            $json['certified_amount'] = $total_certified_amount;
            $json['paid_amount'] = $total_paid_amount;
            echo json_encode($json);

        } else {
            $data['title'] = 'Reports | Project Performance';
            $data['project_options'] = projects_dropdown_options();
            $this->load->view('reports/project_performance', $data);
        }
    }

    public function requests_vs_payments()
    {
        $project_id = $this->input->post('project_id');
        $project_id = !is_null($project_id) ? $project_id : null;
        if ($project_id != null) {
            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $print = $this->input->post('print');

            $this->load->model('project');
            $project = new Project();
            $project->load($project_id);
            $data['from'] = $from;
            $data['to'] = $to;
            $data['project'] = $project;
            $data['print'] = $print;

            $data['requisitions'] = $requests = $project->requisitions($from, $to, true);

            if ($print) {

                $html = $this->load->view('reports/project_requests_vs_payments_sheet', $data, true);
                //load mPDF library
                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage(
                    'L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6, '', '', '', '', '', '', '', '', ''
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
                //$this->mpdf->Output($file_name, 'D'); // download force

                $pdf->Output('requisitions vs payments.pdf', 'I'); // view in the explorer


            } else {
                $json['project_name'] = $project->project_name;
                $json['table_view'] = $this->load->view('reports/project_requests_vs_payments_table', $data, 'true');
                echo json_encode($json);
            }

        } else {

            $data['title'] = 'Reports | Requisitions Vs Payments';
            $data['project_options'] = projects_dropdown_options();
            $this->load->view('reports/project_requests_vs_payments', $data);
        }
    }

	public function project_financial_status()
	{
		set_time_limit(86400);
		ini_set('memory_limit', -1);
		$this->load->model([
			'project',
			'stakeholder',
			'currency',
			'sub_contract'
		]);
		$cost_types = [
			'material',
			'miscellaneous',
			'permanent_labour',
			'equipment',
			'sub_contract',
			'casual_labour',
			'imprest'
		];
		$financial_record_types = [
			'Project',
			'Contract Sum(TSH)',
			'Budget(TSH)',
			'Actual Cost(TSH)',
			'Surplus(TSH)',
			'Certificates(TSH)'
		];

		$project_ids = $this->input->post('project_ids');
		$data['project_options'] = projects_dropdown_options();
		$data['title'] = 'Reports | Project Financial Status';
		$project_ids = is_array($project_ids) ? array_filter($project_ids) : [];
		if (!empty($project_ids)) {
			$project_ids = !empty($project_ids) ? count($project_ids) > 1 ? implode(',', $project_ids) : implode($project_ids) : null;
			$triggered = $this->input->post('triggered') == 'true';
			$as_of = $this->input->post('as_of');
			$where = ' project_id IN (' . $project_ids . ')';
			$projects = $this->project->get(0, 0, $where);
			$data['projects'] = $projects;
			$table_items = [];
			foreach($financial_record_types as $record_type){
				foreach($projects as $project) {
					if ($record_type == "Project") {
						$table_items[$record_type][$project->project_name] = $project->project_name;
					} else if ($record_type == "Contract Sum(TSH)") {
						$table_items[$record_type][$project->project_name] = $project->contract_sum();
					} else if ($record_type == "Budget(TSH)") {
						$table_items[$record_type][$project->project_name] = $project->budget_figure();
					} else if ($record_type == "Actual Cost(TSH)") {
						$table_items[$record_type][$project->project_name] = [
							'material_received_value' => $project->overall_received_value($as_of),
							'material_assigned_out_value' => $project->assigned_out_material_value($as_of),
							'material_unreceived_value' => $project->unreceived_goods_value($as_of,$triggered),
							'actual_cost' => $project->actual_cost($cost_types, null, $as_of,false),
							'material_installed' => $project->equipment_and_material(false, null, $as_of),
							'material_on_site' => $project->project_material_movement($as_of,$triggered),
							'all_items_vat_amount' => $project->vat_amount_for_all_grns($as_of),
							'permanent_labour' => $project->permanent_labour_cost(false, null, $as_of),
							'casual_labour' => $project->casual_labour(false, null, $as_of),
							'unreceived_goods'=> $project->unreceived_goods_value($as_of,$triggered),
							'sub_contracts' => $project->project_sub_contracts_details($as_of,$triggered),
							'overheads' => $project->overheads($as_of,$triggered)
						];
					} else if ($record_type == "Surplus(TSH)") {
						$table_items[$record_type][$project->project_name] = ($project->budget_figure()-($project->actual_cost() + $project->project_material_balance_value($as_of)));
					} else {
						$table_items[$record_type][$project->project_name] = [
							'certified_amount' => $project->certificate_details($as_of,$triggered),
							'paid_amount'=>$project->certificate_paid_amount(null,$as_of)
						];
					}
				}
			}

			$sql = 'SELECT DISTINCT stakeholder_id FROM project_purchase_orders
                    LEFT JOIN purchase_orders ON project_purchase_orders.purchase_order_id = purchase_orders.order_id
                    WHERE issue_date <= "'.$as_of.'" AND project_id IN ('.$project_ids.') ';
			$results = $this->db->query($sql)->result();
			$vendors_with_orders = [];
			foreach($results as $result){
				$vendor = new Stakeholder();
				$vendor->load($result->stakeholder_id);
				$vendors_with_orders[] = $vendor;
			}

			$sql_two = 'SELECT currency_id,currency_name,symbol FROM currencies';
			$results_two = $this->db->query($sql_two)->result();
			$currencies = [];
			foreach($results_two as $result){
				$curr = new Currency();
				$curr->load($result->currency_id);
				$currencies[] = $curr;
			}
			$vendors_table_items = [];
			foreach($vendors_with_orders as $vendor_with_order){
				$vendors_table_items[$vendor_with_order->stakeholder_name][] = $vendor_with_order->stakeholder_name;
				$balance_per_currency = $balance_in_base_currency = 0;
				foreach($currencies as $currency){
					$balance_per_project = 0;
					foreach ($projects as $project) {
						$project_id = $project->{$project::DB_TABLE_PK};
						$currency_id = $currency->currency_id;
						if (in_array($vendor_with_order->{$vendor_with_order::DB_TABLE_PK}, $project->project_suppliers())) {
							$balance_per_project += $vendor_with_order->balance_per_project($project_id, $currency_id, null, $as_of);
						}
						$vendors_table_items[$vendor_with_order->stakeholder_name][$currency->symbol][$project->project_name] = $vendor_with_order->balance_per_project($project_id, $currency_id, null, $as_of);
					}
					$balance_per_currency = $balance_per_project;
					$balance_in_base_currency += ($balance_per_currency*$currency->rate_to_native(date('Y-m-d')));
					$vendors_table_items[$vendor_with_order->stakeholder_name][$currency->symbol]['balance_per_currency'] = $balance_per_currency;
					$data['vendor_with_order'] = $vendor_with_order;
					$data['currency'] = $currency;
					$data['vendors_table_items'] = $vendors_table_items;
					$balance_per_currency_pop_up = $triggered ? '' : $this->load->view('reports/project_financial_status_balance_per_currency_pop_up',$data,true);
					$vendors_table_items[$vendor_with_order->stakeholder_name][$currency->symbol]['pop_up'] = $balance_per_currency_pop_up;
				}
				$vendors_table_items[$vendor_with_order->stakeholder_name]['vendor_balance'] = $balance_in_base_currency;
			}

			$sub_contracts_table_items = [];
			$sql = 'SELECT id FROM sub_contracts WHERE project_id IN ('.$project_ids.')';
			$results = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->result() : false;
			$sub_contracts = [];
			if($results) {
				foreach ($results as $result) {
					$item = new Sub_contract();
					$item->load($result->id);
					$sub_contracts[] = $item;
				}
			}

			foreach ($sub_contracts as $sub_contract) {
				$sub_contract_amount = $sub_contract->certified_amount() - $sub_contract->paid_amount();
				$sub_contracts_table_items[$sub_contract->contract_name][0] = $sub_contract->stakeholder()->stakeholder_name . ' - ' . $sub_contract->contract_name;
				$sub_contracts_table_items[$sub_contract->contract_name]['sub_contractor_id'] = $sub_contract->stakeholder()->{$sub_contract->stakeholder()::DB_TABLE_PK};
				$sub_contracts_table_items[$sub_contract->contract_name]['amount'] = $sub_contract_amount;
			}

			$data['table_items'] = $table_items;
			$data['as_of'] = $as_of;
			$data['triggered'] = $triggered;
			$data['vendors_with_orders'] = $vendors_with_orders;
			$data['vendors_table_items'] = $vendors_table_items;
			$data['sub_contracts_table_items'] = $sub_contracts_table_items;
			$data['sub_contracts'] = $sub_contracts;
			$data['currencies'] = $currencies;
			if ($triggered) {
				$data['print'] = isset($print);
				$html = $this->load->view('reports/project_financial_status_sheet', $data, true);

				$this->load->library('m_pdf');
				//actually, you can pass mPDF parameter on this load() function
				$pdf = $this->m_pdf->load();
				$pdf->AddPage(
					'', // L - landscape, P - portrait
					'', '', '', '',
					15, // margin_left
					15, // margin right
					15, // margin top
					15, // margin bottom
					9, // margin header
					6, '', '', '', '', '', '', '', '', '', 'A3-L'
				); // margin footer
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
				$pdf->Output('Project Financial Status.pdf', 'I');

			} else {
				$json['table_view'] = $this->load->view('reports/project_financial_status_table', $data, true);
				echo json_encode($json);
			}
		} else {
			$data['triggered'] = false;
			$this->load->view('reports/project_financial_status', $data);
		}
	}

	public function vendors_overall_balance()
    {
        $this->load->model(['project', 'vendor', 'currency']);
        $data['title'] = 'Reports | Vendors Overall Balance';
        $triggered = $this->input->post('triggered') == 'true';
        $as_of = $this->input->post('as_of');
        $as_of = $as_of != '' ? $as_of : null;
        $data['currencies'] = $this->currency->get();
        $data['as_of'] = $as_of;
        $data['triggered'] = $triggered;
        $data['vendors'] = $this->vendor->get(0, 0, [], 'vendor_name ASC');
        if (!is_null($as_of)) {
            if ($triggered) {
                $data['print'] = true;
                $html = $this->load->view('reports/vendors_overall_balance_sheet', $data, true);

                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage(
                    '', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6, '', '', '', '', '', '', '', '', '', 'A4-P'
                ); // margin footer
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
                $pdf->Output('Vendors Overall Ballance.pdf', 'I');

            } else {
                $json['table_view'] = $this->load->view('reports/vendors_overall_balance_table', $data, true);
                echo json_encode($json);
            }
        } else {
            $data['triggered'] = false;
            $this->load->view('reports/vendors_overall_balance', $data);
        }
    }

    public function vendors_supply_report(){
        $from = $this->input->post('from');
        $from = $from != '' ? $from : null;
        $to = $this->input->post('to');
        $to = $to != '' ? $to : null;
        $triggered = $this->input->post('triggered') == 'true';
        $data['title'] = 'Reports | Vendors Supply Report';
        if (!is_null($from) && !is_null($to)) {
            $data['from'] = $from;
            $data['to'] = $to;

            $sql = '
                            SELECT
                              vendor_name,
                              COUNT(order_id) AS no_of_orders,
                              COALESCE(
                                  SUM(
                            
                                      (
                                        SELECT COALESCE(SUM(
                                                            quantity*price*(
                                                              SELECT MAX(exchange_rate) FROM exchange_rate_updates
                                                              WHERE purchase_orders.currency_id = exchange_rate_updates.currency_id
                                                              AND update_date <= purchase_orders.issue_date
                                                            )
                                                        ),0) FROM purchase_order_material_items
                                        WHERE purchase_order_material_items.order_id = purchase_orders.order_id
                                      ) + (
                                        SELECT COALESCE(SUM(
                                                            quantity*price*(
                                                              SELECT MAX(exchange_rate) FROM exchange_rate_updates
                                                              WHERE purchase_orders.currency_id = exchange_rate_updates.currency_id
                                                              AND update_date <= purchase_orders.issue_date
                                                            )
                                                        ),0) FROM purchase_order_asset_items
                                        WHERE purchase_order_asset_items.order_id = purchase_orders.order_id
                                      ) + (
                                        SELECT COALESCE(SUM(
                                                            quantity*price*(
                                                              SELECT MAX(exchange_rate) FROM exchange_rate_updates
                                                              WHERE purchase_orders.currency_id = exchange_rate_updates.currency_id
                                                                    AND update_date <= purchase_orders.issue_date
                                                            )
                                                        ),0) FROM purchase_order_service_items
                                        WHERE purchase_order_service_items.order_id = purchase_orders.order_id
                                      ) + freight + inspection_and_other_charges
                            
                                  ), 0
                              )               AS supplied_amount,
                              COALESCE(
                                SUM(
                                    (
                                      SELECT COALESCE(SUM(quantity*material_stocks.price/purchase_order_grns.factor), 0)
                                      FROM material_stocks
                                      LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                                      LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                                      LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                                      WHERE purchase_order_id = purchase_orders.order_id
                                    ) + (
                                      SELECT COALESCE(SUM(book_value/purchase_order_grns.factor) + purchase_order_grns.insurance + purchase_orders.freight + purchase_order_grns.other_charges,0) FROM asset_sub_location_histories
                                      LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                                      LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                                      LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                                      WHERE purchase_order_id = purchase_orders.order_id
                                    )
                                ),0
                              ) AS delivered_amount
                            FROM vendors
                              LEFT JOIN purchase_orders ON vendors.vendor_id = purchase_orders.vendor_id
                              WHERE vendors.active = 1 AND issue_date >= "'.$from.'" AND issue_date <= "'.$to.'"
                            GROUP BY purchase_orders.vendor_id
                            ORDER BY supplied_amount DESC
                            ';

            $query = $this->db->query($sql);
            $results = $query->result();
            $table_items = [];
            foreach ($results as $row) {
                $table_items[] = [
                    'vendor_name'=> $row->vendor_name,
                    'supplied_amount'=>$row->supplied_amount,
                    'delivered_amount'=>$row->delivered_amount
                ];
            }

            $data['table_items'] = $table_items;

            if ($triggered) {
                $data['triggered'] = $triggered;
                $html = $this->load->view('reports/vendors_supply_sheet', $data, true);

                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage(
                    '', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6, '', '', '', '', '', '', '', '', '', 'A4-P'
                ); // margin footer
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
                $pdf->Output('Vendors Supply Report.pdf', 'I');

            } else {
                $data['triggered'] = false;
                echo $this->load->view('reports/vendors_supply_report_table', $data, true);
            }
        } else {
            $this->load->view('reports/vendors_supply_report', $data);
        }
    }

    public function cost_center_payments(){
        $this->load->model('cost_center');
        $cost_center_id = $this->input->post('cost_center_id');
        $cost_center_id = $cost_center_id != 'all' ? $cost_center_id : 'all';
        $from = $this->input->post('from');
        $from = $from != '' ? $from : null;
        $to = $this->input->post('to');
        $to = $to != '' ? $to : null;
        if (!is_null($from) || !is_null($to)) {
            $data['cost_center'] = $cost_center = false;
            if($cost_center_id != "all" && !is_null($cost_center_id)){
                $cost_center = new Cost_center();
                $cost_center->load($cost_center_id);
                $data['cost_center'] = $cost_center;
            }

            $data['cost_center_payments'] = $this->cost_center->cost_center_payments($cost_center_id, $from, $to);
            $data['print'] = $print = $this->input->post('print');
            $data['from'] = $from;
            $data['to'] = $to;

            if($print){
                $html = $this->load->view('reports/cost_center_payments_sheet', $data, true);

                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage(
                    '', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6, '', '', '', '', '', '', '', '', '', 'A4-P'
                ); // margin footer
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
                $pdf->Output('Cost Center Payments.pdf', 'I');
            } else {
                $this->load->view('reports/cost_center_payments_table',$data);
            }

        } else {

            $data['title'] = 'Reports | Cost Center Payments';
            $data['cost_center_options'] = $this->cost_center->dropdown_options();
            $this->load->view('reports/cost_center_payments', $data);
        }
    }

    public function cash_flow(){

        $this->load->model(['project_special_budget','currency','account','cost_center']);

        set_time_limit(86400);
        ini_set('memory_limit', -1);
        $this->load->model('project');
        $data['title'] = "Reports | Project's Income And Expenditures";
        $data['project_options'] = projects_dropdown_options();
        $project_ids = $this->input->post('project_ids');
        $project_ids = is_array($project_ids) ? array_filter($project_ids) : [];
        $data['print'] = $print = $this->input->post('print') == 'true';
        $data['from'] = $from = $this->input->post('from');
        $data['to'] = $to = $this->input->post('to');
        $order_sub_seet = $this->input->post('order_sub_sheet') ? true : false;
        $sub_contract_sub_sheet = $this->input->post('sub_contract_sub_sheet') ? true : false;
        $other_pending_payment_sub_sheet = $this->input->post('other_paending_payment_sub_sheet') ? true : false;
        $payment_sub_sheet = $this->input->post('payment_sub_sheet') ? true : false;
        $othr_admin_costs_sheet = $this->input->post('othr_admin_costs_sheet') ? true : false;
        $all_approved_payments_not_paid = $this->input->post('all_approved_payments_not_paid') ? true : false;

        if($order_sub_seet || $sub_contract_sub_sheet || $other_pending_payment_sub_sheet || $payment_sub_sheet ){
            $project_ids = [$this->input->post('project_ids')];
        }
        if (!empty($project_ids)) {
            $project_ids = !empty($project_ids) ? count($project_ids) > 1 ? implode(',', $project_ids) : implode($project_ids) : null;
            $where = ' project_id IN (' . $project_ids . ')';

            $currencies = $this->currency->get();
            $data['currencies'] = $currencies;

            $table_data = [];
            foreach ($currencies as $currency){
                $currency_id = $currency->{$currency::DB_TABLE_PK};
                $rate_to_native = $currency->rate_to_native();
                $project_special_budgets = $this->project_special_budget->get(0,0,$where.' AND currency_id = '.$currency->{$currency::DB_TABLE_PK});

                foreach ($project_special_budgets as $special_budget){
                    $project = $special_budget->project();
                    $project_total_expenses = $project->total_payments($from, $to);
                    $data['project_total_expenses'] = $project_total_expenses/$rate_to_native;
                    $data['project_payments'] = $project->total_payments($from, $to,true,true);
                    $data['project_id'] = $special_budget->project_id;
                    $data['project_name'] = $project->project_name;
                    $data['currency_symbol'] = $currency->symbol;
                    $payments_pop_up = $print ? '' : $this->load->view('reports/cash_flow/payments_pop_up',$data,true);

                    $project_purchase_orders = $project->purchase_orders();
                    $orders_with_balances = [];
                    $purchase_orders_commitments = 0;
                    foreach ($project_purchase_orders as $order){
                        if($order->status != 'CANCELLED' && $order->status != 'CLOSED') {
                            $cif = $order->cif();
                            $paid_amount = $order->amount_paid(false,true);
                            $other_charges = $order->other_charges();
                            $other_charges_amount = $other_charges->other_charges_cost;
                            $other_charges_in_order_currency =  $this->currency->convert($other_charges->currency_id, $order->currency_id, $other_charges_amount);
                            $balance = ($cif + $other_charges_in_order_currency) - $paid_amount;
                            if (round($balance) > 1) {
                                $purchase_orders_commitments += $balance_in_current_currency = $this->currency->convert($order->currency_id, $currency_id, $balance);
                                $orders_with_balances[] = [
                                    'order_id' => $order->{$order::DB_TABLE_PK},
                                    'order_number' => $order->order_number(),
                                    'vendor_name' => $order->vendor()->vendor_name,
                                    'vendor_id' => $order->vendor_id,
                                    'order_currency_symbol' => $order->currency()->symbol,
                                    'order_value' => $cif,
                                    'value_in_current_currency' => $this->currency->convert($order->currency_id, $currency_id, $cif),
                                    'other_charges_currency_symbol' => 'TSH',
                                    'order_other_charges' => $other_charges_amount,
                                    'other_charges_in_current_currency' => $this->currency->convert($other_charges->currency_id, $currency_id, $other_charges_amount),
                                    'paid_amount' => $paid_amount,
                                    'paid_amount_in_current_currency' => $this->currency->convert($order->currency_id, $currency_id, $paid_amount),
                                    'balance' => $balance,
                                    'balance_in_current_currency' => $balance_in_current_currency
                                ];
                            }
                        }
                    }

                    $data['purchase_order_commitments'] = $purchase_orders_commitments;
                    $data['orders_with_balance'] = $orders_with_balances;

                    $purchase_orders_commitments_pop_up = $print ? '' : $this->load->view('reports/cash_flow/orders_pop_up',$data,'true');

                    if($order_sub_seet == 'true'){
                        $data['title'] = strtoupper($this->input->post('title'));
                        $data['project_name'] = $this->input->post('project_name');
                        $html = $this->load->view('reports/cash_flow/order_sub_sheet', $data, true);

                        $this->load->library('m_pdf');
                        //actually, you can pass mPDF parameter on this load() function
                        $pdf = $this->m_pdf->load();
                        $pdf->AddPage(
                            '', // L - landscape, P - portrait
                            '', '', '', '',
                            15, // margin_left
                            15, // margin right
                            15, // margin top
                            15, // margin bottom
                            9, // margin header
                            6, '', '', '', '', '', '', '', '', '', 'A4-P'
                        ); // margin footer
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
                        $pdf->Output('Order Sub Sheet.pdf', 'I');

                    }

                    ////// Project Sub Contract
                    $project_sub_contractor_certificates = $project->sub_contract_certificates(false,$from,$to);
                    $sub_contractor_wit_certificate = [];
                    $sub_contracts_commitments = 0;
                    foreach ($project_sub_contractor_certificates as $sub_contract_certificate){
                        $certificate_paid_amount = $sub_contract_certificate->paid_amount();
                        $certificate_balance = $sub_contract_certificate->certified_amount - $certificate_paid_amount;
                        $sub_contracts_commitments += $current_certificate_balance = $this->currency->convert(1, $currency_id, $certificate_balance);
                        if($sub_contract_certificate->certified_amount > 0 && $certificate_balance > 0){
                            $sub_contractor_wit_certificate[] = [
                                'certificate_date' => $sub_contract_certificate->certificate_date,
                                'certificate_number' => add_leading_zeros($sub_contract_certificate->certificate_number),
                                'subcontract_description' => $sub_contract_certificate->load_sub_contactor_description(),
                                'certified_amount' => $sub_contract_certificate->certified_amount,
                                'amount_paid' => $sub_contract_certificate->paid_amount(),
                                'current_certificate_balance' => $current_certificate_balance
                            ];
                        }
                    }

                    $data['sub_contracts_commitments'] = $sub_contracts_commitments;
                    $data['other_commitments'] = $project->approved_cash_commitments($currency_id,$from,$to);
                    $data['other_commitments_amount'] = $project->approved_cash_commitments($currency_id,$from,$to,true);
                    $data['sub_contractor_wit_certificate'] = $sub_contractor_wit_certificate;

                    $sub_contracts_pop_up = $print ? '' : $this->load->view('reports/cash_flow/sub_contract_pop_up', $data, 'true');
                    $other_commitments_pop_up = $print ? '' : $this->load->view('reports/cash_flow/pending_payments_pop_up', $data, 'true');

                    if($other_pending_payment_sub_sheet == 'true'){

                        $html = $this->load->view('reports/cash_flow/pending_payments_sub_sheet', $data, true);

                        $this->load->library('m_pdf');
                        //actually, you can pass mPDF parameter on this load() function
                        $pdf = $this->m_pdf->load();
                        $pdf->AddPage(
                            '', // L - landscape, P - portrait
                            '', '', '', '',
                            15, // margin_left
                            15, // margin right
                            15, // margin top
                            15, // margin bottom
                            9, // margin header
                            6, '', '', '', '', '', '', '', '', '', 'A4-P'
                        ); // margin footer
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
                        $pdf->Output('Pending Payments Sub Sheet.pdf', 'I');

                    }

                    if($sub_contract_sub_sheet == 'true'){
                        $data['title'] = strtoupper($this->input->post('title'));
                        $data['project_name'] = $this->input->post('project_name');
                        $html = $this->load->view('reports/cash_flow/sub_contract_sub_sheet', $data, true);

                        $this->load->library('m_pdf');
                        //actually, you can pass mPDF parameter on this load() function
                        $pdf = $this->m_pdf->load();
                        $pdf->AddPage(
                            '', // L - landscape, P - portrait
                            '', '', '', '',
                            15, // margin_left
                            15, // margin right
                            15, // margin top
                            15, // margin bottom
                            9, // margin header
                            6, '', '', '', '', '', '', '', '', '', 'A4-P'
                        ); // margin footer
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
                        $pdf->Output('Sub Contract Sub Seet.pdf', 'I');

                    }

                    if($payment_sub_sheet == 'true'){
                        $data['title'] = strtoupper($this->input->post('title'));
                        $data['project_name'] = $this->input->post('project_name');
                        $html = $this->load->view('reports/cash_flow/payments_sub_sheet', $data, true);

                        $this->load->library('m_pdf');
                        //actually, you can pass mPDF parameter on this load() function
                        $pdf = $this->m_pdf->load();
                        $pdf->AddPage(
                            '', // L - landscape, P - portrait
                            '', '', '', '',
                            15, // margin_left
                            15, // margin right
                            15, // margin top
                            15, // margin bottom
                            9, // margin header
                            6, '', '', '', '', '', '', '', '', '', 'A4-P'
                        ); // margin footer
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
                        $pdf->Output('Cost Center Payments.pdf', 'I');

                    }

                    $table_data[$currency_id][] = [
                        'currency_name' => $currency->currency_name,
                        'project_name' => $project->project_name,
                        'project_id' => $special_budget->project_id,
                        'material_budget' => $special_budget->material_amount,
                        'labour_budget' => $special_budget->labour_amount,
                        'expected_income' => $project->certified_amount(null,$to) - $project->certificate_paid_amount(null,$to),
                        'paid_amount' => $project->certificate_paid_amount(null,$to),
                        'project_total_expenses' => $project_total_expenses/$rate_to_native,
                        'sub_contracts_commitments' => $sub_contracts_commitments,
                        'purchase_orders_commitments' => $purchase_orders_commitments,
                        'orders_pop_up' => $purchase_orders_commitments_pop_up,
                        'sub_contract_pop_up' => $sub_contracts_pop_up,
                        'other_commitments_pop_up' => $other_commitments_pop_up,
                        'other_commitments_amount' => $project->approved_cash_commitments($currency_id,$from,$to,true),
                        'payments_pop_up'=> $payments_pop_up
                    ];
                }
            }


            #----- Administrative Cost -------- #

            $date_begin = explode('-', $from);
            $start_date = $date_begin[0].'-'.$date_begin[1];
            $date_end = explode('-', $to);
            $ending_date = $date_end[0].'-'.$date_end[1];

            $sql = 'SELECT * FROM payroll WHERE payroll_for >= "'.$start_date.'" AND payroll_for <= "'.$ending_date.'" AND status = "Approved"';
            $query = $this->db->query($sql);
            $payroll_results = $query->result();

            $data['payroll_data'] = false;
            if($payroll_results){
                $number_of_payrolls = 0;
                foreach ($payroll_results as $result){
                    $number_of_payrolls++;
                    $payroll_id = $result->id;

                    $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
                    $query = $this->db->query($sql);
                    $payroll_basic_info = $query->result();

                    $total_salary = 0;
                    foreach ($payroll_basic_info as $info){
                        $total_salary += $info->basic_salary;
                    }

                    $sql = 'SELECT * FROM payroll_employee_allowances GROUP BY allowance_name';
                    $query = $this->db->query($sql);
                    $all_allowances = $query->result();

                    $sql = 'SELECT * FROM payroll_employer_deductions GROUP BY deduction_name';
                    $query = $this->db->query($sql);
                    $all_deductions = $query->result();

                    $data['payroll_data'][] = [
                        'payroll_id' => $payroll_id,
                        'department_id' => $result->department_id,
                        'payroll_for' => $result->payroll_for,
                        'total_salary' => $total_salary,
                        'payroll_allowances' => $this->payroll_allowances($payroll_id),
                        'payroll_deductions' => $this->payroll_deductions($payroll_id),
                        'total_payroll_cost' => $this->sum_payroll_costs($payroll_id)
                    ];

                }


                $data['all_allowances'] = $all_allowances;
                $data['all_deductions'] = $all_deductions;
                $data['number_of_payrolls'] = $number_of_payrolls;
            }



            #----- Other-dministrative Cost -------- #
            $cost_centers = $this->cost_center->get();
            $data['cost_centers'] = $cost_centers;
            $cc_with_approved_requests = $cost_center_payments = $cost_center_amount = $othr_admin_costs_pop_up_main = [];
            foreach($cost_centers as $cost_center) {
                $total_per_cc = 0;
                $data['cost_center'] = $cost_center;

                # --- cost center approved payments ----- #
                $sql = 'SELECT * FROM (
                          SELECT approved_request_table.purchase_order_payment_request_id AS requisition_id, approved_request_table.id AS requisition_approval_id, approved_amount, payment_voucher_items.payment_voucher_id,
                          amount AS paid_amount, exchange_rate, purchase_order_payment_requests.currency_id, approval_date AS approved_date, "order_payment" AS nature
                          FROM purchase_order_payment_request_approval_invoice_items 
                          LEFT JOIN purchase_order_payment_request_approvals AS approved_request_table ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = approved_request_table.id
                          LEFT JOIN purchase_order_payment_requests ON approved_request_table.purchase_order_payment_request_id = purchase_order_payment_requests.id
                          LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                          LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                          LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                          LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                          LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                          LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
                          WHERE cost_center_id = '.$cost_center->{$cost_center::DB_TABLE_PK}.' AND is_final = 1 
                          AND purchase_order_payment_requests.status = "APPROVED" 
                          AND approved_request_table.approval_date >= "'.$from.'" 
                          AND approved_request_table.approval_date <= "'.$to.'" AND (
                            (
                                SELECT vendor_invoices.invoice_id FROM vendor_invoices
                                LEFT JOIN invoices ON vendor_invoices.invoice_id = invoices.id
                                LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                                WHERE purchase_order_invoices.purchase_order_id = purchase_orders.order_id AND vendor_invoices.vendor_id = purchase_orders.vendor_id
                                AND purchase_order_invoices.invoice_id = purchase_order_payment_request_invoice_items.invoice_id
                            ) IS NOT NULL
                            OR (
                                SELECT invoice_id FROM grn_invoices
                                LEFT JOIN invoices ON grn_invoices.invoice_id = invoices.id
                                LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                                WHERE purchase_order_grns.purchase_order_id = purchase_orders.order_id AND grn_invoices.invoice_id = purchase_order_payment_request_invoice_items.invoice_id 
                            ) IS NOT NULL
                          )
                          
                          UNION
                                           
                          SELECT approved_request_table.purchase_order_payment_request_id AS requisition_id, approved_request_table.id AS requisition_approval_id,approved_amount, payment_vouchers.payment_voucher_id,
                          (
                            SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items WHERE payment_voucher_id = payment_vouchers.payment_voucher_id
                          ) AS paid_amount, exchange_rate, purchase_order_payment_requests.currency_id, approval_date AS approved_date, "order_payment" AS nature
                          FROM purchase_order_payment_request_approval_cash_items
                          LEFT JOIN purchase_order_payment_request_approvals AS approved_request_table ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = approved_request_table.id
                          LEFT JOIN purchase_order_payment_requests ON approved_request_table.purchase_order_payment_request_id = purchase_order_payment_requests.id
                          LEFT JOIN purchase_order_payment_request_cash_items ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_cash_item_id = purchase_order_payment_request_cash_items.id
                          LEFT JOIN purchase_order_payment_request_approval_payment_vouchers ON approved_request_table.id = purchase_order_payment_request_approval_payment_vouchers.purchase_order_payment_request_approval_id
                          LEFT JOIN payment_vouchers ON purchase_order_payment_request_approval_payment_vouchers.payment_voucher_id = payment_vouchers.payment_voucher_id
                          LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                          LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
                          WHERE cost_center_id = '.$cost_center->{$cost_center::DB_TABLE_PK}.' AND is_final = 1 
                          AND purchase_order_payment_requests.status = "APPROVED" 
                          AND approved_request_table.approval_date >= "'.$from.'" 
                          AND approved_request_table.approval_date <= "'.$to.'"
 
                          UNION
        
                          SELECT main_table.requisition_id, main_table.id AS requisition_approval_id, (
                                  (
                                          (
                                              SELECT COALESCE(SUM(approved_quantity *approved_rate),0) AS amount_approved
                                              FROM requisition_approval_asset_items
                                              WHERE requisition_approval_asset_items.requisition_approval_id =main_table.id AND source_type = "cash"
        
                                          ) + (
                                              SELECT COALESCE(SUM( approved_quantity * approved_rate ), 0) AS amount_approved
                                              FROM requisition_approval_material_items
                                              WHERE requisition_approval_material_items.requisition_approval_id = main_table.id AND source_type = "cash"
        
                                          ) + (
        
                                              SELECT COALESCE(SUM( approved_quantity * approved_rate ), 0) AS amount_approved
                                              FROM requisition_approval_service_items
                                              WHERE requisition_approval_service_items.requisition_approval_id = main_table.id AND source_type = "cash"
        
                                          ) + (
        
                                              SELECT COALESCE(SUM( approved_quantity * approved_rate ), 0) AS amount_approved
                                              FROM requisition_approval_cash_items
                                              WHERE requisition_approval_cash_items.requisition_approval_id = main_table.id
                                          )
                                      ) * (
                                      CASE
                                          WHEN main_table.vat_inclusive = "VAT PRICED" OR main_table.vat_inclusive IS NULL
                                              THEN 1
                                          ELSE 1.18
                                          END
                                      )
                              ) AS approved_amount, requisition_approval_payment_vouchers.payment_voucher_id, (
                                     SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items WHERE payment_voucher_id = requisition_approval_payment_vouchers.payment_voucher_id
                                 ) AS paid_amount, exchange_rate, requisitions.currency_id, approved_date, "requisition" AS nature
                          FROM requisition_approvals AS main_table
                          LEFT JOIN requisitions ON main_table.requisition_id = requisitions.requisition_id
                          LEFT JOIN requisition_approval_payment_vouchers ON main_table.id = requisition_approval_payment_vouchers.requisition_approval_id
                          LEFT JOIN payment_vouchers ON requisition_approval_payment_vouchers.payment_voucher_id = payment_vouchers.payment_voucher_id
                          LEFT JOIN cost_center_requisitions ON requisitions.requisition_id = cost_center_requisitions.requisition_id
                          WHERE cost_center_id = '.$cost_center->{$cost_center::DB_TABLE_PK}.' AND is_final = 1 AND main_table.approved_date >= "'.$from.'" AND main_table.approved_date <= "'.$to.'"
        
                    ) AS cost_center_approved_payments ORDER BY requisition_approval_id DESC';
                $query = $this->db->query($sql);
                $cost_center_approved_payments = $query->num_rows() > 0 ? $query->result() : false;
                if($cost_center_approved_payments){
                    $total_approved_payments_per_cost_center = $total_paid_amount_per_cost_center = 0;
                    $this->load->model('currency');
                    foreach ($cost_center_approved_payments as $approved_payment){
                        $request_currency = new Currency();
                        $request_currency->load($approved_payment->currency_id);
                        $total_approved_payments_per_cost_center += ($approved_payment->approved_amount * $request_currency->rate_to_native());
                        $total_paid_amount_per_cost_center += ($approved_payment->paid_amount * $approved_payment->exchange_rate);
                    }

                    $cc_with_approved_requests[$cost_center->cost_center_name][] = $cost_center;
                    $cost_center_approved_amount[$cost_center->cost_center_name] =  $total_approved_payments_per_cost_center;
                    $approved_data['cost_center_approved_payments'] = $cost_center_approved_payments;
                    $approved_data['cost_center_name'] = $cost_center->cost_center_name;
                    $approved_data['cost_center_id'] = $cost_center->{$cost_center::DB_TABLE_PK};
                    $approved_data['total_approved_amount'] = $total_approved_payments_per_cost_center - $total_paid_amount_per_cost_center;
                    $approved_data['total_approved_requests'] = $total_approved_payments_per_cost_center;
                    $approved_data['total_paid_requests'] = $total_paid_amount_per_cost_center;
                    $approved_data['print_sub_sheet'] = $this->input->post('print_sub_sheet') ? true : false;
                    $approved_data['from'] = $this->input->post('from');
                    $approved_data['to'] = $this->input->post('to');

                    $approved_payments_pop_up[$cost_center->cost_center_name] = $print ? '' : $this->load->view('reports/cash_flow/administrative_costs_comitments_pop_up', $approved_data, true);

                }

                $per_cost_center_payments = $this->cost_center->cost_center_payments_accountwise($cost_center->{$cost_center::DB_TABLE_PK}, $from, $to);
                $cost_center_payments[$cost_center->cost_center_name] = false;
                if ($per_cost_center_payments) {
                    foreach ($per_cost_center_payments as $per_cost_center_payment) {
                        $dbt_account = new Account();
                        $dbt_account->load($per_cost_center_payment->debit_account_id);
                        $data['dbt_account'] = $dbt_account;
                        $data['symbol'] = $per_cost_center_payment->symbol;
                        $data['amount_in_basecurrency'] = $per_cost_center_payment->amount_in_basecurrency;
                        $othr_admin_costs_pop_up = $print ? '' : $this->load->view('reports/cash_flow/othr_admin_costs_pop_up', $data, true);
                        $total_per_cc += $data['amount_in_basecurrency'];

                        $cost_center_payments[$cost_center->cost_center_name][] = [
                            'debit_account_id' => $per_cost_center_payment->debit_account_id,
                            'cost_type' => $per_cost_center_payment->cost_type,
                            'amount' => $per_cost_center_payment->amount,
                            'amount_in_basecurrency' => $data['amount_in_basecurrency'],
                            'othr_admin_costs_pop_up' => $othr_admin_costs_pop_up
                        ];
                    }

                    $data['total_per_cc'] = $total_per_cc;
                    $cost_center_amount[$cost_center->cost_center_name] = $total_per_cc;
                } else {
                    $data['total_per_cc'] = $total_per_cc;
                    $cost_center_amount[$cost_center->cost_center_name] = 0;
                }
                $data['cost_center_payments'] = $cost_center_payments;
                $othr_admin_costs_pop_up_main[$cost_center->cost_center_name] = $print ? '' : $this->load->view('reports/cash_flow/othr_admin_costs_pop_up_main', $data, true);
            }
            $data['cc_with_approved_requests'] = $cc_with_approved_requests;
            $data['approved_payments_pop_up'] = $approved_payments_pop_up;
            $data['cost_center_approved_amount'] = $cost_center_approved_amount;
            $data['cost_center_amount'] = $cost_center_amount;
            $data['othr_admin_costs_pop_up_main'] = $othr_admin_costs_pop_up_main;
            $data['table_data'] = $table_data;

            if ($print) {
                $html = $this->load->view('reports/cash_flow/cash_flow_sheet', $data, true);

                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage(
                    'L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6, '', '', '', '', '', '', '', '', '', 'A4-L'
                ); // margin footer

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
                $pdf->Output('Project Income And Expenditure Report .pdf', 'I');
            } else {
                $json['table_view'] = $this->load->view('reports/cash_flow/cash_flow_table', $data, true);
                $json['from'] = $data['from'];
                $json['to'] = $data['to'];
                echo json_encode($json);
            }

        }else if($othr_admin_costs_sheet){

            $cost_center = new Cost_center();
            $cost_center->load($this->input->post('cost_center_id'));
            $per_cost_center_payments = $this->cost_center->cost_center_payments_accountwise($cost_center->{$cost_center::DB_TABLE_PK}, $from, $to);

            $total_per_cc = 0;
            $data['cost_center'] = $cost_center;
            if ($per_cost_center_payments) {
                $cost_center_payments[$cost_center->cost_center_name] = false;
                foreach ($per_cost_center_payments as $per_cost_center_payment) {
                    $dbt_account = new Account();
                    $dbt_account->load($per_cost_center_payment->debit_account_id);
                    $data['dbt_account'] = $dbt_account;
                    $data['symbol'] = $per_cost_center_payment->symbol;
                    $data['amount_in_basecurrency'] = $per_cost_center_payment->amount_in_basecurrency;
                    $othr_admin_costs_pop_up = $print ? '' : $this->load->view('reports/cash_flow/othr_admin_costs_pop_up', $data, true);
                    $total_per_cc += $data['amount_in_basecurrency'];

                    $cost_center_payments[$cost_center->cost_center_name][] = [
                        'debit_account_id' => $per_cost_center_payment->debit_account_id,
                        'cost_type' => $per_cost_center_payment->cost_type,
                        'amount' => $per_cost_center_payment->amount,
                        'amount_in_basecurrency' => $data['amount_in_basecurrency'],
                        'othr_admin_costs_pop_up' => $othr_admin_costs_pop_up
                    ];

                }

                $data['total_per_cc'] = $total_per_cc;
                $data['cost_center_payments'] = $cost_center_payments;

            }

            $html = $this->load->view('reports/cash_flow/othr_admin_costs_main_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                '', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6, '', '', '', '', '', '', '', '', '', 'A4-P'
            ); // margin footer
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
            $pdf->Output('Other Admin Costs Sheet.pdf', 'I');

        } else if($all_approved_payments_not_paid){
            $cost_center = new Cost_center();
            $cost_center->load($this->input->post('cost_center_id'));
            $per_cost_center_payments = $this->cost_center->cost_center_payments_accountwise($cost_center->{$cost_center::DB_TABLE_PK}, $from, $to);

            $total_per_cc = 0;
            $data['cost_center'] = $cost_center;
            if ($per_cost_center_payments) {
                $cost_center_payments[$cost_center->cost_center_name] = false;
                foreach ($per_cost_center_payments as $per_cost_center_payment) {
                    $dbt_account = new Account();
                    $dbt_account->load($per_cost_center_payment->debit_account_id);
                    $data['dbt_account'] = $dbt_account;
                    $data['symbol'] = $per_cost_center_payment->symbol;
                    $data['amount_in_basecurrency'] = $per_cost_center_payment->amount_in_basecurrency;
                    $othr_admin_costs_pop_up = $print ? '' : $this->load->view('reports/cash_flow/othr_admin_costs_pop_up', $data, true);
                    $total_per_cc += $data['amount_in_basecurrency'];

                    $cost_center_payments[$cost_center->cost_center_name][] = [
                        'debit_account_id' => $per_cost_center_payment->debit_account_id,
                        'cost_type' => $per_cost_center_payment->cost_type,
                        'amount' => $per_cost_center_payment->amount,
                        'amount_in_basecurrency' => $data['amount_in_basecurrency'],
                        'othr_admin_costs_pop_up' => $othr_admin_costs_pop_up
                    ];
                }

                $data['total_per_cc'] = $total_per_cc;
                $data['cost_center_payments'] = $cost_center_payments;

                # --- cost center approved payments print ----- #
                $sql = 'SELECT * FROM (
                          SELECT approved_request_table.purchase_order_payment_request_id AS requisition_id, approved_request_table.id AS requisition_approval_id, approved_amount, payment_voucher_items.payment_voucher_id,
                          amount AS paid_amount, exchange_rate, purchase_order_payment_requests.currency_id, approval_date AS approved_date, "order_payment" AS nature
                          FROM purchase_order_payment_request_approval_invoice_items 
                          LEFT JOIN purchase_order_payment_request_approvals AS approved_request_table ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = approved_request_table.id
                          LEFT JOIN purchase_order_payment_requests ON approved_request_table.purchase_order_payment_request_id = purchase_order_payment_requests.id
                          LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                          LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                          LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                          LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                          LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                          LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
                          WHERE cost_center_id = '.$cost_center->{$cost_center::DB_TABLE_PK}.' AND is_final = 1 
                          AND purchase_order_payment_requests.status = "APPROVED" 
                          AND approved_request_table.approval_date >= "'.$from.'" 
                          AND approved_request_table.approval_date <= "'.$to.'" AND (
                            (
                                SELECT vendor_invoices.invoice_id FROM vendor_invoices
                                LEFT JOIN invoices ON vendor_invoices.invoice_id = invoices.id
                                LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id
                                WHERE purchase_order_invoices.purchase_order_id = purchase_orders.order_id AND vendor_invoices.vendor_id = purchase_orders.vendor_id
                                AND purchase_order_invoices.invoice_id = purchase_order_payment_request_invoice_items.invoice_id
                            ) IS NOT NULL
                            OR (
                                SELECT invoice_id FROM grn_invoices
                                LEFT JOIN invoices ON grn_invoices.invoice_id = invoices.id
                                LEFT JOIN goods_received_notes ON grn_invoices.grn_id = goods_received_notes.grn_id
                                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                                WHERE purchase_order_grns.purchase_order_id = purchase_orders.order_id AND grn_invoices.invoice_id = purchase_order_payment_request_invoice_items.invoice_id 
                            ) IS NOT NULL
                          )
                          
                          UNION
                                           
                          SELECT approved_request_table.purchase_order_payment_request_id AS requisition_id, approved_request_table.id AS requisition_approval_id,approved_amount, payment_vouchers.payment_voucher_id,
                          (
                            SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items WHERE payment_voucher_id = payment_vouchers.payment_voucher_id
                          ) AS paid_amount, exchange_rate, purchase_order_payment_requests.currency_id, approval_date AS approved_date, "order_payment" AS nature
                          FROM purchase_order_payment_request_approval_cash_items
                          LEFT JOIN purchase_order_payment_request_approvals AS approved_request_table ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_approval_id = approved_request_table.id
                          LEFT JOIN purchase_order_payment_requests ON approved_request_table.purchase_order_payment_request_id = purchase_order_payment_requests.id
                          LEFT JOIN purchase_order_payment_request_cash_items ON purchase_order_payment_request_approval_cash_items.purchase_order_payment_request_cash_item_id = purchase_order_payment_request_cash_items.id
                          LEFT JOIN purchase_order_payment_request_approval_payment_vouchers ON approved_request_table.id = purchase_order_payment_request_approval_payment_vouchers.purchase_order_payment_request_approval_id
                          LEFT JOIN payment_vouchers ON purchase_order_payment_request_approval_payment_vouchers.payment_voucher_id = payment_vouchers.payment_voucher_id
                          LEFT JOIN purchase_orders ON purchase_order_payment_requests.purchase_order_id = purchase_orders.order_id
                          LEFT JOIN cost_center_purchase_orders ON purchase_orders.order_id = cost_center_purchase_orders.purchase_order_id
                          WHERE cost_center_id = '.$cost_center->{$cost_center::DB_TABLE_PK}.' AND is_final = 1 
                          AND purchase_order_payment_requests.status = "APPROVED" 
                          AND approved_request_table.approval_date >= "'.$from.'" 
                          AND approved_request_table.approval_date <= "'.$to.'"
 
                          UNION
        
                          SELECT main_table.requisition_id, main_table.id AS requisition_approval_id, (
                                  (
                                          (
                                              SELECT COALESCE(SUM(approved_quantity *approved_rate),0) AS amount_approved
                                              FROM requisition_approval_asset_items
                                              WHERE requisition_approval_asset_items.requisition_approval_id =main_table.id AND source_type = "cash"
        
                                          ) + (
                                              SELECT COALESCE(SUM( approved_quantity * approved_rate ), 0) AS amount_approved
                                              FROM requisition_approval_material_items
                                              WHERE requisition_approval_material_items.requisition_approval_id = main_table.id AND source_type = "cash"
        
                                          ) + (
        
                                              SELECT COALESCE(SUM( approved_quantity * approved_rate ), 0) AS amount_approved
                                              FROM requisition_approval_service_items
                                              WHERE requisition_approval_service_items.requisition_approval_id = main_table.id AND source_type = "cash"
        
                                          ) + (
        
                                              SELECT COALESCE(SUM( approved_quantity * approved_rate ), 0) AS amount_approved
                                              FROM requisition_approval_cash_items
                                              WHERE requisition_approval_cash_items.requisition_approval_id = main_table.id
                                          )
                                      ) * (
                                      CASE
                                          WHEN main_table.vat_inclusive = "VAT PRICED" OR main_table.vat_inclusive IS NULL
                                              THEN 1
                                          ELSE 1.18
                                          END
                                      )
                              ) AS approved_amount, requisition_approval_payment_vouchers.payment_voucher_id, (
                                     SELECT COALESCE(SUM(amount),0) FROM payment_voucher_items WHERE payment_voucher_id = requisition_approval_payment_vouchers.payment_voucher_id
                                 ) AS paid_amount, exchange_rate, requisitions.currency_id, approved_date, "requisition" AS nature
                          FROM requisition_approvals AS main_table
                          LEFT JOIN requisitions ON main_table.requisition_id = requisitions.requisition_id
                          LEFT JOIN requisition_approval_payment_vouchers ON main_table.id = requisition_approval_payment_vouchers.requisition_approval_id
                          LEFT JOIN payment_vouchers ON requisition_approval_payment_vouchers.payment_voucher_id = payment_vouchers.payment_voucher_id
                          LEFT JOIN cost_center_requisitions ON requisitions.requisition_id = cost_center_requisitions.requisition_id
                          WHERE cost_center_id = '.$cost_center->{$cost_center::DB_TABLE_PK}.' AND is_final = 1 AND main_table.approved_date >= "'.$from.'" AND main_table.approved_date <= "'.$to.'"
        
                    ) AS cost_center_approved_payments ORDER BY requisition_approval_id DESC';
                $query = $this->db->query($sql);
                $cost_center_approved_payments = $query->num_rows() > 0 ? $query->result() : false;

                if($cost_center_approved_payments != ''){
                    $total_approved_payments_per_cost_center = $total_paid_amount_per_cost_center = 0;
                    $this->load->model('currency');
                    foreach ($cost_center_approved_payments as $approved_payment){
                        $rate = new Currency();
                        $rate->load($approved_payment->currency_id);
                        $total_approved_payments_per_cost_center += $approved_payment->approved_amount * $rate->rate_to_native();
                        $total_paid_amount_per_cost_center += $approved_payment->paid_amount * $approved_payment->exchange_rate;
                    }
                    $cost_center_approved_amount[$cost_center->cost_center_name] =  $total_approved_payments_per_cost_center;
                    $approved_data['cost_center_approved_payments'] = $cost_center_approved_payments;
                    $approved_data['cost_center_name'] = $cost_center->cost_center_name;
                    $approved_data['cost_center_id'] = $cost_center->{$cost_center::DB_TABLE_PK};
                    $approved_data['total_approved_amount'] = $total_approved_payments_per_cost_center - $total_per_cc;
                    $approved_data['total_approved_requests'] = $total_approved_payments_per_cost_center;
                    $approved_data['total_paid_requests'] = $total_paid_amount_per_cost_center;
                    $approved_data['print_sub_sheet'] =  true;
                    $approved_data['from'] = $this->input->post('from');
                    $approved_data['to'] = $this->input->post('to');
                }


            }

            $html = $this->load->view('reports/cash_flow/administrative_costs_comitments_sub_sheet', $approved_data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                '', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6, '', '', '', '', '', '', '', '', '', 'A4-P'
            ); // margin footer
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
            $pdf->Output('Other Admin Costs Sheet.pdf', 'I');

        } else {
            $project_special_budgets = $this->project_special_budget->get();
            $project_options = [];
            foreach ($project_special_budgets as $special_budget){
                $project_options[$special_budget->project_id] = $special_budget->project()->project_name;
            }
            $data['project_options'] = $project_options;
            $this->load->view('reports/cash_flow/index', $data);
        }
    }

    public function payroll_allowances($payroll_id)
    {
        $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id.' GROUP BY allowance_name';
        $query = $this->db->query($sql);
        $all_allowance = $query->result();

        foreach ($all_allowance as $allowance){

            $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id.' AND allowance_name = "'.$allowance->allowance_name.'"';
            $query = $this->db->query($sql);
            $found_allowances = $query->result();
            $total_allowances = 0;

            foreach ($found_allowances as $item){
                $total_allowances += $item->allowance_amount;
            }

            $data[explode(' ',$allowance->allowance_name)[0]] = $total_allowances;
        }

        return $data;

    }

    public function payroll_deductions($payroll_id)
    {
        $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' GROUP BY deduction_name';
        $query = $this->db->query($sql);
        $all_deduction = $query->result();

        foreach ($all_deduction as $deduction){

            $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id.' AND deduction_name = "'.$deduction->deduction_name.'"';
            $query = $this->db->query($sql);
            $found_deductions = $query->result();

            $total_deductions = 0;
            foreach ($found_deductions as $item){
                $total_deductions += $item->deduction_amount;
            }

            $data[explode(' ', $deduction->deduction_name)[0]] = $total_deductions;

        }
        return $data;

    }

    public function sum_payroll_costs($payroll_id)
    {
        $sql = 'SELECT * FROM payroll_employee_basic_info WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $all_info = $query->result();

        $total_salary = 0;
        foreach ($all_info as $item){
            $total_salary += $item->basic_salary;
        }

        $sql = 'SELECT * FROM payroll_employee_allowances WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $all_allowances = $query->result();

        $total_allowance = 0;
        foreach ($all_allowances as $item){
            $total_allowance += $item->allowance_amount;
        }

        $sql = 'SELECT * FROM payroll_employer_deductions WHERE payroll_id = '.$payroll_id;
        $query = $this->db->query($sql);
        $all_deductions = $query->result();

        $total_deductions = 0;
        foreach ($all_deductions as $item){
            $total_deductions += $item->deduction_amount;
        }

        return $total_salary + $total_allowance + $total_deductions;
    }

    public function services(){
        $client_ids = $this->input->post('client_ids');
        $print = $this->input->post('print') ? true : false;
        $client_ids = is_array($client_ids) ? array_filter($client_ids) : [];
        $generate = $this->input->post('generate') ? true : false;

        if($generate || $print){

            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $data['from'] = $from;
            $data['to'] = $to;

            $this->load->model(['client','maintenance_service','currency']);

            if(!empty($client_ids)){
                $client_ids = !empty($client_ids) ? count($client_ids) > 1 ? implode(',', $client_ids) : implode($client_ids) : null;
                $where = ' WHERE client_id IN (' . $client_ids . ') AND ( service_date >= "'.$from.'" AND service_date <= "'.$to.'" )';
            }else{
                $where = ' WHERE ( service_date >= "'.$from.'" AND service_date <= "'.$to.'" )';
            }

            $sql = 'SELECT service_id, service_date,  maintenance_services.currency_id, maintenance_services.client_id, location, remarks, currencies.symbol AS currency_symbol, maintenance_services.remarks, stakeholders.stakeholder_name AS client_name FROM maintenance_services
                LEFT JOIN stakeholders ON maintenance_services.client_id = stakeholders.stakeholder_id
                LEFT JOIN currencies ON maintenance_services.currency_id = currencies.currency_id '.$where;

            $query = $this->db->query($sql);
            $results = $query->result();


            $services = [];
            foreach ($results as $result){
                $maintenance_service =  new Maintenance_service();
                $maintenance_service->load($result->service_id);

                $currency = new Currency();
                $currency->load($result->currency_id);
                $native_cost = $currency->rate_to_native() * $maintenance_service->maintenance_cost();

                $sql = 'SELECT * FROM maintenance_invoices WHERE service_id = '.$result->service_id;
                $query = $this->db->query($sql);
                $found_invoice = $query->result();
                $found_paid_service = null;

                if($found_invoice){

                    $sql = 'SELECT * FROM maintenance_service_receipts WHERE maintenance_service_id = '.$found_invoice[0]->service_id;
                    $query = $this->db->query($sql);
                    $found_paid_service = $query->result();


                }

                $services[] = [
                    'service_id' => $result->service_id,
                    'service_number' => $maintenance_service->maintenance_services_no(),
                    'invoice_number' => $found_invoice ? $found_invoice[0]->outgoing_invoice_id : null,
                    'paid_invoice' => $found_paid_service ? $found_paid_service[0]->receipt_id : null,
                    'service_date' => $result->service_date,
                    'currency_id' => $result->currency_id,
                    'currency_symbol' => $result->currency_symbol,
                    'location' => $result->location,
                    'remarks' => $result->remarks,
                    'client_name' => $result->client_name,
                    'cost' => $maintenance_service->maintenance_cost(),
                    'native_cost' => $native_cost
                ];
            }

            $data['table_data'] = $services;

            if($print){
                $data['print'] = true;
                $html = $this->load->view('reports/services/service_sheet', $data, true);

                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage(
                    'L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6, '', '', '', '', '', '', '', '', '', 'A4-L'
                ); // margin footer

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
                $pdf->Output('Project Income And Expenditure Report .pdf', 'I');
            }else{
                $data['print'] = false;
                $json['table_view'] = $this->load->view('reports/services/services_table', $data, true);
                $json['from'] = $data['from'];
                $json['to'] = $data['to'];
                echo json_encode($json);

            }


        }else{
            $this->load->model('stakeholder');
            $data['client_options'] = $this->stakeholder->dropdown_options();
            $this->load->view('reports/services/index', $data);
        }
    }

	public function print_financial_status_report_pop_ups(){
		$this->load->model(['project','currency']);
		$pop_up_type = $this->input->post('pop_up_type');
		if($pop_up_type == 'unreceived_goods') {
			$as_of = $this->input->post('as_of');
			$project = $this->input->post('project');
			$orders = $this->input->post('project_orders');
			$goods = $this->input->post('unreceived_goods');
			$currency = $this->input->post('native_currency');
			$data['as_of'] = $as_of;
			$data['project'] = unserialize(urldecode($project));
			$data['project_orders'] = unserialize(urldecode($orders));
			$data['unreceived_goods'] = unserialize(urldecode($goods));
			$data['native_currency'] = unserialize(urldecode($currency));
			$view_sheet = 'reports/project_financial_status_unreceived_goods_pop_up_sheet';
			$print_layout = 'A4-P';
		} else if($pop_up_type == 'material_movement') {
			$as_of = $this->input->post('as_of');
			$project = $this->input->post('project');
			$items = $this->input->post('items');
			$project_material_items = $this->input->post('project_material_items');
			$data['as_of'] = $as_of;
			$data['project'] = unserialize(urldecode($project));
			$data['items'] = unserialize(urldecode($items));
			$data['project_material_items'] = unserialize(urldecode($project_material_items));
			$view_sheet = 'reports/project_financial_status_project_material_items_pop_up_sheet';
			$print_layout = 'A3-L';

		}

		$html = $this->load->view($view_sheet,$data,true);
		//load mPDF library
		$this->load->library('m_pdf');
		//actually, you can pass mPDF parameter on this load() function
		$pdf = $this->m_pdf->load();
		$pdf->AddPage(
			'', // L - landscape, P - portrait
			'', '', '', '',
			15, // margin_left
			15, // margin right
			15, // margin top
			15, // margin bottom
			9, // margin header
			6, '', '', '', '', '', '', '', '', '', $print_layout
		); // margin footer
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
		$pdf->SetFooter($footercontents);
		$pdf->WriteHTML($html);
		//$this->mpdf->Output($file_name, 'D'); // download force

		$pdf->Output($pop_up_type.'.pdf', 'I'); // view in the explorer
	}



}
