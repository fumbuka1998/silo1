<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/17/2018
 * Time: 4:28 PM
 */

$this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Enquiries
        <!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('requisitions')?>"><i class="fa fa-barcode"></i>Requisitions</a></li>
        <li class="active">Enquiries</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <div class="col-xs-12">
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Filter</label>
                        <?= form_dropdown('filter', [
                            'pending' => 'Pending',
                            'ALL' => 'All',
                            'requested' => 'Requested'
                        ],'',' class="form-control searchable"') ?>
                    </div>
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#new_enquiry"
                                class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New Enquiry
                        </button>
                        <div id="new_enquiry" class="modal fade enquiry_form" role="dialog">
                            <?php $this->load->view('requisitions/enquiries/enquiry_form'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="col-xs-12 table-responsive">
                    <table id="enquiries_table" class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Enquiry Date</th><th>Enquiry No</th><th>Vendor</th><th>For</th><th>Required Date</th><th style="width: 100px"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer');
