<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 27-Sep-17
 * Time: 9:40 AM
 */
?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Disposal </h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="disposal_date control_label">Disposal Date</label>
                            <input type="text" name="disposal_date" class="form-control datepicker">
                        </div>
                        <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Material</th><th>Project</th><th>Quantinty</th><th>Unit</th><th>Remarks</th><th></th>
                                </tr>
                                <tr style="display: none" class="row_template">
                                    <td style="width: 30%"><?= form_dropdown('item_id',[],'', 'class="form-control disposal_material_selector "') ?></td>
                                    <td style="width: 30%"><?= form_dropdown('project_id',[],'',' class="form-control"') ?></td>
                                    <td><input type="text" class="form-control" name="quantity" value=""></td>
                                    <td class="unit_display"></td>
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
                                    <td style="width: 30%"><?= form_dropdown('item_id',[],'', 'class="form-control disposal_material_selector searchable"') ?></td>
                                    <td style="width: 30%"><?= form_dropdown('project_id',[],'',' class="form-control searchable"') ?></td>
                                    <td><input type="text" class="form-control" name="quantity" value=""></td>
                                    <td class="unit_display"></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4"></th>
                                    <td colspan="2">
                                        <span class="pull-right">
                                            <button type="button" class="btn btn-default btn-xs row_adder">
                                                <i class="fa fa-plus"></i> Add Row
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