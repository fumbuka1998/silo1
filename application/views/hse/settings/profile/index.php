<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 11:03 AM
 */

$this->load->view('includes/header');
?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $title ?>
            <small>Parameters</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('hse/settings')?>"><i class="fa fa-book"></i>Category</a></li>
            <li class="active">Settings</li>
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
                            <button data-toggle="modal" data-target="#category_parameter_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> Parameter
                            </button>
                            <div id="category_parameter_form" class="modal fade" role="dialog">
                                <?php $this->load->view('hse/settings/profile/parameters/parameter_form');?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="category_parameters_list" category_id ="<?= $category->{$category::DB_TABLE_PK} ?>" class="table table-bordered table-hover" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th style="width: 35%">Parameter Name</th><th style="width: 35%">Description</th><th>Created By</th><th style="width: 15%"></th>
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
