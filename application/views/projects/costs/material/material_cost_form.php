<?php
    $edit = isset($item)
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close specific_modal_hide" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Costs</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-xs-12">
                        <?= form_dropdown('cost_center_id',$cost_center_options,
                            $edit ? isset($item->task_id) ? $item->task_id : '' : '',
                            ' class="form-control searchable" '
                        ) ?>
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>"/>
                        <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="date" class="control-label">Date</label>
                        <input type="text" class="form-control datepicker" required name="date" value="<?= $edit ? $item->cost_date : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="source_sub_location_id" class="control-label">Source Sub-Location</label>
                        <?= form_dropdown('source_sub_location_id', $edit ? [$item->source_sub_location_id => $source_sub_location->sub_location_name] : $store_sub_location_options, $edit ? $item->source_sub_location_id : '', ' class="form-control '.($edit ? '' : 'searchable').'"'); ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="" class="control-label">Material</label>
                        <select name="material_id"
                                class="form-control cost_material_selector <?= $edit ? '' : 'searchable' ?>">
                            <?php
                                if($edit){
                                    ?>
                                    <option value="<?= $item->material_item_id ?>" selected><?= $material_item_name ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="quantity" class="control-label">Quantity</label>
                        <div class="input-group">
                            <input type="text" name="quantity" class="form-control" previous_quantity="<?= $edit ? $item->quantity : 0 ?>" value="<?= $edit ? $item->quantity : '' ?>">
                            <span class="input-group-addon unit_display"><?= $edit ? $unit_symbol : '&nbsp;' ?></span>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" class="form-control"><?= $edit ? $item->description : ''  ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_material_cost">
                Save
            </button>
        </div>
        </form>
    </div>
</div>