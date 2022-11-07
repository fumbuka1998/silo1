<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/8/2018
 * Time: 6:16 PM
 */

?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#project_executions_material_cost_form_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                            <i class="fa fa-plus-circle"></i>Material Costs
                        </button>
                        <div id="project_executions_material_cost_form_<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" class="modal fade plan_task_execution_material_cost_form" role="dialog">
                            <?php $this->load->view('projects/executions/plan_task_execution_materials/plan_task_execution_material_cost_form'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover executions_material_costs_items">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th style="width: 40%">Task</th>
                                <th style="width: 20%">Item Name</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th style="width: 10%"></th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th colspan="6">Total</th>
                                <th id="total_cost_amount_display" style="text-align: right"></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>