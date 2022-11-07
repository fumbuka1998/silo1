<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Clients List
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('clients')?>"><i class="fa fa-users"></i>Clients</a></li>
        <li class="active">Clients List</li>
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
                            <button data-toggle="modal" data-target="#client_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Client
                            </button>
                            <div id="client_form" class="modal fade" role="dialog">
                                <?php $this->load->view('clients/client_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="clients_list">
                            <thead>
                                <tr>
                                    <th>Client Name</th><th>Phone Number</th><th>Alternative Phone</th><th>Email</th><th>Address</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');