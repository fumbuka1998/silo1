<?php
$edit = isset($ssf);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $edit ? '': 'New SSF' ?></h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                        <div class="form-group col-xs-12">

                            <div class="form-group">
                                <label for="ssf_name" class="col-xs-4">SSF Name</label>
                                <input type="text" name="ssf_name" class="col-xs-8 margin-bottom" value="<?= $edit ? $ssf->ssf_name : ''?>">
                            </div>

                            <div class="form-group">
                                <label for="employer_deduction_percent" class="control-label col-xs-4">Employer Deduction %</label>
                                <input type="text" name="employer_deduction_percent" class="col-xs-8 margin-bottom" value="<?= $edit ? $ssf->employer_deduction_percent : '' ?>">
                            </div>

                            <div class="form-group">
                                <label for="employee_deduction_percent" class="control-label col-xs-4">Employee Deduction %</label>
                                <input type="text" name="employee_deduction_percent" class="col-xs-8" value="<?= $edit ? $ssf->employee_deduction_percent : '' ?>">
                            </div>

                        </div>

                </div>
            </div>

            <div class="modal-footer">
                <button ssf_id="<?= $edit ? $ssf->id : '' ?>" type="button" class="btn btn-sm btn-default save_ssf_button">Save</button>
            </div>
        </form>
    </div>
</div>