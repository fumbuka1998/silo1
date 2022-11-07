<?php
$asset_id = $asset->{$asset::DB_TABLE_PK};
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_asset_<?= $asset->{$asset::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_asset_<?= $asset_id ?>" class="modal fade" role="dialog">
       <?php $this->load->view('assets/asset_form');?>
    </div>

    <button class="btn btn-danger btn-xs delete_asset_button" asset_id="<?= $asset_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>