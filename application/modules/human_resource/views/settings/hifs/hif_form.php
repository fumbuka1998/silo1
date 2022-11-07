<?php
$edit = isset($hif);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $edit ? '': 'New HIF' ?></h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="form-group col-xs-12">

                        <div class="form-group">
                            <label for="hif_name" class="control-label col-xs-4">HIF Name</label>
                            <input type="text" name="hif_name" class="col-xs-8 margin-bottom" value="<?= $edit ? $hif->hif_name : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="employer_deduction_percent" class="control-label col-xs-4">Employer Deduction %</label>
                            <input type="text" name="employer_deduction_percent" class="col-xs-8 margin-bottom" value="<?= $edit ? $hif->employer_deduction_percent : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="employee_deduction_percent" class="control-label col-xs-4">Employee Ededuction %</label>
                            <input type="text" name="employee_deduction_percent" class="col-xs-8" value="<?= $edit ? $hif->employee_deduction_percent : ''?>">
                        </div>

                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button hif_id="<?= $edit ? $hif->id : '' ?>" type="button" class="btn btn-sm btn-default save_hif_button">Save</button>
            </div>
        </form>
    </div>
</div>