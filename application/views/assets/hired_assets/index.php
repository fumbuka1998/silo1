<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Hired Assets
            <small><?= /** @var TYPE_NAME $list_type */
                $list_type == "clients" ? "Leased to Clients" : "Hired from Suppliers" ?></small>
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Hired Assets</li>
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
                            <?php
                            if(check_privilege('Hire Assets') || check_permission('Administrative Actions')){ ?>
                            <button data-toggle="modal" data-target="#hired_asset_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Asset
                            </button>
                            <div id="hired_asset_form" class="modal fade hired_asset_form" role="dialog">
                            <?php $this->load->view('assets/hired_assets/hired_asset_form');?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="hired_asset_list" list_type = "<?= $list_type ?>">
                            <thead>
                            <tr>
                                <th>Name</th><th style="width: 7%">Asset Code</th><th>Hiring Date</th><th><?= $list_type == "clients" ? "Client" : "Vendor" ?></th><th>Deadline</th><th>Amount</th><?php if($list_type == "suppliers"){ ?><th>Project</th><?php } ?><th>Status</th><th style="width: 10%;"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');