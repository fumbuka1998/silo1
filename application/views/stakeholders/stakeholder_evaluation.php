<?php

$this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Contractors Evaluations
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('contractors/contractors_list')?>"><i class="fa fa-list"></i>Contractors List</a></li>
            <li class="active">Contractors Evaluation</li>
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
                                <form method="post" target="_blank"  action="<?= base_url('contractors/contractors_evaluation') ?>">
                                    <div class="form-group col-md-6">
                                        <label for="" class="control-label">Contractors</label>
                                        <?= form_multiselect('contractors_ids[]', $contractor_options, '', ' class="contractors_ids form-control searchable" ') ?>
                                        <input type="hidden" name="triggered" value="true">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="" class="control-label">Projects</label>
                                        <?= form_dropdown('project_id', $project_options, '', ' class="project_id form-control searchable" ') ?>
                                        <input type="hidden" name="triggered" value="true">
                                    </div>

                                    <div class="form-group  col-md-2">
                                        <br/>
                                        <button id="generate_contractors_evaluation_report" type="button" class="btn btn-default btn-xs">Generate</button>
                                        <button  class="btn btn-default btn-xs" >
                                            <i class="fa fa-file-pdf-o"></i> PDF
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div id="multiple_evaluation_container" class="col-xs-12 table-responsive">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>