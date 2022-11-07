<?php

class Outgoing_invoice extends MY_Model
{
    const DB_TABLE = 'outgoing_invoices';
    const DB_TABLE_PK = 'id';

    public $invoice_date;
    public $due_date;
    public $reference;
    public $invoice_no;
    public $invoice_to;
    public $vat_percentage;
    public $currency_id;
    public $vat_inclusive;
    public $payment_terms;
    public $bank_details;
    public $notes;


    public function invoice_to()
    {
        $this->load->model('stakeholder');
        $client = new Stakeholder();
        $client->load($this->invoice_to);
        return $client;
    }

    public function creator()
    {
        return 'created_by';
    }

    public function next_invoice_no()
    {
        $last_invoice = $this->get(0, 0, '', 'id DESC');
        $last_invoice = array_shift($last_invoice);
        if ($last_invoice) {
            return 'INV-' . date('Ymd') . '/' . add_leading_zeros($last_invoice->id);
        } else {
            return 'INV-' . date('Ymd') . '/--';
        }
    }

    public function outgoing_inv_number()
    {
        return 'INV/' . add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function currency()
    {
        $this->load->model('currency');
        $currency =  new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function created_by()
    {
        $this->load->model('employee');
        $employee =  new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function outgoing_invoice_amount()
    {
        $this->load->model('outgoing_invoice_item');
        $invoice_items  = $this->outgoing_invoice_item->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        $amount = 0;
        foreach ($invoice_items as $invoice_item) {
            $amount += ($invoice_item->quantity * $invoice_item->rate);
        }
        return $amount;
    }

    public function total_amount_vat_inclusive()
    {
        return $this->outgoing_invoice_amount() + $this->vat_amount();
    }

    public function paid_amount()
    {
        $sql = 'SELECT (
                    (
                        SELECT COALESCE(SUM(withheld_amount),0) FROM withholding_taxes
                        LEFT JOIN receipt_items ON withholding_taxes.receipt_item_id = receipt_items.id
                        LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                        WHERE invoice_id = ' . $this->{$this::DB_TABLE_PK} . '
                    ) + (
                        SELECT COALESCE(SUM(amount),0) AS paid_amount FROM receipt_items
                        LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                        WHERE invoice_id = ' . $this->{$this::DB_TABLE_PK} . '
                    )
                ) AS paid_amount';

        $query = $this->db->query($sql);
        return $query->row()->paid_amount;
    }

    public function unpaid_balance()
    {
        return ($this->outgoing_invoice_amount() + $this->vat_amount()) - $this->paid_amount();
    }

    public function invoice_items()
    {
        $this->load->model('outgoing_invoice_item');
        return $this->outgoing_invoice_item->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function list($limit, $start, $keyword, $order)
    {
        $order_string = dataTable_order_string(['invoice_date', 'outgoing_invoices.id', 'stakeholder_name'], $order, 'invoice_date');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;
        $filter = $this->input->post('filter');
        $stakeholder_id = $this->input->post('stakeholder_id');
        $stakeholder_id = $stakeholder_id != '' ? $stakeholder_id : null;

        switch ($filter) {
            case 'due_in_two':
                $where = ' WHERE DATEDIFF(outgoing_invoices.due_date,CURDATE()) <= 2 AND outgoing_invoices.id NOT IN (SELECT invoice_id FROM receipts)';
                break;
            case 'due_in_five':
                $where = ' WHERE DATEDIFF(outgoing_invoices.due_date,CURDATE()) <= 5 AND outgoing_invoices.id NOT IN (SELECT invoice_id FROM receipts)';
                break;
            case 'due_in_ten':
                $where = ' WHERE DATEDIFF(outgoing_invoices.due_date,CURDATE()) <= 10 AND outgoing_invoices.id NOT IN (SELECT invoice_id FROM receipts)';
                break;
            case 'due_in_aboveten':
                $where = ' WHERE (DATEDIFF(outgoing_invoices.due_date,CURDATE()) > 10 AND DATEDIFF(outgoing_invoices.due_date,CURDATE()) <= 30) AND outgoing_invoices.id NOT IN (SELECT invoice_id FROM receipts) OR payment_terms = "due_on_receipt"';
                break;
            case 'overdue':
                $where = ' WHERE DATEDIFF(outgoing_invoices.due_date,CURDATE()) < 0 AND outgoing_invoices.id NOT IN (SELECT invoice_id FROM receipts)';
                break;
            default:
                $where = '';
                break;
        }

        if (!is_null($stakeholder_id)) {
            $where .= ($where != '' ? ' AND' : ' WHERE') . ' outgoing_invoices.invoice_to = ' . $stakeholder_id;
        }
        $sql = 'SELECT id FROM outgoing_invoices ' . $where;
        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $where .= ' AND (invoice_date LIKE "%' . $keyword . '%" OR outgoing_invoices.id LIKE "%' . $keyword . '%" OR stakeholder_name LIKE "%' . $keyword . '%" OR outgoing_invoices.id IN (
                   SELECT outgoing_invoice_id FROM project_certificate_invoices
                   LEFT JOIN project_certificates ON project_certificate_invoices.project_certificate_id = project_certificates.id
                   LEFT JOIN projects ON project_certificates.project_id = projects.project_id
                   WHERE project_name LIKE "%' . $keyword . '%"
                ))';
        }
        $sql = 'SELECT SQL_CALC_FOUND_ROWS outgoing_invoices.id, invoice_date, stakeholder_id AS client_id, COALESCE(stakeholder_name,"N/A") AS debtor_name  
				FROM outgoing_invoices
				LEFT JOIN stakeholders ON outgoing_invoices.invoice_to = stakeholders.stakeholder_id
				' . $where . $order_string;
        $query = $this->db->query($sql);
        $results = $query->result();

        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model([
            'stakeholder',
            'currency',
            'maintenance_service',
            'stock_sale',
            'project_certificate',
            'account'
        ]);

        $data['accounts'] = $this->account->dropdown_options(['BANK', 'CASH IN HAND']);
        $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
        $data['currency_options'] = currency_dropdown_options();
        $data['measurement_unit_options'] = measurement_unit_dropdown_options();
        $data['type'] = "sales";

        $rows = [];
        foreach ($results as $row) {
            $invoice = new self();
            $invoice->load($row->id);
            $currency = $invoice->currency();
            $invoice_nature = $invoice->outgoing_invoice_debt_nature();
            $data['debt_nature'] = $invoice_nature;
            $data['reffering_object'] = $data['invoice'] = $invoice;
            $data['attachments'] = $invoice->attachments();
            $outstanding_balance = $invoice->total_amount_vat_inclusive() - $invoice->paid_amount();
            $rows[] = [
                set_date($row->invoice_date),
                $invoice->outgoing_inv_number(),
                anchor(base_url('stakeholders/stakeholder_profile/' . $row->client_id), $row->debtor_name, 'tartget="_blank"'),
                '<span class="pull-right">' . $currency->symbol . ' ' . number_format($invoice->total_amount_vat_inclusive(), 2) . '</span>',
                '<span class="pull-right">' . $currency->symbol . ' ' . number_format($outstanding_balance, 2) . '</span>',
                $invoice->status(),
                $this->load->view('finance/invoices/invoice_list_actions', $data, true)
            ];
        }

        $json['data'] = $rows;
        $json['recordsFiltered'] = $records_filtered;
        $json['recordsTotal'] = $records_total;
        return json_encode($json);
    }

