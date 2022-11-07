<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 9:52 AM
 */
?>
<?php $this->load->view('includes/header'); ?>

    <section class="content-header">
        <h1>
            HSE
            <small> | Settings</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#category_parameters" data-toggle="tab">Category</a></li>
                    <li class=""><a href="#certificates" data-toggle="tab"> Registered Certificates</a></li>
                    <li class=""><a href="#toolbox_talk_register_topics" data-toggle="tab">Toolbox Talk Register Topics</a></li>
                    <div class="tab-content">
                        <div class="active tab-pane" id="category_parameters">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <div class="col-xs-12">
                                                <div class="box-tools pull-right">
                                                    <button data-toggle="modal" data-target="#requirement_type_form" class="btn btn-default btn-xs">
                                                        <i class="fa fa-plus"></i> Category
                                                    </button>
                                                    <div id="requirement_type_form" class="modal fade" role="dialog">
                                                        <?php $this->load->view('hse/settings/category_form');?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-xs-12 table-responsive">
                                                    <table id="hse_categories_list" class="table table-bordered table-hover" style="table-layout: fixed">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 35%">Categories</th><th style="width: 35%">Descriptions</th><th>Created By</th><th style="width: 15%"></th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class=" tab-pane" id="certificates">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <div class="col-xs-12">
                                                <div class="box-tools pull-right">
                                                    <button data-toggle="modal" data-target="#registered_certificate_form" class="btn btn-default btn-xs">
                                                        <i class="fa fa-plus"></i> New Certificate
                                                    </button>
                                                    <div id="registered_certificate_form" class="modal fade" role="dialog">
                                                        <?php $this->load->view('hse/settings/registered_certificates/registered_certificate_form');?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-xs-12 table-responsive">
                                                    <table id="registered_certificates_list" class="table table-bordered table-hover" style="table-layout: fixed">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 15%"> Employee </th><th style="width: 20%">Certificates</th><th  style="width: 8%"> Type </th><th>Descriptions</th><th style="width: 15%"></th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class=" tab-pane" id="toolbox_talk_register_topics">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <div class="col-xs-12">
                                                <div class="box-tools pull-right">
                                                    <button data-toggle="modal" data-target="#topic_form" class="btn btn-default btn-xs">
                                                        <i class="fa fa-plus"></i> New Topic
                                                    </button>
                                                    <div id="topic_form" class="modal fade" role="dialog">
                                                        <?php $this->load->view('hse/settings/topics/topic_form');?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-xs-12 table-responsive">
                                                    <table id="hse_topics_list" class="table table-bordered table-hover" style="table-layout: fixed">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 50%">Topics</th><th style="width: 40%">Description</th><th style="width: 10%"></th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
