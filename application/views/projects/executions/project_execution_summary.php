<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/8/2018
 * Time: 6:09 PM
 */


$project_plan_id = $project_plan->{$project_plan::DB_TABLE_PK}
?>

<div style="width: 80%" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $project_plan->title ?>&nbsp;Execution Summary</h4>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a class="project_executions_tasks_activator" href="#project_executions_tasks_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Tasks Execution</a></li>
                        <li><a class="project_executions_materials_activator" href="#project_executions_materials_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Materials</a></li>
                        <li><a class="project_executions_equipments_activator" href="#project_executions_equipments_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Equipments</a></li>
                        <li><a class="project_executions_labour_activator" href="#project_executions_labour_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Labour</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="project_executions_tasks_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/executions/plan_task_execution/plan_task_execution_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="project_executions_materials_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/executions/plan_task_execution_materials/plan_task_execution_material_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="project_executions_equipments_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/executions/plan_task_execution_equipments/plan_task_execution_equipment_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="project_executions_labour_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/executions/plan_task_execution_casual_labour/plan_task_execution_casual_labour_tab');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>