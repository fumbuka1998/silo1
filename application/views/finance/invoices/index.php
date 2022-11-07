<?php
$this->load->view('includes/header');
switch ($type){
	case 'sales':
		$column_title = 'Invoice No';
		break;
	case 'purchases':
		$column_title = 'Order';
		break;
}

?>
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			<?= ucfirst($type) ?>
			<small>Invoices</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
			<li><a href="<?= base_url('finance')?>"><i class="fa fa-users"></i>Finance</a></li>
			<li class="active"><? ucfirst($type) ?> Invoices</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<div class="col-xs-12">
						<div class="form-group col-md-2 box-tools pull-left">
							<button data-toggle="modal" data-target="#pay_in_bulk"
									class="btn btn-flat btn-xs" style="background: #9cc2cb;">
								<?php if($type == 'sales'){ ?><i class="fa fa-send"></i><?php } else { ?><i class="fa fa-download"></i><?php } ?> Invoice
							</button>
							<div id="pay_in_bulk" class="modal fade invoice_form" role="dialog">
								<?php $this->load->view('finance/invoices/invoice_form'); ?>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group col-md-2">
							<label for="" class="control-label">Filter</label>
							<?= form_dropdown('filter', [
								'&nbsp;'=>'&nbsp;',
								'due_in_two' => 'Due in 2 days',
								'due_in_five' => 'Due in 5 days',
								'due_in_ten' => 'Due in 10 days',
								'due_in_aboveten' => 'Due in more than 10 days',
								'overdue' => 'Overdue'
							],'',' class="form-control searchable"') ?>
						</div>
						<?php
							switch($type){
								case 'sales';
									$stakeholder = "Client";
									break;
								case 'purchases';
									$stakeholder = "Vendor";
									break;
							}
						?>
						<div class="form-group col-md-2">
							<label for="" class="control-label"><?= $stakeholder ?></label>
							<?= form_dropdown('stakeholder', $stakeholder_options,'',' class="form-control searchable"') ?>
						</div>
					</div>
				</div>
				<div class="box-body">
					<div class="col-xs-12 table-responsive">
						<table list_type="<?= $type ?>" class="table table-bordered table-hover table-striped" id="invoices_list">
							<thead>
							<tr>
								<th style="width: 7%">Date</th><th><?= $column_title ?></th><th><?= $stakeholder ?></th><th style="width: 15%;" nowrap="true">Total Amount</th><th style="width: 15%;" nowrap="true">Outstanding Balance</th><th>Status</th><th style="width: 12%"></th>
							</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('includes/footer');
