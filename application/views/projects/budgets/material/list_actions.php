<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/19/2016
 * Time: 6:05 PM
 */

if($project_status != 'closed' && (check_privilege('Project Actions') || $project->manager_access())){
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_material_budget_item_<?= $cost_center_level . '_' . $item->{$item::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_material_budget_item_<?= $cost_center_level . '_' . $item->{$item::DB_TABLE_PK} ?>" class="modal fade material_budget_form"
         role="dialog">
        <?php $this->load->view('projects/budgets/material/material_budget_form');?>
    </div>

    <button item_id="<?= $item->{$item::DB_TABLE_PK} ?>" class="btn btn-xs btn-danger budget_item_delete"><i class="fa fa-trash"></i> Delete</button>
</span>
<?php } ?>