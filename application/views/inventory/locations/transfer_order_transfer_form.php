<?php
//$edit=isset($transfer_order);
?>
<div class="modal-dialog" style="width: 90%">
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


                        </div>
                        <div class="form-group col-md-3">
                            <label for="transfer_date" class="control-label">Transfer Date</label>
                            <input type="text" class="form-control datepicker" required name="transfer_date" value="<?=$transfer_order->finalized_date ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="transfer_date" class="control-label">Project</label>
                            <select name="project_id" class="form-control">
                                <option value="<?=$transfer_order->project_id?>" selected><?=$transfer_order->project_name?></option>
                            </select>
                        </div>
                    </div>


                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>From</th><th>Material</th><th>Available</th><th>Quantity</th><th>Unit</th><th>Remarks</th>
                            </tr>

                            </thead>
                            <tbody>

                            <?php
                                foreach($items as $item) {

                                    $material_item = $item['material_item'];
                                    $approval_item = $item['approval_item'];
                                    ?>
                                    <tr>
                                        <td width="20%">
                                                <?= form_dropdown('source_sub_location_id',$sub_location_options,'',' class=" form-control searchable"') ?>

                                                <input type="hidden" name="source_location_id" value="<?=$approval_item->location_id?>">
                                                <input type="hidden" name="requisition_approval_id" value="<?=$transfer_order->requisition_approval_id ?>">
                                                <input type="hidden" name="transfer_id" >

                                        </td>
                                        <td width="20%">
                                             <select name="material_id" class="form-control searchable material_selector">
                                                <option value="<?=$material_item->item_id ?>" selected><?= $material_item->item_name?></option>
                                            </select>
                                        </td>

                                        <td><input name="available_quantity" class="form-control available_quantity" readonly="readonly"></td>
                                        <td>
                                            <input class="form-control" type="text" name="quantity" value="<?= $approval_item->approved_quantity ?>" previous_quantity="0">
                                            <input type="hidden" name="rate" value="<?= $approval_item->approved_rate ?>">
                                        </td>
                                        <td class="unit_display"><?= $material_item->unit()->symbol ?></td>
                                        <td>
                                            <textarea class="form-control" rows="1" name="remarks"></textarea></td>

                                    </tr>
                                    <?php
                                }

                            ?>
                            </tbody>

                        </table>
                        <div class="form-group col-xs-12">
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