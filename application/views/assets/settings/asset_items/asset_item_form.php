<?php
 $edit = isset($asset_item);
?>

<div class="modal-dialog">

    <div class="modal-content">
    <form>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Asset Item Form</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="group_name" class="control-label">Name</label>
                        <input type="text" class="form-control" required name="asset_name" value="<?= $edit ? $asset_item->asset_name : '' ?>">
                        <input type="hidden" name="asset_item_id" value="<?= $edit ? $asset_item->{$asset_item::DB_TABLE_PK} : '' ?>">
                        
                    </div>

                    <div class="form-group col-md-6">
                        <label for="parent_id" class="control-label">Under</label>
                          <?= form_dropdown('asset_group_id', $asset_group_options, $edit ? $asset_item->asset_group_id : '', ' class="form-control searchable" ') ?>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="part_number" class="control-label">Part Number</label>
                        <input type="text" class="form-control" required name="part_number" value="<?= $edit ? $asset_item->part_number : '' ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $asset_item->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_asset_item">
                Save
            </button>
        </div>
    </form>
    </div>
</div>