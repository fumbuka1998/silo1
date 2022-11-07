<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 2:13 PM
 */

$bcws = $project_plan->budgeted_figure_work_scheduled();
$bcwp = $project_plan->performed_budget();
$actual_cost = $project_plan->plan_actual_cost($project_plan->{$project_plan::DB_TABLE_PK});
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="form-horizontal col-xs-12">
                        <div class="form-group col-md-4">
                            <label  class="col-sm-4 control-label">Start Date:</label>
                            <div class="form-control-static col-sm-8">
                                <?php
                                $start_date = $project_plan->start_date;
                                echo $start_date ? custom_standard_date($start_date) : 'N/A';
                                ?>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label  class="col-sm-4 control-label">End Date:</label>
                            <div class="form-control-static col-sm-8">
                                <?php
                                $end_date = $project_plan->end_date;
                                echo $end_date ? custom_standard_date($end_date) : 'N/A';
                                ?>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label  class="col-sm-4 control-label">No. of Tasks:</label>
                            <div class="form-control-static col-sm-8">
                                <?= $project_plan->plan_tasks(true); ?>
                            </div>
                        </div>
                        
                        <div class="form-gruop col-md-12">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                <th>BCWS</th>
                                <th>BCWP</th>
                                <th>ACWP</th>
                                <th>SPI</th>
                                <th>CPI</th>
                                <th>CV</th>
                                </thead>
                                <tbody>
                                <td><?= number_format($bcws, 2) ?></td>
                                <td><?= number_format($bcwp, 2) ?></td>
                                <td><?= number_format($actual_cost, 2) ?></td>
                                <td><?= $bcws > 0 ?  number_format($bcwp / $bcws, 2) : "-" ?></td>
                                <td><?= $actual_cost > 0 ? round(($bcwp / $actual_cost), 2) : "-" ?></td>
                                <td><?= number_format(($bcwp - $actual_cost), 2) ?></td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>