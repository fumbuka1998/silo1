<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Audit Trail
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('administrative_actions')?>"><i class="fa fa-dashboard"></i>Administrative Actions</a></li>
        <li class="active">Audit Trail</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools">
                            <form target="_blank" method="post" action="<?= base_url('administrative_actions/audit_trail_report') ?>">
                                <div class="form-group col-md-3">
                                    <label for="action_type" class="control-label">Action Type</label>
                                    <input type="hidden" name="print" value="true">
                                    <?php
                                        $action_types = sys_log_action_types();
                                        echo form_dropdown('action_type',$action_types,'',' class=" form-control searchable"')
                                    ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="project_id" class="control-label">Project</label>
                                    <?php
                                        $project_options = projects_dropdown_options();
                                        echo form_dropdown('project_id',$project_options,'',' class=" form-control searchable"')
                                    ?>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">From</label>
                                    <input type="text" class="form-control datetime_picker" name="from" value="<?= date('Y-m-d').' 00:00:00' ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">To</label>
                                    <input type="text" class="form-control datetime_picker" name="to" value="<?= date('Y-m-d').' 23:59:59' ?>">
                                </div>
                                <div class="col-md-2 col-xs-6">
                                    <br/>
                                    <button type="button" id="audit_trail_report_generator" class="btn btn-primary btn-xs">
                                        Generate
                                    </button>
                                    <button class="btn btn-primary btn-xs">
                                        <i class="fa fa-print"></i> PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div id="audit_trail_report_container" class="col-xs-12 table-responsive">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');