<?php
$this->load->view('includes/header'); ?>
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Stakeholders
			<small>Clients | Suppliers | Contractors</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
			<li class="active">Stakeholders</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<div class="col-xs-12">
						<div class="box-tools pull-right">
							<button data-toggle="modal" data-target="#new_stakeholder" class="btn btn-default btn-xs">
								<i title="Add Stakeholder" class="fa fa-plus"></i>&nbsp;&nbsp;Stakeholder
							</button>
							<div id="new_stakeholder" class="modal fade" tabindex="-1" role="dialog">
								<?php $this->load->view('stakeholders/stakeholder_form'); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12 table-responsive">
							<table class="table table-bordered table-hover table-striped" id="stakeholders_list">
								<thead>
								<tr>
									<th>Name</th><th>Phone Number</th><th>Alternative Phone</th><th>Email</th><th>Address</th>
								</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('includes/footer');

