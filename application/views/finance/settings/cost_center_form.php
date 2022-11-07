<?php
    $edit = isset($cost_center);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Cost Center</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-12">
                        <label for="cost_center_name" class="control-label">Cost Center Name</label>
                        <input type="text" class="form-control" required name="cost_center_name" value="<?= $edit ? $cost_center->cost_center_name : '' ?>">
                        <input name="cost_center_id" type="hidden" value="<?= $edit ? $cost_center->{$cost_center::DB_TABLE_PK} : '' ?>">
                    </div>
                   
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $cost_center->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_cost_center">
                Save
            </button>
        </div>
        </form>
    </div>
</div>