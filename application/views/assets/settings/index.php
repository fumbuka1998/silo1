<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Settings
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('assets')?>"><i class="fa fa-book"></i>Assets</a></li>
        <li class="active">Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#asset_items" data-toggle="tab">Asset Items</a></li>
                    <li><a href="#asset_groups" data-toggle="tab">Asset Groups</a></li>
                <div class="tab-content">
                    <div class="active tab-pane" id="asset_items">
                        <?php $this->load->view('assets/settings/asset_items/asset_items_tab'); ?>
                    </div>
                    <div class="tab-pane" id="asset_groups">
                        <?php $this->load->view('assets/settings/asset_group/asset_group_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');