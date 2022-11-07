<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $location->location_name ?>
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('inventory')?>"><i class="fa fa-barcode"></i>Inventory</a></li>
        <li><a href="<?= base_url('inventory/locations')?>"><i class="fa fa-building-o"></i>Locations</a></li>
        <li class="active"><?= $location->location_name ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
<?php
    $this->load->view('inventory/locations/location_workspace');
    $this->load->view('includes/footer');