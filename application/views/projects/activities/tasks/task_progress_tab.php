<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 9/12/2016
 * Time: 12:06 PM
 */

    $task_id = $task->{$task::DB_TABLE_PK};
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_task_progress_update_<?= $task_id ?>" class="btn btn-default btn-xs">
                    <i class="fa fa-refresh"></i> Progress Update
                </button>
                <div id="new_task_progress_update_<?= $task_id ?>" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('projects/activities/tasks/task_progress_update_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a class="task_progress_graphical_activator" href="#task_progress_graphical_<?= $task_id ?>" data-toggle="tab">Graphical</a></li>
                        <li><a class="task_progress_list_activator" href="#task_progress_list_<?= $task_id ?>" data-toggle="tab">List</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane task_progress_graphical_container row" task_id="<?= $task_id ?>" id="task_progress_graphical_<?= $task_id ?>">

                        </div>
                        <div class="tab-pane task_progress_list_container row" id="task_progress_list_<?= $task_id ?>">
                            <div class="col-xs-12 table-responsive">
                                <table task_id="<?= $task_id ?>" class="table table-bordered table-hover task_progress_list">
                                    <thead>
                                        <tr>
                                            <th>Datetime</th><th>Percentage</th><th>Description</th><th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
