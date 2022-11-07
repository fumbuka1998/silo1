<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/3/2018
 * Time: 11:59 AM
 */

$material_options = material_item_dropdown_options();
$task_options = $project_plan->project_plan_tasks($project_plan->{$project_plan::DB_TABLE_PK},null);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">New <?= $project_plan->title ?> Material Budget</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-4">
                                <label for="project_plan_task_id" class="control-label">Task Name</label>
                                <?= form_dropdown('project_plan_task_id', [], '', 'class="form-control project_plan_tasks_display"') ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="material_item_id" class="control-label">Item Name</label>
                                <?= form_dropdown('material_item_id', $material_options, '', 'class="form-control"') ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label for=quantity" class="control-label">Quantity</label>
                                <input type="hidden" name="project_plan_id" value="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                                <input type="text" class="form-control " name="quantity" value="">
                            </div>
                            <div class="form-group col-md-4">
                                <label for=rate" class="control-label">Rate</label>
                                <input type="text" class="form-control" name="rate" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btn-sm submit_project_plan_material">Submit</button>
            </div>
        </form>
    </div>
</div>