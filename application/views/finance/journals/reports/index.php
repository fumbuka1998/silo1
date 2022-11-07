<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 08/05/2019
 * Time: 02:54
 */

 $this->load->view('includes/header'); ?>
<?php

$month_string = explode('-',date('Y-m-d'))[1] - 1;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
?>
    <section class="content-header">
        <h1>
            Journal Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('finance') ?>"><i class="fa fa-credit-card"></i>Finance</a></li>
            <li class="active">Journal Reports</li>
        </ol>
    </section>
    <br>
    <div class="box">
        <div class="box-header with-border">
            <div class="col-xs-12">
                <div class="box-tools">
                    <form method="post" target="_blank" action="<?= base_url('finance/journal') ?>">
                        <div class="form-group col-md-2 currency_fg">
                            <label for="currency_id" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options, '', ' class="form-control" ') ?>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="" class="control-label">From</label>
                            <input class="form-control datepicker" name="from" value="<?= $privious_month ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="" class="control-label">To</label>
                            <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <br/>
                            <button type="button" id="generate_journal" class="btn btn-default btn-xs">Generate</button>
                            <button name="print" type="submit" value="true"  class="btn btn-default btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <div id="report_container" class="col-xs-12 table-responsive">

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
