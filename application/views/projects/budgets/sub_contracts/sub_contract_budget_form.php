<?php
$edit = isset($item);

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Sub-contract Budget Form</h4>
        </div>
        <form class="sub_contract_budget_form">
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">

                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control" name="description"><?= $edit ? $item->description: '' ?></textarea>
                            <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>"/>
                            <input type="hidden" name="cost_center_id" value="<?= $edit ? isset($item->task_id) ? $item->task_id : '' : $task->{$task::DB_TABLE_PK} ?>"/>
                            <input type="hidden" name="budget_item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="amount" class="control-label">Amount</label>
                            <input type="text" class="form-control number_format" required name="amount" value="<?= $edit ? $item->amount: '' ?>"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_sub_contract_budget">Save</button>
            </div>
        </form>
    </div>
</div>