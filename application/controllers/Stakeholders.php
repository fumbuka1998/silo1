<?php
class Stakeholders extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		check_login();
		$this->load->model('stakeholder');
	}

	public function index()
	{
		check_permission('Clients', true);
		$limit = $this->input->post('length');
		if ($limit != '') {
			$posted_params = dataTable_post_params();
			echo $this->stakeholder->stakeholders_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
		} else {
			$data['title'] = 'Clients | Suppliers | stakeholders';
			$data['currency_options'] = currency_dropdown_options();
			$this->load->view('stakeholders/index', $data);
		}
	}

	public function save_stakeholder($id = 0)
	{
		$this->load->model(['stakeholder']);
		$stakeholder = new Stakeholder();
		$edit = $stakeholder->load($id);
		$stakeholder->stakeholder_name = $this->input->post('stakeholder_name');
		$stakeholder->email = $this->input->post('email');
		$stakeholder->phone = $this->input->post('phone');
		$stakeholder->alternative_phone = $this->input->post('alternative_phone');
		$stakeholder->address = $this->input->post('address');
		$stakeholder->created_by = $this->session->userdata('employee_id');
		if ($stakeholder->save()) {
			$action = $edit ? 'Stakeholder Update' : 'Stakeholder Registration';
			$description = 'Stakeholder ' . $stakeholder->stakeholder_name . ' was ' . ($edit ? 'Updated' : 'Registered');
			system_log($action, $description);
			redirect(base_url('stakeholders/stakeholder_profile/' . $stakeholder->{$stakeholder::DB_TABLE_PK}));
		}
	}

	public function stakeholder_profile($id = 0, $invoices = false)
	{
		check_permission('Procurements', true);
		$this->load->model(['stakeholder_evaluation_factor', 'stakeholder']);
		$stakeholder = new Stakeholder();
		if ($stakeholder->load($id)) {
			$data['title'] = $stakeholder->stakeholder_name;
			$data['stakeholder'] = $stakeholder;
			$data['enum_options'] = new Stakeholder_evaluation_factor();
			$data['stakeholder_evaluation_factors'] = $this->stakeholder_evaluation_factor->load_stakeholder_factor_and_points($id);
			if ($invoices) {
				$data['invoices'] = $invoices;
			} else {
				$data['invoices'] = false;
			}
			$this->load->view('stakeholders/profile', $data);
		} else {
			redirect(base_url());
		}
	}

	public function lists($id, $list_type)
	{
		$this->load->model($list_type);
		$limit = $this->input->post('length');
		if ($limit != '') {
			$posted_params = dataTable_post_params();
			echo $this->$list_type->projects_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], null, $id);
		}
	}

	public function sub_contracts_list($stakeholder_id = 0)
	{
		$this->load->model('sub_contract');
		$posted_params = dataTable_post_params();
		if ($posted_params['limit'] == null) {
			$data['title'] = 'Sub-Contracts';
			$this->load->view('sub_contractors/sub_contracts_tab', $data);
		} else {
			echo $this->sub_contract->sub_contracts_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $stakeholder_id);
		}
	}

	public function save_sub_contract_certificate()
	{
		$this->load->model(['Sub_contract_certificate', 'sub_contract_certificate_task']);
		$sub_contract_certificate = new Sub_contract_certificate();
		$sub_contract_certificate->sub_contract_id = $this->input->post('sub_contract_id');
		$sub_contract_certificate->certificate_number = $this->input->post('certificate_number');
		$sub_contract_certificate->certificate_date = $this->input->post('certificate_date');
		$sub_contract_certificate->certified_amount = 0;
		$sub_contract_certificate->vat_inclusive = $this->input->post('vat_inclusive');
		$sub_contract_certificate->vat_percentage = 18;
		$sub_contract_certificate->remarks = $this->input->post('remarks');
		$sub_contract_certificate->created_by = $this->session->userdata('employee_id');
		if ($sub_contract_certificate->save()) {
			$task_ids = $this->input->post('certified_task_ids');
			$task_ids = is_array($task_ids) ? array_filter($task_ids) : [];
			$certificate_amount = 0;
			if (!empty($task_ids)) {
				foreach ($task_ids  as $index => $task_id) {
					$sub_contract_certificate_task = new Sub_contract_certificate_task();
					$sub_contract_certificate_task->sub_contract_certificate_id = $sub_contract_certificate->{$sub_contract_certificate::DB_TABLE_PK};
					$sub_contract_certificate_task->task_id = $task_id;
					$sub_contract_certificate_task->amount = $this->input->post('certified_amounts')[$index];
					if ($sub_contract_certificate_task->save()) {
						$certificate_amount += $sub_contract_certificate_task->amount;
					}
				}
			}

			$certificate = new Sub_contract_certificate();
			$certificate->load($sub_contract_certificate_task->sub_contract_certificate_id);
			$certificate->certified_amount = $certificate_amount;
			$certificate->save();
		}
	}

	public function delete_sub_contract_certificate()
	{
		$this->load->model('Sub_contract_certificate');
		$sub_contract_certificate = new Sub_contract_certificate();
		$sub_contract_certificate->load($this->input->post('sub_contract_certificate_id'));
		$sub_contract_certificate->delete();
	}

	public function sub_contracts_certificate_list_table($sub_contract_id)
	{
		$this->load->model('sub_contract_certificate');
		$posted_params = dataTable_post_params();
		echo $this->sub_contract_certificate->sub_contracts_certificate_list_table($sub_contract_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
	}

	public function sub_contract_tasks($sub_contract_id)
	{
		$this->load->model('sub_contract_item');
		$options = ['&nbsp;'];
		foreach ($this->sub_contract_item->get(0, 0, ['sub_contract_id' => $sub_contract_id]) as $sub_contract_item) {
			inspect_object($sub_contract_item);
			$options[$sub_contract_item->task_id] = $sub_contract_item->task()->task_name;
		}
		echo stringfy_dropdown_options($options);
	}

	public function sub_contracts_list_table($sub_contract_id)
	{
		$this->load->model('sub_contract_item');
		$posted_params = dataTable_post_params();
		echo $this->sub_contract_item->sub_contracts_list_table($sub_contract_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
	}

	public function load_sub_contract_certificates()
	{
		$this->load->model('sub_contract');
		$sub_contract_id = $this->input->post('sub_contract_id');
		$sub_contract =  new Sub_contract();
		$sub_contract->load($sub_contract_id);
		echo stringfy_dropdown_options($sub_contract->certificates(true));
	}

	public function load_certificate_amount()
	{
		$this->load->model('sub_contract_certificate');
		$certificate_id = $this->input->post('certificate_id');
		$sub_contract_certificate = new Sub_contract_certificate();
		$sub_contract_certificate->load($certificate_id);
		$balance = $sub_contract_certificate->certified_amount - $sub_contract_certificate->approved_amount();
		if ($balance > 0) {
			if ($sub_contract_certificate->vat_inclusive == 1) {
				$vat_amount = 0.01 * $sub_contract_certificate->vat_percentage * ($balance / 1.18);
				$ret_val['vat_amount'] = $vat_amount;
				$ret_val['cert_amount'] = $balance / 1.18;
			} else {
				$ret_val['vat_amount'] = 0;
				$ret_val['cert_amount'] = $balance;
			}
			echo json_encode($ret_val);
		}
	}

	public function save_stakeholder_evaluation()
	{
		$this->load->model(['stakeholder_evaluation_factor', 'stakeholder_evaluation_score']);
		$stakeholder = $this->stakeholder_evaluation_score->get(1, 0, ['stakeholder_id' => $this->input->post('stakeholder_id')]);

		$stakeholder_evaluation_factor = new Stakeholder_evaluation_factor();
		$stakeholder_evaluation_score = new Stakeholder_evaluation_score();

		if ($stakeholder) {
			$stakeholder_junction = array_shift($stakeholder);
			inspect_object($stakeholder_junction);
			$evaluation_id = $stakeholder_junction->supplier_evaluation_factor_id;
			$junction_id = $stakeholder_junction->id;
			$stakeholder_evaluation_factor->id = $evaluation_id;
			$stakeholder_evaluation_score->id = $junction_id;
		}

		$stakeholder_evaluation_factor->general_experience = $this->input->post('general_experience');
		$stakeholder_evaluation_factor->certificate_of_completion = $this->input->post('certificates_of_comletion');
		$stakeholder_evaluation_factor->two_team_supervisors_with_atleast_a_bachelor_degree = $this->input->post('team_supervisors');
		$stakeholder_evaluation_factor->financial_capacity_of_at_least_payment_of_workers_for_one_month = $this->input->post('financial_capacity');
		$stakeholder_evaluation_factor->proof_of_training_of_casual_laborers = $this->input->post('casual_laborers');
		$stakeholder_evaluation_factor->save();

		$last_supplier_evaluation = $this->stakeholder_evaluation_factor->get(1, 0, '', 'id DESC');
		$last_supplier_evaluation_id = array_shift($last_supplier_evaluation);

		$stakeholder_evaluation_score->stakeholder_id = $this->input->post('stakeholder_id');
		$stakeholder_evaluation_score->stakeholder_evaluation_factor_id = $last_supplier_evaluation_id->id;
		$stakeholder_evaluation_score->save();
	}

	public function check_points($loaded_choice = false)
	{
		$this->load->model('stakeholder_evaluation_factor');
		$choice = $this->input->post('selector_value');

		echo $this->stakeholder_evaluation_factor->factor_to_points($choice);
	}

	public function stakeholders_evaluation()
	{
		$this->load->model(['stakeholder_evaluation_score', 'stakeholder_evaluation_factor', 'project']);
		$data['stakeholder_options'] = $this->stakeholder_evaluation_score->evaluated_stakeholders_options();
		$data['project_options'] = $this->project->project_dropdown_options();

		$stakeholder_ids = $this->input->post('stakeholders_ids');
		$project_id = $this->input->post('project_id');

		$contrator_ids = is_array($stakeholder_ids) ? array_filter($stakeholder_ids) : [];

		$stakeholders_data = [];
		if (!empty($contrator_ids)) {
			$triggered = $this->input->post('triggered') == 'true';
			$project = new Project();
			$project->load($project_id);
			$data['project'] = $project;


			foreach ($contrator_ids as $id) {
				$stakeholder = new Stakeholder();
				$stakeholder->load($id);
				$evaluation_id = $this->stakeholder_evaluation_score->get(1, 0, ['stakeholder_id' => $id]);
				if ($evaluation_id) {
					$found_factor = array_shift($evaluation_id);
					$found_id = $found_factor->stakeholder_evaluation_factor_id;

					$data2 = [];
					$evaluation_factor = new Stakeholder_evaluation_factor();
					$evaluation_factor->load($found_id);
					$total = 0;

					$count = 1;
					foreach ($evaluation_factor as $factor) {
						if ($count > 1) {
							if ($this->stakeholder_evaluation_factor->factor_to_points($factor) != '') {
								$data2[] = $this->stakeholder_evaluation_factor->factor_to_points($factor);
							} else {
								$data2[] = 0;
							}

							$total += $this->stakeholder_evaluation_factor->factor_to_points($factor);
						}
						$count++;
					}
					$data2[] = $total;
					$data2[] = $stakeholder->stakeholder_name;
					$stakeholders_data[] = $data2;
				}
			}

			$data['stakeholders_data'] = $stakeholders_data;
			if ($triggered) {
				$data['print'] = isset($print);
				$html = $this->load->view('stakeholders/stakeholder_evaluation_sheet', $data, true);
				$this->load->library('m_pdf');
				$pdf = $this->m_pdf->load();

				$pdf->AddPage(
					'', // L - landscape, P - portrait
					'',
					'',
					'',
					'',
					15, // margin_left
					15, // margin right
					15, // margin top
					15, // margin bottom
					9, // margin header
					6,
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'A4-L'
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

				$pdf->Output('Stakeholder Evaluations.pdf', 'I');
			} else {
				echo $this->load->view('stakeholders/stakeholder_evaluation_table', $data, true);
			}
		} else {
			$data['triggered'] = false;
			$this->load->view('stakeholders/stakeholder_evaluation', $data);
		}
	}

	public function get_en()
	{
		$this->load->model('stakeholder_evaluation_factor');
		inspect_object($this->stakeholder_evaluation_factor->get_enum_values());
	}

	public function billing_address()
	{
		$stakeholder  = new Stakeholder();
		$stakeholder->load($this->input->post('stakeholder_id'));
		echo $stakeholder->address;
	}
}
