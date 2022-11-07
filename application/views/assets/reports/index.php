<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/9/2018
 * Time: 10:47 AM
 */

$this->load->view('includes/header');
$month_string = explode('-',date('Y-m-d'))[1] - 1;
$previous_month = explode('-', date('Y-m-d'))[0].'-'.$month_string.'-'.explode('-',date('Y-m-d'))[2];

switch ($selected_report){
    case 'asset_item_availability':
        $report_name = 'asset_item_availability';
        break;
}

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Reports
        <small>Assets Reports</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Assets Reports</li>
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
                            <form method="post" target="_blank" action="<?= base_url('assets/reports') ?>">
                                <div class="form-group col-md-2">
                                    <label for="asset_name" class="control-label">Asset Name</label>
                                    <?= form_dropdown('asset_item_id',$asset_options,'',' class="form-control searchable"') ?>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="asset_group_id" class="control-label">Category</label>
                                    <?= form_dropdown('asset_group_id',$asset_group_options,'',' class="form-control searchable"') ?>
                                    <input name="print" type="hidden" value="true">
                                    <input name="report_type" hidden value="<?= $selected_report ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">Location</label>
                                    <?= form_dropdown('location_id',$location_options,'',' class="form-control searchable"') ?>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">From</label>
                                    <input class="form-control datepicker" name="from" value="<?= $previous_month ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">To</label>
                                    <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-2 pull-right">
                                    <br/>
                                    <button type="button" id="generate_assets_availability_report" class="btn btn-default btn-xs">
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
