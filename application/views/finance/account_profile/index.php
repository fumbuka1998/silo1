<?php $this->load->view('includes/header');?>
    <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $title ?>
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li><a href="<?= base_url('finance/accounts_list')?>"><i class="fa fa-list"></i>Accounts</a></li>
        <li class="active"><?= $title ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <?php $this->load->view('finance/account_profile/profile_content'); ?>
<?php $this->load->view('includes/footer');