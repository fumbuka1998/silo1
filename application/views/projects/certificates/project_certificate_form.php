<?php
$edit = isset($certificate);
?>

<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Project Certificate</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Certificate Date</label>
                            <input type="text" class="form-control datepicker" required name="certificate_date" value="<?= $edit ?  $certificate->certificate_date : ''?>" >
                            <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                            <input type="hidden" name="certificate_id" value="<?= $edit ?  $certificate->{$certificate::DB_TABLE_PK} : ''?>">
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Certificate Number</label>
                            <input type="text" class="form-control" id="certificate_number" name="certificate_number" value="<?= $edit ?  $certificate->certificate_number : '' ?>" required>
                        </div>
                        <div class="form-group col-xs-6">
                            <label for="name" class="control-label">Certified Amount</label>
                            <input type="text" class="form-control number_format" id="certified_amount" name="certified_amount" value="<?= $edit ?  $certificate->certified_amount : '' ?>" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea class="form-control" name="comments" ><?= $edit ?  $certificate->comments : ''?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_project_certificate">Save</button>
            </div>
        </form>
    </div>
</div>