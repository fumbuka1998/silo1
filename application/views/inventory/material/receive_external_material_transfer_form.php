<?php
    $edit = isset($grn);
?>
<div class="modal-dialog"  style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"> Receive Material Transferred</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-3">
                        <label for="receive_date" class="control-label">Receive Date</label>
                        <input type="hidden" name="transfer_id" value="<?= $transfer->{$transfer::DB_TABLE_PK} ?>">
                        <input type="hidden" name="location_id" value="<?= $transfer->destination_location_id ?>">
                        <input type="hidden" name="source_location_id" value="<?= $transfer->source_location_id ?>">
                        <input type="text" class="form-control datepicker" required name="receive_date" value="<?= $edit ? $grn->receive_date : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="offloading_sub_location_id" class="control-label">Offloading Sub-location</label>
                        <?= form_dropdown('receiving_sub_location_id',$sub_location_options,'',' class="form-control searchable"') ?>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Material</th><th>Quantity</th><th>Unit</th><th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $items = $transfer->items();
                                foreach($items as $item){
                                    $material = $item->material_item();
                                    $balance = $item->quantity - $item->quantity_received();
                                    if($balance > 0) {
                                        ?>
                                        <tr>
                                            <td style="width: 35%">
                                                <?= wordwrap($material->item_name, 100, '<br/>') ?>
                                                <input type="hidden" name="material_id"
                                                       value="<?= $item->material_item_id ?>">
                                                <input type="hidden" name="price" value="<?= $item->price ?>">
                                                <input type="hidden" name="project_id" value="<?= $item->project_id ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="quantity"
                                                       value="<?= $balance ?>">
                                                <input type="hidden" class="form-control" name="rate"
                                                       value="<?= $item->price ?>">
                                            </td>
                                            <td><?= $material->unit()->symbol ?></td>
                                            <td style="width: 40%"><textarea rows="1" class="form-control"
                                                                             name="remarks"></textarea></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="comments" class="control-label">Comments</label>
                        <textarea name="comments" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm receive_external_material_transfer">Submit</button>
        </div>
        </form>
    </div>
</div>