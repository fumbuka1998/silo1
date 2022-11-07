<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/17/2018
 * Time: 4:48 AM
 */
?>
<?php $this->load->view('includes/header');?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Payments
        <!--<small>Sub-title</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Payments</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <!--<div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">New Payments</button>
                                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-toggle="modal" data-target="#add_new_expense_payment" href="#">Expense Payment</a></li>
                                    <li><a data-toggle="modal" data-target="#add_new_invoice_payment" href="#">Invoice Payment</a></li>
                                </ul>
                            </div>
                            <div id="add_new_expense_payment" class="modal fade expense_payment_form" role="dialog">
                                <?php
/*                                $this->load->view('finance/payments/expense_payment_form');
                                */?>
                            </div>
                            <div id="add_new_invoice_payment" class="modal fade invoice_payment_form" role="dialog">
                                <?php
/*                                $this->load->view('finance/payments/invoice_payment_form');
                                */?>
                            </div>
                        </div>
                    </div>-->
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="payments_table" class="table table-bordered table-hover payments_table">
                                <thead>
                                <tr>
                                    <th>Payment No.</th>
                                    <th>Payment Date</th>
                                    <th>Reference</th>
                                    <th>Credit Account</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>


<?php
exit;
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/17/2018
 * Time: 4:48 AM
 */
?>
<?php $this->load->view('includes/header');?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Payments
            <!--<small>Sub-title</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
            <li class="active">Payments</li>
        </ol>
    </section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="payments_table" class="table table-bordered table-hover payments_table">
                                <thead>
                                <tr>
                                    <th>Payment No.</th>
                                    <th>Payment Date</th>
                                    <th>Reference</th>
                                    <th>Credit Account</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>

