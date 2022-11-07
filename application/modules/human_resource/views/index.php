<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Human Resources
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Human Resources</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <?php
                        if(check_privilege('Employee List') || check_privilege('Timesheet') || check_privilege('Human Resource Settings') ||check_privilege('Payroll')){
                            if(check_privilege('Employee List')){ ?>
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua-gradient">
                                    <div class="inner">
                                        <h3><?= $number_of_employees ?></h3>
                                        <p>Employees</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <a href="<?= base_url('human_resource/Human_resources/employees_lists'); ?>" class="small-box-footer">Employees List <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } if(check_privilege('Payroll')) { ?>

                             <!-- ./col -->
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua-active">
                                    <div class="inner">
                                        <h3>&nbsp;</h3>

                                        <p>Payroll</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <a href="<?= base_url('human_resource/human_resources/payroll') ?>" class="small-box-footer">Payroll <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <? } if(check_privilege('Timesheet')){ ?>
                            <!-- ./col -->

                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua-active">
                                    <div class="inner">
                                        <h3>&nbsp;</h3>

                                        <p>Timesheet</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">Timesheet <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php } if(check_privilege('Human Resource Settings')){ ?>

                             <!-- ./col -->
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua-active">
                                    <div class="inner">
                                        <h3>&nbsp;</h3>

                                        <p>Settings</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-cog"></i>
                                    </div>
                                    <a href="<?= base_url('human_resource/human_resources/settings') ?>" class="small-box-footer">Settings <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <?php }
                        } else { ?>
                            <div style="text-align: center" class="alert alert-warning col-xs-12">
                                Sorry! You do not have any privilege here
                            </div>
                        <?php
                        }
                        ?>
                        <!-- ./col -->

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');