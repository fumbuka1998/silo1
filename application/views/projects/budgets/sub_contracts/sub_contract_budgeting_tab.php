<?php

?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <?php if($project_status != 'closed' && (check_privilege('Project Actions') || $project->manager_access())){ ?>
                <button data-toggle="modal" data-target="#new_sub_contract_<?= $task->{$task::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Sub-contract Budget
                </button>
                <div id="new_sub_contract_<?= $task->{$task::DB_TABLE_PK} ?>" class="modal fade sub_contract_budget_form " role="dialog">
                    <?php $this->load->view('projects/budgets/sub_contracts/sub_contract_budget_form'); ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-bordered table-hover sub_contract_budget_items" task_id="<?= $task->{$task::DB_TABLE_PK} ?>" project_id="<?= $project->{$project::DB_TABLE_PK} ?>">
                    <thead>
                    <tr>
                        <th>Task</th><th>Description</th><th>Amount</th><th></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr style="background-color: #f0f0f0">
                        <th colspan="2">Total</th><th id="total_budget_amount_display" style="text-align: right"></th><th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
