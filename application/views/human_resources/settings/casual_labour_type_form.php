<?php
    $edit = isset($type);
    $modal_heading = $edit ? 'Edit : '.$type->name : 'New Casual Labour Type';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $modal_heading ?></h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <label for="name" class="control-label">Type Name</label>
                        <input type="text" class="form-control" required name="name" value="<?= $edit ? $type->name : '' ?>">
                        <input type="hidden" name="type_id" value="<?= $edit ? $type->{$type::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="initial" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $type->description : '' ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_casual_labour_type_button">Save</button>
        </div>
        </form>
    </div>
</div>