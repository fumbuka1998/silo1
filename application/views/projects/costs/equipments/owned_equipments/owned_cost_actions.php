
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_owned_equipment_cost_item_<?= $Owned_equipment_cost->{$Owned_equipment_cost::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_owned_equipment_cost_item_<?= $Owned_equipment_cost->{$Owned_equipment_cost::DB_TABLE_PK} ?>" class="modal fade owned_equipment_cost_form"
         role="dialog">
        <?php $this->load->view('projects/costs/equipments/owned_equipments/owned_equipment_cost_form');?>
    </div>

    <button owned_equipment_cost_id="<?= $Owned_equipment_cost->{$Owned_equipment_cost::DB_TABLE_PK} ?>" class="btn btn-xs btn-danger delete_owned_equipment_cost"><i class="fa fa-trash"></i> Delete</button>
</span>
