<?php if($project_status != 'closed' && (check_privilege('Project Actions') || $project->manager_access())){ ?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_equipment_budget_item_<?= $Equipment_budget->{$Equipment_budget::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_equipment_budget_item_<?= $Equipment_budget->{$Equipment_budget::DB_TABLE_PK} ?>" class="modal fade equipment_budget_form"
         role="dialog">
        <?php $this->load->view('projects/budgets/equipment/equipment_budget_form');?>
    </div>

    <button equipment_budget_id="<?= $Equipment_budget->{$Equipment_budget::DB_TABLE_PK} ?>" class="btn btn-xs btn-danger delete_equipment_budget"><i class="fa fa-trash"></i> Delete</button>
</span>
<?php } ?>
