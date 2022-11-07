<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Human Resources Settings
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('human_resources')?>"><i class="fa fa-users"></i>Human Resources</a></li>
        <li class="active">Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#job_positions" data-toggle="tab">Job Positions</a></li>
                    <li><a href="#casual_labour_types" data-toggle="tab">Casual Labour Types</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="job_positions">
                        <?php $this->load->view('human_resources/settings/job_positions');?>
                    </div>
                    <div class=" tab-pane" id="casual_labour_types">
                        <?php $this->load->view('human_resources/settings/casual_labour_types');?>
                    </div>
                </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');