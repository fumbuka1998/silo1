<?php
$edit = isset($registered_certificate);
$types_options = [
    '' => '',
    'EMPLOYEE' => 'EMPLOYEE',
    'COMPANY' => 'COMPANY'
];
if($edit){
    $certificate = $registered_certificate->hse_certificate();
}

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
                            <?= form_dropdown('hse_certificate_id', $certificates_options, $edit ? $registered_certificate->hse_certificate_id : '', ' class="form-control searchable"  ') ?>
                            <input type="hidden" name="registered_certificate_id" value="<?= $edit ? $registered_certificate->{$registered_certificate::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="type" class="control-label"> Labour</label>
                            <?= form_dropdown('employee_id', $employees_options, $edit ? $registered_certificate->employee_id : '', ' class="form-control searchable"  ') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_registered_certificate">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
