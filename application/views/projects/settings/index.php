<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Settings
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-cog"></i>Dashboard</a></li>
        <li><a href="<?= base_url('projects')?>"><i class="fa fa-product-hunt"></i>Projects</a></li>
        <li class="active">Settings</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#project_categories" data-toggle="tab">Project Categories</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="project_categories">
                        <?php $this->load->view('projects/settings/project_categories_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');