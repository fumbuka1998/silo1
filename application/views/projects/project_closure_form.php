<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/2/2018
 * Time: 2:13 AM
 */
?>

<div style="width: 30%;" class="modal-dialog modal-sm">
    <div class="modal-content">
        <form method="post" action="<?= base_url('projects/close_project') ?>">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Project Closure</h4>
            </div>
            <div class="modal-body" style="overflow:auto;">
                <div class="form-group col-md-12">
                    <label for="project_name" class="control-label">Closure Date</label>
                    <input type="text" class="form-control datepicker" required name="closure_date" value="<?= date('Y-m-d') ?>">
                    <input type="hidden" name="project_id" value="<?=$project->{$project::DB_TABLE_PK}?>">
                </div>

                <div class="form-group col-xs-12">
                    <label for="remarks" class="control-label">Closure Remarks</label>
                    <textarea class="form-control" name="remarks"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>
