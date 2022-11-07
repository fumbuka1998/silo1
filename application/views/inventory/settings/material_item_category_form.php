<?php
    $edit = isset($category);
    $modal_heading = $edit ? $category->category_name : 'New Material Item Category'
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
                    <div class="form-group col-md-6">
                        <label for="category_name" class="control-label">Category Name</label>
                        <input type="text" class="form-control" required name="category_name" value="<?=  $edit ? $category->category_name : ''  ?>">
                        <input type="hidden" name="category_id" value="<?= $edit ? $category->{$category::DB_TABLE_PK} : '' ?>">
                        <input type="hidden" name="current_parent_category_id" value="<?= $edit ? $category->parent_category_id : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="category_name" class="control-label">Under</label>
                        <?= form_dropdown('parent_category_id',$material_category_options,$edit ? $category->parent_category_id : '',' class="searchable form-control" ') ?>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control"><?= $edit ? $category->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_material_item_category_button">Save</button>
        </div>
        </form>
    </div>
</div>