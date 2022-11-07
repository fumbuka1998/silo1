<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Asset List
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('asset_register/Assets')?>"><i class="fa fa-users"></i>Asset Register</a></li>
        <li class="active">Asset List</li>
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
                          
                          <label for="asset_group_id" class="control-label pull-right">Asset Group</label>

                      </div>

                         <div class="form-group col-md-3">
                            
                            <?= form_dropdown('asset_group_id',$asset_group_options,'',' id="filter_by_group" class="form-control searchable"') ?>
                        </div>

                        <div class="box-tools  pull-right">
                            <button data-toggle="modal" data-target="#asset_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Asset
                            </button>
                            <div id="asset_form" class="modal fade" role="dialog">
                                <?php $this->load->view('assets/asset_form');?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="asset_list">
                            <thead>
                              <tr>
                                <th>Asset Name</th>
                                <th>Asset Code</th>
                                <th>Asset Group</th>
                                <th>Book Value</th>
                                <th>Status</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');