<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Asset Schedule Report
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('asset_register/Assets')?>"><i class="fa fa-users"></i>Asset Register</a></li>
        <li class="active">Asset Schedule Report</li>
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
                          
                        <label for="from_date" class="control-label">From</label>
                        <input type="text" name="from_date" class="form-control datepicker">

                      </div>

                       <div class="form-group col-md-2">
                          
                        <label for="to_date" class="control-label">To</label>
                        <input type="text" name="to_date" class="form-control datepicker">

                      </div>

                       <div class="form-group col-md-3">
                        <label for="asset_group_id" class="control-label">Asset Group</label>
                          <?= form_dropdown('asset_group_id',$asset_group_options,'',' id="filter_by_group" class="form-control searchable"') ?>
                      </div>

                      <div class="form-group col-md-3">
                        <label for="sub_location_id" class="control-label">Sub Location</label>
                          <?= form_dropdown('sub_location_id',$location_options,'',' id="filter_by_location" class="form-control searchable"') ?>
                      </div>

                       <div class="col-lg-2  col-md-2  col-sm-2">
                        <br>
                          <button type="button" class="btn btn-primary input-md" id='schedule_filter_button'>
                          <span class="glyphicon glyphicon-filter"></span>
                           FILTER
                           </button>

                      </div>

                    </div>
                </div>

                <div class="box-body" id="schedule_list">
                
                      <?php $this->load->view('asset_reports/schedule_list');?>  

                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');