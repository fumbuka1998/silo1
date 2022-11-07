<?php
$edit = isset($certificate);
$types_options = [
    '' => '',
    'EMPLOYEE' => 'EMPLOYEE',
    'COMPANY' => 'COMPANY'
];

?>

<div class="modal-dialog " style="width: 50%">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit For : '.$certificate->name : 'Certificate Registration Form' ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="certificate_name" class="control-label">Certificate Name</label>
                            <input type="text" class="form-control" required name="certificate_name" value="<?= $edit ? $certificate->name : '' ?>">
                            <input type="hidden" name="hse_certificate__id" value="<?= $edit ? $certificate->{$certificate::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="type" class="control-label"> Type</label>
                            <?= form_dropdown('type', $types_options, $edit ? $certificate->type : '', ' class="form-control searchable"  ') ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $certificate->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_hse_certificate">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
