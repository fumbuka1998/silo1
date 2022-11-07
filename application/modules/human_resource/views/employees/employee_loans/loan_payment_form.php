<?php

?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Loan Payment Form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
                            <label for="paid_date" class="control-label">Paid Date</label>
                            <input type="text" class="form-control datepicker" required name="paid_date" value="">
                            <input type="hidden" name="cr_account" value="<?= $employee_loan_data->loan_account_id ?>">
                            <input type="hidden" name="employee_loan_id" value="<?= $employee_loan_data->id ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dr_account" class="control-label">Dr Account</label>
                            <?= form_dropdown('dr_account', $account_dropdown_options, '', ' class=" form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="loan_balance" class="control-label">Loan Balance</label>
                            <input style="text-align: right; font-weight: bold" readonly type="text" class="form-control "  name="loan_balance" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="paid_amount" class="control-label">Paid Amount</label><label name="paid_ammout_error" style="color: red; display: none"> ✷</label>
                            <input type="text" class="form-control number_format" required name="paid_amount" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="attachments" class="control-label">Attachments</label>
                            <input type="file" multiple name="attachments" class="form-control">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_employee_loan_payment" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>