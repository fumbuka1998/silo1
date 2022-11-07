<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 2:18 PM
 */
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <?php if (check_privilege('Project Actions')) {
                                ?>
                                <button data-toggle="modal" data-target="#project_plan_task_labour_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                                    <i class="fa fa-plus-circle"></i>&nbsp;Labour Budget
                                </button>
                                <?php

                            } ?>

                            <div id="project_plan_task_labour_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade project_plan_task_labour_form" role="dialog">
                                <?php $this->load->view('projects/plans/project_plan_task_labour/project_plan_task_labour_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped plan_labour_budget_list"  project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                                <thead>
                                <tr>
                                    <th>Labour Type</th>
                                    <th>Task Assigned</th>
                                    <th style="width: 10%">Rate Mode</th>
                                    <th style="width: 10%">No. Of Workers</th>
                                    <th>Rate</th>
                                    <th>Duration</th>
                                    <th>Amount</th>
                                    <th style="width: 13%"></th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th colspan="6">Total</th>
                                    <th class="total_labour_budget_display" style="text-align: right"></th>
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