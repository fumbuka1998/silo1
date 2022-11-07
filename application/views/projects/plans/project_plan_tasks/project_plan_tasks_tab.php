<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 2:15 PM
 */

?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <?php
                               if(check_privilege('Project Actions')){ ?>
                                   <button data-toggle="modal" data-target="#project_plan_task_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                                       <i class="fa fa-plus-circle"></i>&nbsp;Task
                                   </button>
                               <?php }
                            ?>

                            <div id="project_plan_task_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade project_plan_task_form" role="dialog">
                                <?php $this->load->view('projects/plans/project_plan_tasks/project_plan_task_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped plan_tasks_list" project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                                <thead>
                                <tr>
                                    <th style="width: 40%">Task Name</th>
                                    <th>Unit</th>
                                    <th>Planned Quantity</th>
                                    <th>Output Per Day</th>
                                    <th>Duration</th>
                                    <th style="width: 15%"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>