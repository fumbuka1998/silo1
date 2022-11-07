<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/11/2016
 * Time: 5:37 PM
 */

    $cost_types = ['material','miscellaneous','permanent_labour','equipment','sub_contract','activities','tasks'];
    $budget = $project->budget_figure();
    $actual_cost = $project->actual_cost();
    $timeline_percentage = $project->timeline_percentage();
    $percentage_completion = $project->completion_percentage();
    $budget_spending_percentage = $project->budget_spending_percentage();
    $elapsed_days = $project->elapsed_days();
    $duration = $project->duration();
?>
<div class="row">
    <div class="col-xs-12">

        <div class="col-lg-4">
            <div class=" info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Timeline</span>
                    <span class="info-box-number"><?= round($timeline_percentage,2)?>% = <?= $elapsed_days?> Days Elapsed</span>
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
                    <span class="info-box-number"> <?= round($percentage_completion,2)?>%</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?= $percentage_completion?>%"></div>
                    </div>
                    <span class="progress-description">
                         Completed: <?= round($percentage_completion,2)?>%
                    </span>
                </div>
            </div>
        </div>
        <?php if(check_privilege('Budgets')){ ?>
        <div class="col-lg-4">
            <div class=" info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Budget Spending</span>
                    <span class="info-box-number"><?= round($budget_spending_percentage,2)?>% =  <?= number_format($actual_cost)?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?= $budget_spending_percentage?>%"></div>
                    </div>
                    <span class="progress-description">
                        <b>Balance:</b> <?= number_format(($budget - $actual_cost)); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>