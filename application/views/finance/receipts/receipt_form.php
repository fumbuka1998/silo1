<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 1/29/2019
 * Time: 3:53 AM
 */

$outgoing_invoice_options = outgoing_invoices_dropdown_options();
$edit = isset($receipt);
?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Receipt</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Receipt Date</label>
                            <input type="text" class="form-control datepicker" required name="receipt_date" value="<?= $edit ? $receipt->receipt_date : '' ?>" >
                            <input type="hidden" name="receipt_id" value="<?= $edit ? $receipt->{$receipt::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="invoice_id" class="control-label">Invoice</label>
                            <?= form_dropdown('invoice_id',$outgoing_invoice_options, $edit ? $receipt->invoice_id : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id',$edit ? [$receipt->credit_account_id => $receipt->credit_account()->account_name] : [], '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="currency_id" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options, $edit ? $receipt->currency_id : '' , ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control" name="exchange_rate" value="<?= $edit ? number_format($receipt->exchange_rate,2) : '' ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount</label>
                            <input type="text" class="form-control number_format"  name="amount" value="<?= $edit ? number_format($receipt->item()->total_amount(),2) : '' ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="withholding_tax" class="control-label">Withholding Tax(%)</label>
                            <input type="text" class="form-control"  name="withholding_tax" value="<?= $edit ? $receipt->withholding_tax : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount_withheld" class="control-label">Amount Withheld</label>
                            <input type="text" class="form-control"  name="amount_withheld" value="<?= $edit ? number_format($receipt->item()->withholding_tax_amount(),2) : '' ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount_retained" class="control-label">Amount Payable</label>
                            <input type="text" class="form-control"  name="amount_payable" value="<?= $edit ? number_format($receipt->item()->amount,2) : '' ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Debit Account</label>
                            <?= form_dropdown('debit_account_id', $debit_account_options, $edit ?  $receipt->debit_account_id : '' , ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="<?= $edit ? $receipt->reference : '' ?>" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea class="form-control" name="comments" ><?= $edit ? $receipt->remarks : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_receipt">Save</button>
            </div>
        </form>
    </div>
</div>