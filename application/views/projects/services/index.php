<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Services
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Services</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools  pull-right">
                            <button data-toggle="modal" data-target="#service_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i>New Service
                            </button>
                            <div id="service_form" class="modal fade service_form" role="dialog">
                                <?php $this->load->view('projects/services/service_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped " id="services_list">
                            <thead>
                            <tr>
                               <th>Service Date</th><th>Service No.</th><th style="width: 300px">Description</th><th style="width: 100px">Client</th><th>Location</th><th>Cost</th><th>Status</th><th style="width: 10%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');