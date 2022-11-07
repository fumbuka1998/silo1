<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 10:29 PM
 */
$edit = isset($tender_requirement_type);
?>

<div class="modal-dialog modal-sm">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Requirement Type Form</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="requirement_name" class="control-label">Requirement Name</label>
                            <input type="text" class="form-control" required name="requirement_name" value="<?= $edit ? $tender_requirement_type->requirement_name : '' ?>">
                            <input type="hidden" name="requirement_type_id" value="<?= $edit ? $tender_requirement_type->{$tender_requirement_type::DB_TABLE_PK} : '' ?>">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $tender_requirement_type->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_requirement_type">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
