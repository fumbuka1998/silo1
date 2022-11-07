<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:41 PM
 */
$month_string = explode('-',date('Y-m-d'))[1] - 1;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
?>

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
                                    <?php $this->load->view('finance/transactions/journals/journal_voucher_entry_form'); ?>
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
                                    <th style="width: 5%">Transaction Date</th><th style="width: 5%">Transaction No</th><th style="width: 13%">Transaction Type</th><th style="width: 8%">Reference</th><th>Amount</th><th style="width: 40%">Description</th><th style="width: 20%"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
