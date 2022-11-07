<?php
$edit = isset($handover);
?>
<div class="modal-dialog" style="width: 50%;">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Assets Handover</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="disposal_date control_label">Handover Date</label>
                            <input type="text" class="form-control datepicker" required name="handover_date" value="<?= $edit ?  $handover->handover_date : ''?>" >
                            <input type="hidden" name="location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                            <input type="hidden" name="handover_id" value="<?= $edit ?  $handover->{$handover::DB_TABLE_PK} : ''?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name" class="control-label">Employee</label>

                            <?= form_dropdown('employee_id',$employee_options, $edit ?  $handover->handler_id: '','class="form-control searchable"'); ?>
                        </div>
                    <table class="table table-bordered table-hover" style="table-layout: fixed;">
                        <thead>
                        <tr>
                            <th style="width: 50%">Asset</th><th>Remarks</th><th style="width: 5%"></th>
                        </tr>
                        <tr class="handover_row_template" style="display: none">
                            <td>
                                <?= form_dropdown('asset_id',$asset_stock_options,'','class="form-control"'); ?>
                            </td>
                            <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                            </td>
                            <td>
                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(!$edit){ ?>
                            <tr>
                                <td>
                                    <?= form_dropdown('asset_id',$asset_stock_options, '',' class="searchable form-control" ');?>
                                </td>
                                <td><textarea class="form-control" rows="1" name="remarks"></textarea></td>
                                </td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                        <?php } else {
                            $items = $handover->items();
                            foreach ($items as $item){
                                $asset = $item->asset_sub_location_history()->asset();
                                $asset_id = $asset->{$asset::DB_TABLE_PK};
                                ?>
                                <tr>
                                    <td>
                                        <?= form_dropdown('asset_id',[
                                            $asset_id => $asset->asset_code()
                                        ],$asset_id,' class="form-control"') ?>
                                    </td>
                                    <td>
                                        <textarea name="remarks" rows="1" class="form-control"><?= $item->remarks ?></textarea>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-xs remove_row">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>
                        <?php
                            }
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="1"></th>
                            <th colspan="2">
                                <button type="button" class="btn btn-xs btn-default handover_row_adder pull-right">Add Row</button>
                            </th>
                        </tr>
                        </tfoot>
                        </table>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="comments" class="control-label">Comments</label>
                        <textarea class="form-control" name="comments" ><?= $edit ?  $handover->comments : '' ?></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_handover_asset">Save</button>
            </div>
        </form>
    </div>
</div>