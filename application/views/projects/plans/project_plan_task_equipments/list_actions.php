<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/4/2018
 * Time: 12:38 AM
 */
if (check_privilege('Project Actions')) {

    ?>
    <div style="width: 100%">
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                Actions
            </button>
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="#" data-toggle="modal"
                       data-target="#edit_plan_equipment_budget_<?= $plan_equipment_budget->{$plan_equipment_budget::DB_TABLE_PK} ?>"
                       class="btn btn-default btn-xs">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
                <li>
                    <a style="color: white" href="#" class="btn btn-danger btn-xs delete_plan_equipment_budget"
                       plan_equipment_budget_id="<?= $plan_equipment_budget->{$plan_equipment_budget::DB_TABLE_PK} ?>">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </li>
            </ul>
        </div>
        <div id="edit_plan_equipment_budget_<?= $plan_equipment_budget->{$plan_equipment_budget::DB_TABLE_PK} ?>"
             class="modal fade project_plan_task_equipment_form" role="dialog">
            <?php
            $this->load->view('projects/plans/project_plan_task_equipments/project_plan_task_equipment_form');
            ?>
        </div>
    </div>

    <?php
}