    public function status()
    {
        $date1 = date_create($this->due_date);
        $date2 = date_create(date('Y-m-d'));
        $date_interval = date_diff($date2, $date1);
        $days_remaining = $date_interval->format('%R%a');
        $invoice_due_date = $this->due_date != '' ? $this->due_date : null;
        $amount_to_pay = $this->unpaid_balance();
        switch ($days_remaining) {
            case (!$this->has_receipt() && ($days_remaining > 0 && $days_remaining <= 2)):
                $status = '<span class="label" style="background-color: #EC7063; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!$this->has_receipt() && ($days_remaining > 2 && $days_remaining <= 5)):
                $status = '<span class="label" style="background-color: #F5B7B1; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!$this->has_receipt() && ($days_remaining > 5 && $days_remaining <= 10)):
                $status = '<span class="label" style="background-color: #FDEDEC; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!$this->has_receipt() && ($days_remaining > 10 && $days_remaining <= 30)):
                $status = '<span class="label" style="background-color: #C39BD3; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!$this->has_receipt() && $days_remaining < 0):
                $status = '<span class="label" style="background-color: #CB4335; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days Overdue<span>';
                break;
            case (is_null($invoice_due_date) && !$this->has_receipt() && $this->payment_terms == "due_on_receipt"):
                $status = '<span class="label" style="background-color: #9b59b6; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>Due On Receipt<span>';
                break;
            default:
                if ($this->has_receipt() && $amount_to_pay == 0) {
                    $status = '<span class="label label-success" style="font-size: 12px;">Paid<span>';
                } else if ($this->has_receipt() && $amount_to_pay > 0) {
                    $status = '<span class="label" style="background-color: #11FF88; font-size: 12px;">Partial Receipt(s)</span>';
                } else {
                    $status = '<span class="label label-info" style="font-size: 12px;">Pending</span>';
                }
                break;
        }
        return $status;
    }

