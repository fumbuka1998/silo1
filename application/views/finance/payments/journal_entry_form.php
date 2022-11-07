<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/26/2019
 * Time: 10:09 AM
 */

$debit_account_options = $invoice->vendor()->accounts_dropdown_options();
$payment_request_approval_id = $payment_request_approval->{$payment_request_approval::DB_TABLE_PK};
?>

<div class="modal-dialog" style="width: 70%">
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
                            <div class="form-group col-md-4">
                                <label for="transaction_date" class="control-label">Transaction Date</label>
                                <input type="text" class="form-control datepicker" name="transaction_date" value="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="reference" class="control-label">Reference</label>
                                <input type="text" readonly class="form-control" name="reference" value="<?= $request_number ?>">
                                <input type="hidden" name="transaction_type" value="JOURNAL">
                                <input type="hidden" name="purchase_order_payment_request_approval_id" value="<?= $payment_request_approval_id ?>">
                                <input type="hidden" name="invoice_id" value="<?= $invoice->{$invoice::DB_TABLE_PK}?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="currency_id" class="control-label">Currency</label>
                                <?= form_dropdown('currency_id', $currency_options , 1 , 'class="form-control searchable"') ?>
                            </div>

                        </div>
                        <div class="form-group col-md-12 account_adder_container" >
                            <div class="form-group col-md-3">
                                <label for="account_id" class="control-label">Account</label>
                                <?= form_dropdown('account_id', $debit_account_options,  '','class="form-control searchable" style="border-color: red"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="account_operatiion" class="control-label">Operation</label>
                                <?= form_dropdown('account_operation',[
                                    'DEBIT'=>'DEBIT'
                                ],'','class="form-control searchable"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="number_format form-control" name="amount" value="<?= $ammount_to_be_paid ?>">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="narration" class="control-label">Narration</label>
                                <input type="text" class="form-control" name="narration" value="">
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea class="form-control" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_journal_entry2"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>
