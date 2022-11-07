<?php



require 'vendor/autoload.php';

class Finance extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->load->model(['account', 'currency', 'outgoing_invoice', 'invoice', 'stakeholder']);
    }

    public function save_account()
    {
        $this->load->model(['currency', 'account_group', 'bank']);
        $account = new Account();
        $edit = $account->load($this->input->post('account_id'));
        $account_group_id = $this->input->post('account_group_id');
        $account_group_id = $account_group_id != '' ? $account_group_id : null;
        $account_group = $this->input->post('account_group');
        $account_group = $account_group != '' ? $account_group : false;
        if ($account_group) {
            switch ($account_group) {
                case "BANK":
                    $account_groups = $this->account_group->get(0, 0, ['group_name' => $account_group]);
                    $account_group = array_shift($account_groups);
                    $account_group_id = $account_group->{$account_group::DB_TABLE_PK};
                    break;
                case "CASH_IN_HAND":
                    $group_name = str_replace("_", " ", $account_group);
                    $account_groups = $this->account_group->get(0, 0, ['group_name' => (string)$group_name]);
                    $account_group = array_shift($account_groups);
                    $account_group_id = $account_group->{$account_group::DB_TABLE_PK};
                    break;
                case 'LEDGER':
                    $account_group_id = $edit ? $account->account_group_id : $account_group_id;
                    break;
                default:
                    $group_name = 'ACCOUNTS ' . $account_group;
                    $account_groups = $this->account_group->get(0, 0, ['group_name' => (string)$group_name]);
                    $account_group = array_shift($account_groups);
                    $account_group_id = $account_group->{$account_group::DB_TABLE_PK};
                    break;
            }
        }
        $account->account_name = $this->input->post('account_name');
        $account->account_group_id = $account_group_id;
        $account->currency_id = $this->input->post('currency_id');
        $account->description = $this->input->post('description');
        $account->account_code = $this->input->post('account_code');
        if (!$edit) {
            $account->opening_balance = $this->input->post('opening_balance');
        }
        if ($this->account_group->account_group_details($account_group_id)->group_name == 'BANK') {
            $account->bank_id = $this->input->post('bank_id');
        }

        if ($account->save()) {
            $account_for = $this->input->post('account_for');
            $related_to = $this->input->post('related_to');
            $has_account_details = $account->has_account_details($account->bank_id);
            if ($this->account_group->account_group_details($account_group_id)->group_name == 'BANK' || $has_account_details) {
                $this->load->model('bank_account');
                $detail = new Bank_account();
                switch ($has_account_details) {
                    case false:
                        break;
                    default:
                        $detail->load($has_account_details);
                        break;
                }
                $detail->account_id = $account->{$account::DB_TABLE_PK};
                $detail->bank_id = $this->input->post('bank_id');
                $detail->account_number = $this->input->post('account_number');;
                $detail->branch = $this->input->post('branch');;
                $detail->swift_code = $this->input->post('swift_code');;
                $detail->created_by = $this->session->userdata('employee_id');
                $detail->save();
            }
            if (!$edit) {
                if ($account_for == 'project') {
                    $this->load->model('project_account');
                    $project_account = new Project_account();
                    $project_account->account_id = $account->{$account::DB_TABLE_PK};
                    $project_account->project_id = $related_to;
                    $project_account->save();
                } else if ($account_for == 'cost_center') {
                    $this->load->model('cost_center_account');
                    $cost_center_account = new Cost_center_account();
                    $cost_center_account->account_id = $account->{$account::DB_TABLE_PK};
                    $cost_center_account->cost_center_id = $related_to;
                    $cost_center_account->save();
                }
            }
        }

        if ($this->input->post('loan') == 'true') {
            $this->load->model(['employee_account', 'account']);

            $account2 = $this->account->get(1, 0, ['account_name' => $this->input->post('account_name')], 'account_id DESC');
            $found = array_shift($account2);
            $employee_account = new Employee_account();
            $employee_account->account_id = $found->account_id;
            $employee_account->employee_id = $this->input->post('employee_id');
            $employee_account->created_by = $this->session->userdata('employee_id');
            $employee_account->save();
        }

        $action = $edit ? 'Account Creation' : 'Account Update';
        $description = 'Account ' . $account->account_name . ' was ' . ($edit ? 'updated' : 'created');
        system_log($action, $description);
    }

    public function accounts($account_group)
    {
        check_permission('Finance', true);
        $limit = $this->input->post('length');
        if ($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->account->accounts_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $account_group);
        } else {
            $this->load->model(['account_group', 'bank']);
            $data['account_group_options'] = $this->account_group->account_group_options();
            $data['currency_options'] = currency_dropdown_options();
            $data['bank_options'] = $this->bank->bank_options();
            $data['account_group'] = $account_group;
            $data['title'] = $account_group == "cash_in_hand" ? 'Finance | Cash Accounts List' :  'Finance | ' . ucfirst($account_group) . ' Accounts List';
            $this->load->view('finance/accounts_list', $data);
        }
    }

    public function delete_account()
    {
        $account = new Account();
        if ($account->load($this->input->post('account_id'))) {
            $description = $account->account_name . ' was deleted';
            $account->delete();
            system_log('Account Delete', $description);
        }
    }

    public function save_contra()
    {
        $this->load->model(['contra', 'contra_item', 'journal_voucher', 'journal_contra', 'journal_voucher_credit_account', 'journal_voucher_item']);

        $journal = new Journal_voucher();
        $journal->transaction_date = $this->input->post('contra_date');
        $journal->reference = $this->input->post('reference');
        $journal->journal_type = "JOURNAL";
        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $journal->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
        $journal->currency_id = $this->input->post('currency_id');
        $journal->remarks = $this->input->post('remarks');
        $journal->created_by = $this->session->userdata('employee_id');
        if ($journal->save()) {
            $contra = new Contra();
            $edit = $contra->load($this->input->post('contra_id'));
            $contra->contra_date = $this->input->post('contra_date');
            $posted_item = $this->input->post('credit_account_id');
            $credit_account_id = !preg_match('/^[0-9]+$/', $posted_item) ? explode('_', $posted_item)[1] : $posted_item;
            if (explode('_', $posted_item)[0] == "stakeholder") {
                $contra->stakeholder_id = $credit_account_id;
            } else {
                $contra->credit_account_id = $credit_account_id;
            }
            $contra->reference = $this->input->post('reference');
            $contra->employee_id = $this->session->userdata('employee_id');
            $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
            $contra->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
            $contra->remarks = $this->input->post('remarks');
            $contra->currency_id = $this->input->post('currency_id');
            $contra->exchange_rate = $this->input->post('exchange_rate');
            $debit_accounts_ids = $this->input->post('debit_accounts_ids');
            $contra_type = $this->input->post('contra_type');
            if ($contra->save()) {

                if ($edit) {
                    $contra->delete_items();
                }

                $jv_con = new Journal_contra();
                $jv_con->contra_id = $contra->{$contra::DB_TABLE_PK};
                $jv_con->journal_id = $journal->{$journal::DB_TABLE_PK};
                $jv_con->save();

                $contra_amount = 0;
                if (!empty($debit_accounts_ids) && $contra_type != 'from_imprest') {
                    foreach ($debit_accounts_ids as $index => $debit_account_id) {
                        $contra_amount += $this->input->post('amounts')[$index];
                        $contra_item = new Contra_item();
                        $debit_account = !preg_match('/^[0-9]+$/', $debit_account_id) ? explode('_', $debit_account_id)[1] : $debit_account_id;
                        if (explode('_', $debit_account_id)[0] == "stakeholder") {
                            $contra_item->stakeholder_id = $debit_account;
                        } else {
                            $contra_item->debit_account_id = $debit_account;
                        }
                        $contra_item->contra_id = $contra->{$contra::DB_TABLE_PK};
                        $contra_item->amount = $this->input->post('amounts')[$index];
                        $contra_item->description = $this->input->post('descriptions')[$index];
                        $contra_item->save();

                        $jv_item = new Journal_voucher_item();
                        if (explode('_', $debit_account_id)[0] == "stakeholder") {
                            $jv_item->stakeholder_id = $debit_account;
                        } else {
                            $jv_item->debit_account_id = $debit_account;
                        }
                        $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                        $jv_item->amount = $contra_item->amount;
                        $jv_item->narration = $contra->remarks;
                        $jv_item->save();
                    }
                } else {

                    $this->load->model('imprest_voucher_contra');
                    $imprest_voucher_contra = new Imprest_voucher_contra();
                    $imprest_voucher_contra->imprest_voucher_id = $this->input->post('imprest_voucher_id');
                    $imprest_voucher_contra->contra_id = $contra->{$contra::DB_TABLE_PK};
                    $imprest_voucher_contra->save();


                    $contra_item = new Contra_item();
                    $debit_account_id = !preg_match('/^[0-9]+$/', $debit_account) ? explode('_', $debit_account)[1] : $debit_account;
                    if (explode('_', $debit_account)[0] == "stakeholder") {
                        $contra_item->stakeholder_id = $debit_account_id;
                    } else {
                        $contra_item->debit_account_id = $debit_account_id;
                    }
                    $contra_item->contra_id = $contra->{$contra::DB_TABLE_PK};
                    $contra_amount += $contra_item->amount = $this->input->post('amount');
                    $contra_item->description = 'As per Imprest Voucher No' . $imprest_voucher_contra->imprest_voucher_id;
                    $debit_account = $this->input->post('debit_account_id');
                    $contra_item->save();


                    $jv_item = new Journal_voucher_item();
                    $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                    if (explode('_', $debit_account)[0] == "stakeholder") {
                        $jv_item->stakeholder_id = $debit_account_id;
                    } else {
                        $jv_item->debit_account_id = $debit_account_id;
                    }
                    $jv_item->amount = $contra_item->amount;
                    $jv_item->narration = $contra->remarks;
                    $jv_item->save();
                }

                $jv_credit_acc = new Journal_voucher_credit_account();
                if (explode('_', $posted_item)[0] == "stakeholder") {
                    $jv_credit_acc->stakeholder_id = $credit_account_id;
                } else {
                    $jv_credit_acc->account_id = $credit_account_id;
                }
                $jv_credit_acc->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                $jv_credit_acc->amount = $contra_amount;
                $jv_credit_acc->narration = $contra->remarks;
                $jv_credit_acc->save();
                system_log($edit ? 'Contra Update' : 'Contra Entry', 'Contra Entry No ' . add_leading_zeros($contra->{$contra::DB_TABLE_PK}) . ' was ' . ($edit ? ' updated' : 'posted'));
            }
        }
    }

    public function delete_contra()
    {
        $this->load->model('contra');
        $contra = new Contra();
        if ($contra->load($this->input->post('contra_id'))) {
            $description = 'Contra No ' . $contra->{$contra::DB_TABLE_PK} . ' was deleted';
            $contra->delete();
            system_log('Contra Delete', $description);
        }
    }

    public function delete_payment_voucher()
    {
        $this->load->model('payment_voucher');
        $payment_voucher = new Payment_voucher();
        if ($payment_voucher->load($this->input->post('payment_voucher_id'))) {
            $description = 'Payment Voucher No ' . $payment_voucher->{$payment_voucher::DB_TABLE_PK} . ' was deleted';
            $payment_voucher->delete();
            system_log('Payment Voucher Delete', $description);
        }
    }

    public function load_account_group_options()
    {
        $this->load->model('account_group');
        echo stringfy_dropdown_options($this->account_group->account_group_options($this->input->post('account_groups'), $this->input->post('parent_id')));
    }

    public function save_imprest_voucher()
    {
        $this->load->model(array('imprest_voucher', 'requisition_approval_imprest_voucher'));
        $imprest_voucher = new Imprest_voucher();
        $imprest_voucher->imprest_date = $this->input->post('imprest_date');
        $posted_credit_item = $this->input->post('credit_account_id');
        $credit_account_id = !preg_match('/^[0-9]+$/', $posted_credit_item) ? explode('_', $posted_credit_item)[1] : $posted_credit_item;
        $imprest_voucher->credit_account_id = $credit_account_id;
        $posted_debit_item = $this->input->post('debit_account_id');
        $debit_account_id = !preg_match('/^[0-9]+$/', $posted_debit_item) ? explode('_', $posted_debit_item)[1] : $posted_debit_item;
        $imprest_voucher->debit_account_id = $debit_account_id;
        $imprest_voucher->currency_id = $this->input->post('currency_id');
        $imprest_voucher->exchange_rate = $this->input->post('exchange_rate');
        $imprest_voucher->remarks = $this->input->post('remarks');
        $imprest_voucher->handler_id = $this->input->post('handler_id');
        $imprest_voucher->created_by = $this->session->userdata('employee_id');
        if ($imprest_voucher->save()) {

            $ra_imprest_voucher = new Requisition_approval_imprest_voucher();
            $ra_imprest_voucher->requisition_approval_id = $this->input->post('requisition_approval_id');
            $ra_imprest_voucher->imprest_voucher_id = $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
            $ra_imprest_voucher->save();

            $this->load->model([
                'imprest_voucher_material_item',
                'imprest_voucher_cash_item',
                'imprest_voucher_asset_item',
                'imprest_voucher_service_item',
                'cost_center_imprest_voucher_item',
                'project_imprest_voucher_item'
            ]);
            $item_types = $this->input->post('item_types');
            foreach ($item_types as $index => $item_type) {
                $quantity = $this->input->post('quantities')[$index];
                if ($quantity > 0) {
                    if ($item_type == 'material') {
                        $imprest_voucher_material_item = new Imprest_voucher_material_item();
                        $imprest_voucher_material_item->imprest_voucher_id = $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
                        $imprest_voucher_material_item->requisition_approval_material_item_id = $this->input->post('requisition_approval_material_item_ids')[$index];
                        $imprest_voucher_material_item->quantity = $quantity;
                        $imprest_voucher_material_item->rate = $this->input->post('rates')[$index];
                        $imprest_voucher_material_item->save();
                    } else if ($item_type == 'asset') {
                        $imprest_voucher_asset_item = new Imprest_voucher_asset_item();
                        $imprest_voucher_asset_item->imprest_voucher_id = $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
                        $imprest_voucher_asset_item->requisition_approval_asset_item_id = $this->input->post('requisition_approval_asset_item_ids')[$index];
                        $imprest_voucher_asset_item->quantity = $quantity;
                        $imprest_voucher_asset_item->rate = $this->input->post('rates')[$index];
                        $imprest_voucher_asset_item->save();
                    } else if ($item_type == 'service') {
                        $imprest_voucher_service_item = new Imprest_voucher_service_item();
                        $imprest_voucher_service_item->imprest_voucher_id = $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
                        $imprest_voucher_service_item->requisition_approval_service_item_id = $this->input->post('requisition_approval_service_item_ids')[$index];
                        $imprest_voucher_service_item->quantity = $quantity;
                        $imprest_voucher_service_item->rate = $this->input->post('rates')[$index];
                        if ($imprest_voucher_service_item->save()) {
                            $requisition = $imprest_voucher->requisition();
                            $junction_type = $requisition->requested_for();
                            if ($junction_type == 'project') {
                                $project = $requisition->project();
                                $junction = new Project_imprest_voucher_item();
                                $junction->project_id = $project->{$project::DB_TABL_PK};
                                $junction->imprest_voucher_service_item_id = $imprest_voucher_service_item->{$imprest_voucher_service_item::DB_TABLE_PK};
                                $junction->save();
                            } else {
                                $cost_center = $requisition->cost_center();
                                $junction = new Cost_center_imprest_voucher_item();
                                $junction->cost_center_id = $cost_center->{$cost_center::DB_TABL_PK};
                                $junction->imprest_voucher_service_item_id = $imprest_voucher_service_item->{$imprest_voucher_service_item::DB_TABLE_PK};
                                $junction->save();
                            }
                        }
                    } else {
                        $imprest_voucher_cash_item = new Imprest_voucher_cash_item();
                        $imprest_voucher_cash_item->imprest_voucher_id = $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
                        $imprest_voucher_cash_item->requisition_approval_cash_item_id = $this->input->post('requisition_approval_cash_item_ids')[$index];
                        $imprest_voucher_cash_item->quantity = $quantity;
                        $imprest_voucher_cash_item->rate = $this->input->post('rates')[$index];
                        if ($imprest_voucher_cash_item->save()) {
                            $requisition = $imprest_voucher->requisition();
                            $junction_type = $requisition->requested_for();
                            if ($junction_type == 'project') {
                                $project = $requisition->project();
                                $junction = new Project_imprest_voucher_item();
                                $junction->project_id = $project->{$project::DB_TABLE_PK};
                                $junction->imprest_voucher_cash_item_id = $imprest_voucher_cash_item->{$imprest_voucher_cash_item::DB_TABLE_PK};
                                $junction->save();
                            } else {
                                $cost_center = $requisition->cost_center();
                                $junction = new Cost_center_imprest_voucher_item();
                                $junction->cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
                                $junction->imprest_voucher_cash_item_id = $imprest_voucher_cash_item->{$imprest_voucher_cash_item::DB_TABLE_PK};
                                $junction->save();
                            }
                        }
                    }
                }
            }

            $imprest_voucher->update_vat_info();
        }
    }

    public function save_imprest_voucher_retirement()
    {
        $this->load->model(['imprest_voucher_retirement', 'imprest_voucher']);
        $item_types = $this->input->post('item_types');
        $item_ids = $this->input->post('item_ids');
        $asset_item_ids = $this->input->post('asset_item_ids');
        $cash_descriptions = $this->input->post('cash_descriptions');
        $service_descriptions = $this->input->post('service_descriptions');
        $quantities = $this->input->post('quantities');
        $rates = $this->input->post('rates');
        $imprest_voucher_id = $this->input->post('imprest_voucher_id');
        $imprest_voucher = new Imprest_voucher();
        $imprest_voucher->load($imprest_voucher_id);

        $imprest_voucher_retirement = new Imprest_voucher_retirement();
        $imprest_voucher_retirement->retirement_date = $this->input->post('retirement_date');
        $imprest_voucher_retirement->imprest_voucher_id = $imprest_voucher_id;
        $imprest_voucher_retirement->location_id = $this->input->post('location_id');
        $imprest_voucher_retirement->sub_location_id = $this->input->post('sub_location_id');
        $imprest_voucher_retirement->remarks = $this->input->post('remarks');
        $imprest_voucher_retirement->is_examined = 0;
        $imprest_voucher_retirement->created_by = $this->session->userdata('employee_id');
        if ($imprest_voucher_retirement->save()) {
            if (!empty($item_types)) {
                $this->load->model([
                    'imprest_voucher_retirement_material_item',
                    'imprest_voucher_retirement_asset_item',
                    'imprest_voucher_retired_cash',
                    'imprest_voucher_retired_service',
                ]);

                foreach ($item_types as $index => $item_type) {
                    if ($quantities[$index] > 0) {
                        if ($item_type == 'material') {
                            $retirement_material_item = new Imprest_voucher_retirement_material_item();
                            $retirement_material_item->imprest_voucher_retirement_id = $imprest_voucher_retirement->{$imprest_voucher_retirement::DB_TABLE_PK};
                            $retirement_material_item->item_id = $item_ids[$index];
                            $retirement_material_item->quantity = $quantities[$index];
                            $retirement_material_item->rate = $rates[$index];
                            $retirement_material_item->save();
                        } else if ($item_type == 'asset') {
                            $retirement_asset_item = new Imprest_voucher_retirement_asset_item();
                            $retirement_asset_item->imprest_voucher_retirement_id = $imprest_voucher_retirement->{$imprest_voucher_retirement::DB_TABLE_PK};
                            $retirement_asset_item->asset_item_id = $asset_item_ids[$index];
                            $retirement_asset_item->book_value = $rates[$index];
                            $retirement_asset_item->quantity = $quantities[$index];
                            $retirement_asset_item->save();
                        } else if ($item_type == 'service') {
                            $retirement_service = new Imprest_voucher_retired_service();
                            $retirement_service->imprest_voucher_retirement_id = $imprest_voucher_retirement->{$imprest_voucher_retirement::DB_TABLE_PK};
                            $retirement_service->imprest_voucher_service_item_id = $this->input->post('imprest_voucher_service_item_ids')[$index];
                            $retirement_service->description = $service_descriptions[$index];
                            $retirement_service->quantity = $quantities[$index];
                            $retirement_service->rate = $rates[$index];
                            $retirement_service->save();
                        } else {
                            $retirement_cash = new Imprest_voucher_retired_cash();
                            $retirement_cash->imprest_voucher_retirement_id = $imprest_voucher_retirement->{$imprest_voucher_retirement::DB_TABLE_PK};
                            $retirement_cash->imprest_voucher_cash_item_id = $this->input->post('imprest_voucher_cash_item_ids')[$index];
                            $retirement_cash->description = $cash_descriptions[$index];
                            $retirement_cash->quantity = $quantities[$index];
                            $retirement_cash->rate = $rates[$index];
                            $retirement_cash->save();
                        }
                    }
                }
            }

            $imprest_voucher_retirement->update_vat_info();
            $this->load->model('employee');

            $exemine = new Employee();
            $exemine->load($imprest_voucher->created_by);
            $addresses[] = $exemine->email;

            $this->load->library('email');
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://chi-node11.websitehostserver.net',
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
            $subject = 'RETIREMENT FOR IMPREST VOUCHER NO: ' . $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
            $content = '<div style="margin: auto" class="info-box-content">Greetings,<br/> ' . $exemine->full_name() . "." . '<br/>' . '  Imprest voucher no ' . add_leading_zeros($imprest_voucher->{$imprest_voucher::DB_TABLE_PK}) . '
                            has been retired by ' . $this->session->userdata("employee_name") . ',<br/> Please <a href="' . base_url() . '">login</a> and search for imprest vocher no ' . add_leading_zeros($imprest_voucher->{$imprest_voucher::DB_TABLE_PK}) . ' to exemine it. <hr/></div><br/>';
            $content .= $this->preview_imprest_voucher_retirement($imprest_voucher->{$imprest_voucher::DB_TABLE_PK}, 'true');

            $message = $this->load->view('includes/email', ['content' => $content], true);

            $this->email->to($addresses);
            $this->email->subject($subject);
            $this->email->set_mailtype("html");
            $this->email->message($message);
            $this->email->send();
        }
    }

    public function approve_retirement_examination()
    {
        $this->load->model('imprest_voucher_retirement');
        $imprest_voucher_retirement = new Imprest_voucher_retirement();
        $imprest_voucher_retirement->load($this->input->post('imprest_voucher_retirement_id'));
        $imprest_voucher_retirement->is_examined = 1;
        $imprest_voucher_retirement->examination_date = $this->input->post('examination_date');
        $imprest_voucher_retirement->examined_by = $this->session->userdata('employee_id');
        $retirement_to = $this->input->post('retirement_to');
        if ($imprest_voucher_retirement->save()) {

            $retired_material_items = $imprest_voucher_retirement->retired_material_items();
            $retired_asset_items = $imprest_voucher_retirement->retired_asset_items();
            $retired_cash = $imprest_voucher_retirement->retired_cash();
            $retired_services = $imprest_voucher_retirement->retired_services();

            if (!empty($retired_material_items) || !empty($retired_asset_items)) {
                $this->load->model('goods_received_note');
                $grn = new Goods_received_note();
                $grn->receive_date = $imprest_voucher_retirement->retirement_date;
                $grn->location_id = $imprest_voucher_retirement->location_id;
                $grn->receiver_id = $imprest_voucher_retirement->created_by;
                $grn->comments = $imprest_voucher_retirement->remarks;
                if ($grn->save()) {
                    $imprest_voucher_retirement->imprest_voucher_retirement_grn_junction($grn->{$grn::DB_TABLE_PK});
                }

                $this->load->model([
                    'asset',
                    'goods_received_note_material_stock_item',
                    'material_stock',
                    'grn_asset_sub_location_history',
                    'asset_sub_location_history'
                ]);
                $imprest_voucher = $imprest_voucher_retirement->imprest_voucher();
                $project_requisition = $imprest_voucher->requisition()->project_requisition();
                if ($project_requisition) {
                    $project = $project_requisition->project();
                    $project_id = $project->{$project::DB_TABLE_PK};
                } else {
                    $project_id = null;
                }

                if (!empty($retired_material_items)) {
                    foreach ($retired_material_items as $item) {
                        $material_stock = new Material_stock();
                        $material_stock->quantity = $item->quantity;
                        $material_stock->sub_location_id = $imprest_voucher_retirement->sub_location_id;
                        $material_stock->project_id = $project_id;
                        $material_stock->item_id = $item->item_id;
                        $material_stock->price = $item->rate;
                        $material_stock->date_received = $imprest_voucher_retirement->retirement_date;
                        $material_stock->receiver_id = $imprest_voucher_retirement->created_by;
                        $material_stock->description = 'Received Under Imprest Voucher Retirement No. ' . $imprest_voucher_retirement->{$imprest_voucher_retirement::DB_TABLE_PK} . ' of Imprest Voucher No.' . $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
                        $material_stock->save();
                        $material_stock->update_average_price();

                        $grn_item = new Goods_received_note_material_stock_item();
                        $grn_item->grn_id = $grn->{$grn::DB_TABLE_PK};
                        $grn_item->remarks = $material_stock->description;
                        $grn_item->stock_id = $material_stock->{$material_stock::DB_TABLE_PK};
                        $grn_item->rejected_quantity = 0;
                        $grn_item->save();
                    }
                }

                if (!empty($retired_asset_items)) {
                    foreach ($retired_asset_items as $asset_item) {
                        $quantity = $asset_item->quantity;
                        for ($i = 0; $i < $quantity; $i++) {
                            $asset = new Asset();
                            $asset->asset_item_id = $asset_item->asset_item_id;
                            $asset->book_value = $asset_item->book_value;
                            $asset->salvage_value = 0;
                            $asset->status = 'active';
                            $asset->created_by = $imprest_voucher_retirement->created_by;
                            $asset->description = 'Received Under Imprest Voucher Retirement No. ' . $imprest_voucher_retirement->{$imprest_voucher_retirement::DB_TABLE_PK} . ' of Imprest Voucher No.' . $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
                            if ($asset->save()) {

                                $history = new Asset_sub_location_history();
                                $history->asset_id = $asset->{$asset::DB_TABLE_PK};
                                $history->book_value = $asset->book_value;
                                $history->sub_location_id = $imprest_voucher_retirement->sub_location_id;
                                $history->description = 'Received Under Imprest Voucher Retirement No. ' . $imprest_voucher_retirement->{$imprest_voucher_retirement::DB_TABLE_PK} . ' of Imprest Voucher No.' . $imprest_voucher->{$imprest_voucher::DB_TABLE_PK};
                                $history->project_id = $project_id;
                                $history->received_date = $imprest_voucher_retirement->retirement_date;
                                $history->created_by = $imprest_voucher_retirement->created_by;
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

            if ((!empty($retired_cash) || !empty($retired_services)) && intval($retirement_to) > 0) {
                $imprest_voucher_retirement->retirement_to =  intval(str_replace("real_","",$retirement_to));
                $imprest_voucher_retirement->save();
            }
        }
    }

    public function disapprove_retirement_examination()
    {
        $this->load->model('imprest_voucher_retirement');
        $imprest_voucher_retirement = new Imprest_voucher_retirement();
        $imprest_voucher_retirement->load($this->input->post('imprest_voucher_retirement_id'));
        $imprest_voucher_retirement->is_examined = 2;
        $imprest_voucher_retirement->examination_date = $this->input->post('examination_date');
        $imprest_voucher_retirement->examined_by = $this->session->userdata('employee_id');
        if ($imprest_voucher_retirement->save()) {
            $imprest_voucher_retirement->delete_retired_items();

            $employee = $imprest_voucher_retirement->created_by();
            $examiner = $imprest_voucher_retirement->examined_by();
            $imprest_voucher = $imprest_voucher_retirement->imprest_voucher();
            $addresses[] = $employee->email;

            $this->load->library('email');
            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://chi-node11.websitehostserver.net',
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
            $subject = 'IMPREST VOUCHER RETIREMENT NO: ' . $imprest_voucher_retirement->imprest_voucher_retirement_number() . ' FOR IMPREST VOUCHER' . $imprest_voucher->imprest_voucher_number();
            $content = '<div style="margin: auto" class="info-box-content">Greetings ' . $employee->first_name . ',<br/> The retirement mentioned above that you submitted on ' . custom_standard_date($imprest_voucher_retirement->retirement_date) . ' has been rejected by ' . $examiner->full_name() . '
                        in the system,<br/> Please <a href="' . base_url() . '">login</a> and search for retirement mentioned above<hr/></div><br/>';

            $message = $this->load->view('includes/email', ['content' => $content], true);

            $this->email->to($addresses);
            $this->email->subject($subject);
            $this->email->set_mailtype("html");
            $this->email->message($message);
            $this->email->send();
        }
    }

    public function preview_imprest_voucher_retirement($imprest_voucher_retirement_id = 0)
    {
        $this->load->model('imprest_voucher_retirement');
        $imprest_voucher_retirement = new Imprest_voucher_retirement();
        if ($imprest_voucher_retirement->load($imprest_voucher_retirement_id)) {
            $imprest_voucher = $imprest_voucher_retirement->imprest_voucher();
            $this->load->library(['m_pdf']);

            $data['retirement'] = $imprest_voucher_retirement;
            $data['imprest_voucher'] = $imprest_voucher_retirement->imprest_voucher();
            $data['requisition'] = $imprest_voucher->requisition_approval()->requisition();
            $html = $this->load->view('finance/transactions/approved_cash_requests/imprest/imprest_voucher_retirement_sheet', $data, true);

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
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Retirement' . add_leading_zeros($imprest_voucher_retirement->imprest_voucher_retirement_number()) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function imprests()
    {
        if (check_permission('Finance') || check_permission('Procurements')) {
            if (!is_null($this->input->post('length'))) {
                $this->load->model('imprest_voucher');
                $posted_params = dataTable_post_params();
                echo $this->imprest_voucher->imprests_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
            } else {
                $data['title'] = 'Finance | Imprests';
                $data['account_options'] = account_dropdown_options(['CASH IN HAND', 'BANK']);
                $data['currency_options'] = currency_dropdown_options();

                $this->load->view('finance/transactions/approved_cash_requests/imprest/index', $data);
            }
        } else {
            redirect(base_url());
        }
    }

    public function preview_imprest_voucher($imprest_voucher_id = 0)
    {
        $this->load->model('imprest_voucher');
        $imprest_voucher = new Imprest_voucher();
        if ($imprest_voucher->load($imprest_voucher_id)) {
            $this->load->library(['m_pdf']);

            $data['imprest_voucher'] = $imprest_voucher;
            $html = $this->load->view('finance/transactions/approved_cash_requests/imprest/imprest_voucher_sheet', $data, true);

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
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Imprest Voucher' . add_leading_zeros($imprest_voucher->imprest_voucher_number()) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function preview_payment_voucher($pv_id = 0)
    {
        $this->load->model('payment_voucher');
        $payment_voucher = new Payment_voucher();
        if ($payment_voucher->load($pv_id)) {
            $this->load->library(['m_pdf']);

            $data['payment_voucher'] = $payment_voucher;
            $html = $this->load->view('finance/documents/payment_voucher', $data, true);

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
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Payment Voucher' . add_leading_zeros($payment_voucher->payment_voucher_number()) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function settings()
    {
        $data['title'] = 'Finance | Settings';
        $this->load->model(['currency', 'account_group', 'bank']);
        $data['exchange_currencies'] = $this->currency->get(0, 0, ['is_native' => '0']);
        $data['account_group_options'] = $this->account_group->account_group_options();
        $data['currency_options'] = currency_dropdown_options();
        $data['bank_options'] = $this->bank->bank_options();
        $data['parent_groups'] = $this->account_group->parents();
        $this->load->view('finance/settings/index', $data);
    }

    public function account_groups_list()
    {
        $this->load->model('account_group');
        $posted_params = dataTable_post_params();
        echo $this->account_group->account_groups_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function currencies_list()
    {
        $this->load->model('currency');
        $posted_params = dataTable_post_params();
        echo $this->currency->currencies_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function cost_centers_list()
    {
        $this->load->model('Cost_center');
        $posted_params = dataTable_post_params();
        echo $this->Cost_center->cost_centers_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function delete_currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        if ($currency->load($this->input->post('currency_id'))) {
            $currency->delete();
        }
    }

    public function delete_cost_center()
    {
        $this->load->model('Cost_center');
        $Cost_center = new Cost_center();
        if ($Cost_center->load($this->input->post('cost_center_id'))) {
            $Cost_center->delete();
        }
    }

    public function save_currency()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $edit = $currency->load($this->input->post('currency_id'));
        $currency->currency_name = $this->input->post('currency_name');
        $currency->is_native = 0;
        $currency->symbol = $this->input->post('symbol');
        if ($currency->save() && !$edit) {
            $this->load->model('exchange_rate_update');
            $exchange_rate = new Exchange_rate_update();
            $exchange_rate->currency_id = $currency->{$currency::DB_TABLE_PK};
            $exchange_rate->update_date = "1990-01-01";
            $exchange_rate->exchange_rate = $this->input->post('rate_to_native');
            $exchange_rate->save();
        }
    }

    public function save_cost_center()
    {

        $this->load->model('Cost_center');
        $Cost_center = new Cost_center();
        $edit = $Cost_center->load($this->input->post('cost_center_id'));
        $Cost_center->cost_center_name = $this->input->post('cost_center_name');
        $Cost_center->description = $this->input->post('description');
        $Cost_center->save();
    }

    public function update_exchange_rates()
    {
        $this->load->model('exchange_rate_update');
        $currency_ids = $this->input->post('currency_ids');
        $date = $this->input->post('date');
        foreach ($currency_ids as $index => $currency_id) {
            $rate_update = new Exchange_rate_update();
            $rate_update->currency_id = $currency_id;
            $rate_update->update_date = $date;
            $rate_update->exchange_rate = $this->input->post('exchange_rates')[$index];
            $rate_update->save();
        }
    }

    public function account_cash_requisitions_list()
    {
        $this->load->model('cash_requisition');
        $posted_params = dataTable_post_params();
        echo $this->cash_requisition->cash_requisitions_list($this->input->post('account_id'), $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function load_cost_centers_dropdown_options()
    {
        $this->load->model('cost_center');
        echo stringfy_dropdown_options($this->cost_center->dropdown_options());
    }

    public function creditors_options()
    {
        $this->load->model(['stakeholder']);
        $creditors_options = $this->stakeholder->stakeholder_with_unpaid_claims();
        $creditors_options = $creditors_options;
        return $creditors_options;
    }

    public function approved_cash_requisitions_list($account_id = null)
    {
        $this->load->model('requisition_approval');
        $posted_params = dataTable_post_params();
        echo $this->requisition_approval->approved_cash_requisitions_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $account_id);
    }

    public function save_pay_project_certificate()
    {

        $payment_dates = $this->input->post('payment_date');
        if ($payment_dates != '') {
            $this->load->model('Receipt');
            $receipt = new Receipt();
            $receipt->debit_account_id = $this->input->post('debit_account_id');
            $receipt->receipt_date = $payment_dates;
            $receipt->reference = $this->input->post('reference');
            $receipt->currency_id = $this->input->post('currency_id');
            $receipt->exchange_rate = $this->input->post('exchange_rate');
            $receipt->remarks = $this->input->post('comment');
            $receipt->created_by = $this->session->userdata('employee_id');

            if ($receipt->save()) {

                $this->load->model(['receipt_item', 'Project_certificate_receipt']);
                $receipt_item = new Receipt_item();
                $receipt_item->receipt_id = $receipt->{$receipt::DB_TABLE_PK};
                $receipt_item->credit_account_id = $this->input->post('credit_account_id');
                $receipt_item->amount = $this->input->post('amount');
                $receipt_item->remarks = $this->input->post('comment');
                $receipt_item->save();

                $project_certificates_receipt = new Project_certificate_receipt();
                $project_certificates_receipt->receipt_id = $receipt->{$receipt::DB_TABLE_PK};
                $project_certificates_receipt->certificate_id = $this->input->post('certificate_id');
                $project_certificates_receipt->with_holding_tax = $this->input->post('with_holding_tax');
                $project_certificates_receipt->save();
            }
        }
    }

    public function get_exchange_rate()
    {
        $this->load->model('currency');
        $currency = new Currency();
        $currency->load($this->input->post('currency_id'));
        echo $currency->rate_to_native($this->input->post('date'));
    }

    public function save_payment_voucher()
    {
        $this->load->model(['payment_voucher', 'journal_voucher']);
        $payment = new Payment_voucher();
        $journal = new Journal_voucher();
        $request_type = $this->input->post('request_type');
        $posted_item = $this->input->post('credit_account_id');
        $credit_account_id = !preg_match('/^[0-9]+$/', $posted_item) ? explode('_', $posted_item)[1] : $posted_item;
        if (!empty($credit_account_id)) {

            $journal->transaction_date = $this->input->post('payment_date');
            $journal->reference = $this->input->post('reference');
            $journal->journal_type = "CASH PAYMENT";
            $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
            $journal->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
            $journal->currency_id = $this->input->post('currency_id');
            $journal->remarks = $this->input->post('remarks');
            $journal->created_by = $this->session->userdata('employee_id');
            if ($journal->save()) {

                $payment->payment_date = $this->input->post('payment_date');
                $payment->cheque_number = $this->input->post('cheque_number');
                $payment->reference = $this->input->post('reference');
                $payment->payee = $this->input->post('payee');
                $payment->currency_id = $this->input->post('currency_id');
                $payment->exchange_rate = $this->input->post('exchange_rate');
                $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
                $payment->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
                $withholding_tax = $this->input->post('wht_percentage');
                $payment->withholding_tax = $withholding_tax != '' ? $withholding_tax : null;
                $vat_percentage = $this->input->post('vat_percentage');
                $payment->vat_percentage =  $vat_percentage != '' ? $vat_percentage : 0;
                $payment->remarks = $this->input->post('remarks');
                $payment->employee_id = $this->session->userdata('employee_id');
                if ($payment->save()) {
                    $this->load->model([
                        'journal_payment_voucher',
                        'journal_voucher_credit_account',
                        'payment_voucher_credit_account',
                        'journal_voucher_item',
                        'payment_voucher_item',
                        'invoice',
                        'sub_contract_payment_requisition_approval',
                        'sub_contract_payment_requisition_approval_payment_voucher',
                        'purchase_order_payment_request_approval',
                        'purchase_order_payment_request_approval_payment_voucher',
                        'requisition_approval',
                        'requisition_approval_payment_voucher',
                        'purchase_order_payment_request_approval_invoice_item',
                        'sub_contract_payment_requisition_approval_item',
                        'payment_voucher_item_approved_cash_request_item',
                        'payment_voucher_item_approved_invoice_item',
                        'payment_voucher_item_approved_sub_contract_requisition_item',
                        'sub_contract_certificate_payment_voucher',
                        'invoice_payment_voucher',
                        'sub_contract_payment_requisition',
                        'withholding_tax',
                        'currency'
                    ]);

                    $jv_pv = new Journal_payment_voucher();
                    $jv_pv->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                    $jv_pv->journal_id = $journal->{$journal::DB_TABLE_PK};
                    $jv_pv->save();

                    $pv_crdt_acc = new Payment_voucher_credit_account();
                    $pv_crdt_acc->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                    if (explode('_', $posted_item)[0] == "stakeholder") {
                        $pv_crdt_acc->stakeholder_id = $credit_account_id;
                    } else {
                        $pv_crdt_acc->account_id = $credit_account_id;
                    }
                    $pv_crdt_acc->amount = $this->input->post('amount_paid');
                    $pv_crdt_acc->narration = $payment->remarks;
                    $pv_crdt_acc->save();

                    $jv_credit_acc = new Journal_voucher_credit_account();
                    if (explode('_', $posted_item)[0] == "stakeholder") {
                        $jv_credit_acc->stakeholder_id = $credit_account_id;
                    } else {
                        $jv_credit_acc->account_id = $credit_account_id;
                    }
                    $jv_credit_acc->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                    $jv_credit_acc->amount = $this->input->post('amount_paid');
                    $jv_credit_acc->narration = $payment->remarks;
                    $jv_credit_acc->save();

                    $debit_accounts_ids = $this->input->post('debit_accounts_ids');
                    $item_types = $this->input->post('item_types');
                    $junction_type = $this->input->post('junction_type');
                    $junction_id = $this->input->post('junction_id');
                    $amounts = $this->input->post('amounts');
                    $quantities = $this->input->post('quantities');
                    $rates = $this->input->post('rates');
                    $descriptions = $this->input->post('descriptions');
                    $item_ids = $this->input->post('item_ids');
                    $amount_paid = $this->input->post('amount_paid');
                    $total_vat_amount = 0;
                    foreach ($item_types as $index => $item_type) {
                        if ($amounts[$index] > 0) {
                            $posted_debit_item = $debit_accounts_ids[$index];
                            $debit_accounts_id = !preg_match('/^[0-9]+$/', $posted_debit_item) ? explode('_', $posted_debit_item)[1] : $posted_debit_item;
                            if ($request_type == "requisition") {
                                $this->load->model($junction_type . '_payment_voucher_item');
                                if ($quantities[$index] > 0) {
                                    $payment_voucher_item = new Payment_voucher_item();
                                    $payment_voucher_item->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                                    if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                        $payment_voucher_item->stakeholder_id = $debit_accounts_id;
                                    } else {
                                        $payment_voucher_item->debit_account_id = $debit_accounts_id;
                                    }
                                    $payment_voucher_item->amount = $amounts[$index];
                                    if ($payment->vat_percentage > 0) {
                                        $payment_voucher_item->vat_amount = 0.01 * $payment->vat_percentage * $amounts[$index];
                                    } else {
                                        $payment_voucher_item->vat_amount = 0;
                                    }
                                    $payment_voucher_item->description = $descriptions[$index] . ' @ ' . $rates[$index];
                                    if ($payment_voucher_item->save()) {
                                        $total_vat_amount += $payment_voucher_item->vat_amount;

                                        $jv_item = new Journal_voucher_item();
                                        $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                                        $jv_item->amount = $payment_voucher_item->amount;
                                        if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                            $jv_item->stakeholder_id = $debit_accounts_id;
                                        } else {
                                            $jv_item->debit_account_id = $debit_accounts_id;
                                        }
                                        $jv_item->narration = $payment_voucher_item->description;
                                        $jv_item->save();
                                        if ($item_types[$index] != 'material' && $item_types[$index] != 'asset') {
                                            if ($junction_type == 'project') {
                                                $junction = new Project_payment_voucher_item();
                                                $junction->project_id = $junction_id;
                                                $junction->payment_voucher_item_id = $payment_voucher_item->{$payment_voucher_item::DB_TABLE_PK};
                                                $junction->save();
                                            } else {
                                                $junction = new Cost_center_payment_voucher_item();
                                                $junction->cost_center_id = $junction_id;
                                                $junction->payment_voucher_item_id = $payment_voucher_item->{$payment_voucher_item::DB_TABLE_PK};
                                                $junction->save();
                                            }

                                            $pv_item_approved_request_item = new Payment_voucher_item_approved_cash_request_item();
                                            $pv_item_approved_request_item->payment_voucher_item_id = $payment_voucher_item->{$payment_voucher_item::DB_TABLE_PK};
                                            $pv_item_approved_request_item->quantity = $quantities[$index];
                                            $pv_item_approved_request_item->rate = $rates[$index];
                                            switch ($item_types[$index]) {
                                                case 'material':
                                                    $pv_item_approved_request_item->requisition_approval_material_item_id = $item_ids[$index];
                                                    break;
                                                case 'asset':
                                                    $pv_item_approved_request_item->requisition_approval_asset_item_id = $item_ids[$index];
                                                    break;
                                                case 'service':
                                                    $pv_item_approved_request_item->requisition_approval_service_item_id = $item_ids[$index];
                                                    break;
                                                case 'cash':
                                                    $pv_item_approved_request_item->requisition_approval_cash_item_id = $item_ids[$index];
                                                    break;
                                            }
                                            $pv_item_approved_request_item->save();
                                        }
                                    }
                                }
                            } else if ($request_type == "payment_request_invoice") {
                                $popra = new Purchase_order_payment_request_approval();
                                $popra->load($this->input->post('requisition_approval_id'));
                                $order_for = $popra->purchase_order_payment_request()->purchase_order()->purchase_order_nature();
                                $popra_inv_item = new Purchase_order_payment_request_approval_invoice_item();
                                $popra_inv_item->load($item_ids[$index]);
                                $invoice = $popra_inv_item->purchase_order_payment_request_invoice_item()->invoice();
                                $payment_item = new Payment_voucher_item();
                                $payment_item->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                                if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                    $payment_item->stakeholder_id = $debit_accounts_id;
                                } else {
                                    $payment_item->debit_account_id = $debit_accounts_id;
                                }
                                if ($payment->vat_percentage > 0) {
                                    $payment_item->vat_amount = 0.01 * $payment->vat_percentage * $amount_paid;
                                    //									$jv_item = new Journal_voucher_item();
                                    //									$jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                                    //									$jv_item->amount = $payment_item->vat_amount;
                                    //									$vat_returns = $this->vat_returns_account($order_for);
                                    //									$jv_item->debit_account_id = $vat_returns->{$vat_returns::DB_TABLE_PK};
                                    //									$jv_item->narration = 'VAT input '.$invoice->stakeholder()->stakeholder_name;
                                    //									$jv_item->save();
                                } else {
                                    $payment_item->vat_amount = 0;
                                }
                                $amount_withheld = (0.01 * $payment->withholding_tax * $amount_paid);
                                $payment_item->amount = $amount_paid - $amount_withheld;
                                $payment_item->description = 'Being Payment for ' . $invoice->correspondence_number();
                                if ($payment_item->save()) {
                                    $jv_item = new Journal_voucher_item();
                                    $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                                    $jv_item->amount = $payment_item->amount;
                                    if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                        $jv_item->stakeholder_id = $debit_accounts_id;
                                    } else {
                                        $jv_item->debit_account_id = $debit_accounts_id;
                                    }
                                    $jv_item->narration = $payment_item->description;
                                    $jv_item->save();

                                    if ($payment->withholding_tax != null && $payment->withholding_tax > 0) {
                                        $withholding_tax = new Withholding_tax();
                                        $withholding_tax->date = $payment->payment_date;
                                        if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                            $withholding_tax->stakeholder_id = $payment_item->stakeholder_id;
                                        } else {
                                            $withholding_tax->credit_account_id = $payment_item->debit_account_id;
                                        }
                                        $withholding_tax_account = $payment_item->withholding_tax_account();
                                        $withholding_tax->debit_account_id = $withholding_tax_account->{$withholding_tax_account::DB_TABLE_PK};
                                        $withholding_tax->remarks = 'Tax amount withheld for payment of invoice ' . $invoice->detailed_reference() . ' from  ' . strtoupper($invoice->purchase_order()->stakeholder()->stakeholder_name) . '';
                                        $withholding_tax->payment_voucher_item_id = $payment_item->{$payment_item::DB_TABLE_PK};
                                        $withholding_tax->currency_id = $payment->currency_id;
                                        $withholding_tax->currency_id = $payment->currency_id;
                                        $withholding_tax->withheld_amount = $amount_withheld;
                                        $withholding_tax->status = "PENDING";
                                        $withholding_tax->created_by = $this->session->userdata('employee_id');
                                        $withholding_tax->save();


                                        $jv_item = new Journal_voucher_item();
                                        $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                                        $jv_item->amount = $withholding_tax->withheld_amount;
                                        $jv_item->stakeholder_id = null;
                                        $jv_item->debit_account_id = $withholding_tax->debit_account_id;
                                        $jv_item->narration = $withholding_tax->remarks;
                                        $jv_item->save();
                                    }

                                    $pv_item_invoice_item = new Payment_voucher_item_approved_invoice_item();
                                    $pv_item_invoice_item->payment_voucher_item_id = $payment_item->{$payment_item::DB_TABLE_PK};
                                    $pv_item_invoice_item->purchase_order_payment_request_approval_invoice_item_id = $item_ids[$index];
                                    $pv_item_invoice_item->save();
                                }

                                $invoice_payment_voucher = new Invoice_payment_voucher();
                                $invoice_payment_voucher->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                $invoice_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                                $invoice_payment_voucher->save();
                            } else if ($request_type == "sub_contract_payment_requisition") {
                                $requisition_approval = new Sub_contract_payment_requisition_approval();
                                $requisition_approval->load($this->input->post('requisition_approval_id'));
                                $project = $requisition_approval->sub_contract_requisition()->project();
                                $approved_item_id = $this->input->post('item_ids')[$index];
                                $approved_item =  new Sub_contract_payment_requisition_approval_item();
                                $approved_item->load($approved_item_id);
                                $sub_contractor = $approved_item->sub_contract_payment_requisition_item()->certificate()->sub_contract()->stakeholder();
                                $payment_item = new Payment_voucher_item();
                                $payment_item->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                                if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                    $payment_item->stakeholder_id = $debit_accounts_id;
                                } else {
                                    $payment_item->debit_account_id = $debit_accounts_id;
                                }
                                if ($payment->vat_percentage > 0) {
                                    $payment_item->vat_amount = (0.01 * $payment->vat_percentage * $amount_paid);
                                    //									$jv_item = new Journal_voucher_item();
                                    //									$jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                                    //									$jv_item->amount = $payment_item->vat_amount;
                                    //									$vat_returns = $this->vat_returns_account(['project',$project->{$project::DB_TABLE_PK}]);
                                    //									$jv_item->debit_account_id = $vat_returns->{$vat_returns::DB_TABLE_PK};
                                    //									$jv_item->narration = 'VAT input '.$sub_contractor->stakeholder_name;
                                    //									$jv_item->save();
                                } else {
                                    $payment_item->vat_amount = 0;
                                }
                                $amount_withheld = (0.01 * $payment->withholding_tax * $amount_paid);
                                $payment_item->amount = $amount_paid - round($amount_withheld, 2);
                                $payment_item->description = 'Being Payment for ' . $requisition_approval->sub_contract_requisition()->sub_contract_requisition_number();
                                if ($payment_item->save()) {
                                    $jv_item = new Journal_voucher_item();
                                    $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                                    $jv_item->amount = $payment_item->amount;
                                    if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                        $jv_item->stakeholder_id = $debit_accounts_id;
                                    } else {
                                        $jv_item->debit_account_id = $debit_accounts_id;
                                    }
                                    $jv_item->narration = $payment_item->description;
                                    $jv_item->save();

                                    if ($payment->withholding_tax != null && $payment->withholding_tax > 0) {
                                        $withholding_tax = new Withholding_tax();
                                        $withholding_tax->date = $payment->payment_date;
                                        if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                                            $withholding_tax->stakeholder_id = $payment_item->stakeholder_id;
                                        } else {
                                            $withholding_tax->credit_account_id = $payment_item->debit_account_id;
                                        }
                                        $withholding_tax_account = $payment_item->withholding_tax_account();
                                        $withholding_tax->debit_account_id = $withholding_tax_account->{$withholding_tax_account::DB_TABLE_PK};
                                        $withholding_tax->remarks = 'Tax amount withheld for payment of sub contract certifcate(s) listed in ' . $requisition_approval->sub_contract_requisition()->sub_contract_requisition_number() . ' - ' . $sub_contractor->stakeholder_name . '';
                                        $withholding_tax->payment_voucher_item_id = $payment_item->{$payment_item::DB_TABLE_PK};
                                        $withholding_tax->currency_id = $payment->currency_id;
                                        $withholding_tax->withheld_amount = $amount_withheld;
                                        $withholding_tax->status = "PENDING";
                                        $withholding_tax->created_by = $this->session->userdata('employee_id');
                                        $withholding_tax->save();


                                        $jv_item = new Journal_voucher_item();
                                        $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                                        $jv_item->amount = $withholding_tax->withheld_amount;
                                        $jv_item->stakeholder_id = null;
                                        $jv_item->debit_account_id = $withholding_tax->debit_account_id;
                                        $jv_item->narration = $withholding_tax->remarks;
                                        $jv_item->save();
                                    }

                                    $pv_item_approved_requisition_item = new Payment_voucher_item_approved_sub_contract_requisition_item();
                                    $pv_item_approved_requisition_item->payment_voucher_item_id = $payment_item->{$payment_item::DB_TABLE_PK};
                                    $pv_item_approved_requisition_item->sub_contract_payment_requisition_approval_item_id = $item_ids[$index];
                                    $pv_item_approved_requisition_item->save();

                                    $certificate_payment_voucher = new Sub_contract_certificate_payment_voucher();
                                    $certificate = $pv_item_approved_requisition_item->sub_contract_payment_requisition_approval_item()->sub_contract_payment_requisition_item()->certificate();
                                    $certificate_payment_voucher->sub_contract_certificate_id = $certificate->{$certificate::DB_TABLE_PK};
                                    $certificate_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                                    $certificate_payment_voucher->save();
                                }
                            } else {
                                $payment_voucher_item = new Payment_voucher_item();
                                $payment_voucher_item->amount = $amounts[$index];
                                $payment_voucher_item->debit_account_id = $debit_accounts_id;
                                $payment_voucher_item->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                                $payment_voucher_item->description = $descriptions[$index];
                                $payment_voucher_item->vat_amount = 0;
                                $payment_voucher_item->save();
                            }
                        }
                    }

                    if ($request_type == "requisition") {
                        $approval_pv = new Requisition_approval_payment_voucher();
                        $approval_pv->requisition_approval_id = $this->input->post('requisition_approval_id');
                        $approval_pv->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $approval_pv->save();

                        //                        if ($payment->vat_percentage > 0) {
                        //                        $requisition = $approval_pv->requisition_approval()->requisition();
                        //                        switch($requisition->requested_for()){
                        //                            case 'project':
                        //                                $project = $requisition->project();
                        //                                $cost_center_id = $project->{$project::DB_TABLE_PK};
                        //                                $nature = 'project';
                        //                                break;
                        //                            case 'cost_center':
                        //                                $cost_center = $requisition->cost_center();
                        //                                $cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
                        //                                $nature = 'cost_center';
                        //                                break;
                        //                        }
                        //                        $jv_item = new Journal_voucher_item();
                        //                        $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                        //                        $jv_item->amount = $total_vat_amount;
                        //                        $vat_returns = $this->vat_returns_account([$nature, $cost_center_id]);
                        //                        $jv_item->debit_account_id = $vat_returns->{$vat_returns::DB_TABLE_PK};
                        //                        $jv_item->narration = 'VAT input '.$approval_pv->requisition_approval()->requisition()->requisition_number();
                        //                        $jv_item->save();
                        //                        }
                    } else if ($request_type == "payment_request_invoice") {
                        $payment_request_approval_payment_voucher = new Purchase_order_payment_request_approval_payment_voucher();
                        $payment_request_approval_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $payment_request_approval_payment_voucher->purchase_order_payment_request_approval_id = $this->input->post('requisition_approval_id');
                        $payment_request_approval_payment_voucher->save();
                    } else if ($request_type == "sub_contract_payment_requisition") {
                        $requisition_approval_payment_voucher = new Sub_contract_payment_requisition_approval_payment_voucher();
                        $requisition_approval_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $requisition_approval_payment_voucher->sub_contract_payment_requisition_approval_id = $this->input->post('requisition_approval_id');
                        $requisition_approval_payment_voucher->save();
                    }

                    $action = 'Payment Voucher Entry';
                    $description = 'Payment voucher number ' . $payment->payment_voucher_number() . ' for ' . $payment->cost_center_name() . ' was posted';
                    system_log($action, $description);
                }
            }
        }
    }

    public function receipts()
    {
        if (check_permission('Finance')) {
            if ($this->input->post('length') != null) {
                $this->load->model('Receipt');
                $datatables = dataTable_post_params();
                echo $this->Receipt->receipts_list($datatables['limit'], $datatables['start'], $datatables['keyword'], $datatables['order']);
            } else {
                $data['debit_account_options'] = account_dropdown_options(['CASH IN HAND', 'BANK']);
                $data['currency_options'] = currency_dropdown_options();
                $this->load->model('Project_certificate');
                $data['certificate_options'] = $this->Project_certificate->certificates_dropdown();
                $this->load->model('Stock_sale');
                $data['sales_options'] = $this->Stock_sale->stock_sales_dropdown();
                $data['title'] = 'Finance | Receipts';
                $this->load->view('finance/receipts/index', $data);
            }
        }
    }

    public function contras()
    {
        if (check_permission('Finance')) {
            if (!is_null($this->input->post('length'))) {
                $this->load->model('contra');
                $posted_params = dataTable_post_params();
                echo $this->contra->contras($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
            } else {
                $this->load->model('currency');
                $data['title'] = 'Finance | Contras';
                $data['account_options'] = account_dropdown_options(['CASH IN HAND', 'BANK']);
                $data['currency_options'] = $this->currency->dropdown_options();

                $this->load->view('finance/contras/index', $data);
            }
        } else {
            redirect(base_url());
        }
    }

    public function load_invoice_payment_requirements()
    {
        $this->load->model('invoice');
        $invoice = new Invoice();
        if ($invoice->load($this->input->post('invoice_id'))) {
            $options = $invoice->vendor()->accounts_dropdown_options();
            $ret_val['debit_account_options'] = stringfy_dropdown_options($options);
            $ret_val['currency_id'] = $invoice->currency_id;
            $ret_val['unpaid_amount'] = $invoice->unpaid_amount(true);
            echo json_encode($ret_val);
        } else {
            echo '';
        }
    }

    public function preview_receipt($receipt_number = 0)
    {
        $this->load->model('Receipt');
        $receipt = new Receipt();
        if ($receipt->load($receipt_number)) {
            //$data['receipt'] = $this->Receipt->reference();
            $data['receipt'] = $receipt;

            $html = $this->load->view('finance/documents/preview_receipt', $data, true);

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
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Receipt' . date('Y-m-d') . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function save_receipt()
    {
        $this->load->model(['receipt', 'journal_voucher']);
        $payment_dates = $this->input->post('receipt_date');
        if ($payment_dates != '') {
            $journal = new Journal_voucher();
            $journal->transaction_date = $payment_dates;
            $journal->reference = $this->input->post('reference');
            $journal->journal_type = "CASH PAYMENT";
            $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
            $journal->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
            $journal->currency_id = $this->input->post('currency_id');
            $journal->remarks = $this->input->post('comments');
            $journal->created_by = $this->session->userdata('employee_id');
            if ($journal->save()) {
                $receipt = new Receipt();
                $edit = $receipt->load($this->input->post('receipt_id'));
                $posted_debit_item = $this->input->post('debit_account_id');
                $debit_account_id = !preg_match('/^[0-9]+$/', $posted_debit_item) ? explode('_', $posted_debit_item)[1] : $posted_debit_item;
                $receipt->debit_account_id = $debit_account_id;
                $posted_credit_item = $this->input->post('credit_account_id');
                $credit_account_id = !preg_match('/^[0-9]+$/', $posted_credit_item) ? explode('_', $posted_credit_item)[1] : $posted_credit_item;
                $receipt->credit_account_id = $credit_account_id;
                $receipt->receipt_date = $payment_dates;
                $receipt->reference = $this->input->post('reference');
                $receipt->invoice_id = $this->input->post('invoice_id');
                $withholding_tax = $this->input->post('withholding_tax');
                $receipt->withholding_tax = $withholding_tax != '' ? $withholding_tax : null;
                $receipt->currency_id = $this->input->post('currency_id');
                $receipt->exchange_rate = $this->input->post('exchange_rate');
                $receipt->remarks = $this->input->post('comments');
                $receipt->created_by = $this->session->userdata('employee_id');
                if ($receipt->save()) {
                    $this->load->model([
                        'journal_receipt',
                        'journal_voucher_credit_account',
                        'journal_voucher_item',
                        'receipt_item',
                        'stock_sale_receipt',
                        'project_certificate_receipt',
                        'maintenance_service_receipt'
                    ]);
                    if ($edit) {
                        $receipt->clear_items();
                    }
                    $jv_rec = new Journal_receipt();
                    $jv_rec->receipt_id = $receipt->{$receipt::DB_TABLE_PK};
                    $jv_rec->journal_id = $journal->{$journal::DB_TABLE_PK};
                    $jv_rec->save();

                    $outgoing_invoice = $receipt->outgoing_invoice();
                    $debt_nature = $outgoing_invoice->outgoing_invoice_debt_nature();
                    $amount = $this->input->post('amount');
                    $receipt_item = new Receipt_item();
                    $receipt_item->receipt_id = $receipt->{$receipt::DB_TABLE_PK};
                    if (!is_null($receipt->withholding_tax) && $receipt->withholding_tax > 0) {
                        $amount_withheld = (0.01 * $receipt->withholding_tax * $amount);
                    } else {
                        $amount_withheld = 0;
                    }

                    $jv_credit_acc = new Journal_voucher_credit_account();
                    if (explode('_', $posted_credit_item)[0] == "stakeholder") {
                        $jv_credit_acc->stakeholder_id = $credit_account_id;
                    } else {
                        $jv_credit_acc->account_id = $credit_account_id;
                    }
                    $jv_credit_acc->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                    $jv_credit_acc->amount = $amount;
                    $jv_credit_acc->narration = $receipt->remarks;
                    $jv_credit_acc->save();
                    $receipt_item->amount = $amount - $amount_withheld;
                    if ($debt_nature == "certificate") {
                        $certificate = $outgoing_invoice->project_certificate();
                        $receipt_item->remarks = 'Received payment For Project Certificate No ' . $certificate->certificate_number . ' of ' . $certificate->project()->project_name . '.';
                        $withholding_tax_remarks = 'Tax amount withheld by ' . $outgoing_invoice->invoice_to()->client_name . ' for payment of Project Certificate No ' . $certificate->certificate_number;

                        $certificate_receipt = new Project_certificate_receipt();
                        $certificate_receipt->receipt_id = $receipt->{$receipt::DB_TABLE_PK};
                        $certificate_receipt->certificate_id = $certificate->{$certificate::DB_TABLE_PK};
                        $certificate_receipt->save();
                    } else if ($debt_nature == "maintenance_service") {
                        $maintenance_service = $outgoing_invoice->maintenance_service();
                        $receipt_item->remarks = 'Received payment For Maintenance Service No ' . $maintenance_service->maintenance_services_no();
                        $withholding_tax_remarks = 'Tax amount withheld by ' . $outgoing_invoice->invoice_to()->client_name . ' for payment of Maintenance Service No ' . $maintenance_service->maintenance_services_no();

                        $maintenance_svc_receipt = new Maintenance_service_receipt();
                        $maintenance_svc_receipt->receipt_id = $receipt->{$receipt::DB_TABLE_PK};
                        $maintenance_svc_receipt->maintenance_service_id = $maintenance_service->{$maintenance_service::DB_TABLE_PK};
                        $maintenance_svc_receipt->save();
                    } else if ($debt_nature == "stock_sale") {
                        $stock_sale = $outgoing_invoice->stock_sale();
                        $receipt_item->remarks = 'Received payment For Stock Sale No ' . $stock_sale->sale_number();

                        $stock_sale_receipt = new Stock_sale_receipt();
                        $stock_sale_receipt->receipt_id = $receipt->{$receipt::DB_TABLE_PK};
                        $stock_sale_receipt->stock_sale_id = $stock_sale->{$stock_sale::DB_TABLE_PK};
                        $stock_sale_receipt->save();
                    }

                    if ($receipt_item->save()) {
                        $jv_item = new Journal_voucher_item();
                        $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                        $jv_item->amount = $receipt_item->amount;
                        if (explode('_', $posted_debit_item)[0] == "stakeholder") {
                            $jv_item->stakeholder_id = $debit_account_id;
                        } else {
                            $jv_item->debit_account_id = $debit_account_id;
                        }
                        $jv_item->narration = $receipt_item->remarks;
                        $jv_item->save();

                        if ($receipt->withholding_tax != null && $receipt->withholding_tax > 0 && $debt_nature != "stock_sale") {
                            $this->load->model(['withholding_tax', 'currency']);
                            $withholding_tax = new Withholding_tax();
                            $withholding_tax->date = $receipt->receipt_date;
                            $invoice_to = $outgoing_invoice->invoice_to();
                            $withholding_tax->stakeholder_id = $invoice_to->{$invoice_to::DB_TABLE_PK};
                            $withholding_tax_account = $receipt_item->withholding_tax_account();
                            $withholding_tax->debit_account_id = $withholding_tax_account->{$withholding_tax_account::DB_TABLE_PK};
                            $withholding_tax->remarks = $withholding_tax_remarks;
                            $withholding_tax->payment_voucher_item_id = null;
                            $withholding_tax->receipt_item_id = $receipt_item->{$receipt_item::DB_TABLE_PK};
                            $withholding_tax->currency_id = $receipt->currency_id;
                            $withholding_tax->withheld_amount = $amount_withheld;
                            $withholding_tax->status = "PENDING";
                            $withholding_tax->created_by = $this->session->userdata('employee_id');
                            $withholding_tax->save();
                        }
                    }
                }
            }
        }
    }

    public function delete_receipt()
    {
        $this->load->model('Receipt');
        $receipt = new Receipt();
        $receipt->load($this->input->post('receipt_id'));
        $receipt->delete();
    }

    public function expense_voucher_cost_center_options()
    {
        $this->load->model(['cost_center', 'project', 'department']);
        $type = $this->input->post('type');

        if ($type == 'project') {
            echo stringfy_dropdown_options(projects_dropdown_options());
        } else if ($type == 'cost_center') {
            echo stringfy_dropdown_options($this->cost_center->dropdown_options());
        } else if ($type == 'department') {
            echo stringfy_dropdown_options($this->department->department_options());
        } else {
            echo '';
        }
    }

    public function delete_payments()
    {
        $this->load->model('Payment_voucher');
        $payment_voucher = new Payment_voucher();
        $payment_voucher->load($this->input->post('delete_payment_id'));
        $payment_voucher->delete();
    }

    public function statements()
    {
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $account_id = $this->input->post('account_id');
        $currency_id = $this->input->post('currency_id');
        $opening_balance_date = new DateTime($from);
        $opening_balance_date->modify(' - 1 day');
        $opening_balance_date = $opening_balance_date->format('Y-m-d');
        $data['print_pdf'] = $this->input->post('print_pdf');
        $data['export_excel'] = $this->input->post('export_excel');
        $data['from'] = $from;
        $data['to'] = $to;
        $account = new Account();
        if ($account->load($account_id)) {
            $this->load->model(['currency', 'imprest_voucher']);
            $currency = new Currency();
            $currency->load($currency_id);
            $data['currency'] = $currency;

            $data['account'] = $account;
            $data['transactions'] = $transactions = $account->statement($currency_id, $from, $to);
            $data['opening_balance'] = $opening_balance = $account->balance($currency_id, $opening_balance_date);
            if ($data['print_pdf'] || $data['export_excel']) {
                if ($data['print_pdf']) {
                    $html = $this->load->view('finance/statements/statement_sheet', $data, true);

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

                    $pdf->Output('Account Statement.pdf', 'I'); // view in the explorer

                } else {

                    $balance =  $account->balance($currency_id, $opening_balance_date);
                    $filename = $account->account_name . ' Account Statement from ' . custom_standard_date($from) . ' to ' . custom_standard_date($to);

                    $this->load->library('excel');
                    $object = $this->excel;
                    $object->setActiveSheetIndex(0);

                    PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
                    $object->getProperties()->setTitle($account->account_name . ' Account Statement ');
                    $object->getProperties()->setCreator($this->session->userdata('employee_name'));

                    for ($col_index = 'A'; $col_index !== 'H'; $col_index++) {
                        $object->getActiveSheet()->getColumnDimension($col_index)->setAutoSize(true);
                    }

                    $table_columns = array("Date", "Transaction Type", "Description", "Reference", "Debit", "Credit", "Balance");
                    $column = 0;
                    foreach ($table_columns as $field) {
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);
                        $column++;
                    }
                    $object->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
                    $object->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                    $object->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
                    $object->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
                    $object->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
                    $object->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
                    $object->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);

                    $object->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
                    $object->getActiveSheet()->getStyle('B5')->getFont()->setItalic(true);
                    $object->getActiveSheet()->getStyle('G5')->getFont()->setBold(true);

                    $object->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $object->getActiveSheet()->getStyle('G5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                    $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Account: ' . $account->account_name);
                    $object->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Currency: ' . $currency->name_and_symbol());
                    $object->getActiveSheet()->mergeCells('A1:G1');
                    $object->getActiveSheet()->mergeCells('A2:G2');
                    $object->getActiveSheet()->mergeCells('A4:G4');

                    $excel_row = 5;
                    $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, custom_standard_date($from));
                    $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, "Opening Balance");
                    $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, accountancy_number($balance));
                    $object->getActiveSheet()->mergeCells('B5:F5');
                    $excel_row = 6;

                    foreach ($transactions as $transaction) {
                        $balance = $balance + $transaction['debit'] - $transaction['credit'];

                        if ($transaction['debit'] != 0 || $transaction['credit'] != 0) {

                            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, custom_standard_date($transaction['transaction_date']));
                            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $transaction['transaction_type']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $transaction['remarks']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $transaction['detailed_reference']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $transaction['debit'] != 0 ? number_format($transaction['debit'], 2) : '');
                            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $transaction['credit'] != 0 ? number_format($transaction['credit'], 2) : '');

                            if ($balance < 0) {
                                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, accountancy_number($balance) . 'Cr');
                            } else {
                                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, accountancy_number($balance) . 'Dr');
                            }
                            $object->getActiveSheet()->getStyle('E' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('F' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('G' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $excel_row++;
                        }
                    }

                    $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel5');
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                    header('Cache-Control: max-age=0'); //no cache
                    ob_end_clean();
                    $objWriter->save('php://output');
                }
            } else {
                $this->load->view('finance/statements/statement_transactions_table', $data);
            }
        } else {
            $data['currency_options'] = currency_dropdown_options();
            $data['account_options'] = $account->dropdown_options();
            $this->load->view('finance/statements/index', $data);
        }
    }

    public function retirements_examination_list($imprest_voucher_id)
    {
        $this->load->model('imprest_voucher_retirement');
        $posted_params = dataTable_post_params();
        echo $this->imprest_voucher_retirement->retirements_examination_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $imprest_voucher_id);
    }

    public function cancel_approved_payment()
    {
        $request_type = $this->input->post('request_type');
        $this->load->model('approved_' . $request_type . '_payment_cancellation');
        switch ($request_type) {
            case 'sub_contract':
                $class = new  Approved_sub_contract_payment_cancellation();
                $id = 'sub_contract_payment_requisition_approval_id';
                break;
            case 'invoice':
                $class = new  Approved_invoice_payment_cancellation();
                $id = 'purchase_order_payment_request_approval_id';
                break;
            case 'requisition':
                $class = new  Approved_requisition_payment_cancellation();
                $id = 'requisition_approval_id';
                break;
        }
        $payment_cancellation = $class;
        $payment_cancellation->$id = $this->input->post('approval_id');
        $payment_cancellation->date = $this->input->post('date');
        $payment_cancellation->remarks = $this->input->post('remarks');
        $payment_cancellation->cancelled_by = $this->session->userdata('employee_id');
        $payment_cancellation->save();
    }

    public function preview_contra($contra_id)
    {
        $this->load->model('contra');
        $contra = new Contra();
        if ($contra->load($contra_id)) {
            $data['contra'] = $contra;

            $html = $this->load->view('finance/transactions/contras/contra_sheet', $data, true);

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
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Contra' . date('Y-m-d') . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function imprest_contras_list($imprest_voucher_id)
    {
        $this->load->model('contra');
        $posted_params = dataTable_post_params();
        echo $this->contra->contras($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $imprest_voucher_id);
    }

    public function delete_imprest_contra()
    {
        $this->load->model('contra');
        $contra = new Contra();
        $contra->load($this->input->post('contra_id'));
        $contra->delete_items();
        $contra->delete();
    }

    public function cheques()
    {
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $from = $from != '' ? $from : null;
        $to = $to != '' ? $to : null;
        $data['print'] = $this->input->post('print');
        $data['from'] = $from;
        $data['to'] = $to;
        if (!is_null($from) || !is_null($to)) {
            $this->load->model('payment_voucher');
            $data['cheques'] = $this->payment_voucher->cheque_list($from, $to);

            if ($data['print']) {

                $html = $this->load->view('finance/cheques/cheques_sheet', $data, true);

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

                $pdf->Output('Cheque List.pdf', 'I'); // view in the explorer

            } else {
                echo $this->load->view('finance/cheques/cheques_table', $data, true);
            }
        } else {
            $this->load->view('finance/cheques/index');
        }
    }

    public function pay_withholding_tax($withholding_tax_id)
    {
        $this->load->model(['withholding_taxes_payment', 'withholding_tax']);
        $tax_payment =  new Withholding_taxes_payment();
        $tax_payment->payment_date = $this->input->post('payment_date');
        $tax_payment->withholding_tax_id = $withholding_tax_id;
        $withholding_tax =  new Withholding_tax();
        $withholding_tax->load($withholding_tax_id);
        $withholding_tax->status = "PAID";
        $withholding_tax->save();
        $tax_payment->paid_amount = $withholding_tax->withheld_amount;
        $tax_payment->remarks = $withholding_tax->remarks;
        $tax_payment->paid_by = $this->session->userdata('employee_id');
        $tax_payment->save();
    }

    public function bank_options()
    {
        $this->load->model(['bank', 'account_group']);
        if ($this->account_group->account_group_details($this->input->post('selected_account_id'))->group_name != 'BANK') {
            echo '';
        } else {
            echo stringfy_dropdown_options($this->bank->bank_options());
        }
    }

    public function invoices($type)
    {
        if (check_privilege('Debts', true)) {
            if ($this->input->post('length')) {
                $param = dataTable_post_params();
                $this->load->model(['outgoing_invoice', 'invoice']);
                $data['print'] = $this->input->post('print');
                switch ($type) {
                    case 'sales':
                        echo $this->outgoing_invoice->list($param['limit'], $param['start'], $param['keyword'], $param['order']);
                        break;
                    case 'purchases':
                        echo $this->invoice->list($param['limit'], $param['start'], $param['keyword'], $param['order']);
                        break;
                }
            } else {
                $this->load->model(['stakeholder', 'currency', 'maintenance_service']);
                $data['title'] = 'Finance | ' . ucfirst($type) . ' Invoices';
                $data['type'] = $type;
                $data['accounts'] = $this->account->dropdown_options();
                $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
                $data['currency_options'] = currency_dropdown_options();
                $data['measurement_unit_options'] = measurement_unit_dropdown_options();
                $data['services_dropdown_options'] = $this->maintenance_service->dropdown_options();

                $this->load->view('finance/invoices/index', $data);
            }
        }
    }

    public function preview_invoice($invoice_id, $invoice_type)
    {
        switch ($invoice_type) {
            case 'sales':
                $class = 'Outgoing_invoice';
                break;
            case 'purchases':
                $class = 'Invoice';
                break;
        }
        $invoice = new $class();
        if ($invoice->load($invoice_id)) {
            $data['invoice'] = $invoice;
            $data['company_details'] = get_company_details();
            $data['invoice_type'] = $invoice_type;

            $html = $this->load->view('finance/documents/invoice_sheet', $data, true);
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

            $pdf->Output('Invoice-' . add_leading_zeros($invoice_id) . '.pdf', 'I'); // view in the explorer
        }
    }

    public function save_invoice($type)
    {
        $this->load->model(['outgoing_invoice', 'invoice']);
        $class = $type == 'sales' ? 'Outgoing_invoice' : 'Invoice';
        $invoice = new $class();
        $edit = $invoice->load($this->input->post('invoice_id'));
        $invoice->invoice_date = $this->input->post('invoice_date');
        $due_date = $this->input->post('due_date');
        $invoice->due_date = $due_date != '' ? $due_date : null;
        $invoice->reference = $this->input->post('reference');
        $invoice->invoice_no = $this->input->post('invoice_no');
        $invoice->vat_inclusive = $this->input->post('vat_inclusive');
        $invoice->vat_percentage = $this->input->post('vat_percentage');
        $invoice->payment_terms = $this->input->post('payment_term');
        $invoice->currency_id = $this->input->post('currency_id');
        switch ($type) {
            case 'sales':
                $invoice->invoice_to = $this->input->post('stakeholder_id');
                $invoice->bank_details = $this->input->post('bank_details');
                $invoice->notes = $this->input->post('desc_or_note');
                break;
            default:
                $invoice->amount = $this->input->post('invoice_amount');
                $invoice->description = $this->input->post('desc_or_note');
                break;
        }
        $invoice->created_by = $this->session->userdata('employee_id');
        if ($invoice->save()) {

            if ($edit) {
                $invoice->clear_items();
            }
            $quantities = $this->input->post('quantities');
            switch ($type) {
                case 'sales':
                    $this->load->model([
                        'outgoing_invoice_item',
                        'maintenance_invoice',
                        'maintenance_service_item',
                        'stock_sale_invoice',
                        'project_certificate_invoice',
                        'project_certificate',
                        'stock_sale',
                        'stock_sales_asset_item',
                        'stock_sales_material_item'
                    ]);
                    foreach ($quantities as $index => $quantity) {
                        if ($quantity > 0) {
                            $debt_nature = $this->input->post('debt_natures')[$index];
                            $invoice_item = new Outgoing_invoice_item();
                            $posted_item = $this->input->post('debted_item_ids')[$index];
                            $item_id = !preg_match('/^[0-9]+$/', $posted_item) ? explode('_', $posted_item)[1] : $posted_item;
                            if ($debt_nature == "stock_sale") {
                                $item_type = $this->input->post('item_types')[$index];
                                if ($item_type == "material") {
                                    $item = new Stock_sales_material_item();
                                    $item->load($item_id);
                                    $invoice_item->stock_sale_material_item_id = $item_id;
                                    $invoice_item->description = $item->material_item()->item_name . ' - ' . $item->stock_sale()->sale_number();
                                } else {
                                    $item = new Stock_sales_asset_item();
                                    $item->load($item_id);
                                    $invoice_item->stock_sale_asset_item_id = $item_id;
                                    $invoice_item->description = $item->asset_sub_location_history()->asset()->asset_code() . ' - ' . $item->stock_sale()->sale_number();
                                }
                            } else if ($debt_nature == "maintenance_service") {
                                $item = new Maintenance_service_item();
                                $item->load($item_id);
                                $invoice_item->description = $item->description . ' - ' . $item->maintenance_service()->maintenance_services_no();
                                $invoice_item->maintenance_service_item_id = $item->{$item::DB_TABLE_PK};
                            } else {
                                $item = new Project_certificate();
                                $item->load($item_id);
                                $invoice_item->description = 'Payment of Certificate No. ' . $item->certificate_number . ' of ' . $item->project()->project_name;
                                $invoice_item->project_certificate_id = $item->{$item::DB_TABLE_PK};
                            }

                            $invoice_item->outgoing_invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                            $invoice_item->quantity = $quantity;
                            $invoice_item->rate = $this->input->post('rates')[$index];
                            $unit_id = $this->input->post('unit_ids')[$index];
                            $invoice_item->measurement_unit_id = $unit_id != '' ? $unit_id : null;
                            $invoice_item->save();

                            if ($debt_nature == "stock_sale") {

                                $sql = 'SELECT stock_sale_id FROM stock_sale_invoices
									WHERE outgoing_invoice_id = ' . $invoice->{$invoice::DB_TABLE_PK} . ' LIMIT 1';
                                $stock_sale_id = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->row()->stock_sale_id : false;
                                if (!$stock_sale_id) {
                                    $sale_invoice = new Stock_sale_invoice();
                                    $sale_invoice->outgoing_invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                    $sale_invoice->stock_sale_id = $this->input->post('debt_nature_ids')[$index];
                                    $sale_invoice->save();
                                }
                            } else if ($debt_nature == "maintenance_service") {

                                $sql = 'SELECT service_id FROM maintenance_invoices
									WHERE outgoing_invoice_id = ' . $invoice->{$invoice::DB_TABLE_PK} . ' LIMIT 1';
                                $service_id = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->row()->service_id : false;
                                if (!$service_id) {
                                    $service_invoice = new Maintenance_invoice();
                                    $service_invoice->outgoing_invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                    $service_invoice->service_id = $this->input->post('debt_nature_ids')[$index];
                                    $service_invoice->save();
                                }
                            } else {

                                $sql = 'SELECT project_certificate_id FROM project_certificate_invoices
									WHERE outgoing_invoice_id =' . $invoice->{$invoice::DB_TABLE_PK} . ' LIMIT 1';
                                $project_certificate_id = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->row()->project_certificate_id : false;
                                if (!$project_certificate_id) {
                                    $certificate_invoice = new Project_certificate_invoice();
                                    $certificate_invoice->outgoing_invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                    $certificate_invoice->project_certificate_id = $this->input->post('debt_nature_ids')[$index];
                                    $certificate_invoice->save();
                                }
                            }
                        }
                    }
                    break;
                case 'purchases':
                    $this->load->model(['purchase_order_invoice', 'purchase_order', 'stakeholder_invoice']);
                    foreach ($quantities as $index => $quantity) {
                        if ($quantity > 0) {
                            $order_invoice = new Purchase_order_invoice();
                            $order_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                            $order_invoice->purchase_order_id = $this->input->post('debt_nature_ids')[$index];
                            $order_invoice->save();

                            $stakeholder_invoice = new Stakeholder_invoice();
                            $stakeholder_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                            $stakeholder_invoice->stakeholder_id = $this->input->post('stakeholder_id');
                            $stakeholder_invoice->save();
                        }
                    }
                    break;
            }
        }
    }

    public function generate_invoice_dropdown()
    {
        $this->load->model('stakeholder');
        $stakeholder_id = $this->input->post('stakeholder_id');
        $currency_id = $this->input->post('currency_id');
        $invoice_type = $this->input->post('invoice_type');
        $is_for_other_charges = $this->input->post('is_for_other_charges');
        if ($is_for_other_charges) {
            echo stringfy_dropdown_options($this->stakeholders_commitments($currency_id, $invoice_type));
        } else if ($stakeholder_id) {
            $stakeholder = new Stakeholder();
            $stakeholder->load($stakeholder_id);
            echo stringfy_dropdown_options($stakeholder->stakeholders_commitments($currency_id, $invoice_type));
        }
    }

    public function stakeholders_commitments($currency_id = null, $commitment_type = null)
    {
        $this->load->model(['purchase_order', 'maintenance_service', 'stock_sale', 'project_certificate']);
        $options[] = '&nbsp;';
        if ($commitment_type == 'sales') {
            $sql = 'SELECT * FROM (
                  SELECT CONCAT("Sale_",stock_sales.id,"_asset") AS debted_item, "Stock Sales" AS debt_nature, CONCAT("SALE/",LPAD(stock_sales.id, 4, 0)) AS corresponding_alias 
                  FROM stock_sales
                  
                  UNION
                  SELECT CONCAT("Service_",maintenance_services.service_id,"_serv") AS debted_item_id, "Maintenance Services" AS debt_nature, CONCAT("SVC/",LPAD(maintenance_services.service_id, 4, 0)) AS corresponding_alias 
                  FROM maintenance_services
                 
                  UNION
                  SELECT CONCAT("Certificate_",id,"_cert") AS debted_item_id, "Project Certificates" AS debt_nature, certificate_number AS corresponding_alias
                  FROM project_certificates
                  LEFT JOIN projects ON project_certificates.project_id = projects.project_id
                  
                ) AS stakeholders_debts';

            $query = $this->db->query($sql);
            $debted_items = $query->result();
            $debt_categories = [
                'Maintenance Services',
                'Stock Sales',
                'Project Certificates'
            ];
            foreach ($debt_categories as $category) {
                foreach ($debted_items as $item) {
                    $exploded_item = explode('_', $item->debted_item);
                    if ($item->debt_nature == "Maintenance Services") {
                        $maintenance_service = new Maintenance_service();
                        $maintenance_service->load($exploded_item[1]);
                        $outgoing_invoice = $maintenance_service->outgoing_invoice();
                        $maintenance_cost = $outgoing_invoice ? $outgoing_invoice->vat_amount() + $maintenance_service->maintenance_cost() : $maintenance_service->maintenance_cost();
                        $item_balance = $maintenance_cost - $maintenance_service->maintenance_service_invoice_amount();
                    } else if ($item->debt_nature == "Stock Sales") {
                        $sale = new Stock_sale();
                        $sale->load($exploded_item[1]);
                        $item_balance = $sale->sale_amount() - $sale->stock_sale_invoice_amount();
                    } else {
                        $project_certificate = new Project_certificate();
                        $project_certificate->load($exploded_item[1]);
                        $item_balance = $project_certificate->certified_amount - $project_certificate->invoiced_amount();
                    }

                    if ($item->debt_nature == $category && $item_balance > 0) {
                        $options[$category][$item->debted_item] = $item->corresponding_alias;
                    }
                }
            }
        } else {
            $sql = 'SELECT * FROM purchase_orders WHERE status NOT IN ("CANCELLED","CLOSED")';
            if (!is_null($currency_id)) {
                $sql .= ' AND currency_id = ' . $currency_id;
            }
            $query = $this->db->query($sql);
            $credited_items = $query->result();
            foreach ($credited_items as $credited_item) {
                $order = new Purchase_order();
                $order->load($credited_item->order_id);
                $options['Purchase Orders']['Purchase_' . $order->{$order::DB_TABLE_PK} . '_order'] = $order->order_number();
            }
        }
        return $options;
    }

    public function generate_selected_item_data()
    {
        $posted_item = $this->input->post('item_id');
        $item = explode('_', $posted_item);
        $this->load->model(['purchase_order', 'maintenance_service', 'stock_sale', 'project_certificate']);
        if ($item[0] == "Service") {
            echo $this->maintenance_service->generate_service_particulars($item[1]);
        } else if ($item[0] == "Sale") {
            echo $this->stock_sale->generate_sale_particulars($item[1]);
        } else if ($item[0] == "Certificate") {
            echo $this->project_certificate->generate_certificate_particulars($item[1]);
        } else if ($item[0] == "Purchase") {
            echo $this->purchase_order->generate_order_particulars($item[1]);
        }
    }

    public function delete_invoice($invoice_type)
    {
        $this->load->model(['outgoing_invoice', 'invoice']);
        $invoice_id = $this->input->post('invoice_id');
        switch ($invoice_type) {
            case 'purchases':
                $invoice = new Invoice();
                $invoice->load($invoice_id);
                break;
            case 'sales':
                $invoice = new Outgoing_invoice();
                $invoice->load($invoice_id);
                break;
        }
        $invoice->delete();
    }

    public function load_invoice_receipt_requirements()
    {
        $this->load->model('outgoing_invoice');
        $outgoing_invoice = new Outgoing_invoice();
        $outgoing_invoice->load($this->input->post('invoice_id'));
        $invoice_to = $outgoing_invoice->invoice_to();
        $credit_account_options = stringfy_dropdown_options(['stakeholder_' . $invoice_to->{$invoice_to::DB_TABLE_PK} => $invoice_to->stakeholder_name]);
        $ret_val['credit_account_options'] = $credit_account_options;
        $ret_val['unpaid_balance'] = $outgoing_invoice->unpaid_balance();
        $ret_val['currency_options'] = stringfy_dropdown_options([$outgoing_invoice->currency_id => $outgoing_invoice->currency()->currency_name]);
        echo json_encode($ret_val);
    }

    public function save_account_group()
    {
        $this->load->model('account_group');
        $account_group = new Account_group();
        $account_group->load($this->input->post('account_group_id'));
        $account_group->group_name = $this->input->post('account_group_name');
        $account_group->description = $this->input->post('description');
        $account_group->parent_id = $this->input->post('parent_id');
        $account_group->group_nature_id = $this->input->post('group_nature_id');
        $group_code = $this->input->post('group_code');
        $account_group->group_code = $group_code != '' ? $group_code : null;
        $nature = $account_group->group_nature();
        $account_group->level = $nature->level + 1;
        $account_group->save();
    }

    public function approved_cash_requisitions_list_on_dashboard()
    {
        $this->load->model('requisition_approval');
        echo $this->requisition_approval->approved_cash_requisitions_list_on_dashboard();
    }

    public function bulk_payment_list()
    {
        $this->load->model('payment_voucher');
        $currency_id = $this->input->post('currency_id');
        $vendor_string = $this->input->post('vendor_id');
        $vendor_id = $vendor_string != '' ? explode("_", $vendor_string)[1] : null;
        $creditor_type = $vendor_string != '' ? explode("_", $vendor_string)[0] : null;
        $data['approved_items'] = $approved_items = $this->payment_voucher->bulk_payment_list($creditor_type, $vendor_id, $currency_id);
        $json['table_view'] = $this->load->view('finance/transactions/bulk_payment/bulk_payment_table', $data, true);
        echo json_encode($json);
    }

    public function save_bulk_payment()
    {
        $this->load->model(['payment_voucher', 'currency']);
        $payment = new Payment_voucher();
        $credit_account_id = $this->input->post('credit_account_id');
        if (!empty($credit_account_id)) {
            $payment->credit_account_id = $this->input->post('credit_account_id');
            $payment->payment_date = $this->input->post('payment_date');
            $payment->cheque_number = $this->input->post('cheque_number');
            $payment->reference = $this->input->post('reference');
            $payment->payee = $this->input->post('payee');
            $payment->currency_id = $this->input->post('currency_id');
            $currency = new Currency();
            $currency->load($payment->currency_id);
            $payment->exchange_rate = $currency->rate_to_native();
            $withholding_tax = $this->input->post('withholding_tax');
            $payment->withholding_tax = $withholding_tax != '' ? $withholding_tax : null;
            $vat_percentage = $this->input->post('vat_percentage');
            $payment->vat_percentage =  $vat_percentage != '' ? $vat_percentage : 0;
            $payment->remarks = $this->input->post('remarks');
            $payment->employee_id = $this->session->userdata('employee_id');
            if ($payment->save()) {

                $this->load->model([
                    'payment_voucher_item',
                    'invoice',
                    'purchase_order_payment_request_approval',
                    'purchase_order_payment_request_approval_invoice_item',
                    'sub_contract_payment_requisition_approval'
                ]);
                $amounts = $this->input->post('amounts');
                $requisition_approval_ids = $this->input->post('requisition_approval_ids');
                $request_types = $this->input->post('request_types');
                $approved_invoice_item_ids = $this->input->post('approved_invoice_item_ids');

                foreach ($request_types as $index => $request_type) {
                    if ($request_type == "payment_request_invoice") {
                        $popra = new Purchase_order_payment_request_approval();
                        $popra->load($requisition_approval_ids[$index]);

                        $popra_inv_item = new Purchase_order_payment_request_approval_invoice_item();
                        $popra_inv_item->load($approved_invoice_item_ids[$index]);
                        $invoice = $popra_inv_item->purchase_order_payment_request_invoice_item()->invoice();

                        $payment_item = new Payment_voucher_item();
                        $payment_item->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $payment_item->debit_account_id = $this->input->post('debit_account_id');
                        $payment_item->amount = $amounts[$index];
                        if ($payment->vat_percentage > 0) {
                            $payment_item->vat_amount = ($payment->vat_percentage / 100) * $amounts[$index];
                        } else {
                            $payment_item->vat_amount = 0;
                        }
                        $payment_item->description = 'Being Payment for ' . $invoice->correspondence_number() . ' - ' . $invoice->reference . '';
                        if ($payment_item->save()) {
                            $this->load->model('payment_voucher_item_approved_invoice_item');
                            $pv_item_invoice_item = new Payment_voucher_item_approved_invoice_item();
                            $pv_item_invoice_item->payment_voucher_item_id = $payment_item->{$payment_item::DB_TABLE_PK};
                            $pv_item_invoice_item->purchase_order_payment_request_approval_invoice_item_id = $approved_invoice_item_ids[$index];
                            $pv_item_invoice_item->save();
                        }

                        $this->load->model('purchase_order_payment_request_approval_payment_voucher');
                        $payment_request_approval_payment_voucher = new Purchase_order_payment_request_approval_payment_voucher();
                        $payment_request_approval_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $payment_request_approval_payment_voucher->purchase_order_payment_request_approval_id = $requisition_approval_ids[$index];
                        $payment_request_approval_payment_voucher->save();

                        $this->load->model('Invoice_payment_voucher');
                        $invoice_payment_voucher = new Invoice_payment_voucher();
                        $invoice_payment_voucher->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                        $invoice_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $invoice_payment_voucher->save();
                    } else if ($request_type == "sub_contract_payment_requisition") {
                        $sc_req_approval = new Sub_contract_payment_requisition_approval();
                        $sc_req_approval->load($requisition_approval_ids[$index]);
                        $sc_req = $sc_req_approval->sub_contract_requisition();

                        $payment_item = new Payment_voucher_item();
                        $payment_item->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $payment_item->debit_account_id = $this->input->post('debit_account_id');
                        if ($payment->vat_percentage > 0) {
                            $payment_item->vat_amount = ($payment->vat_percentage / 100) * $amounts[$index];
                        } else {
                            $payment_item->vat_amount = 0;
                        }
                        $amount_withheld = (0.01 * $payment->withholding_tax * $amounts[$index]);
                        $payment_item->amount = $amounts[$index] - $amount_withheld;
                        $payment_item->description = 'Being Payment for ' . $sc_req->sub_contract_requisition_number() . ' of ' . $sc_req->project()->project_name . '';
                        if ($payment_item->save()) {
                            $this->load->model('sub_contract_payment_requisition_approval_item');
                            $approved_sub_contract_payment_item =  new Sub_contract_payment_requisition_approval_item();
                            $approved_sub_contract_payment_item->load($approved_invoice_item_ids[$index]);
                            $sub_contractor = $approved_sub_contract_payment_item->sub_contract_payment_requisition_item()->certificate()->sub_contract()->sub_contractor();

                            if ($payment->withholding_tax != null && $payment->withholding_tax > 0) {
                                $this->load->model(['withholding_tax', 'currency']);
                                $withholding_tax = new Withholding_tax();
                                $withholding_tax->date = $payment->payment_date;
                                $withholding_tax->credit_account_id = $payment_item->debit_account_id;
                                $withholding_tax_account = $payment_item->withholding_tax_account();
                                $withholding_tax->debit_account_id = $withholding_tax_account->{$withholding_tax_account::DB_TABLE_PK};
                                $withholding_tax->remarks = 'Tax amount withheld for payment of sub contract certifcate(s) listed in ' . $sc_req->sub_contract_requisition_number() . ' - ' . $sub_contractor->contractor_name . '';
                                $withholding_tax->payment_voucher_item_id = $payment_item->{$payment_item::DB_TABLE_PK};
                                $currency = new Currency();
                                $currency->load($payment->currency_id);
                                $withholding_tax->withheld_amount = $amount_withheld;
                                $withholding_tax->status = "PENDING";
                                $withholding_tax->created_by = $this->session->userdata('employee_id');
                                $withholding_tax->save();
                            }

                            $this->load->model('payment_voucher_item_approved_sub_contract_requisition_item');
                            $pv_item_approved_requisition_item = new Payment_voucher_item_approved_sub_contract_requisition_item();
                            $pv_item_approved_requisition_item->payment_voucher_item_id = $payment_item->{$payment_item::DB_TABLE_PK};
                            $pv_item_approved_requisition_item->sub_contract_payment_requisition_approval_item_id = $approved_invoice_item_ids[$index];
                            $pv_item_approved_requisition_item->save();

                            $this->load->model('sub_contract_certificate_payment_voucher');
                            $certificate_payment_voucher = new Sub_contract_certificate_payment_voucher();
                            $certificate = $pv_item_approved_requisition_item->sub_contract_payment_requisition_approval_item()->sub_contract_payment_requisition_item()->certificate();
                            $certificate_payment_voucher->sub_contract_certificate_id = $certificate->{$certificate::DB_TABLE_PK};
                            $certificate_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                            $certificate_payment_voucher->save();
                        }

                        $this->load->model('sub_contract_payment_requisition_approval_payment_voucher');
                        $requisition_approval_payment_voucher = new Sub_contract_payment_requisition_approval_payment_voucher();
                        $requisition_approval_payment_voucher->payment_voucher_id = $payment->{$payment::DB_TABLE_PK};
                        $requisition_approval_payment_voucher->sub_contract_payment_requisition_approval_id = $requisition_approval_ids[$index];
                        $requisition_approval_payment_voucher->save();
                    }
                }
            }
        }
    }

    public function bank_details()
    {
        $this->load->model('bank_account');
        $posted_item = $this->input->post('account_id');
        $account_id = !preg_match('/^[0-9]+$/', $posted_item) ? explode('_', $posted_item)[1] : $posted_item;
        echo $this->bank_account->bank_details($account_id);
    }

    public function reports_scraped()
    {
        $report_type = $this->input->post('report_type');
        $report_type = $report_type != '' ? $report_type : null;
        $data['from'] = $from = $this->input->post('from');
        $vendor_id = $this->input->post('vendor_id');
        $data['to'] = $to = $this->input->post('to');
        $data['print_pdf'] = $this->input->post('print_pdf');
        $data['export_excel'] = $this->input->post('export_excel');
        $data['title'] = 'Finance | Reports';
        ini_set("pcre.backtrack_limit", "5000000");
        set_time_limit(86400);

        if (!is_null($report_type)) {

            if ($report_type == 'ordered_items') {
                $sql = 'SELECT * FROM (

                                SELECT purchase_order_asset_items.asset_item_id AS item_id, asset_name AS item_name, receive_date,
                                purchase_order_asset_items.order_id, issue_date,
                                purchase_order_asset_items.quantity AS ordered_quantity,
                                (
                                   SELECT COUNT(assets.id) FROM grn_asset_sub_location_histories
                                   LEFT JOIN asset_sub_location_histories ON grn_asset_sub_location_histories.asset_sub_location_history_id = asset_sub_location_histories.id
                                   LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                                   LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                                   WHERE grn_id = purchase_order_grns.goods_received_note_id
                                   AND asset_item_id = purchase_order_asset_items.asset_item_id
                                ) AS received_quantity,
                                asset_sub_location_histories.book_value AS receiving_price,
                                "NO." AS measurement_unit
                                FROM purchase_order_asset_items
                                LEFT JOIN asset_items ON purchase_order_asset_items.asset_item_id = asset_items.id
                                LEFT JOIN purchase_orders ON purchase_order_asset_items.order_id = purchase_orders.order_id
                                LEFT JOIN purchase_order_grns ON purchase_orders.order_id = purchase_order_grns.purchase_order_id
                                LEFT JOIN goods_received_notes ON purchase_order_grns.goods_received_note_id = goods_received_notes.grn_id
                                LEFT JOIN grn_asset_sub_location_histories ON goods_received_notes.grn_id = grn_asset_sub_location_histories.grn_id
                                LEFT JOIN asset_sub_location_histories ON grn_asset_sub_location_histories.asset_sub_location_history_id = asset_sub_location_histories.id
                                WHERE purchase_orders.status = "RECEIVED"
                                GROUP BY asset_item_id
                                AND receive_date >= "' . $from . '"
                                AND receive_date <= "' . $to . '"
        
        
                                UNION 
        
        
                                SELECT material_item_id AS item_id, item_name, receive_date, purchase_order_material_items.order_id, issue_date, purchase_order_material_items.quantity AS ordered_quantity,  material_stocks.quantity AS received_quantity, material_stocks.price AS receiving_price,
                                (
                                  SELECT symbol FROM material_items
                                  LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                                  WHERE material_items.item_id = purchase_order_material_items.material_item_id
                                  LIMIT 1
                                ) AS measurement_unit 
                                FROM purchase_order_material_items
                                LEFT JOIN purchase_orders ON purchase_order_material_items.order_id = purchase_orders.order_id
                                LEFT JOIN purchase_order_material_item_grn_items ON purchase_order_material_items.item_id = purchase_order_material_item_grn_items.purchase_order_material_item_id
                                LEFT JOIN goods_received_note_material_stock_items ON purchase_order_material_item_grn_items.goods_received_note_item_id = goods_received_note_material_stock_items.item_id
                                LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                                LEFT JOIN material_items ON material_stocks.item_id = material_items.item_id
                                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                                WHERE status = "RECEIVED"
                                AND receive_date >= "' . $from . '"
                                AND receive_date <= "' . $to . '"
                                
                              ) AS ordered_items
                              ORDER BY receive_date
                        ';

                $query = $this->db->query($sql);
                $ordered_items = $query->result();

                $this->load->model(['vendor', 'purchase_order', 'material_item']);
                $table_items = [];
                foreach ($ordered_items as $ordered_item) {
                    $purchase_order = new Purchase_order();
                    $purchase_order->load($ordered_item->order_id);

                    $table_items[] = [
                        'measurement_unit' => $ordered_item->measurement_unit,
                        'receive_date' => $ordered_item->receive_date,
                        'purchase_order' => $purchase_order,
                        'issue_date' => $ordered_item->issue_date,
                        'description' => $ordered_item->item_name,
                        'cost_center' => $purchase_order->cost_center_name(),
                        'ordered_quantity' => $ordered_item->ordered_quantity,
                        'received_quantity' => $ordered_item->received_quantity,
                        'receiving_price' => $ordered_item->receiving_price,
                        'amount' => $ordered_item->received_quantity * $ordered_item->receiving_price,
                        'ordered_from' => $purchase_order->vendor()->vendor_name
                    ];
                }

                $data['table_items'] = $table_items;

                if ($data['print_pdf'] || $data['export_excel']) {
                    if ($data['print_pdf']) {
                        $pdf_sheet = 'finance/reports/pdf_sheet';
                        $data['table_view'] = 'reports/ordered_items_table';
                        $data['name_string'] = $name_string = 'Ordered Items';
                        $this->pdf_renderer($pdf_sheet, $data, $name_string);
                    } else {

                        $report_title = explode('_', $report_type);
                        $title = strtoupper($report_title[0]) . ' ' . strtoupper($report_title[1]);
                        $file_name = $title . ' Report From ' . custom_standard_date($from) . ' To ' . custom_standard_date($to);

                        $this->load->library("excel");
                        $object = new PHPExcel();

                        $object->setActiveSheetIndex(0);
                        $this->excel->getProperties()->setCreator($this->session->userdata('employee_name'));
                        $this->excel->getProperties()->setTitle($title . ' REPORT');
                        $object->getActiveSheet()->setTitle($title . ' REPORT');
                        $object->getActiveSheet()->setPrintGridlines(TRUE);


                        for ($col_index = 'A'; $col_index !== 'J'; $col_index++) {
                            $object->getActiveSheet()->getColumnDimension($col_index)->setAutoSize(true);
                        }

                        $table_columns = array("Receive Date", "Descriptions", "Order Number", "Cost Center", "Ordered Quantity", "Received Quantity", "Rate", "Amount", "Requested From");
                        $object->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);

                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $title);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 2, $from . ' to ' . $to);
                        $object->getActiveSheet()->mergeCells('A1:I1');
                        $object->getActiveSheet()->mergeCells('A2:I2');
                        $column = 0;

                        foreach ($table_columns as $field) {
                            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);
                            $column++;
                        }
                        $excel_row = 4;

                        foreach ($table_items as $table_item) {
                            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, custom_standard_date($table_item['receive_date']));
                            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $table_item['description']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $table_item['purchase_order']->order_number());
                            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $table_item['cost_center']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $table_item['ordered_quantity']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $table_item['received_quantity']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $table_item['receiving_price']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $table_item['amount']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $table_item['ordered_from']);


                            $object->getActiveSheet()->getStyle('E' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('F' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('G' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('H' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $excel_row++;
                        }

                        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $file_name . '.xls"');
                        ob_end_clean();
                        $object_writer->save('php://output');
                    }
                } else {
                    echo $this->load->view('finance/reports/ordered_items_table', $data, true);
                }
            } else if ($report_type == "cash_purchased_items") {

                $sql = 'SELECT * FROM (

                            SELECT examination_date,
                            imprest_voucher_asset_items.id AS imprest_item_id,
                            assets_from_requisitions.asset_item_id AS item_id,
                            (
                             SELECT asset_name FROM requisition_approval_asset_items
                             LEFT JOIN requisition_asset_items ON requisition_approval_asset_items.requisition_asset_item_id = requisition_asset_items.id
                             LEFT JOIN asset_items ON requisition_asset_items.asset_item_id = asset_items.id
                             WHERE requisition_approval_asset_items.id = imprest_voucher_asset_items.requisition_approval_asset_item_id
                             LIMIT 1
                            ) AS item_name,
                            approved_quantity,
                            "" AS retired_quantity,
                            assets_from_requisitions.book_value AS retired_rate,
                            "NO." AS unit_symbol,
                            approved_rate,
                            CONCAT(creators.first_name," ",creators.last_name) AS retirer,
                            CONCAT(examiners.first_name," ",examiners.last_name) AS examiner,
                            requisition_approval_id,
                            imprest_voucher_asset_items.imprest_voucher_id, "asset_rq" AS item_type
                            FROM imprest_voucher_retirement_asset_items AS assets_from_requisitions
                            LEFT JOIN imprest_voucher_retirements ON assets_from_requisitions.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                            LEFT JOIN imprest_vouchers ON imprest_voucher_retirements.imprest_voucher_id = imprest_vouchers.id
                            LEFT JOIN imprest_voucher_asset_items ON imprest_vouchers.id = imprest_voucher_asset_items.imprest_voucher_id
                            LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
                            LEFT JOIN requisition_asset_items ON requisition_approval_asset_items.requisition_asset_item_id = requisition_asset_items.id
                            LEFT JOIN employees AS creators ON imprest_voucher_retirements.created_by = creators.employee_id
                            LEFT JOIN employees AS examiners ON imprest_voucher_retirements.examined_by = examiners.employee_id
                            WHERE is_examined = 1
                            AND assets_from_requisitions.asset_item_id = requisition_asset_items.asset_item_id
                            AND assets_from_requisitions.imprest_voucher_retirement_id IN (SELECT imprest_voucher_retirement_id FROM imprest_voucher_retirement_grns)
                            AND examination_date >= "' . $from . '"
                            AND examination_date <= "' . $to . '"
                            
                            UNION
                            
                            SELECT examination_date,
                            NULL AS imprest_item_id,
                            assets_not_from_requisitions.asset_item_id AS item_id,
                            asset_name AS item_name,
                            0 AS approved_quantity, assets_not_from_requisitions.quantity AS retired_quantity,
                            assets_not_from_requisitions.book_value AS retired_rate,
                            "NO." AS unit_symbol,
                            0 AS approved_rate,
                            CONCAT(creators.first_name," ",creators.last_name) AS retirer,
                            CONCAT(examiners.first_name," ",examiners.last_name) AS examiner,
                            NULL AS requisition_approval_id,
                            imprest_voucher_retirements.imprest_voucher_id, "asset_notrq" AS item_type
                            FROM imprest_voucher_retirement_asset_items AS assets_not_from_requisitions
                            LEFT JOIN imprest_voucher_retirements ON assets_not_from_requisitions.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                            LEFT JOIN asset_items ON assets_not_from_requisitions.asset_item_id = asset_items.id
                            LEFT JOIN employees AS creators ON imprest_voucher_retirements.created_by = creators.employee_id
                            LEFT JOIN employees AS examiners ON imprest_voucher_retirements.examined_by = examiners.employee_id
                            WHERE is_examined = 1
                            AND assets_not_from_requisitions.imprest_voucher_retirement_id IN (SELECT imprest_voucher_retirement_id FROM imprest_voucher_retirement_grns)
                            AND assets_not_from_requisitions.id NOT IN (
                              SELECT imprest_voucher_retirement_asset_items.id FROM imprest_voucher_retirement_asset_items
                              LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_asset_items.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                              LEFT JOIN imprest_vouchers ON imprest_voucher_retirements.imprest_voucher_id = imprest_vouchers.id
                              LEFT JOIN imprest_voucher_asset_items ON imprest_vouchers.id = imprest_voucher_asset_items.imprest_voucher_id
                              LEFT JOIN requisition_approval_asset_items ON imprest_voucher_asset_items.requisition_approval_asset_item_id = requisition_approval_asset_items.id
                              LEFT JOIN requisition_asset_items ON requisition_approval_asset_items.requisition_asset_item_id = requisition_asset_items.id
                              WHERE imprest_voucher_retirement_asset_items.asset_item_id = requisition_asset_items.asset_item_id
                             )
                            AND assets_not_from_requisitions.imprest_voucher_retirement_id IN (SELECT imprest_voucher_retirement_id FROM imprest_voucher_retirement_grns)
                            AND examination_date >= "' . $from . '"
                            AND examination_date <= "' . $to . '"
                            
                            UNION
                            
                            SELECT examination_date,
                            imprest_voucher_material_items.id AS imprest_item_id,
                            materials_from_requisitions.item_id AS item_id,
                            (
                             SELECT item_name FROM requisition_approval_material_items
                             LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                             LEFT JOIN material_items ON requisition_material_items.material_item_id = material_items.item_id
                             WHERE requisition_approval_material_items.id = imprest_voucher_material_items.requisition_approval_material_item_id
                             LIMIT 1
                            ) AS item_name,
                            approved_quantity,
                            "" AS retired_quantity,
                            materials_from_requisitions.rate AS retired_rate,
                            "" AS unit_symbol,
                            approved_rate,
                            CONCAT(creators.first_name," ",creators.last_name) AS retirer,
                            CONCAT(examiners.first_name," ",examiners.last_name) AS examiner,
                            requisition_approval_id,
                            imprest_voucher_material_items.imprest_voucher_id, "material_rq" AS item_type
                            FROM imprest_voucher_retirement_material_items AS materials_from_requisitions
                            LEFT JOIN imprest_voucher_retirements ON materials_from_requisitions.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                            LEFT JOIN imprest_vouchers ON imprest_voucher_retirements.imprest_voucher_id = imprest_vouchers.id
                            LEFT JOIN imprest_voucher_material_items ON imprest_vouchers.id = imprest_voucher_material_items.imprest_voucher_id
                            LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
                            LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                            LEFT JOIN employees AS creators ON imprest_voucher_retirements.created_by = creators.employee_id
                            LEFT JOIN employees AS examiners ON imprest_voucher_retirements.examined_by = examiners.employee_id
                            WHERE is_examined = 1
                            AND materials_from_requisitions.item_id = requisition_material_items.material_item_id
                            AND materials_from_requisitions.imprest_voucher_retirement_id IN (SELECT imprest_voucher_retirement_id FROM imprest_voucher_retirement_grns)
                            AND examination_date >= "' . $from . '"
                            AND examination_date <= "' . $to . '"
                            
                            UNION
                            
                            SELECT examination_date,
                            NULL AS imprest_item_id,
                            materials_not_from_requisitions.item_id AS item_id,
                            item_name,
                            0 AS approved_quantity,
                            materials_not_from_requisitions.quantity AS retired_quantity,
                            materials_not_from_requisitions.rate AS retired_rate,
                            symbol AS unit_symbol,
                            0 AS approved_rate,
                            CONCAT(creators.first_name," ",creators.last_name) AS retirer,
                            CONCAT(examiners.first_name," ",examiners.last_name) AS examiner,
                            NULL AS requisition_approval_id,
                            imprest_voucher_retirements.imprest_voucher_id, "material_notrq" AS item_type
                            FROM imprest_voucher_retirement_material_items AS materials_not_from_requisitions
                            LEFT JOIN imprest_voucher_retirements ON materials_not_from_requisitions.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                            LEFT JOIN material_items ON materials_not_from_requisitions.item_id = material_items.item_id
                            LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                            LEFT JOIN employees AS creators ON imprest_voucher_retirements.created_by = creators.employee_id
                            LEFT JOIN employees AS examiners ON imprest_voucher_retirements.examined_by = examiners.employee_id
                            WHERE is_examined = 1
                            AND materials_not_from_requisitions.imprest_voucher_retirement_id IN (SELECT imprest_voucher_retirement_id FROM imprest_voucher_retirement_grns)
                            AND materials_not_from_requisitions.id NOT IN (
                            SELECT imprest_voucher_retirement_material_items.id FROM imprest_voucher_retirement_material_items
                             LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_material_items.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                             LEFT JOIN imprest_vouchers ON imprest_voucher_retirements.imprest_voucher_id = imprest_vouchers.id
                             LEFT JOIN imprest_voucher_material_items ON imprest_vouchers.id = imprest_voucher_material_items.imprest_voucher_id
                             LEFT JOIN requisition_approval_material_items ON imprest_voucher_material_items.requisition_approval_material_item_id = requisition_approval_material_items.id
                             LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                             WHERE imprest_voucher_retirement_material_items.item_id = requisition_material_items.material_item_id
                            )
                            AND materials_not_from_requisitions.imprest_voucher_retirement_id IN (SELECT imprest_voucher_retirement_id FROM imprest_voucher_retirement_grns)
                            AND examination_date >= "' . $from . '"
                            AND examination_date <= "' . $to . '"
                        ) AS cash_purchased_items
                        ORDER BY examination_date
                        ';

                $query = $this->db->query($sql);
                $list_items = $query->result();

                $table_items = [];
                $this->load->model(['requisition_approval', 'imprest_voucher', 'imprest_voucher_asset_item', 'imprest_voucher_material_item']);
                foreach ($list_items as $item) {
                    if (!is_null($item->requisition_approval_id)) {
                        $requisition_approval = new Requisition_approval();
                        $requisition_approval->load($item->requisition_approval_id);
                        $cost_center = $requisition_approval->requisition()->cost_center_name();
                        $requisition_number = $requisition_approval->requisition()->requisition_number();
                    } else {
                        $imprest_voucher = new Imprest_voucher();
                        $imprest_voucher->load($item->imprest_voucher_id);
                        $retirement = $imprest_voucher->retirement();
                        $retirer = $retirement->created_by()->first_name;
                        $location_name = $retirement->location()->location_name;
                        $sub_location_name = $retirement->sub_location()->sub_location_name;
                        $cost_center = 'This item was not on the request but was added to the retirement to ' . $location_name . ' sub location ' . $sub_location_name . ' by ' . $retirer . '';
                        $requisition_number = "";
                    }

                    if ($item->item_type == "material_rq") {
                        $material_item = new Imprest_voucher_material_item();
                        $material_item->load($item->imprest_item_id);
                        $measurement_unit_symbol = $material_item->requisition_approval_material_item()->material_item()->unit()->symbol;
                        $retired_quantity = $material_item->retired_material($item->imprest_voucher_id, $item->item_id);
                    } else if ($item->item_type == "asset_rq") {
                        $asset_item = new Imprest_voucher_asset_item();
                        $asset_item->load($item->imprest_item_id);
                        $measurement_unit_symbol = $item->unit_symbol;
                        $retired_quantity = $asset_item->retired_asset($item->imprest_voucher_id, $item->item_id);
                    } else if ($item->item_type == "asset_notrq" || $item->item_type == "material_notrq") {
                        $retired_quantity = $item->retired_quantity;
                        $measurement_unit_symbol = $item->unit_symbol;
                    }

                    $table_items[] = [
                        'examination_date' => $item->examination_date,
                        'item_id' => $item->item_id,
                        'description' => $item->item_name,
                        'requisition_approval_id' => !is_null($item->requisition_approval_id) ? $item->requisition_approval_id : null,
                        'cost_center' => $cost_center,
                        'requisition_number' => $requisition_number,
                        'measurement_unit_symbol' => $measurement_unit_symbol,
                        'approved_quantity' => $item->approved_quantity,
                        'retired_quantity' => $retired_quantity,
                        'retired_rate' => $item->retired_rate,
                        'amount' => $retired_quantity * $item->retired_rate,
                        'retirer' => $item->retirer,
                        'examiner' => $item->examiner,
                    ];
                }

                $data['table_items'] = $table_items;

                if ($data['print_pdf'] || $data['export_excel']) {
                    if ($data['print_pdf']) {
                        $pdf_sheet = 'finance/reports/pdf_sheet';
                        $data['table_view'] = 'reports/cash_purchased_items_table';
                        $data['name_string'] = $name_string = 'Cash Purchased Items';
                        $this->pdf_renderer($pdf_sheet, $data, $name_string);
                    } else {

                        $report_title = explode('_', $report_type);
                        $title = strtoupper($report_title[0] . ' ' . $report_title[1] . ' ' . $report_title[2]);
                        $file_name = $title . ' Report From ' . custom_standard_date($from) . ' To ' . custom_standard_date($to);

                        $this->load->library("excel");
                        $object = new PHPExcel();

                        $object->setActiveSheetIndex(0);
                        $this->excel->getProperties()->setCreator($this->session->userdata('employee_name'));
                        $this->excel->getProperties()->setTitle($title . ' REPORT');
                        $object->getActiveSheet()->setTitle($title . ' REPORT');
                        $object->getActiveSheet()->setPrintGridlines(TRUE);


                        for ($col_index = 'A'; $col_index !== 'K'; $col_index++) {
                            $object->getActiveSheet()->getColumnDimension($col_index)->setAutoSize(true);
                        }

                        $table_columns = array("Retired Date", "Descriptions", "Requisition Number", "Cost Center", "Approved Quantity", "Received Quantity", "Rate", "Amount", "Retired Personnel", "Examiner");
                        $object->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);
                        $object->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);

                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $title);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 2, $from . ' to ' . $to);
                        $object->getActiveSheet()->mergeCells('A1:J1');
                        $object->getActiveSheet()->mergeCells('A2:J2');
                        $column = 0;

                        foreach ($table_columns as $field) {
                            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);
                            $column++;
                        }
                        $excel_row = 4;

                        foreach ($table_items as $table_item) {
                            $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, custom_standard_date($table_item['examination_date']));
                            $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $table_item['description']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $table_item['requisition_number']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $table_item['cost_center']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $table_item['approved_quantity']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $table_item['retired_quantity']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $table_item['retired_rate']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $table_item['amount']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $table_item['retirer']);
                            $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $table_item['examiner']);


                            $object->getActiveSheet()->getStyle('E' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('F' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('G' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $object->getActiveSheet()->getStyle('H' . $excel_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $excel_row++;
                        }

                        $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $file_name . '.xls"');
                        ob_end_clean();
                        $object_writer->save('php://output');
                    }
                } else {
                    echo $this->load->view('finance/reports/cash_purchased_items_table', $data, true);
                }
            }
        } else {
            $this->load->view('finance/reports/index', $data);
        }
    }

    public function reports()
    {
        $data['title'] = 'Finance | Reports';
        //        ini_set("pcre.backtrack_limit", "5000000");
        //        set_time_limit(86400);
        $this->load->view('finance/reports/index', $data);
    }

    public function report_statements($account_name, $account_type = null)
    {
        $sql = 'SELECT currency_id FROM currencies WHERE is_native = 1 LIMIT 1';
        $native_currency_id = $this->db->query($sql)->row()->currency_id;
        $native_currency = new Currency();
        $native_currency->load($native_currency_id);

        $data['native_currency'] = $native_currency;
        $data['account_name'] = $account_name;
        $data['from'] = $from = $this->input->post('from');
        $data['to'] = $to = $this->input->post('to');
        $data['print_pdf'] = $this->input->post('print_pdf');
        $data['export_excel'] = $this->input->post('export_excel');
        switch ($account_name) {
            case 'receivables':
            case 'payables':
                $type = !is_null($account_type) ? ($account_type != 'aging_details' ? ucfirst($account_type) : 'Aging Details') : '';
                $report_name  = 'Account ' . ucfirst($account_name) . ' ' . $type . '';
                $data['report_name'] = $report_name;
                $data['account_name_and_type'] = $account_name . '_' . $account_type;
                $data['table_items'] = $this->report_statement_transactions($account_name, $native_currency, $from, $to);
                break;
            case 'balance':
                $report_name  = ucfirst($account_name) . ' Sheet';
                $data['report_name'] = $report_name;
                $data['account_name_and_type'] = $account_name . '_' . $account_type;
                $data['table_items'] = $this->report_statement_transactions($account_name, $native_currency, $from, $to);
                $data['account_groups'] = $this->main_report_acc_groups_arr($account_name);
                break;
            case 'income':
                $report_name  = ucfirst('Income Statement') . ' Sheet';
                break;
        }

        $data['title'] = $report_name . ' Report';
        if ($account_name == 'balance') {
            $this->load->view('finance/reports/balance_sheet_index', $data);
        } else {
            $this->load->view('finance/reports/report_statement_index', $data);
        }
    }

    public function report_statement_transactions($report_type, $native_currency, $as_of)
    {
        $this->load->model(['stakeholder', 'currency', 'account_group']);
        $currencies = $this->currency->get();
        $table_items = [];
        switch ($report_type) {
            case 'receivables':
            case 'payables':
                $sql = 'SELECT * FROM (
						SELECT stakeholder_id AS account_id, stakeholder_name AS account_name  FROM stakeholders
					) AS stakeholders_list';
                $query = $this->db->query($sql);
                $results = $query->result();
                $grand_total = 0;
                foreach ($results as $result) {
                    $stakeholder = new Stakeholder();
                    $stakeholder->load($result->account_id);
                    $balance = 0;
                    foreach ($currencies as $currency) {
                        $running_bal = $stakeholder->balance($currency->currency_id, $as_of);
                        $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                        $balance += $running_bal_native_currency;
                    }
                    $account_group = "PAYABLE";
                    $account_type_and_id = $account_group . '_stakeholder_' . $result->account_id;
                    $data['currency'] = $native_currency;
                    $data['symbol'] = $native_currency->symbol;
                    $data['account_name'] = $result->account_name;
                    $data['account_type_and_id'] = $account_type_and_id;
                    $data['account'] = $stakeholder;
                    $data['running_balance'] = $balance;
                    if ($report_type == 'payables') {
                        if (round($balance) < 0) {
                            $grand_total += $balance;
                            $table_items[$report_type][] = [
                                'account_name' => anchor(base_url('stakeholders/stakeholder_profile/' . $result->account_id), $result->account_name, 'target="_blank"'),
                                'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                'balance' => $balance
                            ];
                        }
                    } else {
                        if (round($balance) > 0) {
                            $grand_total += $balance;
                            $table_items[$report_type][] = [
                                'account_name' => anchor(base_url('stakeholders/stakeholder_profile/' . $result->account_id), $result->account_name, 'target="_blank"'),
                                'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                'balance' => $balance
                            ];
                        }
                    }
                }
                $table_items[$report_type]['grand_total'] = $grand_total;
                break;
            case 'balance':
                $data['currency'] = $native_currency;
                $data['symbol'] = $native_currency->symbol;
                $sql = 'SELECT * FROM (
                                        SELECT stakeholder_id AS account_id, stakeholder_name AS account_name  FROM stakeholders
                                    ) AS stakeholders_list';
                $query = $this->db->query($sql);
                $stakeholders = $query->result();

                $parent = new Account_group();
                $parent->load(1);
                $account_natures = $parent->natures($parent->{$parent::DB_TABLE_PK});
                foreach ($account_natures as $account_nature) {
                    $group = new Account_group();
                    $group->load($account_nature->account_group_id);
                    $group_name = $group->group_name;
                    $group_accounts = $group->group_accounts();
                    $lv_III_sub_groups = $group->sub_groups($account_nature->account_group_id);

                    if (empty($group_accounts) && empty($lv_III_sub_groups)) {
                        $table_items[$report_type][$group_name][] = [];
                    }

                    if (!empty($group_accounts) && empty($lv_III_sub_groups)) {
                        foreach ($group_accounts as $group_account) {
                            $account_id = is_array($group_account) ? $group_account['account_id'] : $group_account->account_id;
                            $account_name = is_array($group_account) ? $group_account['account_name'] : $group_account->account_name;
                            $account_group = strtoupper($group_name);
                            $account_type_and_id = $account_group . '_real_' . $account_id;
                            $balance = 0;
                            if ($account_name == 'Accounts Payable') {
                                $grand_total = 0;
                                foreach ($stakeholders as $stakeholder) {
                                    $stakeholder_account = new Stakeholder();
                                    $stakeholder_account->load($stakeholder->account_id);
                                    $balance = 0;
                                    foreach ($currencies as $currency) {
                                        $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                        $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                        $balance += $running_bal_native_currency;
                                    }

                                    if (round($balance) < 0) {
                                        $grand_total += $balance;
                                    }
                                }

                                $account_type_and_id = 'PAYABLE_immaginary_' . $stakeholder->account_id;
                                $data['account_name'] = $account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = '';
                                $data['running_balance'] = $grand_total;
                                $table_items[$report_type][$group_name][] = [
                                    'account_name' => $data['account_name'],
                                    'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                    'balance' => $grand_total
                                ];
                            } else if ($account_name == 'Accounts Receivable') {
                                $grand_total = 0;
                                foreach ($stakeholders as $stakeholder) {
                                    $stakeholder_account = new Stakeholder();
                                    $stakeholder_account->load($stakeholder->account_id);
                                    $balance = 0;
                                    foreach ($currencies as $currency) {
                                        $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                        $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                        $balance += $running_bal_native_currency;
                                    }

                                    if (round($balance) > 0) {
                                        $grand_total += $balance;
                                    }
                                }

                                $account_type_and_id = 'RECEIVABLE_immaginary_' . $stakeholder->account_id;
                                $data['account_name'] = $account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = '';
                                $data['running_balance'] = $grand_total;
                                $table_items[$report_type][$group_name][] = [
                                    'account_name' => $data['account_name'],
                                    'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                    'balance' => $grand_total
                                ];
                            } else if ($account_name == 'Inventory') {
                                $grand_total = 0;
                                $account_type_and_id = 'INVENTORY_immaginary_O';
                                $data['account_name'] = $account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = '';
                                $data['running_balance'] = $grand_total;
                                $table_items[$report_type][$group_name][] = [
                                    'account_name' => $data['account_name'],
                                    'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                    'balance' => $grand_total
                                ];
                            } else {
                                foreach ($currencies as $currency) {
                                    $running_bal = $group_account->balance($currency->currency_id, $as_of);
                                    $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                    $balance += $running_bal_native_currency;
                                }
                                $data['account_name'] = $group_account->account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = $group_account;
                                $data['running_balance'] = $balance;
                                if (round($balance) > 0 || round($balance) < 0) {
                                    $table_items[$report_type][$group_name][] = [
                                        'account_name' => $group_account->account_name,
                                        'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                        'balance' => $balance
                                    ];
                                }
                            }
                        }
                    }

                    if (empty($group_accounts) && !empty($lv_III_sub_groups)) {
                        $table_items[$report_type][$group_name][] = [];
                        $sub_groups = $lv_III_sub_groups;
                        foreach ($sub_groups as $sub_group) {
                            $sub_group_group_accounts = $sub_group->group_accounts(true);
                            if (!empty($sub_group_group_accounts)) {
                                foreach ($sub_group_group_accounts as $sub_group_group_account) {
                                    $account_id = is_array($sub_group_group_account) ? $sub_group_group_account['account_id'] : $sub_group_group_account->account_id;
                                    $account_name = is_array($sub_group_group_account) ? $sub_group_group_account['account_name'] : $sub_group_group_account->account_name;
                                    $account_group = strtoupper($group_name);
                                    $account_type_and_id = $account_group . '_real_' . $account_id;
                                    $balance = 0;
                                    if ($account_name == 'Accounts Payable') {
                                        $grand_total = 0;
                                        foreach ($stakeholders as $stakeholder) {
                                            $stakeholder_account = new Stakeholder();
                                            $stakeholder_account->load($stakeholder->account_id);
                                            $balance = 0;
                                            foreach ($currencies as $currency) {
                                                $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                                $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                                $balance += $running_bal_native_currency;
                                            }

                                            if (round($balance) < 0) {
                                                $grand_total += $balance;
                                            }
                                        }

                                        $account_type_and_id = 'PAYABLE_immaginary_' . $stakeholder->account_id;
                                        $data['account_name'] = $account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = '';
                                        $data['running_balance'] = $grand_total;
                                        $table_items[$report_type][$sub_group->group_name][] = [
                                            'account_name' => $data['account_name'],
                                            'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                            'balance' => $grand_total
                                        ];
                                    } else if ($account_name == 'Accounts Receivable') {
                                        $grand_total = 0;
                                        foreach ($stakeholders as $stakeholder) {
                                            $stakeholder_account = new Stakeholder();
                                            $stakeholder_account->load($stakeholder->account_id);
                                            $balance = 0;
                                            foreach ($currencies as $currency) {
                                                $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                                $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                                $balance += $running_bal_native_currency;
                                            }

                                            if (round($balance) > 0) {
                                                $grand_total += $balance;
                                            }
                                        }

                                        $account_type_and_id = 'RECEIVABLE_immaginary_' . $stakeholder->account_id;
                                        $data['account_name'] = $account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = '';
                                        $data['running_balance'] = $grand_total;
                                        $table_items[$report_type][$sub_group->group_name][] = [
                                            'account_name' => $data['account_name'],
                                            'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                            'balance' => $grand_total
                                        ];
                                    } else if ($account_name == 'Inventory') {
                                        $grand_total = 0;
                                        $account_type_and_id = 'INVENTORY_immaginary_0';
                                        $data['account_name'] = $account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = '';
                                        $data['running_balance'] = $grand_total;
                                        $table_items[$report_type][$sub_group->group_name][] = [
                                            'account_name' => $data['account_name'],
                                            'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                            'balance' => $grand_total
                                        ];
                                    } else {
                                        $balance_array = array();
                                        foreach ($currencies as $currency) {
                                            $running_bal = $sub_group_group_account->balance($currency->currency_id, $as_of);
                                            $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                            $balance += $running_bal_native_currency;
                                            $balance_array[] = array('currency' => $currency->symbol, 'running_bal' => $running_bal);
                                        }

                                        $data['account_name'] = $sub_group_group_account->account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = $sub_group_group_account;
                                        $data['running_balance'] = $balance;
                                        if (round($balance) > 0 || round($balance) < 0) {
                                            $table_items[$report_type][$sub_group->group_name][] = [
                                                'account_name' => $sub_group_group_account->account_name,
                                                'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                                'balance' => $balance
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($group_accounts) && !empty($lv_III_sub_groups)) {
                        foreach ($group_accounts as $group_account) {
                            $account_id = is_array($group_account) ? $group_account['account_id'] : $group_account->account_id;
                            $account_name = is_array($group_account) ? $group_account['account_name'] : $group_account->account_name;
                            $account_group = strtoupper($group_name);
                            $account_type_and_id = $account_group . '_real_' . $account_id;
                            $balance = 0;
                            if ($account_name == 'Accounts Payable') {
                                $grand_total = 0;
                                foreach ($stakeholders as $stakeholder) {
                                    $stakeholder_account = new Stakeholder();
                                    $stakeholder_account->load($stakeholder->account_id);
                                    $balance = 0;
                                    foreach ($currencies as $currency) {
                                        $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                        $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                        $balance += $running_bal_native_currency;
                                    }

                                    if (round($balance) < 0) {
                                        $grand_total += $balance;
                                    }
                                }

                                $account_type_and_id = 'PAYABLE_immaginary_' . $stakeholder->account_id;
                                $data['account_name'] = $account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = '';
                                $data['running_balance'] = $grand_total;
                                $table_items[$report_type][$group_name][] = [
                                    'account_name' => $data['account_name'],
                                    'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                    'balance' => $grand_total
                                ];
                            } else if ($account_name == 'Accounts Receivable') {
                                $grand_total = 0;
                                foreach ($stakeholders as $stakeholder) {
                                    $stakeholder_account = new Stakeholder();
                                    $stakeholder_account->load($stakeholder->account_id);
                                    $balance = 0;
                                    foreach ($currencies as $currency) {
                                        $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                        $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                        $balance += $running_bal_native_currency;
                                    }

                                    if (round($balance) > 0) {
                                        $grand_total += $balance;
                                    }
                                }

                                $account_type_and_id = 'RECEIVABLE_immaginary_' . $stakeholder->account_id;
                                $data['account_name'] = $account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = '';
                                $data['running_balance'] = $grand_total;
                                $table_items[$report_type][$group_name][] = [
                                    'account_name' => $data['account_name'],
                                    'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                    'balance' => $grand_total
                                ];
                            } else if ($account_name == 'Inventory') {
                                $grand_total = 0;
                                $account_type_and_id = 'INVENTORY_immaginary_O';
                                $data['account_name'] = $account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = '';
                                $data['running_balance'] = $grand_total;
                                $table_items[$report_type][$group_name][] = [
                                    'account_name' => $data['account_name'],
                                    'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                    'balance' => $grand_total
                                ];
                            } else {
                                foreach ($currencies as $currency) {
                                    $running_bal = $group_account->balance($currency->currency_id, $as_of);
                                    $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                    $balance += $running_bal_native_currency;
                                }
                                $data['account_name'] = $group_account->account_name;
                                $data['account_type_and_id'] = $account_type_and_id;
                                $data['account'] = $group_account;
                                $data['running_balance'] = $balance;
                                if (round($balance) > 0 || round($balance) < 0) {
                                    $table_items[$report_type][$group_name][] = [
                                        'account_name' => $group_account->account_name,
                                        'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                        'balance' => $balance
                                    ];
                                }
                            }
                        }

                        $sub_groups = $lv_III_sub_groups;
                        foreach ($sub_groups as $sub_group) {
                            $sub_group_group_accounts = $sub_group->group_accounts(true);
                            if (!empty($sub_group_group_accounts)) {
                                foreach ($sub_group_group_accounts as $sub_group_group_account) {
                                    $account_id = is_array($sub_group_group_account) ? $sub_group_group_account['account_id'] : $sub_group_group_account->account_id;
                                    $account_name = is_array($sub_group_group_account) ? $sub_group_group_account['account_name'] : $sub_group_group_account->account_name;
                                    $account_group = strtoupper($group_name);
                                    $account_type_and_id = $account_group . '_real_' . $account_id;
                                    $balance = 0;
                                    if ($account_name == 'Accounts Payable') {
                                        $grand_total = 0;
                                        foreach ($stakeholders as $stakeholder) {
                                            $stakeholder_account = new Stakeholder();
                                            $stakeholder_account->load($stakeholder->account_id);
                                            $balance = 0;
                                            foreach ($currencies as $currency) {
                                                $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                                $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                                $balance += $running_bal_native_currency;
                                            }

                                            if (round($balance) < 0) {
                                                $grand_total += $balance;
                                            }
                                        }

                                        $account_type_and_id = 'PAYABLE_immaginary_' . $stakeholder->account_id;
                                        $data['account_name'] = $account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = '';
                                        $data['running_balance'] = $grand_total;
                                        $table_items[$report_type][$sub_group->group_name][] = [
                                            'account_name' => $data['account_name'],
                                            'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                            'balance' => $grand_total
                                        ];
                                    } else if ($account_name == 'Accounts Receivable') {
                                        $grand_total = 0;
                                        foreach ($stakeholders as $stakeholder) {
                                            $stakeholder_account = new Stakeholder();
                                            $stakeholder_account->load($stakeholder->account_id);
                                            $balance = 0;
                                            foreach ($currencies as $currency) {
                                                $running_bal = $stakeholder_account->balance($currency->currency_id, $as_of);
                                                $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                                $balance += $running_bal_native_currency;
                                            }

                                            if (round($balance) > 0) {
                                                $grand_total += $balance;
                                            }
                                        }

                                        $account_type_and_id = 'RECEIVABLE_immaginary_' . $stakeholder->account_id;
                                        $data['account_name'] = $account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = '';
                                        $data['running_balance'] = $grand_total;
                                        $table_items[$report_type][$sub_group->group_name][] = [
                                            'account_name' => $data['account_name'],
                                            'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                            'balance' => $grand_total
                                        ];
                                    } else if ($account_name == 'Inventory') {
                                        $grand_total = 0;
                                        $account_type_and_id = 'INVENTORY_immaginary_0';
                                        $data['account_name'] = $account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = '';
                                        $data['running_balance'] = $grand_total;
                                        $table_items[$report_type][$sub_group->group_name][] = [
                                            'account_name' => $data['account_name'],
                                            'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                            'balance' => $grand_total
                                        ];
                                    } else {
                                        foreach ($currencies as $currency) {
                                            $running_bal = $sub_group_group_account->balance($currency->currency_id, $as_of);
                                            $running_bal_native_currency = $this->currency->convert($currency->currency_id, $native_currency->{$native_currency::DB_TABLE_PK}, $running_bal);
                                            $balance += $running_bal_native_currency;
                                        }
                                        $data['account_name'] = $sub_group_group_account->account_name;
                                        $data['account_type_and_id'] = $account_type_and_id;
                                        $data['account'] = $sub_group_group_account;
                                        $data['running_balance'] = $balance;
                                        if (round($balance) > 0 || round($balance) < 0) {
                                            $table_items[$report_type][$sub_group->group_name][] = [
                                                'account_name' => $sub_group_group_account->account_name,
                                                'statement_link' => $this->load->view('finance/accounts_statement_link', $data, true),
                                                'balance' => $balance
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case 'income':
                break;
        }
        return $table_items;
    }

    public function main_report_acc_groups_arr($report_type)
    {
        $id = $report_type == "balance" ? 1 : 2;
        $table_items = [];
        $parent = new Account_group();
        $parent->load($id);
        $account_natures = $parent->natures($parent->{$parent::DB_TABLE_PK});
        foreach ($account_natures as $account_nature) {
            $group = new Account_group();
            $group->load($account_nature->account_group_id);
            $group_name = $group->group_name;
            $group_accounts = $group->group_accounts();
            $lv_III_sub_groups = $group->sub_groups($account_nature->account_group_id);

            if ((empty($group_accounts) && empty($lv_III_sub_groups)) || (!empty($group_accounts) && empty($lv_III_sub_groups))) {
                $table_items[] = $group_name;
            }

            if ((empty($group_accounts) && !empty($lv_III_sub_groups)) || (!empty($group_accounts) && !empty($lv_III_sub_groups))) {
                $table_items[] = $group_name;
                foreach ($lv_III_sub_groups as $sub_group) {
                    $table_items[] = $sub_group->group_name;
                }
            }
        }

        return $table_items;
    }

    public function pdf_renderer($pdf_sheet, $data, $pdf_header)
    {
        $html = $this->load->view($pdf_sheet, $data, true);
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

        $pdf->Output($pdf_header . '.pdf', 'I'); // view in the explorer
    }

    public function journal($index = false)
    {
        check_privilege('Make Payment', true);
        $this->load->model(['account']);
        $currency_id = $this->input->post('currency_id');
        $from = $this->input->post('from');
        $data['from'] = $from != '' ? $from : null;
        $to = $this->input->post('to');
        $data['to'] = $to != '' ? $to : null;
        $data['print'] = $print = $this->input->post('print');
        $data['title'] = 'Finance | Journal';
        if (!$index) {

            $this->load->model('journal_voucher');
            $where = ' transaction_date >= "' . $from . '" AND transaction_date <= "' . $to . '" AND currency_id = ' . $currency_id . '';
            $journal_vouchers = $this->journal_voucher->get(0, 0, $where, 'journal_id DESC');

            $data['journal_vouchers'] = $journal_vouchers;

            if ($print) {
                $pdf_sheet = 'finance/reports/pdf_sheet';
                $data['table_view'] = 'journals/reports/jounal_table';
                $data['name_string'] = $name_string = 'Journal';
                $this->pdf_renderer($pdf_sheet, $data, $name_string);
            } else {
                echo $this->load->view('finance/journals/reports/jounal_table', $data, true);
            }
        } else {
            $data['account_options'] = $this->account->dropdown_options();
            $data['currency_options'] = currency_dropdown_options();
            $data['jv_crdit_account_options'] = $this->account->dropdown_options(['BANK', 'CASH IN HAND']);
            $data['jv_debit_account_options'] = $this->account->dropdown_options();
            $this->load->view('finance/journals/reports/index', $data);
        }
    }

    public function journal_transactions()
    {
        check_privilege('Make Payment', true);
        $this->load->model(['account', 'journal_voucher']);
        if ($this->input->post('length')) {
            $param = dataTable_post_params();
            echo $this->journal_voucher->journal_transactions($param['limit'], $param['start'], $param['keyword'], $param['order']);
        } else {
            check_privilege('Make Payment', true);
            $data['title'] = 'Finance | Journal Entries';
            $data['account_options'] = $this->account->dropdown_options();
            $data['currency_options'] = currency_dropdown_options();
            $this->load->view('finance/journals/index', $data);
        }
    }

    public function load_jv_account_details()
    {
        $posted_item = $this->input->post('account_id');
        $account_id = !preg_match('/^[0-9]+$/', $posted_item) ? explode('_', $posted_item)[1] : $posted_item;
        if (explode('_', $posted_item)[0] == 'stakeholder') {
            $stakeholder = new Stakeholder();
            $stakeholder->load($account_id);
            $account_name = $stakeholder->stakeholder_name;
        } else {
            $account = new Account();
            $account->load($account_id);
            $account_name = $account->account_name;
        }
        $return['account_name'] = $account_name;
        $return['account_id'] = $account_id;
        echo json_encode($return);
    }

    public function save_journal_voucher_entry()
    {
        $this->load->model(['journal_voucher']);
        $journal_voucher = new Journal_voucher();
        $edit = $journal_voucher->load($this->input->post('journal_voucher_id'));
        $journal_voucher->transaction_date = $this->input->post('transaction_date');
        $journal_voucher->reference = $this->input->post('reference');
        $journal_voucher->journal_type = $this->input->post('transaction_type');
        $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
        $journal_voucher->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
        $journal_voucher->currency_id = $this->input->post('currency_id');
        $journal_voucher->remarks = $this->input->post('remarks');
        $journal_voucher->created_by = $this->session->userdata('employee_id');
        if ($journal_voucher->save()) {
            if ($edit) {
                $journal_voucher->clear_items(['journal_voucher_items', 'journal_voucher_credit_accounts'], ['journal_voucher_id' => $journal_voucher->{$journal_voucher::DB_TABLE_PK}]);
            }
            $this->load->model([
                'journal_voucher_item',
                'journal_voucher_credit_account',
                'payment_request_approval_journal_voucher',
                'sub_contract_payment_requisition_approval_journal_voucher',
                'invoice_journal_voucher_item',
                'journal_voucher_item_approved_invoice_item',
                'journal_voucher_item_approved_cash_request_item',
                'journal_voucher_item_approved_sub_contract_requisition_item'
            ]);
            $amounts = $this->input->post('amounts');
            $for_corrections = $this->input->post('from_approved_requisitions');
            $item_types = $this->input->post('item_types');
            $total_amount = 0;
            foreach ($amounts as $index => $amount) {
                if ($amount > 0) {
                    $total_amount += floatval($amount);
                    $account_operation = $this->input->post('account_operations')[$index];
                    $posted_account_ids = $this->input->post('account_ids')[$index];
                    $account_id = !preg_match('/^[0-9]+$/', $posted_account_ids) ? explode('_', $posted_account_ids)[1] : $posted_account_ids;
                    switch ($account_operation) {
                        case "CREDIT";
                            $jv_item = new Journal_voucher_credit_account();
                            if ($for_corrections) {
                                if (explode('_', $posted_account_ids)[0] == "stakeholder") {
                                    $jv_item->stakeholder_id = $account_id;
                                } else {
                                    $jv_item->account_id = $account_id;
                                }
                            } else {
                                if (explode('_', $posted_account_ids)[0] == "stakeholder") {
                                    $jv_item->stakeholder_id = $account_id;
                                } else {
                                    $jv_item->account_id = $account_id;
                                }
                            }
                            break;

                        case "DEBIT";
                            $jv_item = new Journal_voucher_item();
                            if ($for_corrections) {
                                if (explode('_', $posted_account_ids)[0] == "stakeholder") {
                                    $jv_item->stakeholder_id = $account_id;
                                } else {
                                    $jv_item->debit_account_id = $account_id;
                                }
                            } else {
                                if (explode('_', $posted_account_ids)[0] == "stakeholder") {
                                    $jv_item->stakeholder_id = $account_id;
                                } else {
                                    $jv_item->debit_account_id = $account_id;
                                }
                            }
                            break;
                    }
                    $jv_item->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
                    $jv_item->amount = $amount;
                    $jv_item->narration = $this->input->post('narrations')[$index];
                    $jv_item->save();

                    if ($account_operation == "DEBIT" && $for_corrections) {
                        switch ($item_types[0]) {
                            case 'invoice':
                                $jv_item_approved_inv_item = new Journal_voucher_item_approved_invoice_item();
                                $jv_item_approved_inv_item->journal_voucher_item_id = $jv_item->{$jv_item::DB_TABLE_PK};
                                $jv_item_approved_inv_item->purchase_order_payment_request_approval_invoice_item_id = $this->input->post('approved_item_id');
                                $jv_item_approved_inv_item->save();
                                break;
                            case 'requisition':
                                break;
                            case 'sub_contract':
                                $jv_item_approved_scrq_item = new Journal_voucher_item_approved_sub_contract_requisition_item();
                                $jv_item_approved_scrq_item->journal_voucher_item_id = $jv_item->{$jv_item};
                                $jv_item_approved_scrq_item->sub_contract_payment_requisition_approval_item_id = $this->input->post('approved_item_id');
                                $jv_item_approved_scrq_item->save();
                                break;
                        }
                    }
                }
            }

            if ($for_corrections) {
                switch ($item_types[0]) {
                    case 'invoice':
                        $junction_item = new Payment_request_approval_journal_voucher();
                        $junction_item->purchase_order_payment_request_approval_id = $this->input->post('requisition_approval_id');
                        $junction_item->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
                        $junction_item->amount = $total_amount;
                        $junction_item->save();


                        $invoice_jv_item = new Invoice_journal_voucher_item();
                        $invoice_jv_item->invoice_id = $this->input->post('invoice_id');
                        $invoice_jv_item->journal_voucher_item_id =  $jv_item->{$jv_item::DB_TABLE_PK};
                        $invoice_jv_item->save();
                        break;

                    case 'sub_contract':
                        $junction_item = new Sub_contract_payment_requisition_approval_journal_voucher();
                        $junction_item->sub_contract_payment_requisition_approval_id = $this->input->post('requisition_approval_id');
                        $junction_item->journal_voucher_id = $journal_voucher->{$journal_voucher::DB_TABLE_PK};
                        $junction_item->amount = $total_amount;
                        $junction_item->save();
                        break;
                }
            }
        }
    }

    public function delete_journal_voucher()
    {
        $this->load->model('journal_voucher');
        $jv = new Journal_voucher();
        if ($jv->load($this->input->post('jv_transaction_id'))) {
            $jv->delete();
        }
    }

    public function preview_journal_voucher($jv_id)
    {
        $this->load->model('journal_voucher');
        $jv = new Journal_voucher();
        if ($jv->load($jv_id)) {
            $this->load->library(['m_pdf']);

            $data['jv'] = $jv;
            $pdf_sheet = 'finance/journals/documents/journal_voucher';
            $pdf_header = 'Journal Voucher';

            $this->pdf_renderer($pdf_sheet, $data, $pdf_header);
        } else {
            redirect(base_url());
        }
    }

    public function transactions()
    {
        check_privilege('Approved Payments', true);
        $this->load->model(['currency', 'Project_certificate', 'Stock_sale', 'stakeholder']);
        $data['creditors_options'] = $this->creditors_options();
        $data['currency_options'] = currency_dropdown_options();
        $data['credit_account_options'] = account_dropdown_options(['CASH IN HAND', 'BANK']);
        $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
        $data['account_options'] = account_dropdown_options();
        $data['currency_options'] = $this->currency->dropdown_options();
        $data['title'] = 'Finance | Journal Entries';
        $data['account_options_journals'] = $this->account->dropdown_options();
        $data['currency_options'] = currency_dropdown_options();
        $data['debit_account_options'] = account_dropdown_options(['CASH IN HAND', 'BANK']);
        $data['certificate_options'] = $this->Project_certificate->certificates_dropdown();
        $data['sales_options'] = $this->Stock_sale->stock_sales_dropdown();
        $data['title'] = 'Finance | Transactions';

        $this->load->view('finance/transactions/index', $data);
    }

    public function check_employee_name()
    {
        $this->load->model('employee_account');
        $employee_account = new Employee_account();
        $employee_account->load($this->input->post('account_id'));
        if ($employee_account) {
            echo $employee_account->employee_id;
        } else {
            echo '';
        }
    }

    public function save_employee_loans()
    {
        $this->load->model(['payment_voucher', 'payment_voucher_item', 'employee_account', 'employee', 'employee_loan']);
        $payment = new Payment_voucher();
        $payment->payment_date = $this->input->post('approved_date');
        $payment->reference = $this->input->post('reference');
        $payment->credit_account_id = $this->input->post('cr_account');

        $employee_account = $this->employee_account->get(1, 0, ['account_id' => $this->input->post('dr_account')]);
        $found_account = array_shift($employee_account);
        $employee = new Employee();
        $employee->load($found_account->employee_id);

        $payment->payee = $employee->full_name();
        $payment->currency_id = 1;
        $payment->exchange_rate = 1;
        $payment->vat_percentage = 0;
        $payment->remarks = $this->input->post('description');
        $payment->employee_id = $this->session->userdata('employee_id');

        if ($payment->save()) {
            $voucher = $this->payment_voucher->get(1, 0, '', 'payment_voucher_id DESC');
            $found_voucher = array_shift($voucher);

            $voucher_item = new Payment_voucher_item();
            $voucher_item->payment_voucher_id = $found_voucher->payment_voucher_id;
            $voucher_item->debit_account_id = $this->input->post('dr_account');
            $voucher_item->amount = $this->input->post('total_loan_amount');
            $voucher_item->vat_amount = 0;
            $voucher_item->description = $this->input->post('description');
            if ($voucher_item->save()) {
                $employee_loan = new Employee_loan();
                $employee_loan->employee_id = $found_account->employee_id;
                $employee_loan->loan_id = $this->input->post('loan_id');
                $employee_loan->loan_account_id = $this->input->post('dr_account');
                $employee_loan->loan_approved_date = $this->input->post('approved_date');
                $employee_loan->loan_deduction_start_date = $this->input->post('deduction_start_date');
                $employee_loan->total_loan_amount = $this->input->post('total_loan_amount');
                $employee_loan->monthly_deduction_amount = $this->input->post('monthly_deduction_rate');
                $employee_loan->loan_balance_amount = $this->input->post('total_loan_amount');
                $employee_loan->loan_application_form_path = '';
                $employee_loan->description = $this->input->post('description');
                $employee_loan->created_by = $this->session->userdata('employee_id');
                $employee_loan->save();
            };
        };
    }

    public function save_employee_loan_repay()
    {
        $this->load->model(['receipt', 'receipt_item', 'employee', 'employee_account', 'employee_loan']);
        $receipt = new Receipt();
        $receipt->debit_account_id = $this->input->post('dr_account');
        $receipt->credit_account_id = $this->input->post('cr_account');
        $receipt->receipt_date = $this->input->post('paid_date');
        $receipt->reference = '';
        $receipt->currency_id = 1;
        $receipt->exchange_rate = 1;
        $receipt->withholding_tax = 0;
        $receipt->remarks = $this->input->post('description');
        $receipt->created_by = $this->session->userdata('employee_id');


        if ($receipt->save()) {
            $last_receipt = $this->receipt->get(1, 0, '', 'id DESC');
            $found_receipt = array_shift($last_receipt);
            $receipt_item = new Receipt_item();
            $receipt_item->receipt_id = $found_receipt->id;
            $receipt_item->amount = $this->input->post('paid_amount');
            $receipt_item->remarks = $this->input->post('description');
            if ($receipt_item->save()) {
                $employee_loan = new Employee_loan();
                $employee_loan->load($this->input->post('employee_loan_id'));
                $employee_loan->loan_balance_amount = ($employee_loan->loan_balance_amount - $this->input->post('paid_amount'));
                $employee_loan->save();
            };
        }
    }

    public function employee_loan_history()
    {
        $cr_account = $this->input->post('cr_account');

        $sql = 'SELECT receipts.*, receipt_items.amount FROM receipts
               LEFT JOIN receipt_items ON receipts.id = receipt_items.receipt_id WHERE credit_account_id = ' . $cr_account;
        $query = $this->db->query($sql);
        $results = $query->result();


        $this->load->model(['loan', 'employee_loan', 'employee']);
        $loan_id = $this->input->post('loan_id');
        $employee_loan_id = $this->input->post('employee_loan_id');
        $employee_id = $this->input->post('employee_id');

        $loan = new Loan();
        $loan->load($loan_id);
        $employee_loan = new Employee_loan();
        $employee_loan->load($employee_loan_id);

        $employee = new Employee();
        $employee->load($employee_id);
        $mployee_fullname = $employee->full_name();

        $data['employee_data'] = $employee;
        $data['loan'] = $loan;
        $data['employee_loan'] = $employee_loan;
        $data['employee_loan_payments'] = $results;

        $html = $this->load->view('finance/loans/loan_payment_history_sheet', $data, true);

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

        $pdf->Output($mployee_fullname . ' Loan Payment History.pdf', 'I'); // view in the explorer

    }

    public function statement($account_type_and_id, $currency_id)
    {
        $this->load->model(['stakeholder', 'account', 'currency']);
        $data['title'] = 'Finance | Account Statement';
        $data['account_type_and_id'] = $account_type_and_id;
        $data['currency_options'] = currency_dropdown_options();
        $data['account_group'] = $account_group = explode('_', $account_type_and_id)[0];
        $data['account_type'] = $account_type = explode('_', $account_type_and_id)[1];
        $account_id = explode('_', $account_type_and_id)[2];
        $month_string = explode('-', date('Y-m-d'))[1] - 1 > 0 ? explode('-', date('Y-m-d'))[1] - 1 : 12;
        $previous_month = explode('-', date('Y-m-d'))[0] . '-' . add_leading_zeros($month_string, 2) . '-' . explode('-', date('Y-m-d'))[2];
        $last_month_date = new DateTime($previous_month);
        $last_month_date->modify(' - 1 day');
        $last_month_date = $last_month_date->format('Y-m-d');
        $data['previous_month'] = $last_month_date;
        $currency = new Currency();
        $currency->load($currency_id);
        $data['currency'] = $currency;
        switch ($account_type) {
            case 'stakeholder':
                $account = new Stakeholder();
                $account->load($account_id);
                $data['account_name'] = $account_name = $account->stakeholder_name;
                $data['base_url'] = 'stakeholders/stakeholder_profile/';
                break;
            case 'real':
                $account = new Account();
                $account->load($account_id);
                $data['account_name'] = $account_name = $account->account_name;
                break;
        }
        $this->load->view('finance/statements/index', $data);
    }

    public function statement_transaction()
    {
        $this->load->model(['stakeholder', 'account', 'currency', 'imprest_voucher']);
        $month_string = explode('-', date('Y-m-d'))[1] - 1 > 0 ? explode('-', date('Y-m-d'))[1] - 1 : 12;
        $previous_month = explode('-', date('Y-m-d'))[0] . '-' . add_leading_zeros($month_string, 2) . '-' . explode('-', date('Y-m-d'))[2];
        $account_type_and_id = $this->input->post('account_type_and_id');
        $currency_id = $this->input->post('currency_id');
        $from = $this->input->post('from');
        $data['start_date'] = $start_date = $from != '' ? $from : $previous_month;
        $to = $this->input->post('to');
        $data['end_date'] = $end_date = $to != '' ? $to : date('Y-m-d');
        $opening_balance_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
        $data['print_pdf'] = $this->input->post('print_pdf');
        $data['export_excel'] = $this->input->post('export_excel');
        $data['currency_options'] = currency_dropdown_options();
        $account_group = explode('_', $account_type_and_id)[0];
        $account_type = explode('_', $account_type_and_id)[1];
        $account_id = explode('_', $account_type_and_id)[2];
        $currency = new Currency();
        $currency->load($currency_id);
        switch ($account_type) {
            case 'stakeholder':
                $account = new Stakeholder();
                $account->load($account_id);
                $data['account_name'] = $account_name = $account->stakeholder_name;
                $data['transactions'] = $transactions = $account->statement($currency_id, $start_date, $end_date);
                $data['opening_balance'] = $opening_balance = $account->balance($currency_id, $opening_balance_date);
                break;
            case 'real':
                $account = new Account();
                $account->load($account_id);
                $data['account_name'] = $account_name = $account->account_name;
                $data['transactions'] = $transactions = $account->statement($currency_id, $start_date, $end_date);
                $data['opening_balance'] = $opening_balance = $account->balance($currency_id, $opening_balance_date);
                break;
        }
        $data['previous_month'] = $opening_balance_date;
        $data['account_name'] = $account_name;
        $data['currency'] = $currency;
        $data['account_group'] = $account_group;
        if ($data['print_pdf'] || $data['export_excel']) {
            if ($data['print_pdf']) {
                $html = $this->load->view('finance/statements/statement_sheet', $data, true);

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

                $pdf->Output('Account Statement.pdf', 'I'); // view in the explorer

            } else {

                $account_nature = $account->account_nature();
                $balance =  $account->balance($currency_id, $opening_balance_date);
                $filename = $account->account_name . ' Account Statement from ' . custom_standard_date($from) . ' to ' . custom_standard_date($to);

                $object = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $object->setActiveSheetIndex(0);

                $object->getProperties()->setTitle($account->account_name . ' Account Statement ');
                $object->getProperties()->setCreator($this->session->userdata('employee_name'));

                for ($col_index = 'A'; $col_index !== 'H'; $col_index++) {
                    $object->getActiveSheet()->getColumnDimension($col_index)->setAutoSize(true);
                }

                $table_columns = array("Date", "Transaction Type", "Description", "Reference", "Debit", "Credit", "Balance");
                $column = 0;
                foreach ($table_columns as $field) {
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);
                    $column++;
                }
                $object->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);

                $object->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
                $object->getActiveSheet()->getStyle('B5')->getFont()->setItalic(true);
                $object->getActiveSheet()->getStyle('G5')->getFont()->setBold(true);

                $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::class;

                $object->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('C3')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('G5')->getAlignment()->setHorizontal($alignment::HORIZONTAL_RIGHT);

                $object->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Account: ' . $account->account_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Currency: ' . $currency->name_and_symbol());
                $object->getActiveSheet()->mergeCells('A1:G1');
                $object->getActiveSheet()->mergeCells('A2:G2');
                $object->getActiveSheet()->mergeCells('A4:G4');

                $excel_row = 5;
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, custom_standard_date($from));
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, "Opening Balance");
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, accountancy_number($balance));
                $object->getActiveSheet()->mergeCells('B5:F5');
                $excel_row = 6;

                foreach ($transactions as $transaction) {
                    $balance = $balance + $transaction['debit'] - $transaction['credit'];

                    if ($transaction['debit'] != 0 || $transaction['credit'] != 0) {

                        $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, custom_standard_date($transaction['transaction_date']));
                        $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $transaction['transaction_type']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $transaction['remarks']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $transaction['detailed_reference']);
                        $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $transaction['debit'] != 0 ? number_format($transaction['debit'], 2) : '');
                        $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $transaction['credit'] != 0 ? number_format($transaction['credit'], 2) : '');

                        if ($balance > 0) {
                            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, accountancy_number($balance) . 'Cr');
                        } else {
                            $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, accountancy_number($balance) . 'Dr');
                        }
                        $object->getActiveSheet()->getStyle('E' . $excel_row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_RIGHT);
                        $object->getActiveSheet()->getStyle('F' . $excel_row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_RIGHT);
                        $object->getActiveSheet()->getStyle('G' . $excel_row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_RIGHT);
                        $excel_row++;
                    }
                }

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($object);


                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache

                ob_end_clean();
                // We'll be outputting an excel file
                $writer->save('php://output');
            }
        } else {
            $ret_val['symbol'] = $currency->symbol;
            $ret_val['statement_table'] = $this->load->view('finance/statements/statement_transactions_table', $data, true);
            echo json_encode($ret_val);
        }
    }

    public function vat_returns_account($account_for)
    {
        switch ($account_for[0]) {
            case 'project':
                $adjacent_joining_query = 'project_accounts ON accounts.account_id = project_accounts.account_id';
                $id = $account_for[0] . '_id';
                break;
            case 'cost_center':
                $adjacent_joining_query = 'cost_center_accounts ON accounts.account_id = cost_center_accounts.account_id';
                $id = $account_for[0] . '_id';
                break;
        }
        $sql = 'SELECT accounts.account_id FROM accounts
                LEFT JOIN ' . $adjacent_joining_query . '
                WHERE account_name LIKE "%VAT Returns%" AND ' . $id . ' = ' . $account_for[1] . '  LIMIT 1';

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $this->load->model('account');
            $account = new Account();
            $account->load($query->row()->account_id);
        } else {
            $this->load->model(['currency', 'account_group', 'bank']);
            $account = new Account();
            $account_groups = $this->account_group->get(0, 0, ['group_name' => 'TAXES']);
            $account_group = array_shift($account_groups);
            $account_group_id = $account_group->{$account_group::DB_TABLE_PK};
            $account->account_name = 'VAT Returns';
            $account->account_group_id = $account_group_id;
            $account->currency_id = 1;
            $account->description = 'An account for administering VAT ins and outs';
            $account->account_code = null;
            $account->opening_balance = 0;
            if ($account->save()) {
                if ($account_for[0] == 'project') {
                    $this->load->model('project_account');
                    $project_account = new Project_account();
                    $project_account->account_id = $account->{$account::DB_TABLE_PK};
                    $project_account->project_id = $account_for[1];
                    $project_account->save();
                } else if ($account_for[0] == 'cost_center') {
                    $this->load->model('cost_center_account');
                    $cost_center_account = new Cost_center_account();
                    $cost_center_account->account_id = $account->{$account::DB_TABLE_PK};
                    $cost_center_account->cost_center_id = $account_for[1];
                    $cost_center_account->save();
                }
            }

            $action = 'Account Creation';
            $description = 'Account ' . $account->account_name . ' was automaticaly created by the system';
            system_log($action, $description);
        }
        return $account;
    }
}
