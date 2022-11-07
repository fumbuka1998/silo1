<?php
$edit = isset($sub_location);
$modal_heading = $edit ? $sub_location->sub_location_name : 'New Sub-location';
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
                        <div class="form-group  col-xs-12">
                            <input type="checkbox" name="is_for_fuel_mgt" <?= ($edit && $sub_location->equipment_id != '') ? 'checked' : '' ?>>
                            <label for="is_for_fuel_mgt">For Fuel Management</label>
                        </div>
                        <div class="form-group col-xs-12 equipment-id-form-group" style="display: <?= ($edit && $sub_location->equipment_id != '') ? 'block' : 'none' ?>">
                            <label for="equipment_id" class="control-label">Equipment</label>
                            <?= form_dropdown('equipment_id', $asset_stock_options, $edit ? $sub_location->equipment_id : '', ' class="form-control searchable"'); ?>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="location_name" class="control-label">Sub-Location Name</label>
                            <input type="text" class="form-control" required name="sub_location_name" value="<?= $edit ? $sub_location->sub_location_name : '' ?>">
                            <input type="hidden" name="sub_location_id" value="<?= $edit ? $sub_location->{$sub_location::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="location_name" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $sub_location->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_sub_location_button">Save</button>
            </div>
        </div>
    </form>
</div>