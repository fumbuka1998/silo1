<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 6/9/2018
 * Time: 10:50 AM
 */
?>

<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Company Details
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('administrative_actions')?>"><i class="fa fa-support"></i>Administrative Actions</a></li>
            <li class="active">Company Details</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li  class="active"><a href="#general_company_details" data-toggle="tab">General Company Details</a></li>
                    <li><a href="#company_attachments" data-toggle="tab">Attachments</a></li>
                    <li><a href="#hse_certificates" data-toggle="tab">HSE Certificates</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="general_company_details">
                        <?php $this->load->view('administrative_actions/company_details/company_details'); ?>
                    </div>
                    <div class="tab-pane" id="company_attachments">
                        <?php $this->load->view('administrative_actions/company_details/company_attachment_tab'); ?>
                    </div>
                    <div class="tab-pane" id="hse_certificates">
                        <?php $this->load->view('hse/certificates/index'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
