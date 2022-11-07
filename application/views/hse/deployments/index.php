<?php
?>
<?php $this->load->view('includes/header'); ?>
    <section class="content-header">
        <h1>
            <?= $index_title ?>
            <small>Deployments</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Deployments</li>
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
                            <a href="<?= base_url('hse/deployment_form')?>" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Deployment
                            </a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="deployments_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Trip Name</th><th>Departure</th><th>Arrival</th><th> Vehicle </th><th> Driver </th><th style="width: 15%"></th>
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