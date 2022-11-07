<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/3/2019
 * Time: 5:24 PM
 */

$this->load->view('includes/header');
$month_string = explode('-',date('Y-m-d'))[1] - 1;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Reports
        <small>Journal</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Journal</li>
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
                            <form method="post" target="_blank" action="<?= base_url('finance/reports') ?>">
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">From</label>
                                    <input name="print" type="hidden" value="true">
                                    <input class="form-control datepicker" name="from" value="<?= $privious_month ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="" class="control-label">To</label>
                                    <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-2 pull-right">
                                    <br/>
                                    <button type="button" id="generate_journal" class="btn btn-default btn-xs">
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
