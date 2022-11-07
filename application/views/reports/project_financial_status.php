<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 04/10/2018
 * Time: 16:07
 */
$this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Reports
            <small>Project Financial Status</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('reports')?>"><i class="fa fa-pie-chart"></i>Reports</a></li>
            <li class="active">Project Financial Status</li>
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
                                <form method="post" target="_blank" action="<?= base_url('reports/project_financial_status') ?>">
                                    <div class="form-group col-md-6">
                                        <label for="" class="control-label">Project</label>
                                        <?= form_multiselect('project_ids[]', $project_options, '', ' class="project_ids form-control searchable" ') ?>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="as_of" class="control-label">As of</label>
                                        <input type="hidden" name="triggered" value="true">
                                        <input type="text" class="form-control datepicker" required name="as_of" value="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="form-group  col-md-3">
                                        <br/>
                                        <button id="generate_project_financial_status_report" type="button" class="btn btn-default btn-xs">Generate</button>
                                        <button class="btn btn-default btn-xs" >
                                            <i class="fa fa-file-pdf-o"></i> PDF
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div id="report_container" class="col-xs-12 table-responsive">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>