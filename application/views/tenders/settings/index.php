<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 10:21 PM
 */
?>
<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Settings
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('tenders/settings')?>"><i class="fa fa-book"></i>Requirements</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#requirement_type" data-toggle="tab">Requirement Type</a></li>
                    <div class="tab-content">
                        <div class="active tab-pane" id="requirement_type">
                            <?php $this->load->view('tenders/settings/requirement_type_tab'); ?>
                        </div>

                    </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
