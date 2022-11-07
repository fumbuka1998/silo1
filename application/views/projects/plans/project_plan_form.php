<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 10:48 AM
 */

$edit = isset($project_plan);
?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $edit ? $project_plan->title : 'Project Plan' ?></h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <div class="form-group col-md-6">
                                <label for="title" class="control-label">Title</label>
                                <input type="text" class="form-control" required name="title" value="<?= $edit ? $project_plan->title : '' ?>">
                                <input type="hidden" name="project_id" value="<?= $edit ? $project_plan->project_id : $project->{$project::DB_TABLE_PK} ?>">
                                <input type="hidden" name="project_plan_id" value="<?= $edit ? $project_plan->{$project_plan::DB_TABLE_PK}: '' ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for=start_date" class="control-label">Start Date</label>
                                <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit ? $project_plan->start_date : date('Y-m-d') ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for=end_date" class="control-label">End Date</label>
                                <input type="text" class="form-control datepicker" required name="end_date" value="<?= $edit ? $project_plan->end_date : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm submit_project_plan">Save</button>
            </div>
        </form>
    </div>
</div>