<?php
    $edit = isset($account_group);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Account Group</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-5">
                        <label for="account_group_name" class="control-label">Group Name</label>
                        <input type="text" class="form-control" required name="account_group_name" value="<?= $edit ? $account_group->group_name : '' ?>">
                        <input name="account_group_id" type="hidden" value="<?= $edit ? $account_group->{$account_group::DB_TABLE_PK} : '' ?>">
                        <input name="parent_id" type="hidden" value="<?= $parent->{$parent::DB_TABLE_PK} ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="parent_id" class="control-label">Under</label>
                        <?= form_dropdown('group_nature_id',$account_group_options,$edit ? $account_group->group_nature_id : '',' class="form-control searchable"') ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="group_code" class="control-label">Code</label>
						<input type="text" class="form-control" name="group_code" value="<?= $edit ? $account_group->group_code : '' ?>" placeholder="Optional">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $account_group->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_account_group">
                Save
            </button>
        </div>
        </form>
    </div>
</div>
