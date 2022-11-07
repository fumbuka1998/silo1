
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_hired_equipment_cost_item_<?= $hired_equipment_cost->{$hired_equipment_cost::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_hired_equipment_cost_item_<?= $hired_equipment_cost->{$hired_equipment_cost::DB_TABLE_PK} ?>" class="modal fade hired_equipment_cost_form"
         role="dialog">
        <?php $this->load->view('projects/costs/equipments/hired_equipments/hired_equipment_cost_form');?>
    </div>

    <button hired_equipment_cost_id="<?= $hired_equipment_cost->{$hired_equipment_cost::DB_TABLE_PK} ?>" class="btn btn-xs btn-danger delete_hired_equipment_cost"><i class="fa fa-trash"></i> Delete</button>
</span>
