<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 1:33 PM
 */

$project_plan_id = $project_plan->{$project_plan::DB_TABLE_PK}
?>

<div style="width: 80%" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $project_plan->title ?>&nbsp;Details</h4>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a class="project_plan_details_activator" href="#project_plan_details_tab_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Details</a></li>
                        <li><a class="project_plan_tasks_activator" href="#project_plan_tasks_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Plan Tasks</a></li>
                        <li><a class="project_plan_materials_activator" href="#project_plan_materials_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Materials Plan</a></li>
                        <li><a class="project_plan_equipments_activator" href="#project_plan_equipments_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Equipments Plan</a></li>
                        <li><a class="project_plan_labour_activator" href="#project_plan_labour_<?= $project_plan_id ?>" project_plan_id="<?= $project_plan_id ?>" data-toggle="tab">Labour Plan</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="project_plan_details_tab_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/plans/project_plan_details_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="project_plan_tasks_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/plans/project_plan_tasks/project_plan_tasks_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="project_plan_materials_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/plans/project_plan_task_materials/project_plan_materials_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="project_plan_equipments_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/plans/project_plan_task_equipments/project_plan_equipments_tab');
                            ?>
                        </div>
                        <div class="tab-pane" id="project_plan_labour_<?= $project_plan_id ?>">
                            <?php
                            $this->load->view('projects/plans/project_plan_task_labour/project_plan_labour_tab');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>