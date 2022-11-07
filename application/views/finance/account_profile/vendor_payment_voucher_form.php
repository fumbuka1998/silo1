<?php
    $edit = isset($payment_voucher);
?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Vendor Payment Voucher Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="payment_date" class="control-label">Payment Date</label>
                        <input type="text" class="form-control datepicker" required name="payment_date" value="<?= $edit ? $payment_voucher->payment_date : '' ?>">
                        <input type="hidden" name="credit_account_id" value="<?= $account->{$account::DB_TABLE_PK} ?>">
                        <input type="hidden" name="payment_voucher_id" value="<?= $edit ? $payment_voucher->{$payment_voucher::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="payment_date" class="control-label">Vendor/Debit Account</label>
                        <?php
                            if($edit) {
                                $pv_items = $payment_voucher->payment_voucher_items();
                                $pv_item = array_shift($pv_items);
                                $debit_account_id = $pv_item->debit_account_id;
                            } else {
                                $debit_account_id = '';
                            }
                            echo form_dropdown('debit_account_id',$vendor_pv_debit_account_options,$debit_account_id,' class="form-control searchable"')
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="payee" class="control-label">Payee</label>
                        <input type="text" class="form-control" required name="payee" value="<?= $edit ? $payment_voucher->payee : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="amount" class="control-label">Amount</label>
                        <input type="text" class="form-control number_format" required name="amount" value="<?= $edit ? number_format($pv_item->amount) : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="reference" class="control-label">Reference</label>
                        <input type="text" class="form-control" name="reference" value="<?= $edit ? $payment_voucher->reference : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="amount" class="control-label">Remarks</label>
                        <textarea class="form-control" name="remarks"><?= $edit ? $payment_voucher->remarks : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_vendor_payment_voucher">Save</button>
        </div>
        </form>
    </div>
</div>