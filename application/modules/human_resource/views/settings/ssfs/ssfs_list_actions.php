
<?php
$ssf_Id = $ssf->{$ssf::DB_TABLE_PK};
//print_r([$ssfs_data]);

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_ssf_<?= $ssf_Id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_ssf_<?= $ssf_Id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('ssf_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_ssf" delete_ssf_id="<?= $ssf_Id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
