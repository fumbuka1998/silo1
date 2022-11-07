<?php
$this->load->view('includes/header');?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Reports
            <small>Cost Center Payments Report</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('reports')?>"><i class="fa fa-pie-chart"></i>Reports</a></li>
            <li class="active">Cost Center Payments Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border no-print">
                        <div class="col-xs-12">
                            <div class="box-tools">
                                <form method="post" target="_blank" action="<?= base_url('reports/cost_center_payments') ?>">
                                    <div class="form-group col-md-4">
                                        <label for="project_id" class="control-label">Cost Center</label>
                                        <?php
                                        $cost_center_options = ['all' => 'ALL'] + $cost_center_options;
                                            echo form_dropdown('cost_center_id',$cost_center_options,'',' class="form-control searchable" required ')
                                        ?>
                                        <input name="print" value="true" type="hidden">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="from" class="control-label">From</label>
                                        <input type="text" class="form-control datepicker" name="from" value="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="to" class="control-label">To</label>
                                        <input type="text" class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <br/>
                                        <button id="generate_cost_center_payments_report" type="button" class="btn btn-default btn-xs">Generate</button>
                                        <button class="btn btn-default btn-xs">
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