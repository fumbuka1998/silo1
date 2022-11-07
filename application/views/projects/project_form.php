<?php
    $action = 'projects/save';
    if(isset($project->{$project::DB_TABLE_PK})){
        $modal_title = 'Edit Project: '.$project->project_name;
        $action .= '/'.$project->{$project::DB_TABLE_PK};
        $edit = true;
    } else {
        $modal_title = 'New Project';
        $edit = false;
    }
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form action="<?= base_url($action) ?>" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $modal_title ?></h4>
            </div>
            <div class="modal-body" style="overflow:auto;">
                <div class="form-group col-md-6">
                    <label for="project_name" class="control-label">Project Name</label>
                    <input type="text" class="form-control" required name="project_name" value="<?= $project->project_name ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="client_id" class="control-label">Client</label>
                    <?= form_dropdown('stakeholder_id', $stakeholder_options, $project->stakeholder_id, " class = ' searchable form-control' "); ?>
                </div>
                <div class="form-group col-md-3">
                    <label for="site_location" class="control-label">Site Location</label>
                    <input type="text" class="form-control" required name="site_location" value="<?= $project->site_location ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="category_id" class="control-label">Category</label>
                    <?= form_dropdown('category_id', $category_options, $project->category_id, " required class = ' searchable form-control' "); ?>
                </div>
                <div class="form-group col-md-3">
                    <label for="reference_number" class="control-label">Reference Number</label>
                    <input type="text" class="form-control" name="reference_number" value="<?= $project->reference_number ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="start_date" class="control-label">Start Date</label>
                    <input type="text" class="form-control datepicker" name="start_date" value="<?= $project->start_date ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="end_date" class="control-label">End Date</label>
                    <input type="text" class="form-control datepicker" name="end_date" value="<?= $project->end_date ?>">
                </div>
                <div class="form-group col-xs-12">
                    <label for="salary" class="control-label">Description</label>
                    <textarea class="form-control" name="description"><?= $project->description ?></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
