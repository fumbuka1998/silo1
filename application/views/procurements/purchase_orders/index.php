<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Purchase Orders
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('procurements')?>"><i class="fa fa-shopping-cart"></i>Procurements</a></li>
        <li class="active">Purchase Orders</li>
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
                                'received' => 'Received',
                                'closed' => 'Closed',
                                'cancelled' => 'Cancelled',
                                'all' => 'All'
                            ],'',' class="form-control" ') ?>
                        </div>
                        <?php if(check_privilege('Procurement Actions')) { ?>
                        <!---
                            <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#new_purchase_order"
                                    class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> Purchase Order
                            </button>
                            <div id="new_purchase_order" class="modal fade purchase_order_form" role="dialog">
                                <?php //$this->load->view('procurements/purchase_orders/purchase_order_form'); ?>
                            </div>
                        </div>
                        --->
                        <?php } ?>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table orders_for="all" id="purchase_orders_table" order_id="" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th><th>Order No.</th><th>Vendor</th><th>Delivery Location</th><th style="width: 200px !important;">Project</th><th>P.O Value</th><th>Received Value</th><th>Balance</th><th>Status</th><th style="width: 10%"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');