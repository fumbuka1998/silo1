<?php

$edit = isset($employee_designation);

//inspect_object($employee_contract);

?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? ' Edit Designation Information': 'Review Designation' ?></h4>
            </div>
            <div class="modal-body">


                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit ? $employee_designation->start_date : '' ?>">

                        </div>

                        <div class="form-group col-md-6">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="text" class="form-control datepicker" required name="end_date" value="<?= $edit ?$employee_designation->end_date : '' ?>">
                        </div>

                        <input type="hidden" name="employee_contract_id" value="<?= $employee_contract->{$employee_contract::DB_TABLE_PK} ?>">
                        <input type="hidden" name="employee_designation_id" value="<?= $edit ? $employee_designation->{$employee_designation::DB_TABLE_PK}: '' ?>">

                    </div>

                </div>

                <div class="row">
                    <div class="col-xs-12">

                        <div class="form-group col-md-4">
                            <label for="department_id" class="control-label">Departiment</label>
                            <?= form_dropdown('department_id',$department_options, $edit ? $employee_designation->department_id : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="job_position_id" class="control-label">Designation</label>
                            <?= form_dropdown('job_position_id',$job_position_options,$edit? $employee_designation->job_position_id:'','class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="branch_id" class="control-label">Work Station</label>
                            <?= form_dropdown('branch_id',$branch_options, $edit? $employee_designation->branch_id:'','class="form-control searchable"') ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_designation_button" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>