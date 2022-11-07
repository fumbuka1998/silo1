<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/17/2018
 * Time: 4:30 PM
 */

?>
<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Requisitions
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Requisitions</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua-gradient">
                                    <div class="inner">
                                        <h3><i class="fa fa-list"></i></h3>
                                        <p>Requisitions List</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <a href="<?= base_url('requisitions/requisitions_list') ?>" class="small-box-footer">Requisitions List <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3>&nbsp;<i class="fa fa-clipboard"></i></h3>
                                        <p>Enquiries</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-clipboard"></i>
                                    </div><a href="<?= base_url('requisitions/enquiries_list') ?>" class="small-box-footer">Enquiries List<i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
