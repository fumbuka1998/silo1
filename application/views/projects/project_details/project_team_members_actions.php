<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/18/2016
 * Time: 10:40 AM
 */
?>
<?php if($project->manager_access() || check_permission('Administrative Actions')){ ?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_project_manager_<?= $member->{$member::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_project_manager_<?= $member->{$member::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
        <?php $this->load->view('projects/project_details/project_team_member_form'); ?>
    </div>
    <button class="btn btn-danger btn-xs delete_project_team_member" member_id="<?= $member->{$member::DB_TABLE_PK} ?>">
        <i class="fa fa-trash-o"></i> Delete
    </button>
</span>
<?php } ?>

