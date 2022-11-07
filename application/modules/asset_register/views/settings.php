<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Settings
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('asset_register/Assets')?>"><i class="fa fa-wrench"></i>Asset Register</a></li>
        <li class="active">Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <?php
                        $active_tab = 0;
                        if(check_permission('Tools')){
                    ?>
                    <li class="<?= $active_tab == 0 ? 'active' : '' ?>"><a href="#tools_types" data-toggle="tab">Asset Groups</a></li>
                    <?php
                            $active_tab = 1;
                        } if(check_permission('Equipment')){
                     ?>
                    <li class="<?= $active_tab == 0 ? 'active' : '' ?>"><a href="#equipment_types" data-toggle="tab">Asset Depreciation Rates</a></li>
                    <?php
                        $active_tab = 1;
                    } ?>
                </ul>
                <div class="tab-content">
                    <?php
                        $active_pane = 0;
                        if(check_permission('Tools')){
                    ?>
                    <div class="<?= $active_pane == 0 ? 'active' : '' ?> tab-pane" id="tools_types">
                        <?php $this->load->view('asset_group_tab'); ?>
                    </div>
                    <?php
                            $active_pane = 1;
                        } if(check_permission('Equipment')){
                     ?>
                    <div class="<?= $active_pane == 0 ? 'active' : '' ?> tab-pane" id="equipment_types">

                        <?php $this->load->view('depreciation/depreciation_list'); ?>
                        
                    </div>
                    <?php
                        $active_pane = 1;
                    } ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');