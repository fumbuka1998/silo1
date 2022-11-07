<?php

$this->load->view('includes/header');

?>
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Procurements
                <small>Purchase Order Status</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="<?= base_url('procurements') ?>">Procurements</a></li>
                <li class="active">Purchase Order Status</li>
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
                                    <form method="post" target="_blank" action="<?= base_url('reports/purchase_order_statuses') ?>">
                                        <div class="form-group col-md-4">
                                            <label for="project_id" class="control-label">Vendor</label>
                                            <?= form_dropdown('vendor_id',$vendor_dropdown_options,'',' class="form-control searchable" required ') ?>
                                            <input name="triggered" value="true" type="hidden">
                                            <input name="print" value="true" type="hidden">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="from" class="control-label">From</label>
                                            <input type="text" class="form-control datepicker" name="from" value="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="from" class="control-label">To</label>
                                            <input type="text" class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <br/>
                                            <button id="generate_purchase_order_status_report" type="button" class="btn btn-default btn-xs">Generate</button>
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
                                <div id="chart_container" class="col-xs-12">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>