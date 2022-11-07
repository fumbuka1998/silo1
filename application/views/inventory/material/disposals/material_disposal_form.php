<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 27-Sep-17
 * Time: 9:40 AM
 */

//inspect_object($sub_location_options);
//inspect_object($location)

?>

<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Stock Disposal </h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="disposal_date control_label">Disposal Date</label>
                            <input type="hidden" name="location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                            <input type="text" class="form-control datepicker" required name="disposal_date" value="" >
                        </div>
                        <div class="form-group col-md-4">
                            <label for="project_id control_label">Project</label>
                            <?php
                            $project_options[''] = 'UNASSIGNED';
                            echo form_dropdown('project_id', $project_options, '', ' class="form-control searchable" ') ?>
                        </div>

                        <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover" style="table-layout: fixed">
                            <thead>
                                <tr>
                                    <th style="width: 28%;">From</th><th style="width: 28%;">Material</th><th style="width: 10%;">Quantinty</th><th style="width: 8%;">Unit</th><th>Remarks</th><th style="width: 5%;"></th>
                                </tr>
                                <tr style="display: none" class="material_row_template">
                                    <td>
                                        <?= form_dropdown('source_sub_location_id',$sub_location_options,'', 'class="form-control  "') ?>
                                        <input type="hidden" name="item_type"  value="material">
                                    </td>
                                    <td><?= form_dropdown('material_id',[],'', 'class="form-control material_selector "') ?></td>
                                    <td>
                                        <input type="text" class="form-control" previous_quantity="0" name="quantity" value="">
                                        <input type="hidden" name="rate">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td>
                                        <button class="btn btn-xs btn-danger row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr style="display: none" class="asset_row_template">
                                    <td>
                                        <?= form_dropdown('source_sub_location_id',$sub_location_options,'', 'class="form-control  "') ?>
                                        <input type="hidden" name="item_type"  value="asset">
                                    </td>
                                    <td><?= form_dropdown('asset_id',[],'', 'class="form-control "') ?></td>
                                    <td colspan="2"></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td>
                                        <button class="btn btn-xs btn-danger row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?= form_dropdown('source_sub_location_id',$sub_location_options,'', 'class="form-control"') ?>
                                        <input type="hidden" name="item_type"  value="material">
                                    </td>
                                    <td><?= form_dropdown('material_id',[],'', 'class="form-control material_selector"') ?></td>
                                    <td>
                                        <input type="text" name="quantity" class="form-control" previous_quantity="0"  value="">
                                        <input type="hidden" name="rate">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td>
                                        <button class="btn btn-xs btn-danger row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2"></th>
                                    <td colspan="4">
                                        <span class="pull-right">
                                            <button type="button" class="btn btn-default btn-xs material_row_adder">
                                                <i class="fa fa-plus"></i> Material
                                            </button>

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
                <button type="button" class="btn btn-default btn-md pull-right save_material_disposal">Submit</button>
            </div>
        </form>
    </div>
</div>