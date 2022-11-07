<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 12/05/2018
 * Time: 09:28
 */

$month_string = explode('-',date('Y-m-d'))[1] - 1 > 0 ? explode('-',date('Y-m-d'))[1] - 1 : 12;
$previous_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
$account = array_shift($project_accounts);

?>
    <div class="row" id="account_statement_main_container">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-12">
                        <form method="post" target="_blank" action="<?= base_url('finance/statement_transaction') ?>">
                            <div class=" box-tools">
                                <div class="form-group col-md-3">
                                    <label class="col-md-3 control-label" for="to">From</label>
                                    <div class="col-md-9">
                                        <input class="form-control datepicker" name="from" value="<?= $previous_month ?>">
                                        <input type="hidden" name="currency_id" value="<?= $account->currency_id ?>">
                                        <input type="hidden" name="account_type_and_id" value="<?= 'CASH_real_'.$account->{$account::DB_TABLE_PK} ?>">
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="col-md-3" for="to">To</label>
                                    <div class="col-md-9">
                                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <button type="button" id="generate_account_statement" class="btn btn-default btn-xs"><i class="fa fa-download"></i>Generate</button>
                                    <button name="print_pdf" type="submit" value="true"  class="btn btn-default btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                    <button name="export_excel" type="submit" value="true"  class="btn btn-default btn-xs"><i class="fa fa-file-excel-o"></i>Export Excel</button>
                                </div>
                                <div class="form-group col-md-3">


                                    <div class="btn-group btn-group-xs pull-right">
                                        <button type="button" class="btn btn-default btn-sm">
                                            <i class="fa fa-plus-circle"></i>
                                            Voucher
                                        </button>
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#" data-toggle="modal" data-target="#new_project_payment">Payment</a></li>
                                            <li><a href="#">Transfer</a></li>
                                        </ul>
                                    </div>

                                    <div>
                                        <div class="modal fade" role="dialog" id="new_project_payment">
                                            <?php $this->load->view('finance/payments/expense_payment_form') ?>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="account_statement_container" class="col-xs-12 table-responsive">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



