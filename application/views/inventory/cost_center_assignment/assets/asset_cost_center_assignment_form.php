<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 24/06/2019
 * Time: 10:44
 */

?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Asset Cost Center Assignment </h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="assignment_date control_label">Assignment Date</label>
                            <input type="hidden" name="location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                            <input type="text" class="form-control datepicker" required name="assignment_date" value="" >
                        </div>
                        <div class="form-group col-md-4">
                            <label for="project_id control_label">Source Project</label>
                            <?= form_dropdown('project_id', $project_options, '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="destination_project_id control_label">Destination Project</label>
                            <?= form_dropdown('destination_project_id', $project_options, '', ' class="form-control searchable" ') ?>
                        </div>

                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th style="width: 30%;">From</th><th style="width: 30%;">AssetCode</th><th>Remarks</th><th style="width: 5%"></th>
                                </tr>
                                <tr style="display: none" class="asset_row_template">
                                    <td><?= form_dropdown('source_sub_location_id',$sub_location_options,'', 'class="form-control "') ?></td>
                                    <td><?= form_dropdown('asset_id',[],'', 'class="form-control asset_selector "') ?></td>
                                    <td>
                                        <textarea name="remarks" rows="1" class="form-control"></textarea>
                                        <input type="hidden" name="rate" value="">
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-danger row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= form_dropdown('source_sub_location_id',$sub_location_options,'', 'class="form-control "') ?></td>
                                    <td><?= form_dropdown('asset_id',[],'', 'class="form-control asset_selector "') ?></td>
                                    <td>
                                        <textarea name="remarks" rows="1" class="form-control"></textarea>
                                        <input type="hidden" name="rate" value="">
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="3"></th>
                                    <td>
                                        <span class="pull-right">
                                            <button type="button" class="btn btn-default btn-xs asset_row_adder">
                                                <i class="fa fa-plus"></i> Asset
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-md pull-right" id="save_asset_cost_center_assignment">Submit</button>
            </div>
        </form>
    </div>
</div>