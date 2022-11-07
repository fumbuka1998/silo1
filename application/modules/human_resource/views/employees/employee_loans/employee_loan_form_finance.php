<?php
$edit = isset($employee_loan_data);

/*
     ssf_id
     ssf_no
     start_date
     end_date
     created_at
     created_by
     */
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Loan Informations</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="loan_id" class="control-label">Loan</label>
                            <?= form_dropdown('loan_id',$loan_type_options, '' , ' class="form-control searchable" ') ?>
                            <input type="hidden" name="employee_id" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="loan_id" class="control-label">Dr Account</label>
                            <?= form_dropdown('dr_account',$contract_employee_dropdown_options, '' , ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="loan_id" class="control-label">Cr Account</label>
                            <?= form_dropdown('cr_account',$account_dropdown_options, '' , ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="approved_date" class="control-label">Approved Date</label>
                            <input type="text" class="form-control datepicker" name="approved_date" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="deduction_start_date" class="control-label">Deduction Start Date</label>
                            <input type="text" class="form-control datepicker" name="deduction_start_date" value="">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="reference" class="control-label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="<?=''?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="total_loan_amount" class="control-label">Total Loan Amount</label>
                            <input type="text" class="form-control number_format" name="total_loan_amount" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="monthly_deduction_rate" class="control-label">Monthly Deduction Rate</label><label name="monthly_deduction_error" style="color: red; display: none"> ✷</label>
                            <input type="text" class="form-control number_format" name="monthly_deduction_rate" value="">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="application_letter" class="control-label">Application Letter</label>
                            <input type="file" multiple class="form-control " name="application_letter" >
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Remarks</label>
                            <textarea name="description" class="form-control" placeholder="Required"></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_employee_loan" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>