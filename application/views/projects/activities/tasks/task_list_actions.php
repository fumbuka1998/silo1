<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/14/2016
 * Time: 11:20 AM
 */

$task_id = $task->{$task::DB_TABLE_PK}
?>

<button data-toggle="modal" data-target="#task_details_<?= $task_id ?>" class="btn btn-xs btn-default">
    <i class="fa fa-info-circle"></i> Details
</button>
<div id="task_details_<?= $task_id ?>" class="modal fade" tabindex="-1" role="dialog">
    <?php $this->load->view('projects/activities/tasks/task_details'); ?>
</div>
<?php if(check_privilege('Project Actions') || $project->manager_access()){ ?>
<button data-toggle="modal" data-target="#edit_task_<?= $task_id ?>" class="btn btn-default btn-xs">
    <i class="fa fa-edit"></i> Edit
</button>
<div id="edit_task_<?= $task_id ?>" class="modal fade" tabindex="-1" role="dialog">
    <?php $this->load->view('projects/activities/tasks/task_form'); ?>
</div>
<button class="btn btn-danger btn-xs delete_task" task_id="<?= $task_id ?>" >
    <i class="fa fa-trash"></i> Delete
</button>
<?php } ?>

