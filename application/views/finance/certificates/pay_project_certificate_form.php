<?php

?>

<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Payment Project Certificate</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Payment Date</label>
                            <input type="text" class="form-control datepicker" required name="payment_date" value="" >
                            <input type="hidden" name="certificate_id" value="<?= $certificate->{$certificate::DB_TABLE_PK}?>">
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Amount</label>
                            <input type="text" certified_amount="<?= $certificate->certified_amount ?>" amount_paid="<?= $amount_paid ?>" class="form-control number_format" id="amount" name="amount" value="0" required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id', $credit_account_options, '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Debit Account</label>
                            <?= form_dropdown('debit_account_id', $debit_account_options, '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options, 1, ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control number_format" id="exchange_rate" readonly="readonly" name="exchange_rate" value="1" required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Reference</label>
                            <input type="text" class="form-control" id="reference" name="reference" value="" required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">With Holding Tax</label>
                            <input type="text" class="form-control number_format" id="with_holding_tax" name="with_holding_tax" value="0" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea class="form-control" name="comments" ></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_pay_project_certificate">Save</button>
            </div>
        </form>
    </div>
</div>