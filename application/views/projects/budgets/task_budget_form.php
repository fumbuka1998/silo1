<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 24/10/2018
 * Time: 08:21
 */

?>

<div style="width: 80%" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Task Resources Budget</h4>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a class="task_details_activator" href="#task_details_tab_<?= $task->{$task::DB_TABLE_PK} ?>" data-toggle="tab">Details</a></li>
                        <li><a class="task_material_budget_activator" href="#material_budgeting<?= $task->{$task::DB_TABLE_PK} ?>" data-toggle="tab">Material Budget</a></li>
                        <li><a class="task_equipment_budget_activator" href="#equipment_budgeting_tab<?= $task->{$task::DB_TABLE_PK} ?>" data-toggle="tab">Equipments Budget</a></li>
                        <li><a class="labour_budget_activator" href="#labour_budgeting_tab<?= $task->{$task::DB_TABLE_PK} ?>" data-toggle="tab">Labour Budget</a></li>
                        <li><a class="miscellaneous_budget_activator"  href="#miscellaneous_budgeting_tab_<?= $task->{$task::DB_TABLE_PK} ?>" data-toggle="tab">Miscellaneous</a></li>
                        <li><a class="sub_contract_budget_activator"  href="#sub_contract_budgeting_tab_<?= $task->{$task::DB_TABLE_PK} ?>" data-toggle="tab">Sub-contact budget</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="task_details_tab_<?= $task->{$task::DB_TABLE_PK} ?>">
                            <?php
                            $this->load->view('projects/budgets/task_details_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="material_budgeting<?= $task->{$task::DB_TABLE_PK} ?>">
                            <?php
                            $this->load->view('projects/budgets/material/material_budgeting');
                            ?>
                        </div>
                        <div class="tab-pane" id="equipment_budgeting_tab<?= $task->{$task::DB_TABLE_PK} ?>">
                            <?php
                            $this->load->view('projects/budgets/equipment/equipment_budgeting');
                            ?>
                        </div>
                        <div class="tab-pane" id="labour_budgeting_tab<?= $task->{$task::DB_TABLE_PK} ?>">
                            <?php
                            $this->load->view('projects/budgets/labour/labour_budgeting_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="miscellaneous_budgeting_tab_<?= $task->{$task::DB_TABLE_PK} ?>">
                            <?php $this->load->view('projects/budgets/miscellaneous/miscellaneous_budget_tab'); ?>
                        </div>
                        <div class="tab-pane fade" id="sub_contract_budgeting_tab_<?= $task->{$task::DB_TABLE_PK} ?>">
                            <?php $this->load->view('projects/budgets/sub_contracts/sub_contract_budgeting_tab'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>