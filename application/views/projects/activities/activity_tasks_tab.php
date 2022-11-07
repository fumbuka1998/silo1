<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/13/2016
 * Time: 10:11 PM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <?php if($project_status != 'closed' && ($project->manager_access() || check_privilege('Project Actions')) ){ ?>
                        <button data-toggle="modal" data-target="#new_task_<?= $activity->{$activity::DB_TABLE_PK} ?>"
                                class="btn btn-xs btn-default">
                            <i class="fa fa-plus"></i> New Task
                        </button>
                        <div id="new_task_<?= $activity->{$activity::DB_TABLE_PK} ?>" class="modal fade"
                             role="dialog">
                            <?php $this->load->view('projects/activities/tasks/task_form'); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover table-striped activity_tasks_table" activity_id="<?= $activity->{$activity::DB_TABLE_PK} ?>">
                        <thead>
                            <tr>
                                <th>Task Name</th><th>Start Date</th><th>End Date</th>
                                <th>Unit</th><th>Quantity</th><th>Rate</th><th>Contract Sum</th><th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
