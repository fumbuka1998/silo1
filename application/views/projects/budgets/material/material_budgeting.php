<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/17/2016
 * Time: 7:46 PM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <br/>
                <?php if($project_status != 'closed' && (check_privilege('Project Actions') || $project->manager_access())){ ?>
                <button data-toggle="modal" data-target="#new_material_budget_<?= $task->{$task::DB_TABLE_PK} ?>" class="btn btn-xs btn-default">
                    <i class="fa fa-plus"></i> Add Material
                </button>
                <div id="new_material_budget_<?= $task->{$task::DB_TABLE_PK} ?>" class="modal fade material_budget_form" role="dialog">
                    <?php
                    $data['cost_center_level'] = 'project';
                    $data['cost_center_id'] = $project->{$project::DB_TABLE_PK};
                    $this->load->view('projects/budgets/material/material_budget_form',$data);
                    ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-bordered table-hover material_budget_items" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" task_id="<?= $task->{$task::DB_TABLE_PK} ?>">
                    <thead>
                        <tr>
                            <th>Material</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th>Description</th><th></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total</th><th id="total_budget_amount_display" style="text-align: right"></th><th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
