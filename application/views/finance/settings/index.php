<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Settings
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
					<li class="active"><a href="#chart_of_accounts" data-toggle="tab">Chart Of Accounts</a></li>
                    <li><a href="#currencies" data-toggle="tab">Currencies</a></li>
                    <li><a href="#cost_centers" data-toggle="tab">Cost Centers</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="chart_of_accounts">
                        <?php $this->load->view('finance/settings/chart_of_accounts_tab'); ?>
                    </div>
                    <div class="tab-pane" id="currencies">
                        <?php $this->load->view('finance/settings/currencies_tab'); ?>
                    </div>
                     <div class="tab-pane" id="cost_centers">
                        <?php $this->load->view('finance/settings/cost_centers_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
