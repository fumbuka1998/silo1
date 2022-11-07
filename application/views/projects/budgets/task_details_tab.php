<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 24/10/2018
 * Time: 08:33
 */
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 style="text-align: center"><?= wordwrap($task->task_name,80,'<br/>') ?></h4>
                </div>
                <div class="box-body">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
