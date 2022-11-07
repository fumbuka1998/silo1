<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/11/2016
 * Time: 5:37 PM
 */

    $actual_cost = $activity->actual_cost();
    $budget = $activity->budget_figure();
    $timeline_percentage = $activity->timeline_percentage();
    $percentage_completion = $activity->completion_percentage();
    $budget_spending_percentage = $activity->budget_spending_percentage();
    $elapsed_days = $activity->elapsed_days();
    $duration = $activity->duration();
?>
<div class="col-xs-12">

    <div class="col-lg-4">
        <div class=" info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Timeline</span>
                <span class="info-box-number"><?= $timeline_percentage?>% = <?= $elapsed_days?> Days Elapsed</span>
                <div class="progress">
                    <div class="progress-bar" style="width: <?= $timeline_percentage?>%"></div>
                </div>
                <span class="progress-description">
                    <b>Duration: </b><?= $duration?>, <b>Remaining: </b><?= $duration - $elapsed_days?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class=" info-box bg-light-blue">
            <span class="info-box-icon"><i class="fa fa-tasks"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Progress</span>
                <span class="info-box-number"> <?= round($percentage_completion,2) ?>%</span>
                <div class="progress">
                    <div class="progress-bar" style="width: <?= $percentage_completion?>%"></div>
                </div>
                <span class="progress-description">
                     Completed: <?= round($percentage_completion,2) ?>%
                </span>
            </div>
        </div>
    </div>
    <?php if(check_permission('Budgets')){ ?>
    <div class="col-lg-4">
        <div class=" info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Budget Spending</span>
                <span class="info-box-number"><?= $budget_spending_percentage ?>% =  <?= number_format($actual_cost)?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: <?= $budget_spending_percentage ?>%"></div>
                </div>
                <span class="progress-description">
                    <b>Balance:</b> <?= number_format(($budget - $actual_cost)); ?>
                </span>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
