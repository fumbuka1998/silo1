<?php
    $edit = isset($progress_update);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close specific_modal_hide" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Progress Update</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="datetime" class="control-label">Date and Time</label>
                        <input type="text" class="form-control datetime_picker" required name="datetime" value="<?= $edit ? $progress_update->datetime_updated : datetime() ?>">
                        <input type="hidden" name="task_id" value="<?= $task_id ?>">
                        <input type="hidden" name="progress_update_id" value="<?= $edit ? $progress_update->{$progress_update::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="datetime" class="control-label">Percentage</label>
                        <input type="text" class="form-control" required name="percentage" value="<?= $edit ? $progress_update->percentage : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description"><?= $edit ? $progress_update->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_task_progress_update">Update</button>
        </div>
        </form>
    </div>
</div>