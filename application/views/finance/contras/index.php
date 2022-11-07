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
        Contras
        <!--<small>Sub-title</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Contras</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#new_contra" class="btn btn-default btn-xs">
                                New Contra
                            </button>
                            <div id="new_contra" class="modal fade contra_form" role="dialog">
                                <?php $this->load->view('finance/contras/contra_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table account_id="" id="contras_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Contra Date</th>
                                    <th>Contra No</th>
                                    <th>Credit Account</th>
                                    <th>Reference</th>
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

