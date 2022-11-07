<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Asset Register
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Asset Register</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua-gradient">
                                <div class="inner">
                                   <h3>&nbsp;</h3>

                                    <p>Assets</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-list"></i>
                                </div>
                                <a href="<?= base_url('asset_register/Assets/assets_list');?>" class="small-box-footer">Assets <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>&nbsp;</h3>

                                    <p>Asset Transfers</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-exchange"></i>
                                </div>
                                <a href="<?= base_url('asset_register/Asset_transfers/asset_transfers') ?>" class="small-box-footer">Asset Transfers <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->

                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua-active">
                                    <div class="inner">
                                        <h3>&nbsp;</h3>

                                        <p>Asset Depreciations</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-percent"></i>
                                    </div>
                                    <a href="<?= base_url('asset_register/Asset_reports/asset_depreciation_report') ?>" class="small-box-footer">Depreciations <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-blue-gradient">
                                    <div class="inner">
                                        <h3>&nbsp;</h3>

                                        <p>Asset Schedule</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <a href="<?= base_url('asset_register/Asset_reports/asset_schedule_report');?>" class="small-box-footer">Asset Schedule <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-blue">
                                <div class="inner">
                                    <h3>&nbsp;</h3>

                                    <p>Asset Disposal</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-ban"></i>
                                </div>
                                <a href="<?= base_url('asset_register/Assets') ?>" class="small-box-footer">Asset Disposal <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                         <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-blue-active">
                                <div class="inner">
                                    <h3>&nbsp;</h3>

                                    <p>Settings</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <a href="<?= base_url('asset_register/Assets/settings') ?>" class="small-box-footer">Settings <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');