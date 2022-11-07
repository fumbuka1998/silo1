<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 10:33 AM
 */
$edit = isset($category);
?>

<div class="modal-dialog" style="width: 50%">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit For : '.$category->name : 'Category Registration Form' ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="category_name" class="control-label">Category Name</label>
                            <input type="text" class="form-control" required name="category_name" value="<?= $edit ? $category->name : '' ?>">
                            <input type="hidden" name="category_id" value="<?= $edit ? $category->{$category::DB_TABLE_PK} : '' ?>">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $category->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_category">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>