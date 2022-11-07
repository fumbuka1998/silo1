


<?php
    $edit = isset($depreciation_rate);

    if ($edit){
        $depreciation_rate_items=$depreciation_rate->depreciation_rate_items();
    }
    $edit_item=isset($depreciation_rate_item);

?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Depreciation Rate</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <input type="hidden" name="depreciation_rate_id" value="<?= $edit ? $depreciation_rate->id: '' ?>">

                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit ? $depreciation_rate->start_date : '' ?>">
                        </div>

                        <?php if(!$edit){ ?>
                            <?php if(count($asset_groups)>0){?>

                                <table class="table table striped table-hover table-bordered">
                                    <thead>

                                    <tr>
                                        <th>Asset Group</th>
                                        <th>Depreciation rate(%)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach($asset_groups as $asset_group){ ?>
                                        <tr>
                                            <td><?php echo $asset_group->group_name?></td>
                                            <td>
                                                <input type="hidden" name="asset_group_id" value=" <?php echo $asset_group->id ?> ">
                                                <input type="text" name="depreciation_rate" value="" placeholder="0" class="form-control input-sm"  required >

                                            </td>

                                        </tr>

                                    <?php }  ?>
                                    </tbody>
                                </table>

                            <?php } ?>
                        <?php }else{ ?>
                            <table class=" table table striped table-hover table-bordered " >
                                <thead>
                                <tr>
                                    <th>Asset Group</th>
                                    <th>Depreciation Rate %</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     foreach($depreciation_rate_items as $rate_item ){ ?>
                                        <tr>
                                            <td><?php echo $rate_item->asset_group()->group_name ?></td>
                                            <td>
                                                <input type="hidden" name="asset_depreciation_rate_item_id" value="<?= $rate_item->id ?>" class="form-control input-sm"  required>
                                                <input type="hidden" name="asset_group_id" value="<?= $rate_item->asset_group_id ?>" class="form-control input-sm"  required>

                                                <input type="text" name="depreciation_rate" class="form-control input-sm"  required
                                                       value="<?php echo $rate_item->rate ?> " >
                                            </td>
                                        </tr>
                                     <?php }  ?>

                                    <!--   new groups -->
                                    <?php
                                    foreach($asset_groups as $asset_group ){ ?>
                                        <tr style=" color: #00a7d0">
                                            <td><?= $asset_group->group_name;?></td>
                                            <td>
                                                <input type="hidden" name="asset_group_id" value="<?= $asset_group->id ?>" class="form-control input-sm"  required>
                                                <input type="text" name="depreciation_rate" required value=" "  class="form-control input-sm"  >
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php }  ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm  save_depreciation_rates">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>