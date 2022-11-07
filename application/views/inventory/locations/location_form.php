<?php
    $edit = isset($location);
    $path = 'inventory/save_location/'.($edit ? $location->{$location::DB_TABLE_PK} : '');
    $modal_heading = $edit ? $location->location_name : 'New location';
?>
<div class="modal-dialog">
    <form method="post" action="<?= base_url($path) ?>">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $modal_heading ?></h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <label for="location_name" class="control-label">Location Name</label>
                        <input type="text" class="form-control" required name="location_name" value="<?= $edit ? $location->location_name : '' ?>">
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="location_name" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $location->description : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-default">Save</button>
        </div>
    </div>
    </form>
</div>