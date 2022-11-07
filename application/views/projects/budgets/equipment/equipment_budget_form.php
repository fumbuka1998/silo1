<?php
//<?= $project->{$project::DB_TABLE_PK}
    $edit = isset($Equipment_budget);
    //inspect_object($Equipment_budget);

?>
<div class="modal-dialog">
    <div class="modal-content">
        <form class="equipment_budget_form">
        <div class="modal-header">
            <button type="button" class="close specific_modal_hide" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Equipment Budget</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-4">
                        <label for="asset_item_id" class="control-label">Equipment </label>
                        <?= form_dropdown('asset_item_id',$asset_item_options, $edit ? $Equipment_budget->asset_item_id: '', ' class="form-control searchable" '
                        ) ?>

                        <input type="hidden" name="item_id" value="<?= $edit ? $Equipment_budget->{$Equipment_budget::DB_TABLE_PK} : '' ?>">
                        <input type="hidden" name="project_id" value="<?= $edit ? $Equipment_budget->project_id:$project->{$project::DB_TABLE_PK}?>">
                        <input type="hidden" name="equipment_budget_id" value="<?= $edit ? $Equipment_budget->{$Equipment_budget::DB_TABLE_PK}:''?>">
                        <input type="hidden" name="cost_center_id" value="<?= $edit ? $Equipment_budget->task_id : $task->{$task::DB_TABLE_PK} ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="rate_mode" class="control-label">Rate Mode</label>

                        <?php
                        $options = [
                             ''=>'',
                            'hourly' => 'Hourly',
                            'daily' => 'Daily'
                        ];
                        echo form_dropdown('rate_mode',$options,$edit ? $Equipment_budget->rate_mode : '', ' required class="form-control"')
                        ?>
<!--/* cost_center_id   task_id project_id rate_mode asset_id  item_id  quantity  rate description amount  */-->

                    </div>
                    <div class="form-group col-md-4">
                        <label for="duration" class="control-label">Duration</label>
                        <input type="text" name="duration" class="form-control"  value="<?= $edit ? $Equipment_budget->duration : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="quantity" class="control-label">Quantity</label>
                        <input type="text" name="quantity" class="form-control"  value="<?= $edit ? $Equipment_budget->quantity : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="rate" class="control-label">Rate</label>
                        <input class="form-control number_format" name="rate" value="<?= $edit ? $Equipment_budget->rate : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="amount" class="control-label">Amount</label>
                        <input class="form-control number_format" readonly name="amount"  value="<?= $edit ? $Equipment_budget->rate*$Equipment_budget->quantity : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $Equipment_budget->description : ''  ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button"  class="btn btn-default btn-sm save_equipment_budget_btn">
                Save
            </button>
        </div>
        </form>
    </div>
</div>