<?php
    $edit = isset($task);
    $modal_title = $edit ? '' : 'New Task';
    $measurement_unit_options = measurement_unit_dropdown_options();
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $modal_title ?></h4>
        </div>
        <form>

            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <label for="task_name" class="control-label">Task Name</label>
                            <input type="text" class="form-control" required name="task_name" value="<?= $edit ? $task->task_name : '' ?>">
                            <input type="hidden" name="task_id" value="<?= $edit ? $task->{$task::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="activity_id" value="<?= $activity->{$activity::DB_TABLE_PK} ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" name="start_date" value="<?= $edit ? $task->start_date : '' ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="text" class="form-control datepicker" name="end_date" value="<?= $edit ? $task->end_date : '' ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="measurement_unit_id" class="control-label">Unit</label>
                            <?= form_dropdown('measurement_unit_id',$measurement_unit_options,$edit ? $task->measurement_unit_id : '','class="form-control searchable"') ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="quantity" class="control-label">Quantity</label>
                            <input type="text" class="form-control " name="quantity" value="<?= $edit ? $task->quantity : '' ?>">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="rate" class="control-label">Rate</label>
                            <input type="text" class="form-control number_format" name="rate" value="<?= $edit ? $task->rate : '' ?>">
                        </div>

                        <div class="form-group col-xs-12">
                            <label for="end_date" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $task->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-sm btn-default save_task" type="button">Save</button>
            </div>
        </form>

</div>
</div>