    public function maintainance_invoice()
    {
        $this->load->model('Maintenance_invoice');
        $maintanance_invoices = $this->maintanance_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($maintanance_invoices) ? array_shift($maintanance_invoices) : false;
    }

    public function clear_items()
    {
        $this->db->delete(['outgoing_invoice_items', 'stock_sale_invoices', 'maintenance_invoices', 'project_certificate_invoices'], ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function project_certificate()
    {
        $this->load->model(['project_certificate_invoice', 'project_certificate']);
        $certificate_invoices = $this->project_certificate_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        if (!empty($certificate_invoices)) {
            foreach ($certificate_invoices as $certificate_invoice) {
                $project_certificate =  new Project_certificate();
                $project_certificate->load($certificate_invoice->project_certificate_id);
                return $project_certificate;
            }
        } else {
            return false;
        }
    }

    public function maintenance_services()
    {
        $this->load->model(['maintenance_invoice', 'maintenance_service']);
        $service_invoices = $this->maintenance_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        $maintenance_services = [];
        if (!empty($service_invoices)) {
            foreach ($service_invoices as $service_invoice) {
                $maintenance_service =  new Maintenance_service();
                $maintenance_service->load($service_invoice->service_id);
                $maintenance_services[] = $maintenance_service;
            }
        }
        return $maintenance_services;
    }

    public function maintenance_service()
    {
        $this->load->model(['maintenance_invoice', 'maintenance_service']);
        $service_invoices = $this->maintenance_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        if (!empty($service_invoices)) {
            foreach ($service_invoices as $service_invoice) {
                $maintenance_service =  new Maintenance_service();
                $maintenance_service->load($service_invoice->service_id);
                return $maintenance_service;
            }
        } else {
            return false;
        }
    }

    public function stock_sale()
    {
        $this->load->model(['stock_sale_invoice', 'stock_sale']);
        $stock_sale_invoices = $this->stock_sale_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        if (!empty($stock_sale_invoices)) {
            foreach ($stock_sale_invoices as $stock_sale_invoice) {
                $stock_sale =  new Stock_sale();
                $stock_sale->load($stock_sale_invoice->stock_sale_id);
                return $stock_sale;
            }
        } else {
            return false;
        }
    }

    public function outgoing_invoice_debt_nature()
    {
        $this->load->model(['maintenance_invoice', 'stock_sale_invoice', 'project_certificate_invoice']);
        $maintenance_invoices = $this->maintenance_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        $sale_invoices = $this->stock_sale_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        $certificate_invoices = $this->project_certificate_invoice->get(0, 0, ['outgoing_invoice_id' => $this->{$this::DB_TABLE_PK}]);
        if (!empty($maintenance_invoices) && empty($sale_invoices) && empty($certificate_invoices)) {
            return "maintenance_service";
        } else if (!empty($sale_invoices) && empty($maintenance_invoices) && empty($certificate_invoices)) {
            return "stock_sale";
        } else if (!empty($certificate_invoices) && empty($sale_invoices) && empty($maintenance_invoices)) {
            return "certificate";
        } else {
            return "bulk";
        }
    }

    public function dropdown_options()
    {
        $this->load->model('stakeholder');
        $clients = $this->stakeholder->get(0, 0, ' stakeholder_id IN ( SELECT invoice_to FROM outgoing_invoices )');
        $options[] = '&nbsp;';
        foreach ($clients as $client) {
            $sql = 'SELECT * FROM outgoing_invoices AS main_table
                    WHERE invoice_to = ' . $client->{$client::DB_TABLE_PK} . '
                     AND (
                        (
                            SELECT COALESCE(SUM(quantity*rate),0) FROM outgoing_invoice_items
                            WHERE outgoing_invoice_items.outgoing_invoice_id = main_table.id
                        ) - (
                           (
                               SELECT COALESCE(SUM(amount),0) FROM receipt_items
                               LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                               WHERE receipts.invoice_id = main_table.id
                           ) + (
                               SELECT COALESCE(SUM(withheld_amount),0) FROM withholding_taxes
                               LEFT JOIN receipt_items ON withholding_taxes.receipt_item_id = receipt_items.id
                               LEFT JOIN receipts ON receipt_items.receipt_id = receipts.id
                               WHERE receipts.invoice_id = main_table.id
                           )
                        )
                     ) > 1
             ';
            $query = $this->db->query($sql);
            $client_invoices = $query->result();

            foreach ($client_invoices as $client_invoice) {
                $options[$client->stakeholder_name][$client_invoice->id] = $client_invoice->invoice_no;
            }
        }
        return $options;
    }

    public function vat_amount()
    {
        if ($this->vat_inclusive == 1) {
            return (($this->vat_percentage / 100) * $this->outgoing_invoice_amount());
        } else {
            return 0;
        }
    }

    public function detailed_reference()
    {
        $debt_nature = $this->outgoing_invoice_debt_nature();
        if ($debt_nature == "maintenance_service") {
            return $this->maintenance_service()->maintenance_services_no() . ' - ' . $this->reference;
        } else if ($debt_nature == "stock_sale") {
            return $this->stock_sale()->sale_number() . ' - ' . $this->reference;
        } else if ($debt_nature == "certificate") {
            return $this->project_certificate()->certificate_number . ' - ' . $this->reference;
        } else {
            return $this->outgoing_inv_number() . ' - ' . $this->reference;
        }
    }

    public function has_receipt()
    {
        $this->load->model('receipt');
        $receipts = $this->receipt->get(0, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($receipts) ? array_shift($receipts) : false;
    }

    public function invoice_details()
    {
        $invoice = new self();
        $invoice->load($this->{$this::DB_TABLE_PK});
        $invoice_arr = array(
            'amount' => $invoice->total_amount_vat_inclusive(),
            'stakeholder_id' => $invoice->invoice_to,
            'agent_id' => $invoice->agent_id,
            'bank_details' => $invoice->bank_details,
            'desc_or_note' => $invoice->notes,
            'billing_address' => $invoice->invoice_to()->address
        );
        return $invoice_arr;
    }

    public function invoice_edit_details()
    {
        $invoice_item_types = ['purchase_order', 'maintenance_service', 'stock_sale', 'project_certificate'];
        $this->load->model(['purchase_order', 'maintenance_service', 'stock_sale', 'project_certificate']);
        $invoice_edit_details = [];
        foreach ($invoice_item_types as $item_type) {
            switch ($item_type) {
                case 'stock_sale':
                case 'project_certificate':
                case 'purchase_order':
                    $item_id = $item_type . '_id';
                    $table = $item_type . '_invoices';
                    $invoice_id = 'outgoing_invoice_id';
                    if ($item_type == 'purchase_order') {
                        $invoice_id = 'invoice_id';
                    }
                    break;
                case 'maintenance_service':
                    $item_id = explode('_', $item_type)[1] . '_id';
                    $table = explode('_', $item_type)[0] . '_invoices';
                    $invoice_id = 'outgoing_invoice_id';
                    break;
            }

            $sql = 'SELECT ' . $item_id . ' FROM ' . $table . '
					WHERE ' . $invoice_id . ' = ' . $this->{$this::DB_TABLE_PK} . ' LIMIT 1';
            $id = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->row()->$item_id : false;
            if ($id) {

                $junction = explode('_', $item_type)[1];
                $function_name = 'generate_' . $junction . '_particulars';
                $edit_item = $this->$item_type->$function_name($id, 'return');
                $invoice_edit_details[] = json_decode($edit_item);
            }
        }
        return $invoice_edit_details;
    }

    public function invoice_edit_debted_item_id($id)
    {
        $debt_nature = $this->outgoing_invoice_debt_nature();
        switch ($debt_nature) {
            case 'maintenance_service':

                break;
            case 'stock_sale':
                break;
            case 'certificate':
                break;
        }
    }

    public function payment_terms()
    {
        $company_details = get_company_details();
        if (isset($this->due_date)) {
            $number_of_days = number_of_days($this->invoice_date . ' 00:00', $this->due_date . ' 23:59');
        } else {
            $number_of_days = null;
        }
        switch ($this->payment_terms) {
            case 'net_ten':
            case 'net_twenty':
            case 'net_thirty':
                $string = '1. Payment is required within ' . $number_of_days . ' days from invoice date.' . '<br/>' . '2. All payments should be addressed to ' . strtoupper($company_details->company_name);
                break;
            case 'set_manually':
                $string = '1. Payment is required within between invoice date up to ' . set_date($this->due_date) . '.' . '<br/>' . '2. All payments should be addressed to ' . strtoupper($company_details->company_name);
                break;
            case 'due_on_receipt':
                $string = '1. Payment is required soon after receiving this invoice.' . '<br/>' . '2. All payments should be addressed to ' . strtoupper($company_details->company_name) . '.';
                break;
        }
        return $string;
    }

    public function attachments()
    {
        $this->load->model('procurement_attachment');
        $where = 'reffering_to = "O-INV" AND reffering_id =' . $this->{$this::DB_TABLE_PK} . '';
        $junctions = $this->procurement_attachment->get(0, 0, $where);
        $attachments = [];
        foreach ($junctions as $junction) {
            $attachments[] = $junction->attachment();
        }
        return $attachments;
    }
}
