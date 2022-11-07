<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 10/26/2016
 * Time: 9:04 AM
 */
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <?php if($project_status != 'closed' && (check_privilege('Project Actions') || $project->manager_access())){ ?>
                <button data-toggle="modal" data-target="#new_miscellaneous_budget_<?= $task->{$task::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Miscellaneous Budget
                </button>
                <div id="new_miscellaneous_budget_<?= $task->{$task::DB_TABLE_PK} ?>" class="modal fade miscellaneous_budget_form" role="dialog">
                    <?php $this->load->view('projects/budgets/miscellaneous/miscellaneous_budget_form'); ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" task_id="<?= $task->{$task::DB_TABLE_PK} ?>" class="table table-bordered table-hover miscellaneous_budget_items">
                    <thead>
                        <tr>
                            <th>Budget Type</th><th>Amount</th><th>Description</th><th></th>
                        </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Total</th><th id="total_budget_amount_display" style="text-align: right"></th><th colspan="2"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
