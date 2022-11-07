<?php
    $edit = isset($activity);
    if($edit){
        $modal_title = 'Edit: '.$activity->activity_name;
    } else {
        $modal_title = 'New Activity';
    }
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $modal_title ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <label for="activity_name" class="control-label">Activity name</label>
                            <input type="text" class="form-control" required name="activity_name" value="<?= $edit ? $activity->activity_name : '' ?>">
                            <input type="hidden" class="form-control" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                            <input type="hidden" class="form-control" name="activity_id" value="<?= $edit ? $activity->{$activity::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="weight_percentage" class="control-label">Percentage Weight</label>
                            <input type="text" class="form-control" required name="weight_percentage" value="<?= $edit ? $activity->weight_percentage : 0 ?>">
                        </div>
                        <div class="form-group col-md-8">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control" name="description"><?= $edit ? $activity->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_activity">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>