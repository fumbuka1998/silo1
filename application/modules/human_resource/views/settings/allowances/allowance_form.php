<?php
$edit = isset($allowance_data);
$modal_heading = $edit ?$allowance_data->allowance_name : 'New Allowance';
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
                            <label for="allowance_name" class="control-label">Allowance Name</label>
                            <input type="text" class="form-control" required name="allowance_name" value="<?= $edit ? $allowance_data->allowance_name : '' ?>">
                            <input type="hidden" name="allowance_id" value="<?= $edit ? $allowance_data->{$allowance_data::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $allowance_data->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_allowance">Save</button>
            </div>
        </form>
    </div>
</div>