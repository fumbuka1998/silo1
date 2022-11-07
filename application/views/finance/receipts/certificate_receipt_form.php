<?php $edit = isset($receipt);
    if($edit){
        $certificate_receipt = $receipt->certificate_junction();
        $certificate = $certificate_receipt->certificate();
        $certificate_options = [$certificate->{$certificate::DB_TABLE_PK} => $certificate->certificate_number];
        $items = $receipt->items();
        $credit_account = $receipt->credit_account();
    }
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Certificate Receipt</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Receipt Date</label>
                            <input type="text" class="form-control datepicker" required name="receipt_date" value="<?= $edit ? $receipt->receipt_date : '' ?>" >
                            <input type="hidden" name="receipt_id" value="<?= $edit ? $receipt->{$receipt::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Amount</label>
                            <input type="text" class="form-control number_format" name="amount" value="<?= $edit ? $receipt->amount() : '' ?>" required>
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="cost_center_id" class="control-label">Certificates</label>
                            <?= form_dropdown('certificate_id',  $edit ? [$certificate->project()->project_name => [
                                            $certificate->{$certificate::DB_TABLE_PK} => $certificate->certificate_number
                                         ]
                                     ] : $certificate_options, $edit ? $certificate->{$certificate::DB_TABLE_PK} : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id', $edit ? [$receipt->credit_account_id => $credit_account->account_name] : [], '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Debit Account</label>
                            <?= form_dropdown('debit_account_id', $debit_account_options, $edit ? $receipt->debit_account_id : '', ' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options, $edit ? $receipt->currency_id : '', ' class="form-dropdown searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Exchange Rate</label>
                            <input type="text" <?= !$edit || $receipt->currency_id ? 'readonly' : '' ?> class="form-control number_format" id="exchange_rate" name="exchange_rate" value="<?= $edit ? $receipt->exchange_rate : 1 ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Reference</label>
                            <input type="text" class="form-control" id="reference" name="reference" value="<?= $edit ? $receipt->reference : '' ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">With Holding Tax</label>
                            <input type="text" class="form-control number_format" id="holding_tax" name="holding_tax" value="<?= $edit ? $certificate_receipt->with_holding_tax : '' ?>" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea class="form-control" name="comments" ><?= $edit ? $receipt->remarks : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_certificate_receipt">Save</button>
            </div>
        </form>
    </div>
</div>