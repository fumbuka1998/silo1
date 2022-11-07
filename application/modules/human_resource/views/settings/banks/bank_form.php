<?php
$edit = isset($banks_data);
$modal_heading = $edit ?$banks_data->bank_name : 'New Bank';
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
                            <label for="bank_name" class="control-label">Bank Name</label>
                            <input type="text" class="form-control" required name="bank_name" value="<?= $edit ? $banks_data->bank_name : '' ?>">
                            <input type="hidden" name="bank_id" value="<?= $edit ? $banks_data->{$banks_data::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $banks_data->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_bank_button">Save</button>
            </div>
        </form>
    </div>
</div>