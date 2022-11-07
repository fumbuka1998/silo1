<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Procurements
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li class="active">Procurements</li>
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
                                    <h3>&nbsp;</h3>

                                    <p>Approved Requisitions</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-clipboard"></i>
                                </div>
                                 <a href="<?= base_url('procurements/pre_orders') ?>" class="small-box-footer">Pre-orders <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <?php if(check_privilege('Purchase Orders')){ ?>
                            <div class="small-box bg-aqua-active">
                                <div class="inner">
                                    <h3>&nbsp;</h3>

                                    <p>Purchase Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-credit-card"></i>
                                </div><a href="<?= base_url('procurements/purchase_orders') ?>" class="small-box-footer">Purchase Orders <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <?php if(check_privilege('Vendors')){ ?>
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3><?= $number_of_vendors ?></h3>

                                    <p>Vendors</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-suitcase"></i>
                                </div><a href="<?= base_url('procurements/vendors') ?>" class="small-box-footer">Vendors <i class="fa fa-arrow-circle-right"></i></a>
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