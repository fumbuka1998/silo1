
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <?php if($project_status != 'closed' && $project->manager_access()){ ?>
                <button data-toggle="modal" data-target="#new_permanent_employee_budget_<?= $task->{$task::DB_TABLE_PK} ?>" class="btn btn-xs btn-default">
                    <i class="fa fa-plus"></i> Add Staff
                </button>
                <?php } ?>
                <div id="new_permanent_employee_budget_<?= $task->{$task::DB_TABLE_PK} ?>" class="modal fade permanent_labour_budget_form" role="dialog">
                    <?php
                     $data['cost_center_level'] = 'project';
                     $data['cost_center_id'] = $project->{$project::DB_TABLE_PK};
                      $this->load->view('projects/budgets/labour/permanent_labour/permanent_labour_budget_form',$data);
                    ?>
                </div>
            </div>
        </div>
    </div>
<div class="box-body">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover permanent_labour_budget_items" project_id="<?= $project->{$project::DB_TABLE_PK} ?>"  task_id="<?= $task->{$task::DB_TABLE_PK} ?>">
                        <thead>
                        <tr>
                            <th>Position</th>
                            <th>Rate Mode</th>
                            <th>Duration</th>
                            <th>Salary Rate</th>
                            <th>Allowance Rate</th>
                            <th>No of Staff</th>
                            <th>Salary Amount</th>
                            <th>Allowance Amount</th>
                            <th>Sub Total</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th colspan="8">Total</th><th id="total_budget_amount_display" style="text-align: right"></th><th colspan="2"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
