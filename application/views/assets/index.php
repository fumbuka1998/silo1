<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Assets
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Assets</li>
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
                                   <h3><i class="fa fa-list"></i></h3>
                                    <p>Assets</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-list"></i>
                                </div>
                                <a href="<?= base_url('assets/assets_list');?>" class="small-box-footer">Assets <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                         <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3><i class="fa fa-cog"></i></h3>

                                    <p>Settings</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <a href="<?= base_url('assets/settings') ?>" class="small-box-footer">Settings <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');