<?php

$priority_options = [
    '' => '',
    'High' => 'High',
    'Medium' => 'Medium',
    'Low' => 'Low'
];

if($type == 'Inspection'){
    $id = $job_card_type->inspection_id;
    $name = 'inspection_id';
} else {
    $id = $job_card_type->incident_id;
    $name = 'incident_id';
}
?>

<div class="modal-dialog ">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= 'Edit for Job Card' ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="parameter_name" class="control-label">Date</label>
                            <input type="text" class="form-control datepicker" required name="date"
                                   value="<?= $job_card->date ?>">
                            <input type="hidden" name="<?= $name ?>"
                              value="<?= $id ?>">
                            <input type="hidden" name="job_type" value="inspection"/>
                            <input type="hidden" name="job_card_id"
                                   value="<?= $job_card->{$job_card::DB_TABLE_PK}  ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="description" class="control-label">Priority</label>
                            <?= form_dropdown('priority', $priority_options,  $job_card->priority , ' class="form-control searchable" ') ?>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Remarks</label>
                            <textarea name="remarks"
                                      class="form-control"><?= $job_card->remarks ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_job_card">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
