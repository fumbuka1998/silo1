<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 9/13/2016
 * Time: 2:44 PM
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_task_progress_update_<?= $progress_update->{$progress_update::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_task_progress_update_<?= $progress_update->{$progress_update::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('projects/activities/tasks/task_progress_update_form'); ?>
    </div>
    <button class="btn btn-danger btn-xs delete_task_progress_update" update_id="<?= $progress_update->{$progress_update::DB_TABLE_PK} ?>"><i class="fa fa-trash"></i> Delete</button>
</span>
