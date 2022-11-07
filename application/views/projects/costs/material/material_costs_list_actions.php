<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 10/2/2016
 * Time: 2:47 PM
 */

$cost_id = $item->{$item::DB_TABLE_PK};

if(check_privilege('Edit material cost') || check_permission('Administrative Actions')) {
    ?>
    <span class="pull-right">
        <button data-toggle="modal" data-target="#edit_material_cost_<?= $cost_id ?>"
                class="btn btn-default btn-xs">
            <i class="fa fa-edit"></i> Edit
        </button>
        <?php if ($cost_type == 'executions') { ?>
            <div id="edit_material_cost_<?= $cost_id ?>" class="modal fade plan_task_execution_material_cost_form"
                 role="dialog">
            <?php $this->load->view('projects/executions/plan_task_execution_materials/plan_task_execution_material_cost_form'); ?>
        </div>
        <?php } else { ?>
            <div id="edit_material_cost_<?= $cost_id ?>" class="modal fade material_cost_form"
                 role="dialog">
            <?php $this->load->view('projects/costs/material/material_cost_form'); ?>
        </div>
        <?php }

        if($cost_type=='executions'){
            ?>
            <button class="btn btn-xs btn-danger execution_cost_item_delete" item_id="<?= $cost_id ?>"
                    cost_id="<?= $cost_id ?>">
            <i class="fa fa-trash"></i> Delete
        </button>
        <?php } else {?>
            <button class="btn btn-xs btn-danger cost_item_delete" item_id="<?= $cost_id ?>"
                    cost_id="<?= $cost_id ?>">
            <i class="fa fa-trash"></i> Delete
        </button>
        <?php } ?>
    </span>
    <?php
}

