<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Equipment Requisitions
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('asset_register/Assets')?>"><i class="fa fa-barcode"></i>Asset Register</a></li>
        <li class="active">Requisitions</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#all" data-toggle="tab">All</a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="all">
                    <div class="box">
                        <div class="box-header">
                            <div class="col-xs-12">
                                <div class="box-tools pull-right">
                                    <button data-toggle="modal" data-target="#new_requisition" class="btn btn-default btn-xs">
                                        New Requisition
                                    </button>
                                    <div id="new_requisition" class="modal fade requisition_form" role="dialog">
                                        <?php $this->load->view('equipments_requisitions/requisition_form'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="equipment_requisitions_table" class="table table-bordered requisitions_table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Request Date</th><th>Requisition Number</th><th>Requested For</th><th>Required Date</th><th>Status</th><th></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');