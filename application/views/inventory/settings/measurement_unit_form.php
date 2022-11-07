<?php
    $edit = isset($unit);
    $modal_heading = $edit ? 'Edit : '.$unit->name : 'New Measurement Unit';
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
                    <div class="form-group col-md-8">
                        <label for="name" class="control-label">Unit Name</label>
                        <input type="text" class="form-control" required name="name" value="<?= $edit ? $unit->name : '' ?>">
                        <input type="hidden" name="unit_id" value="<?= $edit ? $unit->{$unit::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="symbol" class="control-label">Symbol</label>
                        <input type="symbol" class="form-control" required name="symbol" value="<?= $edit ? $unit->symbol : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="initial" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $unit->description : '' ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_measurement_unit_button">Save</button>
        </div>
        </form>
    </div>
</div>