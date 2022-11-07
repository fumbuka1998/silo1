<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/3/2018
 * Time: 11:49 AM
 */

$edit = isset($project_plan_task);
?>
<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close specific_modal_hide" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $edit ? $project_plan_task->task()->task_name : $project_plan->title.'&nbsp;Task' ?></h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-6">
                                <label for="task_id" class="control-label">Task Name</label>
                                <?= form_dropdown('task_id',$plan_cost_center_options, $edit ? $project_plan_task->task_id : '', 'class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="quantity">Quantity</label>
                                <div class="input-group">
                                    <span class="input-group-addon unit_display"><?= $edit ? $unit_symbol : '&nbsp;' ?></span>
                                    <input type="text" width="20" class="form-control " name="quantity" value="<?= $edit ? $project_plan_task->quantity : '' ?>" previous_quantity="0">
                                </div>
                                <input type="hidden" name="project_plan_id" value="<?= $edit ? $project_plan_task->project_plan_id : $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                                <input type="hidden" name="project_plan_task_id" value="<?=$edit ? $project_plan_task->{$project_plan_task::DB_TABLE_PK} : '' ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="daily_output" class="control-label">Output Per Day</label>
                                <input type="text" class="form-control" name="output_per_day" value="<?= $edit ? $project_plan_task->output_per_day : '' ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="duration" class="control-label">Duration</label>
                                <input type="text" class="form-control display_duration" value="<?= $edit ? $project_plan_task->duration() : '' ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default submit_project_plan_task" >Submit</button>
            </div>
        </form>
    </div>
</div>