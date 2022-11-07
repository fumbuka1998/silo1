<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/14/2016
 * Time: 12:52 PM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">New Payment Voucher</button>
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a data-toggle="modal" data-target="#new_expense_payment_voucher" href="#">
                                Expense PV
                            </a>
                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#new_vendor_payment_voucher" href="#">
                                Vendor PV
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="new_expense_payment_voucher" class="modal fade expense_pv_form" role="dialog">
                    <?php $this->load->view('finance/account_profile/expense_payment_voucher_form'); ?>
                </div>
                <div id="new_vendor_payment_voucher" class="modal fade" role="dialog">
                    <?php $this->load->view('finance/account_profile/vendor_payment_voucher_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table id="account_payment_vouchers_list" account_id="<?= $account->{$account::DB_TABLE_PK} ?>" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>PV Date</th><th>PV No.</th><th>Reference</th><th>Action</th><th>Supplementary Account(s)</th><th>Amount</th><th>Remarks</th><th>Datetime Posted</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
