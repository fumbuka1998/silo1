<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Transfer</h4>
        </div>
        <form>

            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="location_id" class="control-label">Destination Location</label>
                            <input type="hidden" name="requisition_approval_id" value="<?= $requisition_approval_id ?>">
                            <select name="destination_location_id" class="form-control">
                                <option value="<?= $transfer_order->destination_id ?>" selected><?= $transfer_order->destination_name ?></option>
                            </select>
                            <input type="hidden" name="source_location_id" value="<?= $location_id ?>">

                        </div>
                        <div class="form-group col-md-3">
                            <label for="transfer_date" class="control-label">Transfer Date</label>
                            <input type="text" class="form-control datepicker" required name="transfer_date" value="<?=$transfer_order->approved_date ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="transfer_date" class="control-label">Project</label>
                            <select name="project_id" class="form-control">
                                <option value="<?=$transfer_order->project_id?>" selected><?=$transfer_order->project_name?></option>
                            </select>
                        </div>
                    </div>


                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover" style="table-layout: fixed;">
                            <thead>
                            <tr>
                                <th style="width: 20%;">From</th><th style="width: 30%;">Item</th><th style="width: 10%;">Available/Asset Item</th><th style="width: 10%;">Quantity</th><th style="width: 8%;">Unit</th><th>Remarks</th><th style="width: 5%;"></th>
                            </tr>

                            </thead>
                            <tbody>

                            <?php
                                foreach($material_items as $item) {
                                    $material_item = $item['material_item'];
                                    $approval_item = $item['approval_item'];
                                    $balance_to_transfer = $approval_item->transfer_order_balance();
                                    if($balance_to_transfer > 0) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?= form_dropdown('source_sub_location_id', $sub_location_options, '', ' class=" form-control "') ?>
                                                <input type="hidden" name="item_type" value="material">

                                            </td>
                                            <td>
                                                <select name="material_id"
                                                        class="form-control searchable material_selector">
                                                    <option value="<?= $material_item->item_id ?>"
                                                            selected><?= $material_item->item_name ?></option>
                                                </select>
                                            </td>

                                            <td><input name="available_quantity" class="form-control available_quantity"
                                                       readonly="readonly"></td>
                                            <td>
                                                <input class="form-control" type="text" name="quantity"
                                                       value="<?= $balance_to_transfer ?>"
                                                       previous_quantity="0">
                                                <input type="hidden" name="rate"
                                                       value="<?= $approval_item->approved_rate ?>">
                                            </td>
                                            <td class="unit_display"><?= $material_item->unit()->symbol ?></td>
                                            <td>
                                                <textarea class="form-control" rows="1" name="remarks"></textarea>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-xs row_remover" type="button">
                                                    <i class="fa fa-close"></i>
                                                </button>
                                            </td>

                                        </tr>
                                        <?php
                                    }
                                }

                                foreach ($asset_items as $item){
                                    $asset_item = $item['asset_item'];
                                    $approval_item = $item['approval_item'];
                                    $balance_to_transfer = $approval_item->transfer_order_balance();
                                    for($i = 0; $i < $balance_to_transfer; $i++) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class="form-control " ') ?>
                                                <input type="hidden" name="item_type" value="asset">
                                                <input type="hidden" name="asset_item_id" value="<?= $asset_item->{$asset_item::DB_TABLE_PK} ?>">
                                            </td>
                                            <td><?= form_dropdown('asset_id', [], '', ' class="form-control searchable" ') ?></td>
                                            <td colspan="3"><?= $asset_item->asset_name ?></td>
                                            <td>
                                                <textarea class="form-control" rows="1" name="remarks"></textarea>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-xs row_remover" type="button">
                                                    <i class="fa fa-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }

                            ?>
                            </tbody>

                        </table>
                        <div class="form-group col-md-3">
                            <label for="driver_name" class="control-label">Driver Name</label>
                            <input type="text" class="form-control" required name="driver_name" value="">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="vehicle_number" class="control-label">Vehicle Number</label>
                            <input type="text" class="form-control" required name="vehicle_number" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea class="form-control" name="comments"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_external_material_transfer ">Submit</button>
            </div>
        </form>
    </div>
</div>