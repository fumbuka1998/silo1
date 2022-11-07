<?php
$edit = isset($transfer_data);
?>

<div class="modal-dialog">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New  Asset Transfer</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="transfer_date" class="control-label">Transfer Date</label>
                            <input type="text" class="form-control datepicker" required name="transfer_date"  value="<?= $edit ? $transfer_data->transfer_date : '' ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <input type="hidden" name="transfer_id" value="<?= $edit ? $transfer_data->{$transfer_data::DB_TABLE_PK} : '' ?>">
                            <label for="asset_id" class="control-label">Asset Name</label>
                            <?= form_dropdown('asset_id',$asset_item_options, $edit ? $transfer_data->asset_id : '', ' class="form-control searchable" ') ?>

                        </div>

                        <div class="form-group col-md-6">
                            <label for="department_id" class="control-label">Department</label>
                            <?= form_dropdown('department_id', $department_options, $edit ? $transfer_data->department_id : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sub_location_id" class="control-label">Location</label>
                            <?= form_dropdown('sub_location_id', $sub_location_options, $edit ? $transfer_data->sub_location_id : '', ' class="form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="employee_id" class="control-label">Under</label>
                            <?= form_dropdown('employee_id', $employee_options, $edit ? $transfer_data->employee_id : '', ' class="form-control searchable" ') ?>

                        </div>
                        <div class="form-group col-md-6">
                            <label for="description" class="control-label">Comment</label><br>
                             <textarea rows="2" name="description"  class="form-control"><?= $edit ? $transfer_data->description : '' ?></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm  save_transfer">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>