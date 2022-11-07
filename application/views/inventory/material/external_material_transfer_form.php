<?php
    $edit = isset($transfer);
?>
<div class="modal-dialog" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">External Material Transfer</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="location_id" class="control-label">Destination Location</label>
                        <input type="hidden" name="source_location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                        <input type="hidden" name="transfer_id" value="<?= $edit ? $transfer->{$transfer::DB_TABLE_PK} : '' ?>">
                        <select name="destination_location_id" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="transfer_date" class="control-label">Transfer Date</label>
                        <input type="text" class="form-control datepicker" required name="transfer_date" value="<?= $edit ? $transfer->transfer_date : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="project_id" class="control-label">Project</label>
                        <?= form_dropdown('project_id',!$edit ? $project_options : [
                            $project->{$project::DB_TABLE_PK} => $project->project_name
                        ], $edit ? $transfer->project_id : '',' class=" form-control searchable"') ?>
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>From</th><th>Material</th><th>Available</th><th>Quantity</th><th>Unit</th><th>Remarks</th><th></th>
                            </tr>
                            <tr class="row_template" style="display: none">
                                <td width="20%">
                                    <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control"') ?>
                                </td>
                                <td width="20%">
                                    <?= form_dropdown('material_id',[],'',' class=" form-control material_selector"') ?>
                                </td>
                                <td><input name="available_quantity" class="form-control" readonly="readonly"></td>
                                <td>
                                    <input class="form-control" type="text"  available_quantity="0" name="quantity" previous_quantity="0">
                                    <input type="hidden" name="rate">
                                </td>
                                <td class="unit_display"></td>
                                <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!$edit){ ?>
                            <tr>
                                <td width="20%">
                                    <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control searchable"') ?>
                                </td>
                                <td width="20%">
                                    <?= form_dropdown('material_id',[],'',' class=" form-control material_selector searchable"') ?>
                                </td>
                                <td><input name="available_quantity" class="form-control" readonly="readonly"></td>
                                <td>
                                    <input class="form-control" type="text" name="quantity" available_quantity="0" previous_quantity="0">
                                    <input type="hidden" name="rate">
                                </td>
                                <td class="unit_display"></td>
                                <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                <td></td>
                            </tr>
                            <?php } else {
                                    $items = $transfer->items();
                                    foreach($items as $item) {
                                        $source_sub_location = $item->source_sub_location();
                                        $material = $item->material_item();
                                        ?>
                            <tr class="edit_row">
                                <td width="20%">
                                    <select name="source_sub_location_id" class="form-control">
                                        <option value="<?= $item->source_sub_location_id ?>" selected><?= $source_sub_location->sub_location_name ?></option>
                                    </select>
                                </td>
                                <td width="20%">
                                    <select name="material_id" class="form-control">
                                        <option value="<?= $item->material_item_id ?>" selected><?= $material->item_name ?></option>
                                    </select>
                                </td>
                                <td><input name="available_quantity" class="form-control" value="<?= $item->quantity + $material->sub_location_balance($source_sub_location->{$source_sub_location::DB_TABLE_PK}, $item->project_id) ?>" readonly="readonly"></td>
                                <td>
                                    <input class="form-control" type="text" name="quantity" value="<?= $item->quantity ?>" previous_quantity="<?= $item->quantity ?>">
                                    <input type="hidden" name="rate" value="<?= $item->price ?>">
                                </td>
                                <td class="unit_display"><?= $material->unit()->symbol ?></td>
                                <td><textarea class="form-control" rows="1" name="remarks"><?= $item->remarks ?></textarea></td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                                        <?php
                                    }
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6"></th>
                                <th>
                                    <button type="button" class="btn btn-xs btn-default row_adder">Add Row</button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="form-group col-xs-12">
                        <label for="comments" class="control-label">Comments</label>
                        <textarea class="form-control" name="comments"><?= $edit ? $transfer->comments : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_external_material_transfer">Submit</button>
        </div>
        </form>
    </div>
</div>