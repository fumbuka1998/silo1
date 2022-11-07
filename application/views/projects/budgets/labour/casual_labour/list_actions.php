<?php if($project_status != 'closed' && (check_privilege('Project Actions') || $project->manager_access())){ ?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_casual_labour_budget_item_<?= $item->{$item::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_casual_labour_budget_item_<?= $item->{$item::DB_TABLE_PK} ?>" class="modal fade casual_labour_budget_form"
         role="dialog">
        <?php $this->load->view('projects/budgets/labour/casual_labour/casual_labour_budget_form');?>
    </div>

    <button item_id="<?= $item->{$item::DB_TABLE_PK} ?>" class="btn btn-xs btn-danger budget_item_delete"><i class="fa fa-trash"></i> Delete</button>
</span>
<?php } ?>