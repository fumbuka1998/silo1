
<div class="row">
   <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <?php if($project_status != 'closed' && (check_privilege('Project Actions') || $project->manager_access())){ ?>
                        <button data-toggle="modal" data-target="#new_equipment_budget_item_<?= $task->{$task::DB_TABLE_PK} ?>"
                                class="btn btn-xs btn-default">
                            <i class="fa fa-plus"></i> Add Equipment
                        </button>
                        <div id="new_equipment_budget_item_<?= $task->{$task::DB_TABLE_PK} ?>" class="modal fade equipment_budget_form" role="dialog">
                            <?php $this->load->view('projects/budgets/equipment/equipment_budget_form'); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
          <div class="box-body">
                <div class="col-xs-12 table-responsive">
                    <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover equipment_budget_items"  task_id="<?= $task->{$task::DB_TABLE_PK} ?>">
                        <thead>
                        <tr>
                            <th>Equipment</th>
                            <th>Task</th>
                            <th>Rate mode</th>
                            <th>Rate</th>
                            <th>Duration</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th colspan="6">Total</th>
                            <th id="total_budget_amount_display" style="text-align: right"></th>
                            <th colspan=""></th><th colspan=""></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
   </div>
</div>
