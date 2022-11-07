<?php
$edit = isset($loan_type_data);
$modal_heading = $edit ?$loan_type_data->loan_type : 'New Loan Type';
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
                            <label for="loan_type" class="control-label">Loan Type</label>
                            <input type="text" class="form-control" required name="loan_type" value="<?= $edit ? $loan_type_data->loan_type : '' ?>">
                            <input type="hidden" name="loan_type_id" value="<?= $edit ? $loan_type_data->{$loan_type_data::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $loan_type_data->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_loan_type">Save</button>
            </div>
        </form>
    </div>
</div>