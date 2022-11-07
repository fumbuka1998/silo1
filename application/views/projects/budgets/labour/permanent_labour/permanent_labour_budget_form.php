<?php
    $edit = isset($item);
?>


<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Permanent Labour Budget</h4>
        </div>
        <form> 
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-4">
                        <label for="rate_mode" class="control-label">Rate Mode</label>
                        <?php
                        $options = [
                            'hourly' => 'Hourly',
                            'daily' => 'Daily',
                            'monthly' => 'Monthly',
                        ];
                        echo form_dropdown('rate_mode',$options,$edit ? $item->rate_mode : '', ' required class="form-control"')
                        ?>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="job_position_id" class="control-label">Position</label>
                        <select name="job_position_id" class="form-control <?= $edit ? '' : ' searchable budget_job_position_selector' ?>">
                            <?php
                            if($edit){
                                ?>
                                <option value="<?= $item->job_position_id ?>" selected><?= $job_position->position_name ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>"/>
                        <input type="hidden" name="cost_center_id" value="<?= $edit ? isset($item->task_id) ? $item->task_id : '' : $task->{$task::DB_TABLE_PK} ?>"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="duration" class="control-label">Duration</label>
                        <input type="text" name="duration" class="form-control"  value="<?= $edit ? $item->duration : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="allowance_rate" class="control-label">Allowance Rate</label>
                        <input class="form-control number_format" name="allowance_rate" value="<?= $edit ? $item->allowance_rate : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="salary_rate" class="control-label">Salary Rate</label>
                        <input class="form-control number_format" name="salary_rate" value="<?= $edit ? $item->salary_rate : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="no_of_staff" class="control-label">No of Staff</label>
                        <input type="text" name="no_of_staff" class="form-control"  value="<?= $edit ? $item->no_of_staff : '' ?>">
                    </div>
                    <?php
                        if($edit) {
                            $allowance_amount = $item->allowance_rate*$item->no_of_staff*$item->duration;
                            $salary_amount = $item->salary_rate*$item->no_of_staff*$item->duration;
                        }
                    ?>
                    <div class="form-group col-md-4">
                        <label for="amount" class="control-label">Allowance Amount</label>
                        <input class="form-control number_format" readonly name="allowance_amount"  value="<?= $edit ? $allowance_amount : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="amount" class="control-label">Salary Amount</label>
                        <input class="form-control number_format" readonly name="salary_amount"  value="<?= $edit ? $salary_amount : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="amount" class="control-label">Total Amount</label>
                        <input class="form-control number_format" readonly name="total_amount"  value="<?= $edit ? $allowance_amount+$salary_amount : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $item->description : ''  ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_permanent_labour_budget_item">Save</button>
        </div>
        </form>
    </div>
</div>