<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 1:05 PM
 */
?>
<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Tenders
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('tenders')?>"><i class="fa fa-files-o"></i>Tender</a></li>
            <li class="active">Tenders</li>
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
                            <button data-toggle="modal" data-target="#tender_form" class="btn btn-xs btn-default">
                                <i class="fa fa-plus"></i> New Tender
                            </button>
                            <div id="tender_form" class="modal fade" role="dialog">
                                <?php $this->load->view('tenders/tender_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class='row'>
                        <div class="col-xs-12 table-responsive">
                            <table id="tenders_list" class="table table-bordered table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Tender No</th>
                                    <th>Tender Name</th>
                                    <th>Tender Category</th>
                                    <th>Client</th>
                                    <th>Date Procured</th>
                                    <th>Supervisor</th>
                                    <th></th>
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