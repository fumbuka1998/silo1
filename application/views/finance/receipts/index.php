<?php $this->load->view('includes/header');?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Receipts
            <!--<small>Sub-title</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('finance') ?>"><i class="fa fa-money"></i>Finance</a></li>
            <li class="active">Receipts</li>
        </ol>
    </section>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="col-xs-12">
                            <div class="box-tools pull-right">
                            <?php if(check_privilege('Make Payment')){ ?>
                                <div class="btn-group">
                                    <button data-toggle="modal" data-target="#acknowledge_receipt" class="btn btn-default btn-xs">
                                        <i class="fa fa-plus"></i> Receive
                                    </button>
                                    <div id="acknowledge_receipt" class="modal fade receipt_form" role="dialog">
                                        <?php
                                        $this->load->view('finance/receipts/receipt_form');
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table id="receipts_list" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Receipt. No.</th><th>Receipt. Date</th><th>Debit Account</th><th>Reference</th><th>Amount</th><th></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>