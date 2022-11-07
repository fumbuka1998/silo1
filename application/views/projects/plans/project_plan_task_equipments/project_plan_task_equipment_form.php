<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/3/2018
 * Time: 12:17 PM
 */

$project_id = $project->{$project::DB_TABLE_PK};
$project_plan_id = $project_plan->{$project_plan::DB_TABLE_PK};
$rate_mode_options = [
        ''=>'&nbsp;',
        'daily'=>'Daily',
        'hourly'=>'Hourly',
        'monthly'=>'Monthly'
];

$edit = isset($plan_equipment_budget);
if($edit){
    $task = $plan_equipment_budget->project_plan_task()->task();
}
?>
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Equipment Budget</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-6">
                                <label for="project_plan_task_id" class="control-label">Task Name</label>
                                <?= form_dropdown('project_plan_task_id',$edit ? [$plan_equipment_budget->project_plan_task_id=>$task->task_name] : '', $edit ? $plan_equipment_budget->project_plan_task_id : '', 'class="form-control project_plan_tasks_display"') ?>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="asset_group_id" class="control-label">Asset Name</label>
                                <?= form_dropdown('asset_id',$location_asset_options, $edit ? $plan_equipment_budget->asset_id : '', 'class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rate_mode" class="control-label">Rate Mode</label>
                                <?= form_dropdown('rate_mode',$rate_mode_options,$edit ? $plan_equipment_budget->rate_mode : '','class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="quantity" class="control-label">Quantity</label>
                                <input type="text" class="form-control" name="quantity" value="<?= $edit ? $plan_equipment_budget->quantity : '' ?>">
                                <input type="hidden" name="plan_equipment_budget_id" value="<?= $edit ? $plan_equipment_budget->{$plan_equipment_budget::DB_TABLE_PK} : '' ?>"><input type="hidden" name="project_id" value="<?= $project_id ?>">
                                <input type="hidden" name="project_plan_id" value="<?= $project_plan_id ?>">
                                <input type="hidden" name="project_id" value="<?= $project_id ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rate" class="control-label">Rate</label>
                                <input type="text" class="form-control number_format" name="rate" value="<?= $edit ? $plan_equipment_budget->rate : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="duration" class="control-label">Duration</label>
                                <input type="text" class="form-control" name="duration" value="<?= $edit ? $plan_equipment_budget->duration : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="form-control number_format" name="amount" value="<?= $edit ? $plan_equipment_budget->amount() : '&nbsp;' ?>" readonly>
                                <input type="hidden" name="form_type" value="equipment">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description"><?= $edit ? $plan_equipment_budget->description : '' ?></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default submit_plan_equipment_budget" >Submit</button>
            </div>
        </form>
    </div>
</div>