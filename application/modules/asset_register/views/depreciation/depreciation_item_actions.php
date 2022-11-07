<?php
$depreciation_item_id = $rate_item_data->{$rate_item_data::DB_TABLE_PK};
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_asset_group_<?= $depreciation_item_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_asset_group_<?= $depreciation_item_id ?>" class="modal fade" role="dialog">
        <?php // $this->load->view('asset_group_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_asset_group" group_id="<?= $depreciation_item_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>