<?php
 $edit = isset($group);
?>

<div class="modal-dialog">

    <div class="modal-content">
    <form>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">New  Asset Group</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="group_name" class="control-label">Name</label>
                        <input type="text" class="form-control" required name="group_name" value="<?= $edit ? $group->group_name : '' ?>">
                        <input type="hidden" name="group_id" value="<?= $edit ? $group->{$group::DB_TABLE_PK} : '' ?>">
                        
                    </div>

                      <div class="form-group col-md-6">
                        <label for="parent_id" class="control-label">Under</label>
                          <?= form_dropdown('parent_id', $parent_group_options, $edit ? $group->parent_id : '', ' class="form-control searchable" ') ?>
                    </div>

                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $group->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_asset_group">
                Save
            </button>
        </div>
    </form>
    </div>
</div>