<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/14/2018
 * Time: 5:12 PM
 */
?>
<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $category->category_name ?>&nbsp;TENDERS
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('tenders')?>"><i class="fa fa-folder"></i>Tenders</a></li>
            <li class="active"><?= $category->category_name ?>&nbsp;TENDERS</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="modal-body">
                    <div class='row'>
                        <div class="col-xs-12 table-responsive">
                            <table id="tenders_categorized_list" class="table table-bordered table-hover table-striped" category_id="<?= $category->{$category::DB_TABLE_PK} ?>">
                                <thead>
                                    <tr>
                                        <th>Tender No.</th><th>Tender Name</th><th>Client</th><th>Date Procured</th><th>Supervisor</th><th></th>
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