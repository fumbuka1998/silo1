<?php
    $edit = isset($transfer);
?>
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Internal Stock Transfer</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-3">
                        <label for="transfer_date" class="control-label">Transfer Date</label>
                        <input type="hidden" name="transfer_id" value="<?= $edit ? $transfer->{$transfer::DB_TABLE_PK} : '' ?>">
                        <input type="hidden" name="location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                        <input type="text" class="form-control datepicker" required name="transfer_date" value="<?= $edit ? $transfer->transfer_date : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="project_id" class="control-label">Project</label>
                        <?= form_dropdown('project_id', $project_options, isset($project) ? $project->{$project::DB_TABLE_PK} : '', ' class="form-control searchable" ') ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="receiver" class="control-label">Receiver</label>
                        <input type="text" class="form-control" required name="receiver" value="">
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover" width="100%" style="table-layout: fixed">
                            <thead>
                                <tr>
                                    <th style="width: 17%">From</th><th style="width: 17%">To</th><th style="width: 20%">Material/Asset</th><th style="width: 9%">Available</th><th style="width: 9%">Quantity</th><th style="width: 7%">Unit</th><th>Remarks</th><th style="width: 5%"></th>
                                </tr>
                                <tr class="material_row_template" style="display: none">
                                    <td>
                                        <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control"') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>
                                    <td>
                                        <?= form_dropdown('destination_sub_location_id',$sub_location_options,'',' class=" form-control"') ?>
                                    </td>
                                    <td>
                                        <select name="material_id" class="form-control"></select>
                                    </td>
                                    <td>
                                        <input name="available_quantity" class="form-control" readonly="readonly">
                                    </td>
                                    <td>
                                        <input name="quantity" class="form-control" value="" previous_quantity="0">
                                        <input type="hidden" name="rate">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td>
                                        <textarea class="form-control" rows="1" name="remarks"></textarea>
                                    </td>
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
                                        <?= form_dropdown('destination_sub_location_id',$sub_location_options,'',' class=" form-control"') ?>
                                    </td>
                                    <td>
                                        <select name="asset_id" class="form-control"></select>
                                    </td>
                                    <td colspan="3">

                                    </td>
                                    <td>
                                        <textarea class="form-control" rows="1" name="remarks"></textarea>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control searchable"') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>
                                    <td>
                                        <?= form_dropdown('destination_sub_location_id',$sub_location_options,'',' class=" form-control searchable"') ?>
                                    </td>
                                    <td>
                                        <select name="material_id" class="form-control searchable"></select>
                                    </td>
                                    <td><input name="available_quantity" class="form-control" readonly="readonly"></td>
                                    <td>
                                        <input name="quantity" class="form-control" value="" previous_quantity="0">
                                        <input type="hidden" name="rate">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align: right" colspan="8">
                                        <button type="button" class="btn btn-default btn-xs material_row_adder">
                                            <i class="fa fa-plus"></i> Material
                                        </button>
                                        <button type="button" class="btn btn-default btn-xs asset_row_adder">
                                            <i class="fa fa-plus"></i> Asset
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea type="text" class="form-control" name="comments"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_internal_material_transfer">Submit</button>
        </div>
        </form>
    </div>
</div>