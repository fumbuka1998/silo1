<?php
    $edit = isset($item);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Miscellaneous Budget Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="" class="control-label">Budget Type</label>
                        <select name="expense_account_id" class="form-control searchable <?= $edit ? '' : ' budget_expense_account_selector' ?>">
                            <?php
                            if($edit){
                                ?>
                                <option value="<?= $item->expense_account_id ?>" selected><?= $expense_account_name ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>"/>
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>"/>
                        <input type="hidden" name="cost_center_id" value="<?= $edit ? isset($item->task_id) ? $item->task_id : '' : $task->{$task::DB_TABLE_PK} ?>"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="amount" class="control-label">Amount</label>
                        <input type="text" class="form-control number_format" required name="amount" value="<?= $edit ? $item->amount : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="amount" class="control-label">Description</label>
                        <textarea class="form-control" name="description"><?= $edit ? $item->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_miscellaneous_budget_item">Save</button>
        </div>
        </form>
    </div>
</div>