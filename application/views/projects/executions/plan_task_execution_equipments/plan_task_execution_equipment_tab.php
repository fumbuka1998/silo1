<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/27/2018
 * Time: 11:09 AM
 */

?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#project_task_equipment_execution_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                                <i class="fa fa-plus-circle"></i>Execution Equipment
                            </button>
                            <div id="project_task_equipment_execution_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade plan_task_execution_equipment_form" role="dialog">
                                <?php $this->load->view('projects/executions/plan_task_execution_equipments/plan_task_execution_equipment_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover table-striped project_task_equipment_execution_list"  project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" project_id="<?= $project->{$project::DB_TABLE_PK} ?>">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th style width="20%">Equipment Name</th>
                                    <th style width="40%">Task Assigned</th>
                                    <th>Rate Mode</th>
                                    <th>Quantity</th>
                                    <th>Rate</th>
                                    <th>Duration</th>
                                    <th>Amount</th>
                                    <th style="width: 13%"></th>
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
