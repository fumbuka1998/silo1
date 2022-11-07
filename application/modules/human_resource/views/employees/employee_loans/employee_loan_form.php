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
                            <?= form_dropdown('loan_id',$loan_type_options, $edit ? $employee_loan_data->loan_id : '' , ' class="form-control searchable" ') ?>
                            <input type="hidden" class="form-control" name="employee_loan_id" value="<?= $edit ? $employee_loan_data->{$employee_loan_data::DB_TABLE_PK} : ''?>">
                            <input type="hidden" name="employee_id" value="<?= $edit ? $employee_id : $employee->{$employee::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="approved_date" class="control-label">Approved Date</label>
                            <input type="text" class="form-control datepicker" name="approved_date" value="<?= $edit ? $employee_loan_data->loan_approved_date : ''?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="deduction_start_date" class="control-label">Deduction Start Date</label>
                            <input type="text" class="form-control datepicker" name="deduction_start_date" value="<?= $edit ? $employee_loan_data->loan_deduction_start_date : ''?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="total_loan_amount" class="control-label">Total Loan Amount</label>
                            <input type="text" class="form-control number_format" name="total_loan_amount" value="<?= $edit ? number_format($employee_loan_data->total_loan_amount) : ''?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="monthly_deduction_rate" class="control-label">Monthly Deduction Rate</label><label name="monthly_deduction_error" style="color: red; display: none"> ✷</label>
                            <input type="text" class="form-control number_format" name="monthly_deduction_rate" value="<?= $edit ? number_format($employee_loan_data->monthly_deduction_amount) : ''?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="application_letter" class="control-label">Application Letter</label>
                            <input type="file" multiple class="form-control " name="application_letter" >
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $employee_loan_data->description : ''?></textarea>
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