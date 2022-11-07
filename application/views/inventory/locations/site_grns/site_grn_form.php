<?php
$sub_location_options = isset($sub_location_options) ? $sub_location_options : [];
$is_site_grn = !is_null($location->project_id);
?>
<div  style="width: 80%" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Delivery Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-3">
                            <label for="receive_date" class="control-label">Receive Date</label>
                            <input type="text" class="form-control datetime_picker" required name="receive_date" value="<?= date('Y-m-d') ?>">
                            <input type="hidden" name="exchange_rate" class="number_format" value="1">
                            <input type="hidden" name="location_id" class="number_format" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="offloading_sub_location_id" class="control-label">Offloading Sub-location</label>
                            <?= form_dropdown('receiving_sub_location_id',$sub_location_options,'',' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="offloading_sub_location_id" class="control-label">Project</label>
                            <?= form_dropdown('project_id',$is_site_grn ? [$location->project_id => $location->project()->project_name] : $project_options,$is_site_grn ? $location->project_id : '',' class="form-control "') ?>
                        </div>
                    </div>
                </div>
                <div class='row'>
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th style="width: 33%">Item Description</th><th>Received Quantity </th><th> Rejected Quantity</th><th style="width: 6%">Unit</th><th>Price</th><th>Amount</th><th style="width: 4%"></th>
                                </tr>
                                <tr style="display: none" class="material_row_template">
                                    <td style="width: 35%">
                                        <input type="hidden" name="item_type" value="material">
                                        <?= form_dropdown('item_id',$material_options,'','class="form-control"') ?>
                                    </td>
                                    <td><input type="text" class="form-control" name="quantity" value=""></td>
                                    <td><input type="text" class="form-control" name="rejected_quantity" value=""></td>
                                    <td><span class="unit_display"></span></td>
                                    <td><input type="text" name="rate" class="form-control money" value=""></td>
                                    <td><input style="text-align: right" type="text" name="amount" readonly class="form-control money" value=""></td>
                                    <td><button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button></td>
                                </tr>
                                <tr style="display: none" class="asset_row_template">
                                    <td>
                                        <?= form_dropdown('item_id',$asset_options,'','class="form-control "') ?>
                                        <input type="hidden" name="item_type" value="asset">
                                    </td>
                                    <td><input type="text" class="form-control" name="quantity" value=""></td>
                                    <td><input type="text" class="form-control" name="rejected_quantity" value=""></td>
                                    <td>No.</td>
                                    <td><input type="text" name="rate" class="form-control money" value=""></td>
                                    <td><input style="text-align: right" type="text" name="amount" readonly class="form-control money" value=""></td>
                                    <td><button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button></td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="width: 35%">
                                        <input type="hidden" name="item_type" value="material">
                                        <?= form_dropdown('item_id',$material_options,'','class="form-control"') ?>
                                    </td>
                                    <td><input type="text" class="form-control" name="quantity" value=""></td>
                                    <td><input type="text" class="form-control" name="rejected_quantity" value=""></td>
                                    <td><span class="unit_display"></span></td>
                                    <td><input type="text" name="rate" class="form-control money" value=""></td>
                                    <td><input style="text-align: right" type="text" name="amount" readonly class="form-control money" value=""></td>
                                    <td><button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button></td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="5">Total</th><th style="text-align: right" class="total_amount_display"></th><th></th>
                                </tr>
                                <tr>
                                    <th colspan="7">
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-default btn-xs material_row_adder">
                                                <i class="fa fa-plus"></i> Material
                                            </button>
                                            <button type="button" class="btn btn-default btn-xs asset_row_adder">
                                                <i class="fa fa-plus"></i> Asset
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea name="comments" class="form-control"></textarea>
                        </div>
                    </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm receive_delivery"><i class="fa fa-save"></i> Submit</button>
            </div>
        </form>
    </div>
</div>
