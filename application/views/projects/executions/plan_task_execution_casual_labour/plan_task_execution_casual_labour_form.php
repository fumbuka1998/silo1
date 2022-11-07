<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/27/2018
 * Time: 2:01 PM
 */

$project_id = $project->{$project::DB_TABLE_PK};
$project_plan_id = $project_plan->{$project_plan::DB_TABLE_PK};
$casual_labour_options = $project_plan->casual_labour_type_options();
$rate_mode_options = [
    ''=>'&nbsp;',
    'Daily'=>'Daily',
    'Hourly'=>'Hourly',
    'Monthly'=>'Monthly'
];

$edit = isset($plan_execution_labour);
if($edit){
    $task = $plan_execution_labour->project_plan_task_execution()->task();
}
?>
<div class="modal-dialog" style="width: 75%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Execution Casual Labour</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="date" class="control-label">Date</label>
                            <input type="text" class="form-control datepicker" required name="date" value="<?= $edit ? $plan_execution_labour->date : date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-8">
                                <label for="project_plan_task_id" class="control-label">Task Name</label>
                                <?= form_dropdown('plan_task_execution_id',
                                    $edit ? [$plan_execution_labour->plan_task_execution_id=>$plan_execution_labour->project_plan_task_execution()->task()->task_name] : [],
                                    $edit ? $plan_execution_labour->plan_task_execution_id : '',
                                    'class="form-control project_plan_tasks_display searchable"') ?>
                                <input type="hidden" name="task_type" value="executed">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="casual_labour_type_id" class="control-label">Labour Type</label>
                                <?= form_dropdown('casual_labour_type_id',$casual_labour_options, $edit ? $plan_execution_labour->casual_labour_type_id : '', 'class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="rate_mode" class="control-label">Rate Mode</label>
                                <?= form_dropdown('rate_mode',$rate_mode_options,$edit ? $plan_execution_labour->rate_mode : '','class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="no_of_workers" class="control-label">No. Of Workers</label>
                                <input type="text" class="form-control" name="no_of_workers" value="<?= $edit ? $plan_execution_labour->no_of_workers : '' ?>">
                                <input type="hidden" name="plan_labour_execution_id" value="<?= $edit ? $plan_execution_labour->{$plan_execution_labour::DB_TABLE_PK} : '' ?>">
                                <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                <input type="hidden" name="project_plan_id" value="<?= $project_plan_id ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rate" class="control-label">Rate</label>
                                <input type="text" class="form-control number_format" name="rate" value="<?= $edit ? $plan_execution_labour->rate : '' ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="duration" class="control-label">Duration</label>
                                <input type="text" class="form-control" name="duration" value="<?= $edit ? $plan_execution_labour->duration : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="form-control" name="amount" value="<?= $edit ? $plan_execution_labour->amount() : '&nbsp;' ?>" readonly>
                                <input type="hidden" name="form_type" value="casual_labour">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description"><?= $edit ? $plan_execution_labour->description : '' ?></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default submit_plan_labour_execution" >Submit</button>
            </div>
        </form>
    </div>
</div>