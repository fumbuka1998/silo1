<?php
    $edit = isset($position);
    $modal_heading = $edit ? 'Edit Job Position' : 'New Job Position';
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
                        <label for="department_name" class="control-label">Position Name</label>
                        <input type="text" class="form-control" required name="position_name" value="<?= $edit ? $position->position_name : '' ?>">
                        <input type="hidden" name="position_id" value="<?= $edit ? $position->{$position::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $position->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_job_position_button">Save</button>
        </div>
        </form>
    </div>
</div>