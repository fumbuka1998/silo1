<?php
$edit = isset($member);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Project Team Member</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="employee_id" class="control-label">Employee</label>
                            <?php if(!$edit){
                                echo form_dropdown('employee_id', [],'', " class = ' searchable form-control' required ");
                            } else {
                                $employee = $member->employee();
                                ?>
                                <select name="employee_id" class="form-control">
                                    <option value="<?= $member->employee_id ?>"><?= $employee->full_name() ?></option>
                                </select>
                                <?php
                            } ?><input type="hidden" name="member_id" value="<?= $edit ? $member->{$member::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="project_id" value="<?= $edit ? $member->project_id : $project->{$project::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="date_assigned" class="control-label">Position</label>
                            <?= form_dropdown('job_position_id',$job_position_options,$edit ? $member->job_position_id : '', ' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="date_assigned" class="control-label">Date Assigned</label>
                            <input type="text" class="form-control datepicker" required name="date_assigned" value="<?= $edit ? $member->date_assigned : date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="manager_access" class="control-label">Manager Access</label><br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="checkbox" name="manager_access" <?= $edit && $member->manager_access ? 'checked' : '' ?> value="1">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="salary" class="control-label">Remarks</label>
                            <textarea class="form-control" name="remarks"><?= $edit ? $member->remarks : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_project_team_member" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>