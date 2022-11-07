<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/14/2016
 * Time: 12:08 PM
 */
?>
<div class="row">
    <div class="form-horizontal col-xs-12">

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">Start Date:</label>
            <div class="form-control-static col-sm-8">
                <?= $task->start_date != '' ? custom_standard_date($task->start_date) : 'N/A' ?>
            </div>
        </div>

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">End Date:</label>
            <div class="form-control-static col-sm-8">
                <?= $task->end_date != '' ? custom_standard_date($task->end_date) : 'N/A' ?>
            </div>
        </div>

        <?php if(check_permission('Budgets')){ ?>
            <div class="form-group col-md-4">
                <label  class="col-sm-4 control-label">Contract Sum:</label>
                <div class="form-control-static col-sm-8">
                    <?= number_format($task->contract_sum()); ?>
                </div>
            </div>

            <div class="form-group col-md-4">
                <label  class="col-sm-4 control-label">Budget:</label>
                <div class="form-control-static col-sm-8">
                    <?= number_format($task->budget_figure()) ?>
                </div>
            </div>
        <?php } ?>

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">Completion %:</label>
            <div class="form-control-static col-sm-8">
                <?= round($task->completion_percentage(),2); ?>%
            </div>
        </div>
    </div>
    <div class="row">
        <?php $this->load->view('projects/activities/tasks/task_summary_widgets'); ?>
    </div>
</div>
