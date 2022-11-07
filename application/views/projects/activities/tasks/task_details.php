<?php
    $task_id = $task->{$task::DB_TABLE_PK};
?>
<div class="modal-dialog" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $task->task_name ?></h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a class="task_summary_activator" task_id="<?= $task_id ?>" href="#task_summary_<?= $task_id ?>" data-toggle="tab">Summary</a></li>
                            <!--<li><a class="task_material_activator" task_id="<?/*= $task_id */?>" href="#task_material_<?/*= $task_id */?>" data-toggle="tab">Material</a></li>-->
                            <?php if(check_privilege('Project Actions')){ ?>
                            <li><a class="task_progress_activator" task_id="<?= $task_id ?>" href="#task_progress_<?= $task_id ?>" data-toggle="tab">Progress</a></li>
                            <?php } ?>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane task_summary_container" task_id="<?= $task_id ?>" id="task_summary_<?= $task_id ?>">
                                <?php $this->load->view('projects/activities/tasks/task_summary_tab'); ?>
                            </div>
                            <?php if(check_privilege('Project Actions')){ ?>
                            <div class="tab-pane task_progress_container" id="task_progress_<?= $task_id ?>">
                                <?php $this->load->view('projects/activities/tasks/task_progress_tab',['task_id' => $task_id]); ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">

        </div>
    </div>
</div>