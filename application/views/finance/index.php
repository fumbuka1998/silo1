<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Finance
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Finance</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <?php if(check_privilege('Accounts')){ ?>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua-gradient">
                                <div class="inner">
                                    <h3><i class="fa fa-list"></i></h3>

                                    <p>Accounts List</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-list"></i>
                                </div>
                                <a href="<?= base_url('finance/accounts_list') ?>" class="small-box-footer">Accounts List <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <?php } if(check_privilege('Approved Payments')){ ?>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>&nbsp;<i class="fa fa-clipboard"></i></h3>
                                    <p>Approved Cash</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-clipboard"></i>
                                </div><a href="<?= base_url('finance/approved_cash_requisitions') ?>" class="small-box-footer">Approved Cash <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <?php } ?>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua-active">
                                <div class="inner">
                                    <p class="sub_menu_links" style=" height: 73px; font-size: 17px">
                                        <a href="<?= base_url('finance/payments') ?>"><i class="fa fa-credit-card-alt"></i> Payments</a><br/>
                                        <?php if(check_privilege('Receipts')){ ?><a href="<?= base_url('finance/receipts') ?>"><i class="fa fa-money"></i> Receipts</a><br/><?php } ?>
                                        <?php if(check_privilege('Contras')){ ?><a href="<?= base_url('finance/contras') ?>"><i class="fa fa-exchange"></i> Contras</a><br/><?php } ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-credit-card-alt"></i>
                                </div>
                                <a href="#" class="small-box-footer">Transactions <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <?php if(check_privilege('Statements')){ ?>
                            <div class="small-box bg-blue-gradient">
                                <div class="inner">
                                    <p class="sub_menu_links" style=" height: 73px; font-size: 17px">
                                        <a href="<?= base_url('finance/account_statement') ?>"><i class="fa fa-list-alt"></i> Statements</a>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="#" class="small-box-footer">Statements <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <?php if(check_privilege('Finance Settings')){ ?>
                            <div class="small-box bg-blue-active">
                                <div class="inner">
                                    <h4>&nbsp;<i class="fa fa-cog"></i></h4>
                                    <p style="font-size: 18px !important;">Settings</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <a href="<?= base_url('finance/settings') ?>" class="small-box-footer">Settings <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');