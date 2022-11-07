<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"> Edit Assets</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Registration Date</label>
                            <input type="text" class="form-control datepicker" id="registration_date" name="registration_date" value="<?= $asset->registration_date ?>"  required>
                            <input type="hidden" name="asset_id" value="<?= $asset->{$asset::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="asset_code" class="control-label">Asset Code</label>
                            <input type="text" class="form-control" id="book_value" name="asset_code" value="<?= $asset->asset_code() ?>"  required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Book Value</label>
                            <input type="text" class="form-control number_format" id="book_value" name="book_value" value="<?= $asset->book_value ?>"  required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Salvage Value</label>
                            <input type="text" class="form-control number_format" id="salvage_value" name="salvage_value" value="<?= $asset->salvage_value ?>"  required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Status</label>

                            <?php
                            $options['active'] = 'Active';
                            $options['inactive'] = 'Inactive';
                            $options['disposed'] = 'Disposed';
                            echo form_dropdown('status',$options, $asset->status,'class="form-control"');
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_edit_asset">Save</button>
            </div>
        </form>
    </div>
</div>