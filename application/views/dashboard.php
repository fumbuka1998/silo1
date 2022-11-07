<?php $this->load->view('includes/header');

?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">


        <?php
        if(check_privilege('Exchange Rates')){$privilege = 'true';}else{$privilege = 'false';}
        $exchange_rate_privilege = 'true';
        $this->load->model('exchange_rate_update');
        $last_exchangerate_date = $this->exchange_rate_update->get(1,0, ['update_date' => date('Y-m-d')]);
        if($last_exchangerate_date || $privilege == 'false'){
            $exchange_rate_privilege = 'false';
        }
         ?>

        <div id="exchange_rate_privilege" exchange_rate_privilege="<?= $exchange_rate_privilege ?>"></div>
        <div id="update_exchange_rates" class="modal fade" tabindex="-1" role="dialog">
            <?php $this->load->view('finance/settings/update_exchange_rates_form'); ?>
        </div>


        <div class="row">
            <?php

            $display_location_pie_chart = check_permission('Administrative Actions') && 1 == 0;
            if($display_location_pie_chart){
                ?>
                <div class="col-md-3 col-xs-12">
                    <div class=" box box-info">
                        <div class="box-body wrapper" style="min-height: 280px;" >
                            <div id="requisition_pie_chart_container" style="text-align: center; width:100%; height:100%;">
                                <div class="jm_spinner_store_values"></div>
                            </div>
                        </div>
                        <div class="box-footer clearfix">
                            <a style="border-radius: 7px;" href="<?= base_url('inventory/locations') ?>" class="btn btn-sm btn-yahoo btn-flat pull-right">Go to Locations</a>
                        </div>
                    </div>
                </div>
                <?php
            } ?>


            <div class="<?= $display_location_pie_chart ? 'col-md-6' : 'col-md-9'  ?> col-xs-12">
                <div class=" box box-info" style="font-size: 12px; min-height: 334px;" >
                    <div class="box-header with-border">
                        <h3 class="box-title" style="font-size: 15px">PENDING REQUISITIONS</h3>
                          <?php if(check_permission('Requisitions')){
                              ?>
                              <a style="border-radius: 7px;" href="<?= base_url('requisitions/requisitions_list') ?>" class="btn btn-sm btn-success btn-flat pull-right">View All Requisitions</a>
                              <?php
                          } ?>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" style="min-height: 240px;">
                        <div class="table-responsive">
                            <table id="dashboard_requisition_table" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Requisition ID</th>
                                    <th>Requested For</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-xs-12">
                <div class=" box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="font-size: 15px">REQUISITIONS</h3>
                    </div>
                    <div class="box-body" style="min-height: <?= check_permission('Requisitions') ? '239px' : '269px' ?>;">

                        <div class="progress-group">
                            <span class="progress-text">Pending Requisitions</span>
                            <span class="progress-number"><b><?= $requisitions['pending_requisitions'] ?></b>/<?= $requisitions['all_requisitions'] ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-aqua" style="width: <?= $requisitions['pending_requisitions_percent'] ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Rejected Requisitions</span>
                            <span class="progress-number"><b><?= $requisitions['rejected_requisitions'] ?></b>/<?= $requisitions['all_requisitions'] ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-red" style="width: <?= $requisitions['rejected_requisitions_percent'] ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Approved Requisitions</span>
                            <span class="progress-number"><b><?= $requisitions['approved_requisitions'] ?></b>/<?= $requisitions['all_requisitions'] ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-green" style="width: <?= $requisitions['approved_requisitions_percent'] ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                        <div class="progress-group">
                            <span class="progress-text">Incomplete Requisitions</span>
                            <span class="progress-number"><b><?= $requisitions['incomplete_requisitions'] ?></b>/<?= $requisitions['all_requisitions'] ?></span>

                            <div class="progress sm">
                                <div class="progress-bar progress-bar-yellow" style="width: <?= $requisitions['incomplete_requisitions_percent'] ?>%"></div>
                            </div>
                        </div>
                        <!-- /.progress-group -->
                    </div>
                    <div class="box-footer clearfix">
                        <?php if(check_permission('Requisitions')){
                            ?>
                            <a style="border-radius: 7px;" href="<?= base_url('requisitions/requisitions_list') ?>" class="btn btn-sm btn-twitter btn-flat pull-right">Go to Requisitions</a>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <?php if(check_permission('Finance')){ ?>
            <div class="col-md-12 col-xs-12">
                <div class=" box box-info">
                    <div class="box-body" >
                        <div id="approved_payments_chart_container" style="min-width: 100%; max-width: 100%; height: 200px; margin: 0 auto"></div>
                    </div>
                    <div class="box-footer clearfix">
                        <a style="border-radius: 7px;" href="<?= base_url('finance/approved_cash_requisitions') ?>" class="btn btn-sm btn-success btn-flat pull-right">Go to Approved Payments</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>


        <div class="row">
            <?php if(check_permission('Procurements')){
                ?>
                <div class="col-md-3 col-xs-12">
                    <div class=" box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title" style="font-size: 15px">ORDERS</h3>
                        </div>
                        <div class="box-body" style="min-height: <?= check_privilege('Procurement Actions') ? '239px' : '269px' ?> ;">

                            <div class="progress-group">
                                <span class="progress-text">Pending P.Os</span>
                                <span class="progress-number"><b><?= $purchase_orders['pending_purchase_orders'] ?></b>/<?= $purchase_orders['all_purchase_orders'] ?></span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-aqua" style="width: <?= $purchase_orders['pending_purchase_orders_percent'] ?>%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">Cancelled P.Os</span>
                                <span class="progress-number"><b><?= $purchase_orders['cancelled_purchase_orders'] ?></b>/<?= $purchase_orders['all_purchase_orders'] ?></span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-red" style="width: <?= $purchase_orders['cancelled_purchase_orders_percent'] ?>%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">Received P.Os</span>
                                <span class="progress-number"><b><?= $purchase_orders['received_purchase_orders'] ?></b>/<?= $purchase_orders['all_purchase_orders'] ?></span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-green" style="width: <?= $purchase_orders['received_purchase_orders_percent'] ?>%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                            <div class="progress-group">
                                <span class="progress-text">Closed P.Os</span>
                                <span class="progress-number"><b><?= $purchase_orders['closed_purchase_orders'] ?></b>/<?= $purchase_orders['all_purchase_orders'] ?></span>

                                <div class="progress sm">
                                    <div class="progress-bar progress-bar-yellow" style="width: <?= $purchase_orders['closed_purchase_orders_percent'] ?>%"></div>
                                </div>
                            </div>
                            <!-- /.progress-group -->
                        </div>
                        <div class="box-footer clearfix">

                            <?php if(check_privilege('Procurement Actions')){
                                ?>
                                <a style="border-radius: 7px;" href="<?= base_url('procurements/purchase_orders') ?>" class="btn btn-sm btn-info btn-flat pull-right">Go to Purchase Orders</a>
                                <?php
                            }
                            ?>


                        </div>
                    </div>
                </div>

                <div class="col-md-9 col-xs-12">
                    <div class=" box box-info" style="font-size: 12px; min-height: 334px;" >
                        <div class="box-header with-border">
                            <h3 class="box-title" style="font-size: 15px">PENDING ORDERS</h3>

                            <?php if(check_privilege('Procurement Actions')){
                                ?>
                                <a style="border-radius: 7px;" href="<?= base_url('procurements/purchase_orders') ?>" class="btn btn-sm btn-yahoo btn-flat pull-right">View All Purchase Orders</a>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="min-height: 240px;">
                            <div class="table-responsive">
                                <table id="dashboard_purchase_orders_table" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Vendor </th>
                                        <th>Delivery Location</th>
                                        <th>Project</th>
                                        <th>P.0 Amount</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } ?>
        </div>


    </section>
<?php $this->load->view('includes/footer');