<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 11:17 AM
 */
$edit = isset($parameter);
?>

<div class="modal-dialog" style="width: 50%">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit For : '.$parameter->name : 'Parameter Registration Form' ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="parameter_name" class="control-label">Parameter Name</label>
                            <input type="text" class="form-control" required name="parameter_name" value="<?= $edit ? $parameter->name : '' ?>">
                            <input type="hidden" name="parameter_id" value="<?= $edit ? $parameter->{$parameter::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="category_id" value="<?= $edit ? $parameter->category_id : $category->{$category::DB_TABLE_PK} ?>">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $parameter->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_parameter">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>