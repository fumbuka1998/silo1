<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Vendors
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('procurements') ?>"><i class="fa fa-credit-card"></i>Procurements</a></li>
        <li class="active">Vendors</li>
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
                            <button data-toggle="modal" data-target="#new_vendor" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Vendor
                            </button>
                            <div id="new_vendor" class="modal fade" tabindex="-1" role="dialog">
                                <?php $this->load->view('procurements/vendors/vendor_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="vendors_list">
                                <thead>
                                <tr>
                                    <th>Vendor Name</th><th>Phone Number</th><th>Alternative Phone</th><th>Email</th><th>Address</th>
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