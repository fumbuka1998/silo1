<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Locations
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('inventory')?>"><i class="fa fa-barcode"></i>Inventory</a></li>
        <li class="active">Locations</li>
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
                            <button data-toggle="modal" data-target="#new_location" class="btn btn-xs btn-default">
                                <i class="fa fa-plus"></i> New location
                            </button>
                            <div id="new_location" class="modal fade" role="dialog">
                                <?php $this->load->view('inventory/locations/location_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class='row'>
                        <div class="col-xs-12 table-responsive">
                            <table id="locations_list" class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Location Name</th><th>Project</th><th>Description</th><th></th>
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