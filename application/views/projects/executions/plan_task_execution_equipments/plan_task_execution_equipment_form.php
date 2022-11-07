<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/27/2018
 * Time: 11:16 AM
 */

$asset_group_options = asset_group_dropdown_options();
$project_id = $project->{$project::DB_TABLE_PK};
$project_plan_id = $project_plan->{$project_plan::DB_TABLE_PK};
$rate_mode_options = [
    ''=>'&nbsp;',
    'Daily'=>'Daily',
    'Hourly'=>'Hourly',
    'Monthly'=>'Monthly'
];

$edit = isset($plan_equipment_execution);
if($edit){
    $task = $plan_equipment_execution->project_plan_task_execution()->task();
}
?>
<div class="modal-dialog" style="width: 75%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Execution Equipment</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-6">
                                <label for="date" class="control-label">Date</label>
                                <input type="text" class="form-control datepicker" required name="date" value="<?= $edit ? $plan_equipment_execution->date : date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-8">
                                <label for="plan_task_execution_id" class="control-label">Task Name</label>
                                <?= form_dropdown('plan_task_execution_id',
                                    $edit ? [$plan_equipment_execution->plan_task_execution_id=>$plan_equipment_execution->project_plan_task_execution()->task()->task_name] : [],
                                    $edit ? $plan_equipment_execution->plan_task_execution_id : '',
                                    'class="form-control project_plan_tasks_display searchable"') ?>
                                <input type="hidden" name="task_type" value="executed">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="asset_id" class="control-label">Asset Name</label>
                                <?= form_dropdown('asset_id',$location_asset_options, $edit ? $plan_equipment_execution->asset_id : '', 'class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="rate_mode" class="control-label">Rate Mode</label>
                                <?= form_dropdown('rate_mode',$rate_mode_options,$edit ? $plan_equipment_execution->rate_mode : '','class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="quantity" class="control-label">Quantity</label>
                                <input type="text" class="form-control" name="quantity" value="<?= $edit ? $plan_equipment_execution->quantity : '' ?>">
                                <input type="hidden" name="plan_equipment_execution_id" value="<?= $edit ? $plan_equipment_execution->{$plan_equipment_execution::DB_TABLE_PK} : '' ?>">
                                <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                <input type="hidden" name="project_plan_id" value="<?= $project_plan_id ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rate" class="control-label">Rate</label>
                                <input type="text" class="form-control number_format" name="rate" value="<?= $edit ? $plan_equipment_execution->rate : '' ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="duration" class="control-label">Duration</label>
                                <input type="text" class="form-control" name="duration" value="<?= $edit ? $plan_equipment_execution->duration : '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="form-control number_format" name="amount" value="<?= $edit ? $plan_equipment_execution->amount() : '&nbsp;' ?>" readonly>
                                <input type="hidden" name="form_type" value="equipment">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description"><?= $edit ? $plan_equipment_execution->description : '' ?></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default submit_execution_equipment" >Submit</button>
            </div>
        </form>
    </div>
</div>
