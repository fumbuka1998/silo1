<?php $this->load->view('includes/header'); ?>
<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/27/2018
 * Time: 1:17 PM
 */

$month_string = explode('-',date('Y-m-d'))[1] - 1;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.$month_string.'-'.explode('-',date('Y-m-d'))[2];
?>
    <section class="content-header">
        <h1>
             Cheques
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('finance') ?>"><i class="fa fa-credit-card"></i>Finance</a></li>
            <li class="active">Cheques</li>
        </ol>
    </section>
    <br>
    <div class="box">
        <div class="box-header with-border">
            <div class="col-xs-12">
                <div class="box-tools">
                    <form method="post" target="_blank" action="<?= base_url('finance/cheques') ?>">
                        <div class="form-group col-md-2">
                            <label for="" class="control-label">From</label>
                            <input class="form-control datepicker" name="from" value="<?= $privious_month ?>">
                            <input type="hidden" name="print" value="true">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="" class="control-label">To</label>
                            <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <br/>
                            <button type="button" id="generate_cheques_list" class="btn btn-default btn-xs" >
                                Generate
                            </button>
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
                <div class="col-xs-12">
                    <div id="cheques_list_container" class="col-xs-12 table-responsive">

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
