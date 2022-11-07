<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/15/2019
 * Time: 2:01 AM
 */

$month_string = explode('-',date('Y-m-d'))[1] - 1;
$previous_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
?>
<?php $this->load->view('includes/header');?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Reports
        <small>Cash Flow</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('reports')?>"><i class="fa fa-pie-chart"></i>Reports</a></li>
        <li class="active">Cash Flow</li>
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
                            <form method="post" target="_blank" action="<?= base_url('reports/cash_flow') ?>">
                                <div class="form-group col-md-6">
                                    <label for="" class="control-label">Project</label>
                                    <?php
                                        $selected = [];

                                        foreach ($project_options as $id => $project_name){
                                            $selected[] = $id;
                                        }
                                        echo form_multiselect('project_ids[]', $project_options, $selected, ' class="project_ids form-control searchable" ')
                                    ?>
                                    <input name="print" value="true" type="hidden">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="from" class="control-label">From</label>
                                    <input type="text" class="form-control datepicker" name="from" value="2019-04-01">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="to" class="control-label">To</label>
                                    <input type="text" class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-2">
                                    <br/>
                                    <button id="generate_cash_flow_report" type="button" class="btn btn-default btn-xs">Generate</button>
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
