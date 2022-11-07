<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Projects
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Projects</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua-gradient">
                                <div class="inner">
                                    <h3><?= $number_of_projects ?></h3>

                                    <p>Projects</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-product-hunt"></i>
                                </div>
                                <a href="<?= base_url('projects/projects_list') ?>" class="small-box-footer">Projects List <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <?php if(check_permission('Projects')){ ?>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>&nbsp;</h3>

                                    <p>Settings</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <a href="<?= base_url('projects/settings') ?>" class="small-box-footer">Settings <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');