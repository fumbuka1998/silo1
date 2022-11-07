
<?php
$branch_Id = $branches_data->{$branches_data::DB_TABLE_PK};
//print_r([$branches_data]);

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_branch_<?= $branch_Id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_branch_<?= $branch_Id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('branch_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_branch" delete_branch_id="<?= $branch_Id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
