<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/27/2018
 * Time: 1:56 PM
 */
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#project_plan_task_execution_labour_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                                <i class="fa fa-plus-circle"></i>&nbsp;Labour Execution
                            </button>
                            <div id="project_plan_task_execution_labour_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade plan_task_execution_casual_labour_form" role="dialog">
                                <?php $this->load->view('projects/executions/plan_task_execution_casual_labour/plan_task_execution_casual_labour_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped project_plan_labour_execution_list"  project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th style="width: 20%">Labour Type</th>
                                    <th style="width: 40%">Task Assigned</th>
                                    <th>Rate Mode</th>
                                    <th>No. Of Workers</th>
                                    <th>Rate</th>
                                    <th>Duration</th>
                                    <th>Amount</th>
                                    <th style="width: 14%"></th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th colspan="7">Total</th>
                                    <th class="total_execution_amount_display" style="text-align: right"></th>
                                    <th colspan="1"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>