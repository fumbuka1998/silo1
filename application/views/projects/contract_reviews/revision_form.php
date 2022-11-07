<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 8/6/2018
 * Time: 3:45 PM
 */

$cost_center_options = $project->cost_center_options();
$edit = isset($revision);
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
<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Revision</h4>
        </div>
        <div class="modal-body">
                <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-4">
                        <label for="revision_date" class="control-label">Revision Date</label>
                        <input type="text" class="form-control datepicker" name="revision_date" value="<?= $edit ? $revision->revision_date : date('Y-m-d')?>">
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                        <input type="hidden" name="revision_id" value="<?= $edit ? $revision->{$revision::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Revised Task/Reason</th><th colspan="3">Revision Informations</th><th></th>
                                </tr>
                                <tr class="revision_row_template" style="display: none">
                                    <td style="width: 20%;">
                                        <?= form_dropdown('task_id', $plan_cost_center_options, '', ' class="form-control"') ?>
                                        <input type="hidden" name="item_type" value="task_revision">
                                    </td>
                                    <td colspan="3">
                                        <table class="table table-bordered table-hover task_revision_table">
                                            <thead>
                                                <tr>
                                                    <th>Quantity</th><th>Rate</th><th>Amount</th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control revised_task_quantity" name="quantity" value="">
                                                            <span class="input-group-addon unit_display"></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control number_format" name="rate" value="">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control number_format" name="amount" value="" readonly>
                                                    </td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                                <tr class="extension_row_template" style="display: none">
                                    <td style="width: 20%;">
                                        <textarea style="width: 340px; height: 100px; " name="reason" class="form-control"><?= ''?></textarea>
                                        <input type="hidden" name="item_type" value="project_extension">
                                    </td>
                                    <td colspan="3">
                                        <table class="table table-bordered table-hover extension_table">
                                            <thead>
                                                <tr>
                                                    <th>Variation Type</th><th>Duration</th><th>Unit</th><th>Contract Sum</th><th>Variation Type</th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?= form_dropdown('plus_or_minus_duration', $variation_type_oprions,  '','class="form_control"') ?>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control duration_variation_input" required name="duration_variation" value="<?= ''?>">
                                                    </td>
                                                    <td>
                                                        <?= form_dropdown('duration_type', $duration_type_oprions,'', ' class="form-control"'); ?>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control number_format" required name="contract_sum_variation" value="<?= '' ?>">
                                                    </td>
                                                    <td>
                                                        <?= form_dropdown('plus_or_minus_contract_sum', $variation_type_oprions, '', 'class="form-control "') ?>
                                                    </td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody class="parent_table_tbody">
                            <?php if(!$edit){?>
                                <tr>
                                    <td style="width: 20%;">
                                        <textarea style="width: 340px; height: 100px; " name="reason" class="form-control"><?= ''?></textarea>
                                        <input type="hidden" name="item_type" value="project_extension">
                                    </td>
                                    <td colspan="3">
                                        <table class="table table-bordered table-hover extension_table">
                                            <thead>
                                            <tr>
                                                <th>Variation Type</th><th>Duration</th><th>Unit</th><th>Contract Sum</th><th>Variation Type</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?= form_dropdown('plus_or_minus_duration', $variation_type_oprions,  '','class="form_control"') ?>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control duration_variation_input" required name="duration_variation" value="<?= ''?>">
                                                </td>
                                                <td>
                                                    <?= form_dropdown('duration_type', $duration_type_oprions,'', ' class="form-control"'); ?>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control number_format" required name="contract_sum_variation" value="<?= '' ?>">
                                                </td>
                                                <td>
                                                    <?= form_dropdown('plus_or_minus_contract_sum', $variation_type_oprions, '', 'class="form-control "') ?>
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            <?php } else {
                                /*****EDIT GOES HERE*****/
                            } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-xs-12">
                        <div class="pull-right">
                            <button type="button" class="btn btn-xs btn-default revision_row_adder"><i class="fa fa-plus-circle"></i>Revision</button>
                            <button type="button" class="btn btn-xs btn-default extension_row_adder"><i class="fa fa-plus-circle"></i>Extension</button>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="comments" class="control-label">Comments</label>
                        <textarea name="description" class="form-control"><?= '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-sm btn-default submit_revision" type="button">Save</button>
        </div>
        </form>
    </div>
</div>