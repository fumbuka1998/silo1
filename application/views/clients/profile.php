<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $client->client_name ?>
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('clients/clients')?>"><i class="fa fa-black-tie"></i>Clients</a></li>
        <li class="active"><?= $client->client_name ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#client_details" data-toggle="tab">Client Details</a></li>
                    <?php if(check_permission('Projects')){ ?>
                    <li><a href="#client_projects" data-toggle="tab">Projects</a></li>
                    <?php } if(check_permission('Tenders')){ ?>
                    <li><a href="#client_tenders"  client_id="<?= $client->{$client::DB_TABLE_PK} ?>"  data-toggle="tab">Tenders</a></li>
                    <?php } if(check_permission('Inventory')){ ?>
                    <li><a href="#client_sales" client_id="<?= $client->{$client::DB_TABLE_PK} ?>" data-toggle="tab">Sales</a></li>
                    <?php } ?>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="client_details">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <div class="col-xs-12">
                                            <div class="box-tools pull-right">
                                                <button data-toggle="modal" data-target="#edit_form"
                                                        class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <div id="edit_form" class="modal fade" tabindex="-1" role="dialog">
                                                    <?php $this->load->view('clients/client_form'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="form-horizontal">

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Name:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $client->client_name ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $client->phone ? $client->phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Alt. Phone:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $client->alternative_phone ? $client->alternative_phone : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Email:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $client->email ? $client->email : 'N/A' ?>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4 col-sm-6">
                                                <label  class="col-sm-4 control-label">Address:</label>
                                                <div class="form-control-static col-sm-8">
                                                    <?= $client->address ? nl2br($client->address) : 'N/A' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(check_permission('Projects')){ ?>
                    <div class="tab-pane" id="client_projects">
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-bordered table-hover" client_id="<?= $client->{$client::DB_TABLE_PK} ?>" id="clients_projects_table">
                                    <thead>
                                        <tr>
                                            <th>Project Name</th><th>Category</th><th>Reference No.</th><th>Client</th><th>Start Date</th><th>End Date</th><th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php }
                    if(check_permission('Tenders')){ ?>
                        <div class="tab-pane" id="client_tenders">
                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-bordered table-hover" client_id="<?= $client->{$client::DB_TABLE_PK} ?>" id="client_tenders_table">
                                        <thead>
                                            <tr>
                                                <th>Tender No.</th><th>Tender Name</th><th>Category</th><th>Date Announced</th><th>Date Procured</th><th>Supervisor</th><th></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if(check_permission('Inventory')){ ?>
                        <div class="tab-pane" id="client_sales">
                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-bordered table-hover" client_id="<?= $client->{$client::DB_TABLE_PK} ?>" id="sales_table">
                                        <thead>
                                        <tr>
                                            <th>Sales No.</th><th>Sale Date</th><th>Location</th><th>Reference</th><th></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');