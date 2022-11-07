<?php
    $edit = isset($transfer);
?>
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">External Stock Transfer</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-6">
                        <label for="location_id" class="control-label">Destination Location</label>
                        <input type="hidden" name="source_location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                        <input type="hidden" name="transfer_id" value="<?= $edit ? $transfer->{$transfer::DB_TABLE_PK} : '' ?>">
                        <select style="width: 100%" name="destination_location_id" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="transfer_date" class="control-label">Transfer Date</label>
                        <input type="text" class="form-control datepicker" required name="transfer_date" value="<?= $edit ? $transfer->transfer_date : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="project_id" class="control-label">Project</label>
                        <?= form_dropdown('project_id',
                            !$edit  ? $project_options : [ $transfer->project_id => $project->project_name],
                            $edit ? $transfer->project_id : (isset($project) ? $project->{$project::DB_TABLE_PK} : ''),' class=" form-control searchable"') ?>
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 20%;">From</th><th style="width: 30%;">Material/Asset</th><th style="width: 10%;">Available</th><th style="width: 10%;">Quantity</th><th style="width: 8%;">Unit</th><th>Remarks</th><th style="width: 5%;"></th>
                            </tr>
                            <tr class="material_row_template" style="display: none">
                                <td>
                                    <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control"') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </td>
                                <td>
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
                            <tr class="asset_row_template" style="display: none">
                                <td>
                                    <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control"') ?>
                                    <input type="hidden" name="item_type" value="asset">

                                </td>
                                <td>
                                    <?= form_dropdown('asset_id',[],'',' class=" form-control material_selector"') ?>
                                </td>
                                <td colspan="3">

                                </td>
                                <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!$edit){ ?>
                            <tr>
                                <td>
                                    <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control "') ?>
                                </td>
                                <td>
                                    <?= form_dropdown('material_id',[],'',' class=" form-control material_selector "') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </td>
                                <td><input name="available_quantity" class="form-control" readonly="readonly"></td>
                                <td>
                                    <input class="form-control" type="text" name="quantity" available_quantity="0" previous_quantity="0">
                                    <input type="hidden" name="rate">
                                </td>
                                <td class="unit_display"></td>
                                <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <?php } else {
                                    $material_items = $transfer->material_items();
                                    foreach($material_items as $item) {
                                        $source_sub_location = $item->source_sub_location();
                                        $material = $item->material_item();
                                        ?>
                            <tr class="edit_row">
                                <td>
                                    <select name="source_sub_location_id" class="form-control">
                                        <option value="<?= $item->source_sub_location_id ?>" selected><?= $source_sub_location->sub_location_name ?></option>
                                    </select>
                                    <input type="hidden" name="item_type" value="material">
                                </td>
                                <td>
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

                                    $asset_items = $transfer->asset_items();
                                    foreach($asset_items as $item) {
                                        $source_history = $item->asset_sub_location_history();
                                        $source_sub_location = $source_history->sub_location();
                                        $asset = $source_history->asset();
                                        ?>
                            <tr class="edit_row">
                                <td>
                                    <select name="source_sub_location_id" class="form-control">
                                        <option value="<?= $source_sub_location->{$source_sub_location::DB_TABLE_PK} ?>" selected><?= $source_sub_location->sub_location_name ?></option>
                                    </select>
                                    <input type="hidden" name="item_type" value="asset">
                                </td>
                                <td>
                                    <select name="asset_id" class="form-control">
                                        <option value="<?= $asset->{$asset::DB_TABLE_PK} ?>" selected><?= $asset->asset_code() ?></option>
                                    </select>
                                </td>
                                <td colspan="3"></td>
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
                                <th style="text-align: right" colspan="7">
                                    <button type="button" class="btn btn-xs btn-default material_row_adder">
                                        <i class="fa fa-plus"></i> Material
                                    </button>
                                    <button type="button" class="btn btn-xs btn-default asset_row_adder">
                                        <i class="fa fa-plus"></i> Asset
                                    </button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="form-group col-md-3">
                        <label for="driver_name" class="control-label">Driver Name</label>
                        <input type="text" class="form-control" required name="driver_name" value="<?= $edit ? $transfer->driver_name : '' ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="vehicle_number" class="control-label">Vehicle Number</label>
                        <input type="text" class="form-control" required name="vehicle_number" value="<?= $edit ? $transfer->vehicle_number : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
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