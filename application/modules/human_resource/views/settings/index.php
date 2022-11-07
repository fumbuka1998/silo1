<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Human Resources Settings
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('human_resource/human_resources')?>"><i class="fa fa-users"></i>Human Resources</a></li>
        <li class="active">Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#banks" data-toggle="tab">Banks</a></li>
                    <li><a href="#departments" data-toggle="tab">Departments</a></li>
                    <li><a href="#job_positions" data-toggle="tab">Job Positions</a></li>
                    <li><a href="#casual_labour_types" data-toggle="tab">Casual Labour Types</a></li>
                    <li><a href="#branches" data-toggle="tab">Branches</a></li>
                    <li><a href="#ssfs" data-toggle="tab">SSFs</a></li>
                    <li><a href="#hifs" data-toggle="tab">HIFs</a></li>
                    <li><a href="#tax_table" data-toggle="tab">Tax Table</a></li>
                    <li><a href="#allowances" data-toggle="tab">Allowances</a></li>
                    <li><a href="#loans" data-toggle="tab">Loans</a></li>
                </ul>

                <div class="tab-content">
                    <div class="active tab-pane" id="banks">
                        <?php $this->load->view('settings/banks/banks_list');?>
                    </div>
                    <div class=" tab-pane" id="departments">
                        <?php $this->load->view('settings/departments/index');?>
                    </div>
                    <div class="tab-pane" id="job_positions">
                        <?php $this->load->view('settings/job_positions');?>
                    </div>
                    <div class=" tab-pane" id="casual_labour_types">
                        <?php $this->load->view('settings/casual_labour_types');?>
                    </div>
                    <div class=" tab-pane" id="branches">
                        <?php $this->load->view('settings/branches/branches_list');?>
                    </div>
                    <div class=" tab-pane" id="ssfs">
                        <?php $this->load->view('settings/ssfs/ssfs_list');?>
                    </div>
                    <div class=" tab-pane" id="tax_table">
                        <?php $this->load->view('settings/tax_tables/tax_tables_list');?>
                    </div>
                    <div class=" tab-pane" id="hifs">
                        <?php $this->load->view('settings/hifs/hifs_list');?>
                    </div>
                    <div class=" tab-pane" id="allowances">
                        <?php $this->load->view('settings/allowances/allowance_list');?>
                    </div>
                    <div class=" tab-pane" id="loans">
                        <?php $this->load->view('settings/loans/loan_type_list');?>
                    </div>

                </div>
    </div>
<?php $this->load->view('includes/footer');