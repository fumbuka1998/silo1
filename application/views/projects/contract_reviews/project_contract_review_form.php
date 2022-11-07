<?php
$edit= isset($project_contract_review);
$duration_type_oprions = [
        '' =>'&nbsp;',
        'days' =>'Days',
        'months' =>'Months',
        'years' =>'Years'
        ];
$variation_type_oprions = [
        '' =>'&nbsp;',
        'plus' =>'Plus',
        'minus' =>'Minus'
        ];
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Project Review</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="review_date" class="control-label">Review Date</label>
                            <input type="text" class="form-control datepicker" required name="review_date" value="<?=  $edit ? $project_contract_review->review_date:'' ?>">
                            <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                            <input type="hidden" name="project_contract_review_id" value="<?= $edit? $project_contract_review->{$project_contract_review::DB_TABLE_PK}:'' ?>">
                        </div>
                        <div style="padding: 2px !important;" class="form-group col-md-8">
                            <div class="col-xs-4">
                                <label for="variation_type" class="control-label ">Variation Type</label>
                                <?= form_dropdown('plus_or_minus_duration', $variation_type_oprions, $edit ? $project_contract_review->plus_or_minus_duration : '','class="form_control searchable"') ?>
                            </div>
                            <div class="col-xs-4">
                                <label for="quantity" class="control-label ">Duration</label>
                                <input type="text" class="form-control" required name="duration_variation" value="<?= $edit? $project_contract_review->duration_variation :''?>">
                            </div>
                            <div class="col-xs-4">
                                <label for="duration_type" class="control-label ">Unit</label>
                                <?= form_dropdown('duration_type', $duration_type_oprions,$edit? $project_contract_review->duration_type:'', ' class="form-control searchable"'); ?>
                            </div>
                        </div>
                        <div class="form-group col-md-8">
                            <label for="contract_sum_variation" class="control-label">Variation Cost</label>
                            <input type="text" class="form-control number_format" required name="contract_sum_variation" value="<?= $edit? $project_contract_review->contract_sum_variation:'' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="contract_sum_variation" class="control-label">Variation Type</label>
                            <?= form_dropdown('plus_or_minus_contract_sum', $variation_type_oprions, $edit ? $project_contract_review->plus_or_minus_contract_sum : '', 'class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="reason" class="control-label">Reason</label>
                            <textarea style="width: 100%" name="reason" class="form-control"><?= $edit? $project_contract_review->reason:''?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_project_contract_review">Submit</button>
            </div>
        </form>
    </div>
</div>