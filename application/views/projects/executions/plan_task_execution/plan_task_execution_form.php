<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/26/2018
 * Time: 10:42 AM
 */

$edit = isset($plan_task_execution)
?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $project_plan->title ?> Task Execution</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-8">
                                <label for="execution_date" class="control-label">Execution Date</label>
                                <input type="text" class="form-control datepicker" name="execution_date" value="<?= $edit ? $plan_task_execution->execution_date : date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-8">
                                <label for="project_plan_task_id" class="control-label">Task Name</label>
                                <?= form_dropdown('task_id', $edit ? [$plan_task_execution->task_id=>$task->task_name] : $plan_cost_center_options, $edit ? $plan_task_execution->task_id : '', 'class="form-control project_plan_tasks_display"') ?>
                                <input type="hidden" name="project_plan_id" value="<?= $project_plan->{$project_plan::DB_TABLE_PK }?>">
                                <input type="hidden" name="plan_task_execution_id" value="">
                            </div>
                            <div class="form-group col-md-2">
                                <label for=quantity" class="control-label">Quantity</label>
                                <input type="text" class="form-control " name="remain_quantity" value="" readonly>
                            </div>
                            <div class="form-group col-md-2">
                                <label for=quantity" class="control-label">Executed Qty</label>
                                <input type="text" class="form-control " name="quantity" value="<?= $edit ? $plan_task_execution->executed_quantity : '' ?>" previous_quantity="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_plan_task_execution">Submit</button>
            </div>
        </form>
    </div>
</div>
