<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 9:33 PM
 */
?>
<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $tender->tender_name ?>
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('tenders')?>"><i class="fa fa-files-o"></i>Tenders</a></li>
            <li><a href="<?= base_url('tenders/tenders_list')?>"><i class="fa fa-list-alt"></i>Tenders List</a></li>
            <li class="active"><?= $tender->tender_name ?></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li  class="active"><a href="#tender_general" data-toggle="tab">General</a></li>
                    <li><a href="#tender_components" data-toggle="tab">Components</a></li>
                    <li><a href="#tender_attachments" data-toggle="tab">Attachments</a></li>
                    <li><a href="#tender_requirements" data-toggle="tab">Requirements</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tender_components">
                        <?php $this->load->view('tenders/profile/components/components_tab'); ?>
                    </div>
                    <div class="tab-pane" id="tender_attachments">
                        <?php $this->load->view('tenders/profile/attachments/tender_attachments_tab'); ?>
                    </div>
                    <div class="tab-pane" id="tender_requirements">
                        <?php $this->load->view('tenders/profile/requirements/tender_requirements_tab'); ?>
                    </div>
                    <div class="active tab-pane" id="tender_general">
                        <?php $this->load->view('tenders/profile/tender_general_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    $this->load->view('includes/footer');