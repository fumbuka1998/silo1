<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Hired Equipments
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('asset_register/Assets')?>"><i class="fa fa-barcode"></i>Asset Register</a></li>
        <li class="active">Hired Equipments</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               
                <li class="active"><a href="#equipment_receipts" data-toggle="tab">Equipments List</a></li>
                <li class=""><a href="#receipts_equipments" data-toggle="tab">Equipment Receipts</a></li>
            </ul>
            <div class="tab-content">
               
                <div class="active tab-pane" id="equipment_receipts">
                    <div class="box">
                        <div class="box-header">    </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-12 table-responsive">
                                        <table id="hired_equipments_table" class="table table-bordered hired_equipments_table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Equipment Code</th>
                                                    <th>Equipment Group</th>
                                                    <th>Rate</th>
                                                    <th>Rate Mode</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="tab-pane" id="receipts_equipments">
                    <div class="box">
                        <div class="box-header">
                            <div class="col-xs-12">
                                <div class="box-tools pull-right">
                                    <button data-toggle="modal" data-target="#new_equipment" class="btn btn-default btn-xs">
                                        Receive Equipment
                                    </button>
                                    <div id="new_equipment" class="modal fade equipment_receipt_form" role="dialog">
                                        <?php $this->load->view('hired_equipments/equipment_receipt_form');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-12 table-responsive">
                                        <table id="hired_equipments_list_table" class="table table-bordered hired_equipments_list_table table-hover">
                                            <thead>
                                            <tr>

                                                <th>Receipt Date</th>
                                                <th>Vendor</th>
<!--                                                <th>Hiring Order</th>-->
                                                <th>Comments</th>
                                                <th></th>
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