<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 21/02/2018
 * Time: 14:45
 */

class Invoice extends MY_Model
{

    const DB_TABLE = 'invoices';
    const DB_TABLE_PK = 'id';
    const INVOICE_TYPES = ['purchase_order'];

    public $invoice_date;
    public $invoice_no;
    public $due_date;
    public $currency_id;
    public $amount;
    public $vat_inclusive;
    public $vat_percentage;
    public $reference;
    public $description;
    public $payment_terms;


    public function currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->currency_id);
        return $currency;
    }

    public function creator()
    {
        return 'created_by';
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function paid_invoices()
    {
        $this->load->model('invoice_payment_voucher');
        $invoice_payment_vouchers = $this->invoice_payment_voucher->get(1, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}], 'invoice_id ASC');
        $paid_invoices[] = '&nbsp;';
        if (!empty($invoice_payment_vouchers)) {
            foreach ($invoice_payment_vouchers as $invoice_payment_voucher) {
                $paid_invoices[] = $invoice_payment_voucher->invoice_id;
            }
        }
        return $paid_invoices;
    }

    public function status()
    {
        $date1 = date_create($this->due_date);
        $date2 = date_create(date('Y-m-d'));
        $date_interval = date_diff($date2, $date1);
        $days_remaining = $date_interval->format('%R%a');
        $invoice_due_date = $this->due_date != '' ? $this->due_date : null;
        $amount_to_pay = $this->unpaid_amount();
        switch ($days_remaining) {
            case (!is_null($invoice_due_date) && !$this->invoice_payment_voucher() && ($days_remaining > 0 && $days_remaining <= 2)):
                $status = '<span class="label" style="background-color: #EC7063; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!is_null($invoice_due_date) && !$this->invoice_payment_voucher() && ($days_remaining > 2 && $days_remaining <= 5)):
                $status = '<span class="label" style="background-color: #F5B7B1; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!is_null($invoice_due_date) && !$this->invoice_payment_voucher() && ($days_remaining > 5 && $days_remaining <= 10)):
                $status = '<span class="label" style="background-color: #FDEDEC; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!is_null($invoice_due_date) && !$this->invoice_payment_voucher() && ($days_remaining > 10 && $days_remaining <= 30)):
                $status = '<span class="label" style="background-color: #C39BD3; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days<span>';
                break;
            case (!is_null($invoice_due_date) && !$this->invoice_payment_voucher() && $days_remaining < 0):
                $status = '<span class="label" style="background-color: #CB4335; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>' . abs($days_remaining) . ' Days Overdue<span>';
                break;
            case (is_null($invoice_due_date) && !$this->invoice_payment_voucher() && $this->payment_terms == "due_on_receipt"):
                $status = '<span class="label" style="background-color: #9b59b6; font-size: 12px;"><i class="fa fa-clock-o" style="padding-right: 3%"></i>Due On Receipt<span>';
                break;
            default:
                if ($this->invoice_payment_voucher() && $amount_to_pay == 0) {
                    $status = '<span class="label label-success" style="font-size: 12px;">Paid<span>';
                } else if ($this->invoice_payment_voucher() && $amount_to_pay > 0) {
                    $status = '<span class="label" style="background-color: #11FF88; font-size: 12px;">Partial Payment(s)</span>';
                } else {
                    $status = '<span class="label label-info" style="font-size: 12px;">Pending</span>';
                }
                break;
        }
        return $status;
    }

    public function request_date()
    {
        $this->load->model('purchase_order_payment_request_invoice_item');
        $payment_request_invoice_items = $this->purchase_order_payment_request_invoice_item->get(1, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}], 'invoice_id ASC');
        if (!empty($payment_request_invoice_items)) {
            foreach ($payment_request_invoice_items as $invoice_item) {
                return $invoice_item->purchase_order_payment_request()->request_date;
            }
        }
    }

    public function purchase_order_payment_request()
    {
        $this->load->model('purchase_order_payment_request_invoice_item');
        $payment_request_invoice_items = $this->purchase_order_payment_request_invoice_item->get(1, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}], 'invoice_id ASC');
        if (!empty($payment_request_invoice_items)) {
            foreach ($payment_request_invoice_items as $invoice_item) {
                return $invoice_item->purchase_order_payment_request();
            }
        }
    }

    public function grn_invoice()
    {
        $this->load->model('grn_invoice');
        $grn_invoices = $this->grn_invoice->get(1, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($grn_invoices) ? array_shift($grn_invoices) : false;
    }

    public function purchase_order_invoice()
    {
        $this->load->model('purchase_order_invoice');
        $purchase_order_invoices = $this->purchase_order_invoice->get(1, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($purchase_order_invoices) ? array_shift($purchase_order_invoices) : false;
    }

    public function purchase_order()
    {
        $purchase_order_invoice = $this->purchase_order_invoice();
        if ($purchase_order_invoice) {
            return $purchase_order_invoice->purchase_order();
        } else {
            $grn_invoice = $this->grn_invoice();
            if ($grn_invoice) {
                return $grn_invoice->grn()->purchase_order();
            } else {
                return false;
            }
        }
    }

    public function reference()
    {
        //        return $this->reference != '' ? $this->reference : $this->invoice_no != '' ? $this->invoice_no : $this->purchase_order()->order_number().' - INV/'.$this->{$this::DB_TABLE_PK};
        return $this->reference != '' ? 'REF-' . $this->reference : $this->purchase_order()->order_number() . ' - INV/' . $this->{$this::DB_TABLE_PK};
    }

    public function detailed_reference()
    {
        $order = $this->purchase_order();
        return $order ? $order->order_number() . ' - ' . $this->reference : $this->reference;
    }

    public function correspondence_number()
    {
        $grn_invoice = $this->grn_invoice();
        if ($grn_invoice) {
            $corresponding_number = $grn_invoice->grn()->grn_number();
        } else {
            $purchase_order_invoice = $this->purchase_order_invoice();
            if ($purchase_order_invoice) {
                $corresponding_number = $purchase_order_invoice->purchase_order()->order_number();
            } else {
                $corresponding_number = 'N/A';
            }
        }
        return $corresponding_number;
    }

    public function supplementary_accounts()
    {
        return 'N/A';
    }

    public function unpaid_dropdown_options()
    {
        $this->load->model('stakeholder');
        $stakeholders = $this->stakeholder->get();
        $options = ['' => '&nbsp;'];
        foreach ($stakeholders as $stakeholder) {
            //$invoices = $stakeholder->invoices(null,null,['approved','unpaid']);
            $invoices = $stakeholder->invoices();
            foreach ($invoices as $invoice) {
                $options[$stakeholder->stakeholder_name][$invoice->{$invoice::DB_TABLE_PK}] = $invoice->reference;
            }
        }
        return $options;
    }

    public function stakeholder()
    {
        $this->load->model('stakeholder_invoice');
        $junctions = $this->stakeholder_invoice->get(1, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions)  ? array_shift($junctions)->stakeholder() : false;
    }

    public function paid_amount()
    {
        $sql = 'SELECT (
                   (
                      SELECT COALESCE(SUM(journal_voucher_items.amount),0) FROM journal_voucher_items
                      LEFT JOIN invoice_journal_voucher_items ON journal_voucher_items.item_id = invoice_journal_voucher_items.journal_voucher_item_id
                      WHERE invoice_id = ' . $this->{$this::DB_TABLE_PK} . '
                   ) + (
                      SELECT COALESCE(SUM(amount),0) AS amount_paid FROM payment_voucher_items
                      LEFT JOIN payment_vouchers ON payment_voucher_items.payment_voucher_id = payment_vouchers.payment_voucher_id
                      LEFT JOIN invoice_payment_vouchers ON payment_vouchers.payment_voucher_id = invoice_payment_vouchers.payment_voucher_id
                      WHERE invoice_id = ' . $this->{$this::DB_TABLE_PK} . '
                   )
               ) AS amount_paid';
        $query = $this->db->query($sql);
        return $query->row()->amount_paid;
    }

    public function amount_approved_to_be_paid()
    {
        $sql = 'SELECT COALESCE(SUM(approved_amount),0) AS approved_amount FROM purchase_order_payment_request_approval_invoice_items
              LEFT JOIN purchase_order_payment_request_approvals ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_approval_id = purchase_order_payment_request_approvals.id
              LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
              LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_approvals.purchase_order_payment_request_id = purchase_order_payment_requests.id
              WHERE is_final = 1 AND invoice_id = ' . $this->{$this::DB_TABLE_PK} . ' AND status = "APPROVED"';
        $query = $this->db->query($sql);
        return $query->row()->approved_amount;
    }

    public function unpaid_amount($approved = false)
    {
        $paid_amount = $this->paid_amount();
        return $approved ? $this->amount_approved_to_be_paid() - $paid_amount : $this->amount - $paid_amount;
    }

    public function invoice_payment_voucher()
    {
        $this->load->model('invoice_payment_voucher');
        $junctions = $this->invoice_payment_voucher->get(1, 0, ['invoice_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function payment_voucher($payment_request_approval = false)
    {
        $this->load->model(['invoice_payment_voucher', 'payment_voucher']);
        $where['invoice_id'] = $this->{$this::DB_TABLE_PK};
        $invoice_payment_vouchers = $this->invoice_payment_voucher->get(0, 0, $where, 'id DESC');

        if (!empty($invoice_payment_vouchers)) {
            foreach ($invoice_payment_vouchers as $invoice_payment_voucher) {
                $payment_voucher = new Payment_voucher();
                $payment_voucher->load($invoice_payment_voucher->payment_voucher_id);
                if (!$payment_request_approval) {
                    return $payment_voucher;
                } else {
                    if ($payment_voucher->purchase_order_payment_request_approval_payment_voucher()) {
                        $invoice_payment_approval_id = $payment_voucher->purchase_order_payment_request_approval_payment_voucher()->purchase_order_payment_request_approval_id;
                        return !is_null($invoice_payment_approval_id) ? $invoice_payment_approval_id : false;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }

    public function list($limit, $start, $keyword, $order, $stakeholder = null)
    {
        $order_string = dataTable_order_string(['invoice_date', 'invoices.id', 'stakeholder_name'], $order, 'invoice_date');
        $order_string = " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;
        $filter = $this->input->post('filter');
        $stakeholder_id = $this->input->post('stakeholder_id');
        $stakeholder_id = $stakeholder_id != '' ? $stakeholder_id : null;

        switch ($filter) {
            case 'due_in_two':
                $where = ' WHERE DATEDIFF(invoices.due_date,CURDATE()) <= 2';
                break;
            case 'due_in_five':
                $where = ' WHERE DATEDIFF(invoices.due_date,CURDATE()) <= 5';
                break;
            case 'due_in_ten':
                $where = ' WHERE DATEDIFF(invoices.due_date,CURDATE()) <= 10';
                break;
            case 'due_in_aboveten':
                $where = ' WHERE (DATEDIFF(invoices.due_date,CURDATE()) > 10 OR payment_terms = "due_on_receipt")';
                break;
            case 'overdue':
                $where = ' WHERE DATEDIFF(invoices.due_date,CURDATE()) < 0';
                break;
            default:
                $where = '';
                break;
        }

        $invoice_for = (!is_null($stakeholder_id) || !is_null($stakeholder)) ? !is_null($stakeholder_id) ? $stakeholder_id : $stakeholder : false;
        if ($invoice_for) {
            $where .= ($where != '' ? ' AND' : ' WHERE') . ' stakeholder_invoices.stakeholder_id = ' . $invoice_for;
        }

        $sql = 'SELECT invoices.id FROM invoices
				LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id ' . $where;
        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $where .= ($where != '' ? ' AND ' :  'WHERE ' ). ' (invoice_date LIKE "%' . $keyword . '%" OR invoices.id LIKE "%' . $keyword . '%" OR stakeholder_name LIKE "%' . $keyword . '%") ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS invoices.id AS invoice_id, stakeholder_invoices.stakeholder_id, stakeholder_name, invoice_date, due_date, reference, amount, purchase_order_id AS order_id 
				FROM invoices
				LEFT JOIN stakeholder_invoices ON invoices.id = stakeholder_invoices.invoice_id
                LEFT JOIN stakeholders ON stakeholder_invoices.stakeholder_id = stakeholders.stakeholder_id
                LEFT JOIN purchase_order_invoices ON invoices.id = purchase_order_invoices.invoice_id ' . $where . $order_string;
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
        $data['type'] = "purchases";

        $rows = [];
        foreach ($results as $row) {
            $invoice = new self();
            $invoice->load($row->invoice_id);
            $currency = $invoice->currency();
            $data['reffering_object'] = $data['invoice'] = $invoice;
            $data['attachments'] = $invoice->attachments();

            if (!is_null($stakeholder)) {
                $rows[] = [
                    custom_standard_date($row->invoice_date),
                    anchor(base_url('procurements/preview_purchase_order/' . $invoice->purchase_order()->order_id), $invoice->correspondence_number(), 'target="_blank"'),
                    $row->reference,
                    $currency->symbol . '<span class="pull-right">' . number_format($row->amount, 2) . '</span>',
                    $currency->symbol . '<span class="pull-right">' . number_format($invoice->unpaid_amount(), 2) . '</span>',
                    $invoice->status()
                ];
            } else {
                $rows[] = [
                    set_date($row->invoice_date),
                    anchor(base_url('procurements/preview_purchase_order/' . $invoice->purchase_order()->order_id), $invoice->correspondence_number(), 'target="_blank"'),
                    anchor(base_url('stakeholders/stakeholder_profile/' . $row->stakeholder_id . '/' . true), $row->stakeholder_name, 'tartget="_blank"'),
                    $currency->symbol . '<span class="pull-right">' . number_format($invoice->amount, 2) . '</span>',
                    $currency->symbol . '<span class="pull-right">' . number_format($invoice->unpaid_amount(), 2) . '</span>',
                    $invoice->status(),
                    $this->load->view('finance/invoices/invoice_list_actions', $data, true)
                ];
            }
        }

        $json['data'] = $rows;
        $json['recordsFiltered'] = $records_filtered;
        $json['recordsTotal'] = $records_total;
        return json_encode($json);
    }

    public function invoice_details()
    {
        $invoice = new self();
        $invoice->load($this->{$this::DB_TABLE_PK});
        $stakeholder = $invoice->purchase_order()->stakeholder();
        $invoice_arr = array(
            'amount' => $invoice->amount,
            'stakeholder_id' => $stakeholder->{$stakeholder::DB_TABLE_PK},
            'bank_details' => '',
            'desc_or_note' => $invoice->description,
            'billing_address' => ''
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

    public function clear_items()
    {
        $this->db->delete(['purchase_order_invoices', 'stakeholder_invoices'], ['invoice_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function attachments()
    {
        $this->load->model('procurement_attachment');
        $where = ' (reffering_id =' . $this->{$this::DB_TABLE_PK} . ' AND  reffering_to="P-INV")';
        $purchase_order  = $this->purchase_order();
        if ($purchase_order) {
            $where .= ' OR (reffering_id =' . $purchase_order->order_id . ' AND reffering_to = "ORDER")';
            $purchase_order_grns = $purchase_order->grns();
            if (!empty($purchase_order_grns)) {
                $order_grns_arr = array();
                foreach ($purchase_order_grns as $grn) {
                    $order_grns_arr[] = $grn->grn_id;
                }
                $where .= ' OR (reffering_id IN (' . implode(',', $order_grns_arr) . ') AND reffering_to = "GRN")';
            }
        }
        $junctions = $this->procurement_attachment->get(0, 0, $where);
        $attachments = [];
        foreach ($junctions as $junction) {
            $attachments[] = $junction->attachment();
        }
        return $attachments;
    }
}
