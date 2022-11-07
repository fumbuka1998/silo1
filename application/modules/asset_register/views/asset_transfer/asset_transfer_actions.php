<?php
$transfer_id = $transfer_data->{$transfer_data::DB_TABLE_PK};
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_asset_transfer_<?= $transfer_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_asset_transfer_<?= $transfer_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('asset_transfer_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_asset_transfer" trans_id="<?= $transfer_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>