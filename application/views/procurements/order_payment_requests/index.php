<?php $this->load->view('includes/header');
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/23/2018
 * Time: 6:33 AM
 */
?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Order Payment Requests
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('procurements')?>"><i class="fa fa-shopping-cart"></i>Procurements</a></li>
            <li class="active">Order Payment Requests</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="form-group col-md-2">
                            <label for="" class="control-label">Status</label>
                            <?= form_dropdown('status', [
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'all' => 'All'
                            ],'',' class="form-control" ') ?>
                        </div>
                        <?php if(check_privilege('Procurement Actions')) { ?>
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#new_order_payment"
                                    class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> Request
                            </button>
                            <div id="new_order_payment" class="modal fade order_payment_request_form" role="dialog">
                                <?php $this->load->view('procurements/order_payment_requests/order_payment_request_form'); ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped purchase_order_payment_request_list">
                                <thead>
                                    <tr>
                                        <th style="width: 110px">Request Date</th><th>P0. No.</th><th>Request No.</th><th>Order Supplier</th><th>Amount</th><th>Status</th><th style="width: 150px"></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right" colspan="4">Total Pending Amount(in Base Currency)</th><th id="total_requested_amount_display" style="text-align: right"></th><th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');