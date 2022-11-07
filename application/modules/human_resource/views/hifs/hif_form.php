<?php
$edit = isset($hifs_data);
$modal_heading = $edit ?$hifs_data->hif_name : 'New HIF';
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
                            <label for="hif_name" class="control-label">hif Name</label>
                            <input type="text" class="form-control" required name="hif_name" value="<?= $edit ? $hifs_data->hif_name : '' ?>">
                            <input type="text" name="hif_id" value="<?= $edit ? $hifs_data->{$hifs_data::DB_TABLE_PK} : '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_hif_button">Save</button>
            </div>
        </form>
    </div>
</div>