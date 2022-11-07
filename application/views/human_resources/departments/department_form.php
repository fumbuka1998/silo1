<?php
    $edit = isset($department);
    $modal_heading = $edit ? $department->department_name : 'New Department';
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
                        <label for="department_name" class="control-label">Department Name</label>
                        <input type="text" class="form-control" required name="department_name" value="<?= $edit ? $department->department_name : '' ?>">
                        <input type="hidden" name="department_id" value="<?= $edit ? $department->{$department::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $department->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_department_button">Save</button>
        </div>
        </form>
    </div>
</div>