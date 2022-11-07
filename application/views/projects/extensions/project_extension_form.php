<?php
$edit= isset($project_extension);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Project Extension</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="extension_date" class="control-label">Extension Date</label>
                            <input type="text" class="form-control datepicker" required name="extension_date" value="<?=  $edit ? $project_extension->extension_date:'' ?>">
                            <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                            <input type="hidden" name="project_extension_id" value="<?= $edit? $project_extension->{$project_extension::DB_TABLE_PK}:'' ?>">
                        </div>
                        <div style="padding: 2px !important;" class="form-group col-md-6">
                            <div class="col-xs-12">
                                <label for="quantity" class="control-label ">Duration</label>
                            </div>
                            <div class="col-xs-5">
                                <input type="text" class="form-control" required name="duration" value="<?= $edit? $project_extension->duration :''?>">
                            </div>
                            <div class="col-xs-7">
                                    <?php
                                        $options['']='';
                                        $options['days']='Days';
                                        $options['months']='Months';
                                        $options['years']='Years';
                                    ?>
                                    <?= form_dropdown('duration_type', $options,$edit? $project_extension->duration_type:'', ' class="form-control" '); ?>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="extension_cost" class="control-label">Cost</label>
                            <input type="text" class="form-control number_format" required name="extension_cost" value="<?= $edit? $project_extension->extension_cost:'' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="reason" class="control-label">Reason</label>
                            <textarea name="reason" class="form-control"><?= $edit? $project_extension->reason:''?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_project_extension">Submit</button>
            </div>
        </form>
    </div>
</div>