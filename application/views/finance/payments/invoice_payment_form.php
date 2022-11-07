<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/16/2018
 * Time: 6:42 PM
 */
$edit = isset($payment);
if($edit){
    $items = $payment->payment_voucher_items();
   $payment_item = array_shift($items);
   $credit_account = $payment->credit_account();
   $debit_account = $payment_item->debit_account();
   $invoice = $payment->invoice();
}
?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Invoice Payment</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="payment_date" class="control-label">Payment Date</label>
                            <input type="text" class="form-control datepicker" required name="payment_date" value="<?= $edit ? $payment->payment_date : '' ?>" >
                            <input name="payment_voucher_id" type="hidden" value="<?= $edit ? $payment->{$payment::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount</label>
                            <input type="text" class="form-control number_format" name="amount" value="<?= $edit ? $payment->amount() : '' ?>" required>
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="invoice" class="control-label">Invoice</label>
                            <?= form_dropdown('invoice_id',$edit ? [$invoice->{$invoice::DB_TABLE_PK} => $invoice->reference] : $invoice_dropdown_options, '', ' class="form-control searchable"  id="vendor_invoice_id" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="credit_account" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id', $edit ? [$credit_account->{$credit_account::DB_TABLE_PK} => $credit_account->account_name] : $credit_account_options, $edit ? $payment->credit_account_id : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="debit_account" class="control-label">Debit Account</label>
                            <?= form_dropdown('debit_account_id',$edit ? [$debit_account->{$debit_account::DB_TABLE_PK} => $debit_account->account_name] :[], '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="payee" class="control-label">Payee</label>
                            <input type="text" class="form-control" name="payee" value="<?= $edit ? $payment->payee : '' ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="currency" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id',$currency_options, $edit ? $payment->currency_id : '', ' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control" name="exchange_rate" value="<?= $edit ? $payment->exchange_rate : 1 ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="reference" class="control-label">Reference</label>
                            <input type="text" class="form-control" id="reference" name="reference" value="<?= $edit ? $payment->reference : '' ?>" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea class="form-control" name="remarks" ><?= $edit ? $payment->remarks : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_invoice_payment">Submit</button>
            </div>
        </form>
    </div>
</div>
