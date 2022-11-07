<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Requisitions
        <!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Requisitions</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="requisition_header_container box-header">
                    <div class="col-xs-12">
                        <div class="form-group col-md-2">
                            <label for="" class="control-label">Status</label>
                            <?= form_dropdown('status', [
                                'mine' => 'My Requests',
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'incomplete' => 'Incomplete',
                                'rejected' => 'Rejected',
                                'all' => 'All'
                            ], '', ' class="form-control" ') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="" class="control-label">Approval Module</label>
                            <?= form_dropdown('approval_module', $approval_modules, '', ' class="form-control" ') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="" class="control-label">Approver</label>
                            <?= form_dropdown('approval_level', [], '', ' class="form-control" ') ?>
                        </div>
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#new_requisition" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> Requisition
                            </button>
                            <div id="new_requisition" class="modal fade requisition_form" role="dialog">
                                <?php $this->load->view('requisitions/requisitions_list/requisition_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="requisitions_table" class="table table-bordered table-striped requisitions_table table-hover">
                                <thead>
                                    <tr>
                                        <th><span class="event_date_title">Date Requested</span></th>
                                        <th>Requisition No</th>
                                        <th>Requested For</th>
                                        <th>Required Date</th>
                                        <th><span class="event_amount_title">Requested Amount</span></th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 150px"></th>
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
