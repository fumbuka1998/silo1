<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Inventory Settings
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('inventory')?>"><i class="fa fa-barcode"></i>Inventory</a></li>
        <li class="active">Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#material_item_categories" data-toggle="tab">Material Item Categories</a></li>
                    <li><a href="#measurement_units" data-toggle="tab">Measurement Units</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="material_item_categories">
                        <?php $this->load->view('inventory/settings/material_item_categories_tab'); ?>
                    </div>
                    <div class=" tab-pane" id="measurement_units">
                        <?php $this->load->view('inventory/settings/measurement_units_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');