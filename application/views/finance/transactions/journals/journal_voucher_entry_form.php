<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/26/2019
 * Time: 10:09 AM
 */
$edit = isset($jv_transaction)
?>

<div class="modal-dialog" style="width: 90%">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Journal Entry</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-3">
                                <label for="transaction_date" class="control-label">Transaction Date</label>
                                <input type="text" class="form-control datepicker" name="transaction_date" value="<?= $edit ? $jv_transaction->transaction_date : '' ?>">
                                <input type="hidden" name="journal_voucher_id" value="<?= $edit ? $jv_transaction->{$jv_transaction::DB_TABLE_PK} : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="reference" class="control-label">Reference</label>
                                <input type="text" class="form-control" name="reference" value="<?= $edit ? $jv_transaction->reference : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="transaction_type" class="control-label">Transaction Type</label>
                                <?= form_dropdown('transaction_type', [
                                    '&nbsp;'=>'&nbsp;',
                                    'SALES'=>'SALES',
                                    'CASH PAYMENT'=>'CASH PAYMENT',
                                    'PURCHASE'=>'PURCHASE',
                                    'CASH RECEIPT'=>'CASH RECEIPT',
                                    'PURCHASE RETU'=>'PURCHASE RETURN',
                                    'SALES RETURN'=>'SALES RETURN'
                                ], $edit ? $jv_transaction->journal_type : '' , 'class="form-control searchable"') ?>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="currency_id" class="control-label">Currency</label>
                                <?= form_dropdown('currency_id', $currency_options , $edit ? $jv_transaction->currency_id : '' , 'class="form-control searchable"') ?>
                            </div>

                        </div>
                        <div class="form-group col-md-12 account_adder_container" >
                            <div class="form-group col-md-3">
                                <label for="account_id" class="control-label">Account</label>
                                <?= form_dropdown('account_id',$account_options,'','class="form-control searchable" style="border-color: red"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="account_operatiion" class="control-label">Operation</label>
                                <?= form_dropdown('account_operation',[
                                    '&nbsp;'=>'&nbsp;',
                                    'CREDIT'=>'CREDIT',
                                    'DEBIT'=>'DEBIT'
                                ],'','class="form-control searchable"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="number_format form-control" name="amount" value="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="narration" class="control-label">Narration</label>
                                <input type="text" class="form-control" name="narration" value="">
                            </div>
                            <div class="form-group col-xs-1">
                                <label class="control-label"></label>
                                <button type="button" style="width: 50%" class="btn btn-sm btn-default btn-block add_jv_account"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-xs-12 ">
                            <div class="col-md-6 credit_table_container">
                                <table  width="100%" class="table " id="credit_table" >
                                    <thead>
                                    <tr>
                                        <th colspan="4" style="text-align: center">Credit</th>
                                    </tr>
                                    <tr style="display: none" class="row_template">
                                        <td>
                                            <span style="padding-right: 5%"><button type="button" class="row_remover"><i class="fa fa-trash"></i></button></span>
                                        </td>
                                        <td>
                                            <span class="credit_account_display"></span>
                                            <input type="hidden" name="credit_account_id" value="">
                                            <input type="hidden" name="account_operation" value="credit">
                                        </td>
                                        <td>
                                            <span class="credit_amount_display pull-right"></span>
                                            <input type="hidden" name="amount" value="">
                                        </td>
                                        <td>
                                            <span class="credit_narration_display"></span>
                                            <input type="hidden" name="credit_narration" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 5%"></th>
                                        <th style="width: 35%">Account</th>
                                        <th>Amount</th>
                                        <th>Narration</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($edit){
                                        $transaction_type = "CREDIT";
                                        $total_crdit_amount = 0;
                                        $credit_transactions = $jv_transaction->jv_transactions($transaction_type);
                                        if(!empty($credit_transactions)) {
                                            foreach ($credit_transactions as $transaction) {
                                                $total_crdit_amount = $transaction->amount;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span style="padding-right: 5%"><button type="button"
                                                                                                class="row_remover"><i
                                                                        class="fa fa-trash"></i></button></span>
                                                    </td>
                                                    <td>
                                                        <span class="credit_account_display"><?= $transaction->account('name') ?></span>
                                                        <input type="hidden" name="credit_account_id"
                                                               value="<?= $transaction->account('id') ?>">
                                                        <input type="hidden" name="account_operation"
                                                               value="<?= $transaction_type ?>">
                                                    </td>
                                                    <td>
                                                        <span class="credit_amount_display pull-right"><?= number_format($transaction->amount, 2) ?></span>
                                                        <input type="hidden" name="amount"
                                                               value="<?= number_format($transaction->amount, 2) ?>">
                                                    </td>
                                                    <td>
                                                        <span class="credit_narration_display"><?= wordwrap($transaction->narration,32,'<br/>') ?></span>
                                                        <input type="hidden" name="credit_narration"
                                                               value="<?= $transaction->narration ?>">
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>

                                    <tfoot>
                                    <tr class="text_styles">
                                        <th colspan="2">TOTAL</th>
                                        <th class=" total_amount_display" style="text-align: right"><?= $edit ? number_format($total_crdit_amount,2) : '' ?></th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-6 debit_table_container">
                                <table  width="100%" class="table " id="debit_table">
                                    <thead>
                                    <tr>
                                        <th colspan="4" style="text-align: center">Debit</th>
                                    </tr>
                                    <tr style="display: none" class="row_template">
                                        <td>
                                            <span style="padding-right: 5%"><button type="button" class="row_remover"><i class="fa fa-trash"></i></button></span>
                                        </td>
                                        <td>
                                            <span class="debit_account_display"></span>
                                            <input type="hidden" name="debit_account_id" value="">
                                            <input type="hidden" name="account_operation" value="">
                                        </td>
                                        <td>
                                            <span class="debit_amount_display pull-right"></span>
                                            <input type="hidden" name="amount" value="">
                                        </td>
                                        <td>
                                            <span class="debit_narration_display"></span>
                                            <input type="hidden" name="debit_narration" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 5%"></th>
                                        <th style="width: 35%">Account</th>
                                        <th>Amount</th>
                                        <th>Narration</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($edit){
                                        $transaction_type = "DEBIT";
                                        $total_debit_amount = 0;
                                        $debit_transactions = $jv_transaction->jv_transactions($transaction_type);
                                        if($debit_transactions) {
                                            foreach ($debit_transactions as $transaction) {
                                                $total_debit_amount += $transaction->amount;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span style="padding-right: 5%"><button type="button"
                                                                                                class="row_remover"><i
                                                                        class="fa fa-trash"></i></button></span>
                                                    </td>
                                                    <td>
                                                        <span class="debit_account_display"><?= $transaction->account('name') ?></span>
                                                        <input type="hidden" name="debit_account_id"
                                                               value="<?= $transaction->account('id') ?>">
                                                        <input type="hidden" name="account_operation"
                                                               value="<?= $transaction_type ?>">
                                                    </td>
                                                    <td>
                                                        <span class="debit_amount_display pull-right"><?= number_format($transaction->amount, 2) ?></span>
                                                        <input type="hidden" name="amount"
                                                               value="<?= number_format($transaction->amount, 2) ?>">
                                                    </td>
                                                    <td>
                                                        <span class="debit_narration_display"><?= wordwrap($transaction->narration,32,'<br/>') ?></span>
                                                        <input type="hidden" name="debit_narration"
                                                               value="<?= $transaction->narration ?>">
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>

                                    <tfoot>
                                    <tr class="text_styles">
                                        <th colspan="2">TOTAL</th>
                                        <th class=" total_amount_display" style="text-align: right"><?= $edit ? number_format($total_debit_amount,2) : '' ?></th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea class="form-control" name="remarks"><?= $edit ? $jv_transaction->remarks : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_journal_entry"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>
