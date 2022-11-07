<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 11:34 PM
 */
?>
<div class="modal-dialog modal-lg">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Material Price</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <input type="hidden" name="tender_component_id" value="<?= $tender_component_id ?>">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover" id="material_price">
                            <thead>
                            <tr>
                                <th>Material Name</th><th>Quantity</th><th>Unit</th><th>Price</th><th>Remarks</th><th></th>
                            </tr>
                            <tr class="material_price_row_template" style="display: none">
                                <td width="20%">
                                    <?= form_dropdown('material_id',$material_dropdown_options,'',' class="form-control"') ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control" required name="quantity" id = "quantity" value="">
                                </td>
                                <td class="unit_display"></td>
                                <td>
                                    <input type="text" id="display_material_price" class="form-control number_format" name="rate"  previous value="0">
                                </td>
                                <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <tbody>
                            <tr>
                                <td width="20%">
                                    <?= form_dropdown('material_id',$material_dropdown_options,'',' class=" form-control searchable"') ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control" required name="quantity" id = "quantity" value="">
                                </td>
                                <td class="unit_display"></td>
                                <td>
                                    <input type="text" id="display_material_price" class="form-control number_format" name="rate"  previous value="0">
                                </td>
                                <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="5"></th>
                                <th colspan="2">
                                    <button type="button" class="btn btn-xs btn-default material_price_row_adder">Add Row</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_material_price">Save</button>
            </div>
        </div>
            </div>
    </form>
</div>


