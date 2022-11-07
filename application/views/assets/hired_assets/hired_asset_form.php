<?php
$edit = isset($hired_asset);
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Asset</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <?php
                        if($edit) {
                            $asset_item = $hired_asset->asset()->asset_item();
                        }
                        if($list_type == "clients"){
                            ?>
                            <div class="form-group col-md-6">
                                <label for="item_id" class="control-label">Name</label>
                                <?= form_dropdown('item_id',$asset_options,$edit ? $asset_item->{$asset_item::DB_TABLE_PK} : '','class="form-control searchable"') ?>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="vendor_id" class="control-label">Client</label>
                                <?= /** @var TYPE_NAME $vendor_options */
                                form_dropdown('other_end_id', $client_options, $edit ? $hired_asset->client_id : '', ' class="form-control searchable" ') ?>
                            </div>

                        <?php } else { ?>
                            <div class="form-group col-md-6">
                                <label for="item_id" class="control-label">Name</label>
                                <?= form_dropdown('item_id',$asset_item_options,$edit ? $asset_item->{$asset_item::DB_TABLE_PK} : '','class="form-control searchable"') ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-xs-12">
                        <?php if($list_type == "suppliers"){ ?>
                            <div class="form-group col-md-4">
                                <label for="vendor_id" class="control-label">Vendor</label>
                                <?= /** @var TYPE_NAME $vendor_options */
                                form_dropdown('other_end_id', $vendor_options, $edit ? $hired_asset->vendor_id : '', ' class="form-control searchable" ') ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="Project" class="control-label">Project</label>
                                <?= form_dropdown('project_id', $project_options, $edit ? $hired_asset->project_id : '', ' class="form-controll searchable" ') ?>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="sub_location_id" class="control-label">Sub-Location</label>
                                <?= /** @var TYPE_NAME $sub_location_name */
                                form_dropdown('sub_location_id', $edit ? [$hired_asset->sub_location_id => $sub_location_name] : [], $edit ? $hired_asset->sub_location_id : '', ' class="form-controll searchable" ') ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="hired_date" class="control-label"> Hired Date</label>
                            <input type="text" class="form-control datepicker" required name="hired_date" value="<?= $edit ? set_date($hired_asset->hired_date) : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="dead_line" class="control-label">Dead-Line</label>
                            <input type="text" class="form-control datepicker" name="dead_line" value="<?= $edit ? set_date($hired_asset->dead_line) : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount</label>
                            <input type="hidden" name="type" value="<?= /** @var TYPE_NAME $list_type */
                            strtoupper($list_type) ?>">
                            <input type="text" name="amount" class="form-control number_format" value="<?= $edit ? number_format($hired_asset->asset()->book_value) : '' ?>">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                           <textarea class="form-control" name="description"><?= $edit ? $hired_asset->asset()->description : '' ?></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button hired_asset_id="<?= $edit ? $hired_asset->{$hired_asset::DB_TABLE_PK} : '' ?>" type="button" id="save_hired_assets_<?= $edit ? $hired_asset->{$hired_asset::DB_TABLE_PK} : '' ?>" class="btn btn-default btn-sm save_hired_assets">Save</button>
            </div>
        </form>
    </div>
</div>