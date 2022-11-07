<?php
/**
 * Created by PhpStorm.
 * User: miralearn
 * Date: 03/12/2018
 * Time: 10:53
 */

$edit = isset($item);
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Edit Material Stock</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="project_id" class="control-label">Project</label>
                            <?= form_dropdown('project_id',$project_options,$item->project_id, 'class="form-control  searchable opening_stock_project_selector"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="date" class="control-label">Date</label>
                            <input type="text" class="form-control datetime_picker" required name="date" value="<?= $item->date ?>">
                        </div>
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Material</th><th>Quantity</th><th>Unit</th><th>Price</th><th>Remarks</th><th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="width: 30%"><?= form_dropdown('item_id',[$item->item_id =>$item->material_item()->item_name],$item->item_id,' class="material_selector form-control searchable"') ?></td>
                                    <td><input type="text" class="form-control" name="quantity" value="<?= $item->quantity ?>"></td>
                                    <td class="unit_display"></td>
                                    <td><input type="text" class="form-control number_format" name="<?= $item->price ?>" value=""></td>
                                    <td><textarea name="<?= $item->remarks ?>" rows="1" class="form-control"></textarea></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_material_opening_stock">Submit</button>
            </div>
        </form>
    </div>
</div>