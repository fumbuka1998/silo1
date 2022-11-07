<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		<?= strtoupper(str_replace("_"," ",$account_group)) ?>
		<small>Accounts List</small>
	</h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Accounts List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <div class="box-tools pull-left">
                            <?php
							if($account_group != "payable" && $account_group != "receivable") {
								if (check_privilege('Finance Actions')) { ?>
								<div class="form-group col-md-3">
									<button data-toggle="modal" data-target="#new_account"
											class="btn btn-default btn-xs">
										<i class="fa fa-plus"></i> New Account
									</button>
									<div id="new_account" class="modal fade" role="dialog">
										<?php $this->load->view('finance/account_form'); ?>
									</div>
								</div>
								<?php }
							}
							?>
							<div class="form-group col-md-9">
								<label class="col-md-4" for="currency">Currency: </label>
								<div style="padding-left: 2%" class="col-md-8">
									<?= form_dropdown('currency',$currency_options,'',' class="form-control"') ?>
								</div>
							</div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="accounts_list" account_group="<?= strtoupper($account_group) ?>" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Account Name</th><?php if($account_group == "bank"){ ?><th>Bank</th><?php } ?><th>Currency</th><?php if($account_group != "payable" && $account_group != "receivable"){ ?><th>Account For</th><?php } ?><th>Running Balance</th><?php if($account_group != "payable" && $account_group != "receivable"){ ?><th></th><?php } ?>
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
