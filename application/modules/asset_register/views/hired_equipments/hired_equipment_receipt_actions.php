

<?php

$hired_equipment_receipt_id = $hired_equipment_receipt->{$hired_equipment_receipt::DB_TABLE_PK};
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#view_hired_equipment_item_<?= $hired_equipment_receipt_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-eye"></i> view Details
    </button>
    <div id="view_hired_equipment_item_<?= $hired_equipment_receipt_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hired_equipment_item_details'); ?>
    </div>


    <button data-toggle="modal" data-target="#edit_hired_equipment_item_<?= $hired_equipment_receipt_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_hired_equipment_item_<?= $hired_equipment_receipt_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('equipment_receipt_form'); ?>
    </div>


    <button class="btn btn-danger btn-xs delete_hired_equipment_receipt" hired_equipment_receipt_id="<?= $hired_equipment_receipt_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>