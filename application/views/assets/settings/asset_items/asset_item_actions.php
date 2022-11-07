<?php
$asset_item_id = $asset_item->{$asset_item::DB_TABLE_PK};
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_asset_item_<?= $asset_item_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_asset_item_<?= $asset_item_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('assets/settings/asset_items/asset_item_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_asset_item" asset_item_id="<?= $asset_item_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>