<?php
$edit = isset($branches_data);
$modal_heading = $edit ?$branches_data->branch_name : 'New Branch';
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
                            <label for="branch_name" class="control-label">Branch Name</label>
                            <input type="text" class="form-control" required name="branch_name" value="<?= $edit ? $branches_data->branch_name : '' ?>">
                            <input type="hidden" name="branch_id" value="<?= $edit ? $branches_data->{$branches_data::DB_TABLE_PK} : '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_branch_button">Save</button>
            </div>
        </form>
    </div>
</div>