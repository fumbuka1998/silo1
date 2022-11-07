<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/26/2018
 * Time: 11:13 AM
 */
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#project_task_execution_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                                <i class="fa fa-plus-circle"></i> Execution
                            </button>
                            <div id="project_task_execution_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade plan_task_execution_form" role="dialog">
                                <?php $this->load->view('projects/executions/plan_task_execution/plan_task_execution_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped plan_executed_task_list"  project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" project_id="<?= $project->{$project::DB_TABLE_PK} ?>">
                                <thead>
                                <tr>
                                    <th>Execution Date</th>
                                    <th>Task Name</th>
                                    <th>Executed Quantity</th>
                                    <th>Remaining Quantity</th>
                                    <th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
