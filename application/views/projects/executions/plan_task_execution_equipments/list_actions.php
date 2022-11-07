<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 7/27/2018
 * Time: 1:01 PM
 */
?>
<div style="width: 100%">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            Actions
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="#"  data-toggle="modal" data-target="#edit_plan_equipment_execution_<?= $plan_equipment_execution->{$plan_equipment_execution::DB_TABLE_PK} ?>"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </li>
            <li>
                <a style="color: white" href="#" class="btn btn-danger btn-xs delete_plan_equipment_budget" plan_equipment_execution_id="<?= $plan_equipment_execution->{$plan_equipment_execution::DB_TABLE_PK} ?>">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </li>
        </ul>
    </div>
    <div id="edit_plan_equipment_execution_<?= $plan_equipment_execution->{$plan_equipment_execution::DB_TABLE_PK} ?>" class="modal fade plan_task_execution_equipment_form" role="dialog">
        <?php
        $this->load->view('projects/executions/plan_task_execution_equipments/plan_task_execution_equipment_form');
        ?>
    </div>
</div>