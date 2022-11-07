<?php
$group_id = $group->{$group::DB_TABLE_PK};
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_asset_group_<?= $group->{$group::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_asset_group_<?= $group_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('assets/settings/asset_group/asset_group_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_asset_group" delete_asset_group_id="<?= $group_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>