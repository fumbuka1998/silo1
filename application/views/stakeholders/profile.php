<?php $this->load->view('includes/header'); ?>
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			<?= strtoupper($stakeholder->stakeholder_name)."'S" ?>
			<small>Profile</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
			<li><a href="<?= base_url('stakeholders/')?>"><i class="fa fa-suitcase"></i>Stakeholders</a></li>
			<li class="active"><?= ucfirst($stakeholder->stakeholder_name) ?></li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li <?= !$invoices ? 'class="active"' : '' ?>><a href="#stakeholder_details_tab" stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" data-toggle="tab">Details</a></li>
					<?php if(check_permission('Projects')){ ?><li><a href="#stakeholder_projects_tab" stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" data-toggle="tab">Projects</a></li><?php } ?>
					<?php if(check_privilege('Sub Contracts')){ ?><li><a href="#stakeholder_sub_contracts_tab" stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" data-toggle="tab">Sub Contracts</a></li><?php } ?>
					<?php if(check_privilege('Purchase Orders') || check_privilege('Make Payment')){ ?><li><a href="#stakeholder_purchase_orders_tab" stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" data-toggle="tab">Purchase Orders</a></li><?php } ?>
					<?php if(check_privilege('Purchase Orders') || check_privilege('Make Payment')){ ?><li <?= $invoices ? 'class="active"' : '' ?>><a href="#stakeholder_invoices_tab" stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" data-toggle="tab">Invoices</a></li><?php } ?>
					<?php if(check_permission('Inventory')){ ?><li><a href="#stakeholder_sales_tab" stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" data-toggle="tab">Sales</a></li><?php } ?>
					<li><a href="#stakeholder_reports_tab" stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" data-toggle="tab">Reports</a></li>
				</ul>
				<div class="tab-content">
					<div class="<?= !$invoices ? 'active' : '' ?> tab-pane" id="stakeholder_details_tab">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header with-border">
										<div class="col-xs-12">
											<div class="box-tools pull-right">
												<button data-toggle="modal" data-target="#edit_form"
														class="btn btn-default btn-xs">
													<i class="fa fa-edit"></i> Edit
												</button>
												<div id="edit_form" class="modal fade" tabindex="-1" role="dialog">
													<?php $this->load->view('stakeholders/stakeholder_form'); ?>
												</div>
											</div>
										</div>
									</div>
									<div class="box-body">
										<div class="form-horizontal">

											<div class="form-group col-md-4 col-sm-6">
												<label  class="col-sm-4 control-label">Name:</label>
												<div class="form-control-static col-sm-8">
													<?= $stakeholder->stakeholder_name ?>
												</div>
											</div>

											<div class="form-group col-md-4 col-sm-6">
												<label  class="col-sm-4 control-label">Phone:</label>
												<div class="form-control-static col-sm-8">
													<?= $stakeholder->phone ? $stakeholder->phone : 'N/A' ?>
												</div>
											</div>

											<div class="form-group col-md-4 col-sm-6">
												<label  class="col-sm-4 control-label">Alt. Phone:</label>
												<div class="form-control-static col-sm-8">
													<?= $stakeholder->alternative_phone ? $stakeholder->alternative_phone : 'N/A' ?>
												</div>
											</div>

											<div class="form-group col-md-4 col-sm-6">
												<label  class="col-sm-4 control-label">Email:</label>
												<div class="form-control-static col-sm-8">
													<?= $stakeholder->email ? $stakeholder->email : 'N/A' ?>
												</div>
											</div>

											<div class="form-group col-md-4 col-sm-6">
												<label  class="col-sm-4 control-label">Address:</label>
												<div class="form-control-static col-sm-8">
													<?= $stakeholder->address ? nl2br($stakeholder->address) : 'N/A' ?>
												</div>
											</div>
										</div>
									</div>

									<div class="container-fluid pull-left col-xs-12">
										<div class="margin">
											<a href="#"  data-toggle="collapse" data-target="#evaluation_div"><strong>Stakeholders Evaluation</strong></a>
											<div id="evaluation_status" class="pull-right">
												<?php
												if($stakeholder_evaluation_factors){
													?>
													<span style="color: #06d604"><strong>Evaluated</strong></span>
													<?php
												}else{
													?>
													<i style="color: red" class="fa fa-warning"></i><span style="color: slategrey"><strong> Not Evaluated</strong></span>
													<?php
												} ?>
											</div>
										</div>
										<br/>

										<div id="evaluation_div" class="collapse col-xs-12">
											<table class="table table-bordered table-responsive stakeholder_evaluation" style="table-layout: fixed">
												<tbody>
												<input type="hidden" name="stakeholder_id" value="<?= $stakeholder->stakeholder_id ?>">
												<tr>
													<td>1</td>
													<td class="col-md-8 col-xs-12">General experience of the company in the field at least 3 years minimum: <strong>(15 points)</strong></td>
													<td style="width: 23%">
														<?= form_dropdown('general_experience', $enum_options->get_enum_values('general_experience'), $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_factor'][0] : '', "  class = ' form-control searchable' "); ?>
													</td>
													<td>
														<h5 id="general_experience_points"><?= $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_point'][0] : 0?> %</h5>
													</td>
												</tr>
												<tr>
													<td>2</td>
													<td>At least two (2) certificates of completion issued by the recognized institutions: <strong>(20 points)</strong></td>
													<td style="width: 23%">
														<?= form_dropdown('certificates_of_comletion', $enum_options->get_enum_values('certificate_of_completion'), $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_factor'][1] : '', "  class = ' form-control searchable' "); ?>
													</td>
													<td>
														<h5 id="certificates_of_comletion_points"><?= $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_point'][1] : 0?> %</h5>
													</td>
												</tr>
												<tr>
													<td>3</td>
													<td>Two (2) team supervisors with at least a bachelor's degree in management or any other related field: <strong>(30 points)</strong></td>
													<td style="width: 23%">
														<?= form_dropdown('team_supervisors', $enum_options->get_enum_values('two_team_supervisors_with_atleast_a_bachelor_degree'), $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_factor'][2] : '', "  class = ' form-control searchable' "); ?>
													</td>
													<td>
														<h5 id="team_supervisors_points"><?= $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_point'][2] : 0?> %</h5>
													</td>
												</tr>
												<tr>
													<td>4</td>
													<td>Financial capacity of at least payment of workers for 1 month salary: <strong>(5 points)</strong></td>
													<td style="width: 23%">
														<?= form_dropdown('financial_capacity', $enum_options->get_enum_values('financial_capacity_of_at_least_payment_of_workers_for_one_month'), $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_factor'][3] : '', "  class = ' form-control searchable' "); ?>
													</td>
													<td>
														<h5 id="financial_capacity_points"><?= $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_point'][3] : 0?> %</h5>
													</td>
												</tr>
												<tr>
													<td>5</td>
													<td>Proof of traning of the casual labourers in constructuin related fields/traning in water infrastructure as an added advantage: <strong>(30 points)</strong></td>
													<td style="width: 23%">
														<?= form_dropdown('casual_laborers', $enum_options->get_enum_values(), $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_factor'][4] : '', "  class = ' form-control searchable' "); ?>
													</td>
													<td>
														<h5 id="casual_laborers_points"><?= $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['evaluation_point'][4] : 0?> %</h5>
													</td>
												</tr>
												<tr>
													<td colspan="3"><strong>TOTAL POINTS (100%)</strong></td>
													<td>
														<h5 id="total_points"><strong><?= $stakeholder_evaluation_factors != '' ? $stakeholder_evaluation_factors['total_points'] : 0?> %</strong></h5>
													</td>
												</tr>
												</tbody>
											</table>
											<div class="pull-right">
												<button class="button btn-default btn-xs save_stakeholder_evaluation">Submit</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php if(check_permission('Projects')){ ?>
					<div class="tab-pane" id="stakeholder_projects_tab">
						<?php $this->load->view('stakeholders/projects/projects_tab'); ?>
					</div>
					<?php } if(check_privilege('Sub Contracts')){ ?>
					<div class="tab-pane" id="stakeholder_sub_contracts_tab">
						<?php $this->load->view('stakeholders/sub_contracts/sub_contracts_tab'); ?>
					</div>
					<?php } if(check_privilege('Purchase Orders') || check_privilege('Make Payment')){ ?>
					<div class="tab-pane" id="stakeholder_purchase_orders_tab">
						<?php $this->load->view('stakeholders/purchase_orders/purchase_orders_tab'); ?>
					</div>
					<?php } if(check_privilege('Purchase Orders') || check_privilege('Make Payment')){ ?>
					<div class="<?= $invoices ? 'active' : '' ?> tab-pane" id="stakeholder_invoices_tab">
						<?php $this->load->view('stakeholders/invoices/invoices_tab'); ?>
					</div>
					<?php } if(check_permission('Inventory')){ ?>
					<div class="tab-pane" id="stakeholder_sales_tab">
						<?php $this->load->view('stakeholders/sales/sales_tab'); ?>
					</div>
					<?php } ?>
					<div class="tab-pane" id="stakeholder_reports_tab">
						<?php $this->load->view('stakeholders/reports/reports_tab'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('includes/footer');
