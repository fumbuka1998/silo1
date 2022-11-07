<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/4/2018
 * Time: 12:38 AM
 */

if(check_privilege('Project Actions')) {

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
                       data-target="#edit_project_plan_task_<?= $project_plan_task->{$project_plan_task::DB_TABLE_PK} ?>"
                       class="btn btn-default btn-xs">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
                <li>
                    <a style="color: white" href="#" class="btn btn-danger btn-xs delete_project_plan_task"
                       project_plan_task_id="<?= $project_plan_task->{$project_plan_task::DB_TABLE_PK} ?>">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </li>
            </ul>
        </div>
        <div id="edit_project_plan_task_<?= $project_plan_task->{$project_plan_task::DB_TABLE_PK} ?>"
             class="modal fade project_plan_task_form" role="dialog">
            <?php $this->load->view('projects/plans/project_plan_tasks/project_plan_task_form'); ?>
        </div>
    </div>

    <?php
}