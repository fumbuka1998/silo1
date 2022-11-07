<?php
$edit = isset($sub_contract);
$modal_heading = $edit ? $sub_contract->contract_name : 'New Sub-contract';
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $modal_heading ?></h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                          <label for="contract_date" class="control-label"> Contract Date</label>
                          <input type="text"  class="form-control datepicker" name="contract_date" required  value="<?= $edit ? $sub_contract->contract_date: '' ?>">
                          <input type="hidden" class=" form-control" name="project_id" value="<?=$edit ? $sub_contract->project_id: $project->project_id ?> ">
                          <input type="hidden" class=" form-control" name="sub_contract_id" value="<?=$edit ? $sub_contract->id: '' ?> ">
                        </div>

                         <div class="form-group col-md-8">
                            <label for="sub_contractor_id" class="control-label">Sub-contractor</label>
                            <?= form_dropdown('contractor_id',$stakeholder_options,
                            $edit ? isset($sub_contract->stakeholder_id) ? $sub_contract->stakeholder_id : '' : '',
                             ' class="form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="contract_name" class="control-label ">Contract Name</label>
                            <input type="text" class="form-control" name="contract_name" value="<?= $edit ? $sub_contract->contract_name: '' ?>">
                        </div>

                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" rows="5" class="form-control"><?= $edit ? $sub_contract->description: '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_project_sub_contract">Save</button>
            </div>
        </form>
    </div>
</div>
