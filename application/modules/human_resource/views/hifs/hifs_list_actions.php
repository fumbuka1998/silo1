
<?php
$hif_Id = $hifs_data->{$hifs_data::DB_TABLE_PK};
//print_r([$hifs_data]);

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_hif_<?= $hif_Id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_hif_<?= $hif_Id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hif_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_hif" delete_hif_id="<?= $hif_Id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
