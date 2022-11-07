<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/13/2018
 * Time: 1:26 PM
 */

?>
<?php $this->load->view('includes/header');?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Imprests
        <!--<small>Sub-title</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Imprests</li>
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
                            <table id="imprests_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Imprest Date</th>
                                    <th>Imprest Voucher No</th>
                                    <th>Credit Account</th>
                                    <th>Debit Account</th>
                                    <th>Amount</th>
                                    <th>Status</th>
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

