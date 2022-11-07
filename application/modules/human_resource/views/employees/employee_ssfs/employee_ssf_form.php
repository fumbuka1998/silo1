<?php
$edit = isset($employee_ssf);

/*
     ssf_id
     ssf_no
     start_date
     end_date
     created_at
     created_by
     */
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">SSF Information</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <input type="hidden" name="employee_id" value="<?= $edit ? $employee_ssf->employee_id : $employee->{$employee::DB_TABLE_PK} ?>">
                            <input type="hidden" name="employee_ssf_id" value="<?= isset($employee_ssf) ? $employee_ssf->{$employee_ssf::DB_TABLE_PK} : '' ?>">

                           <label for="ssf_id" class="control-label">SSF Name</label>
                            <?= form_dropdown('ssf_id',$ssf_options, $edit ? $employee_ssf->ssf_id : '', ' class="form-control searchable" ') ?>

                        </div>
                        <div class="form-group col-md-6">
                            <label for="ssf_no" class="control-label">SSF Number</label>
                            <input type="text" class="form-control" required name="ssf_no" value="<?= $edit ? $employee_ssf->ssf_no : '' ?>">

                        </div>
                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">Deduction Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit ? $employee_ssf->start_date : '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_employee_ssf_button" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>