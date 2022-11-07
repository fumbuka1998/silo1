<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Transactions
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Transactions</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <?php if(check_privilege('Approved Payments')){ ?><li id="approved_cash_requisition_button" class="active"><a href="#payment_vouchers" data-toggle="tab">Approved Payments</a></li><?php } ?>
                    <?php if(check_privilege('Contra')){ ?><li><a href="#contras" data-toggle="tab">Contras</a></li><?php } ?>
                    <?php if(check_privilege('Make Payment')){ ?><li><a href="#journals" data-toggle="tab">Journals</a></li><?php } ?>
                    <?php if(check_privilege('Receipts')){ ?><li><a href="#receipts" data-toggle="tab">Receipts</a></li><?php } ?>
                </ul>
                <div class="tab-content">
                    <?php if(check_privilege('Approved Payments')){ ?>
                        <div class="active tab-pane" id="payment_vouchers">
                            <?php $this->load->view('finance/transactions/approved_cash_requests/index'); ?>
                        </div>
                    <?php } if(check_privilege('Contra')){ ?>
                        <div class="tab-pane" id="contras">
                            <?php $this->load->view('finance/transactions/contras/index'); ?>
                        </div>
                    <?php } if(check_privilege('Make Payment')){ ?>
                        <div class="tab-pane" id="journals">
                            <?php $this->load->view('finance/transactions/journals/index'); ?>
                        </div>
                    <?php } if(check_privilege('Receipts')){ ?>
                        <div class="tab-pane" id="receipts">
                            <?php $this->load->view('finance/transactions/receipts/index'); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->load->view('includes/footer');
