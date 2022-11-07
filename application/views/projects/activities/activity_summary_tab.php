<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/13/2016
 * Time: 9:22 PM
 */
?>
<div class="row">
    <div class="form-horizontal col-xs-12">

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">Start Date:</label>
            <div class="form-control-static col-sm-8">
                <?php
                    $start_date = $activity->start_date();
                    echo $start_date ? custom_standard_date($start_date) : 'N/A';
                ?>
            </div>
        </div>

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">End Date:</label>
            <div class="form-control-static col-sm-8">
                <?php
                    $end_date = $activity->end_date();
                    echo $end_date ? custom_standard_date($end_date) : 'N/A';
                ?>
            </div>
        </div>

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">No. of Tasks:</label>
            <div class="form-control-static col-sm-8">
                <?= $activity->tasks(true); ?>
            </div>
        </div>

        <?php if(check_permission('Budgets')){ ?>
        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">Contract Sum:</label>
            <div class="form-control-static col-sm-8">
                <?= number_format($activity->contract_sum()) ?>
            </div>
        </div>

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">Budget:</label>
            <div class="form-control-static col-sm-8">
                <?= number_format($activity->budget_figure()) ?>
            </div>
        </div>
        <?php } ?>

        <div class="form-group col-md-4">
            <label  class="col-sm-4 control-label">Completed %:</label>
            <div class="form-control-static col-sm-8">
                <?= round($activity->completion_percentage(),2) ?>%
            </div>
        </div>
    </div>
    <div class="row">
        <?php $this->load->view('projects/activities/activity_summary_widgets'); ?>
    </div>
</div>
