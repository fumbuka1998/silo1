<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/5/2018
 * Time: 2:31 PM
 */

$project_id = $project->{$project::DB_TABLE_PK};
$project_plan_id = $project_plan->{$project_plan::DB_TABLE_PK};
$casual_labour_options = $project_plan->casual_labour_type_options();
$rate_mode_options = [
    ''=>'&nbsp;',
    'daily'=>'Daily',
    'hourly'=>'Hourly',
    'monthly'=>'Monthly'
];

$edit = isset($plan_labour_budget);
if($edit){
    $task = $plan_labour_budget->project_plan_task()->task();
}
?>
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Labour Budget</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-6">
                                <label for="project_plan_task_id" class="control-label">Task Name</label>
                                <?= form_dropdown('project_plan_task_id',$edit ? [$plan_labour_budget->project_plan_task_id=>$task->task_name] : [], $edit ? $plan_labour_budget->project_plan_task_id : '', 'class="form-control project_plan_tasks_display"') ?>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="casual_labour_type_id" class="control-label">Labour Type</label>
                                <?= form_dropdown('casual_labour_type_id',$casual_labour_options, $edit ? $plan_labour_budget->casual_labour_type_id : '', 'class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rate_mode" class="control-label">Rate Mode</label>
                                <?= form_dropdown('rate_mode',$rate_mode_options,$edit ? $plan_labour_budget->rate_mode : '','class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="no_of_workers" class="control-label">No. Of Workers</label>
                                <input type="text" class="form-control" name="no_of_workers" value="<?= $edit ? $plan_labour_budget->no_of_workers : '' ?>">
                                <input type="hidden" name="plan_labour_budget_id" value="<?= $edit ? $plan_labour_budget->{$plan_labour_budget::DB_TABLE_PK} : '' ?>">
                                <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                <input type="hidden" name="project_plan_id" value="<?= $project_plan_id ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rate" class="control-label">Rate</label>
                                <input type="text" class="form-control number_format" name="rate" value="<?= $edit ? $plan_labour_budget->rate : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="duration" class="control-label">Duration</label>
                                <input type="text" class="form-control" name="duration" value="<?= $edit ? $plan_labour_budget->duration : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="form-control" name="amount" value="<?= $edit ? $plan_labour_budget->amount() : '&nbsp;' ?>" readonly>
                                <input type="hidden" name="form_type" value="casual_labour">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description"><?= $edit ? $plan_labour_budget->description : '' ?></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default submit_plan_labour_budget" >Submit</button>
            </div>
        </form>
    </div>
</div>