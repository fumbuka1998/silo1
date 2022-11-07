

<?php $this->load->view('includes/header');
$vendor_options = isset($vendor_options) ? $vendor_options : vendor_dropdown_options();
?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Reports
            <small>Vendor Supply Report</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('reports')?>"><i class="fa fa-pie-chart"></i>Reports</a></li>
            <li class="active">Vendor Supply Report</li>
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
                                <form method="post" target="_blank" action="<?= base_url('reports/vendors_supply_report') ?>">
                                    <div class="form-group col-md-3">
                                        <label for="from" class="control-label">From</label>
                                        <input class="form-control datepicker" name="from" value="<?= date('Y-m-d') ?>">
                                        <input name="triggered" value="true" type="hidden">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="to" class="control-label">To</label>
                                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="form-group col-md-2 pull-right">
                                        <br/>
                                        <button type="button" id="generate_vendors_supply_report" class="btn btn-default btn-xs">
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