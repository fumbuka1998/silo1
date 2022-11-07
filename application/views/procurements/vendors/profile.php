<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $vendor->vendor_name ?>
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('procurements')?>"><i class="fa fa-shopping-cart"></i>Procurements</a></li>
        <li><a href="<?= base_url('procurements/vendors')?>"><i class="fa fa-suitcase"></i>Vendors List</a></li>
        <li class="active"><?= $vendor->vendor_name ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li <?= !$invoices ? 'class="active"' : '' ?>><a href="#vendor_details" data-toggle="tab">Vendor Details</a></li>
                    <li><a href="#vendor_purchase_orders_tab" data-toggle="tab">Purchase Orders</a></li>
                    <li <?= $invoices ? 'class="active"' : '' ?>><a href="#vendor_invoices" data-toggle="tab">Invoices</a></li>
                    <li><a href="#vendor_reports" data-toggle="tab">Reports</a></li>
                </ul>
                <div class="tab-content">
                    <div class="<?= !$invoices ? 'active' : '' ?> tab-pane" id="vendor_details">
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
                                                    <?php $this->load->view('procurements/vendors/vendor_form'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-horizontal">

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Name:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $vendor->vendor_name ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $vendor->phone ? $vendor->phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Alt. Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $vendor->alternative_phone ? $vendor->alternative_phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Email:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $vendor->email ? $vendor->email : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Address:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $vendor->address ? nl2br($vendor->address) : 'N/A' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="vendor_purchase_orders_tab">
                        <?php $this->load->view('procurements/vendors/purchase_orders_tab'); ?>
                    </div>
                    <div class="<?= $invoices ? 'active' : '' ?> tab-pane" id="vendor_invoices">
                        <?php $this->load->view('procurements/vendors/invoices/vendor_invoices_tab'); ?>
                    </div>
                    <div class="tab-pane" id="vendor_reports">
                        <?php $this->load->view('procurements/vendors/reports_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');