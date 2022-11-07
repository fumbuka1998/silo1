<?php
$this->load->view('includes/header');
$month_string = explode('-',date('Y-m-d'))[1] - 1 > 0 ? explode('-',date('Y-m-d'))[1] - 1 : 12;
$previous_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		<?= strtoupper($account_name) ?>
		<small>Account statement (<span id="currency_display"><?= $currency->symbol ?></span>)</small>
	</h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li><a href="<?= base_url('finance/accounts/'.strtolower($account_group))?>"><i class="fa fa-money"></i>Accounts List</a></li>
        <li class="active"><?= strtoupper($account_name) ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" id="account_statement_main_container">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-md-12">
						<form method="post" target="_blank" action="<?= base_url('finance/statement_transaction') ?>">
                        	<div class="box-tools pull-left">
							<div class="form-group col-md-4">
								<label class="col-md-3 control-label" for="to">From</label>
								<div class="col-md-9">
									<input class="form-control datepicker" name="from" value="<?= $previous_month ?>">
									<input type="hidden" name="currency_id" value="<?= $currency->{$currency::DB_TABLE_PK} ?>">
									<input type="hidden" name="account_type_and_id" value="<?= $account_type_and_id ?>">
								</div>
							</div>
							<div class="form-group col-md-4">
								<label class="col-md-3" for="to">To</label>
								<div class="col-md-9">
									<input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
								</div>
							</div>
							<div class="form-group col-md-4">
								<button type="button" id="generate_account_statement" class="btn btn-default btn-xs"><i class="fa fa-download"></i>Generate</button>
								<button name="print_pdf" type="submit" value="true"  class="btn btn-default btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
								<button name="export_excel" type="submit" value="true"  class="btn btn-default btn-xs"><i class="fa fa-file-excel-o"></i>Export Excel</button>
							</div>
                        </div>
						</form>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                			<div id="account_statement_container" class="col-xs-12 table-responsive">
                			</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');

