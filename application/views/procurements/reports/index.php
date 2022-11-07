<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/13/2018
 * Time: 9:30 AM
 */

$this->load->view('includes/header');
$report_type_options = [
    '&nbsp;'=>'&nbsp;',
    'requested_items'=>'Requested Items'
];

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Reports
        <small>Procurements Reports</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Procurements Reports</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-xs-12">
                        <div class="box-tools col-xs-12">
                            <form method="post" target="_blank" action="<?= base_url('procurements/reports') ?>">
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">Report Type</label>
                                    <?= form_dropdown('report_type',$report_type_options,'',' class="form-control"') ?>
                                    <input name="print" type="hidden" value="true">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">From</label>
                                    <input class="form-control datepicker" name="from" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">To</label>
                                    <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-2 pull-right">
                                    <br/>
                                    <button type="button" id="generate_procurement_report" class="btn btn-default btn-xs">
                                        Generate
                                    </button>
                                    <button name="pdf" value="true" class="btn btn-default btn-xs">
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
