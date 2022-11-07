<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:41 PM
 */

$this->load->view('includes/header');
$month_string = explode('-',date('Y-m-d'))[1] - 1;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Finance
        <small>Journal Entries</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Journal Entries</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-xs-12">
                        <div class="box-tools col-xs-12">
                            <div class="box-tools pull-right">
                                <button data-toggle="modal" data-target="#new_journal_voucher"
                                        class="btn btn-default btn-xs">
                                    <i class="fa fa-plus"></i> Entry
                                </button>
                                <div id="new_journal_voucher" class="modal fade journal_voucher_entry_form" role="dialog">
                                    <?php $this->load->view('finance/journals/journal_voucher_entry_form'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped journal_transactions_table">
                                <thead>
                                <tr>
                                    <th style="width: 5%">Transaction Date</th><th style="width: 5%">Transaction No</th><th style="width: 13%">Transaction Type</th><th style="width: 8%">Reference</th><th>Amount</th><th style="width: 40%">Description</th><th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>

