
<?php
$this->load->view('includes/header');
$report_type_options = [
     '' => 'ALL',
    'PENDING' => 'PENDING',
    'RECEIVED' => 'RECEIVED',
    'CANCELLED' => 'CANCELLED',
    'CLOSED' => 'CLOSED'
];
?>
<section class="content-header">
    <h1>
        Purchase Order Reports
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('procurements') ?>"><i class="fa fa-credit-card"></i>Procurement</a></li>
        <li class="active">Purchase Order Reports</li>
    </ol>
</section>
</br>
<div class="box">
    <div class="box-header">
        <div class="col-xs-12">
            <div class="box-tools col-xs-12">
                <form method="post" target="_blank" action="<?= base_url('procurements/orders_report') ?>">
                    <div class="form-group col-md-3">
                        <label for="" class="control-label">Report Type:</label>
                        <?= form_dropdown('report_type',$report_type_options,'',' class="form-control"') ?>
                        <input name="print" type="hidden" value="true">
                        <input name="triggered" type="hidden" value="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="" class="control-label">Vendor Name:</label>
                        <?= form_dropdown('vendor_id',$vendor_options,'',' class="form-control searchable"'); ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">From</label>
                        <input class="form-control datepicker" name="from" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">To</label>
                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-2 ">
                        <br/>
                        <button type="button" id="generate_purchase_order_report" class="btn btn-default btn-xs">
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
            <div id="purchase_order_report_container" class="col-xs-12 table-responsive">

            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer');