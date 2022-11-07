<?php
$edit = isset($row);
?>

<div class="modal-dialog">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Depreciation Rate</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date"
                                   value="<?= $edit ? $row->start_date : '' ?>">
                            <?php inspect_object($depreciation_rates)?>
                            <?php inspect_object($depreciation_rate_items)?>

                                <input type="hidden" class="form-control" required name="asset_depreciation_rate_id"
                                   value="<?= $edit ? $row->id : '' ?>">
                        </div>

                        <hr>
                        <?php if(count($asset_groups)>0){?>
                            <table class="table table striped table-hover table-bordered">
                                <thead>

                                        <tr>

                                            <th>Group Name</th>
                                            <th>Depreciation rate(%)</th>

                                        </tr>

                                </thead>
                                <tbody>

                                <?php foreach($asset_groups as $asset_group){ ?>

                                    <tr>

                                            <td><?php echo $asset_group->group_name?></td>

                                            <td>
                                                <input type="hidden" name="asset_group_id" value="" class="form-control input-sm"  required>

                                                <input type="text" name="depreciation_rate" value="<?php $edit ? $depreciation_rate_item->rate: '' ?>" class="form-control input-sm"  required>

                                            </td>

                                    </tr>

                                <?php }  ?>
                                </tbody>
                            </table>

                        <?php } ?>


                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm  save_depreciation_rate">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>