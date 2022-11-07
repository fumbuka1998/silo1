<?php
    $edit = isset($item)
?>
<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close specific_modal_hide" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Costs</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Date</th><th style="width: 15%;">Cost Center</th><th style="width: 15%;">Source Sub-Location</th><th style="width: 25%;">Material Item</th><th style="width: 12%;">Quantity</th><th>Description</th><th></th>
                            </tr>
                            <tr style="display: none" class="row_template">
                                <td>
                                    <input type="text" class="form-control datepicker" required name="date" value="<?= $edit ? $item->cost_date : date('Y-m-d') ?>">
                                </td>
                                <td style="width: 15%;">
                                    <?= form_dropdown('cost_center_id',$cost_center_options,
                                        $edit ? isset($item->task_id) ? $item->task_id : '' : '',
                                        ' class="form-control" '
                                    ) ?>
                                    <select style="display: none" name="project_id"><option value="<?= $project->{$project::DB_TABLE_PK} ?>">SELECTED</option></select>
                                    <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">
                                </td>
                                <td style="width: 15%;">
                                    <?= form_dropdown('source_sub_location_id', $edit ? [$item->source_sub_location_id => $source_sub_location->sub_location_name] : $store_sub_location_options, $edit ? $item->source_sub_location_id : '', ' class="form-control "'); ?>
                                </td>
                                <td style="width: 25%;">
                                    <select name="material_id" class="form-control cost_material_selector"></select>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" name="quantity" class="form-control" previous_quantity="<?= $edit ? $item->quantity : 0 ?>" value="<?= $edit ? $item->quantity : '' ?>">
                                        <span class="input-group-addon unit_display"><?= $edit ? $unit_symbol : '&nbsp;' ?></span>
                                    </div>
                                </td>
                                <td>
                                    <textarea name="description" rows="1" class="form-control"><?= $edit ? $item->description : ''  ?></textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs row_remover">
                                        <i class="fa fa-close"></i>
                                    </button>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control datepicker" required name="date" value="<?= $edit ? $item->cost_date : date('Y-m-d') ?>">
                                </td>
                                <td style="width: 15%;">
                                    <?= form_dropdown('cost_center_id',$cost_center_options,
                                        $edit ? isset($item->task_id) ? $item->task_id : '' : '',
                                        ' class="form-control searchable" '
                                    ) ?>
                                    <select style="display: none" name="project_id"><option value="<?= $project->{$project::DB_TABLE_PK} ?>">SELECTED</option></select>
                                    <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">
                                </td>
                                <td style="width: 15%;">
                                    <?= form_dropdown('source_sub_location_id', $edit ? [$item->source_sub_location_id => $source_sub_location->sub_location_name] : $store_sub_location_options, $edit ? $item->source_sub_location_id : '', ' class="form-control '.($edit ? '' : 'searchable').'"'); ?>
                                </td>
                                <td style="width: 25%;">
                                    <select name="material_id"
                                            class="form-control <?= $edit ? '' : 'searchable cost_material_selector' ?>">
                                        <?php
                                        if($edit){
                                            ?>
                                            <option value="<?= $item->material_item_id ?>" selected><?= $material_item_name ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td style="width: 12%;">
                                    <div class="input-group">
                                        <input type="text" name="quantity" class="form-control" previous_quantity="<?= $edit ? $item->quantity : 0 ?>" value="<?= $edit ? $item->quantity : '' ?>">
                                        <span class="input-group-addon unit_display"><?= $edit ? $unit_symbol : '&nbsp;' ?></span>
                                    </div>
                                </td>
                                <td>
                                    <textarea name="description" rows="1" class="form-control"><?= $edit ? $item->description : ''  ?></textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs row_remover">
                                        <i class="fa fa-close"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5"></th>
                                <td style="text-align: right" colspan="2">
                                    <button type="button" class="btn btn-default btn-xs row_adder">
                                        <i class="fa fa-plus"></i> Add Row
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button project_id="<?= $project->{$project::DB_TABLE_PK} ?>" type="button" class="btn btn-default btn-sm save_material_cost">
                Save
            </button>
        </div>
        </form>
    </div>
</div>