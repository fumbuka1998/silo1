<?php
$edit = isset($incident);

$types_options = [
   '' => '',
   'NEAR MISS' => 'NEAR MISS',
    'ACCIDENT' => 'ACCIDENT',
    'BREAKDOWN' => 'BREAKDOWN'
];

$causative_agents_options = [
    '' => '',
    'THIRD PARTY' => 'THIRD PARTY',
    'WHEATHER CONDITION' => 'WHEATHER CONDITION',
    'MECHANICAL' => 'MECHANICAL'
];

$is_reported_options = [
    '' => '',
    'YES' => 'YES',
    'NO' => 'NO'
]
?>

<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit For Incident ': 'Incident Registration Form' ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="category_name" class="control-label">Date</label>
                            <input type="text" class="form-control datepicker" required name="incident_date" value="<?= $edit ? $incident->incident_date : '' ?>">
                            <input type="hidden" name="incident_id" value="<?= $edit ? $incident->{$incident::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="" class="control-label">Site</label>
                            <?= form_dropdown('site_id', $projects_options,  $edit ? $incident->site_id : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="" class="control-label">Incident Type</label>
                            <?= form_dropdown('type', $types_options, $edit ? $incident->type : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="" class="control-label">Causative Agent</label>
                            <?= form_dropdown('causative_agent', $causative_agents_options, $edit ? $incident->causative_agent : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="" class="control-label">Problem Reported ?</label>
                            <?= form_dropdown('is_reported', $is_reported_options, $edit ? $incident->is_reported : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="" class="control-label">Reference</label>
                            <input type="text" class="form-control "  name="reference" value="<?= $edit ? $incident->reference : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="" class="control-label">Location</label>
                            <input type="text" class="form-control "  name="location" value="<?= $edit ? $incident->location : '' ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" rows="4" class="form-control"><?= $edit ? $incident->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_incident">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
