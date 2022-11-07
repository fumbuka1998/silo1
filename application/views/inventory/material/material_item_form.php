<?php
    $edit = isset($item);
    $modal_heading = $edit ? $item->item_name : 'New Material Item';
?>
<div class="modal-dialog">
    <form>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $modal_heading ?></h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <label for="material_name" class="control-label">Item Name</label>
                        <input type="text" class="form-control" required name="item_name" value="<?= $edit ? htmlentities($item->item_name) : '' ?>">
                        <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="unit_id" class="control-label">Unit</label>
                        <?= form_dropdown('unit_id',$measurement_unit_options,$edit ? $item->unit_id : '',' class="form-control searchable"') ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="part_number" class="control-label">Part Number</label>
                        <input type="text" class="form-control" name="part_number" value="<?= $edit ? $item->part_number : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="category_id" class="control-label">Category</label>
                        <?= form_dropdown('category_id',$material_item_category_options,$edit ? $item->category_id : '',' class="form-control searchable"') ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="image" class="control-label">Image</label>
                        <input type="file" class="form-control" name="image">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="location_name" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $item->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_material_item_button">Save</button>
        </div>
    </div>
    </form>
</div>