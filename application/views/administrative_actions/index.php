<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Administrative Actions
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Administrative Actions</li>
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
                                    <h3><i class="fa fa-info-circle"></i></h3>

                                    <p>Company Details</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-info-circle"></i>
                                </div>
                                <a href="<?= base_url('administrative_actions/company_details') ?>" class="small-box-footer">Company Details <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3><i class="fa fa-search-plus"></i></h3>

                                    <p>Audit trail</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-search-plus"></i>
                                </div>
                                <a href="<?= base_url('administrative_actions/audit_trail') ?>" class="small-box-footer">Audit Trail <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');