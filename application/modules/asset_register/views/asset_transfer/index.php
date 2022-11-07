<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Asset Transfers
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('asset_register/Assets')?>"><i class="fa fa-wrench"></i>Asset Register</a></li>
            <li class="active">Asset Transfers</li>
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
                        <button data-toggle="modal" data-target="#new_transfer_form" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New
                        </button>
                        <div id="new_transfer_form" class="modal fade" role="dialog">
                            <?php $this->load->view('asset_transfer_form');?>
                        </div>
                    </div>
                </div>
            </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table id="asset_transfer_list" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Department</th>
                                <th>Location</th>
                                <th>Under</th>
                                <th>Transfer Date</th>
                                <th>Transfered by</th>
                                <th>Posted On</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');