<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/15/2016
 * Time: 12:30 PM
 */

if(check_privilege('Inventory Actions')){
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_material_item_<?= $item->{$item::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_material_item_<?= $item->{$item::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
        <?php $this->load->view('inventory/material/material_item_form'); ?>
    </div>
    <button class="btn btn-danger btn-xs delete_material_item" item_id="<?= $item->{$item::DB_TABLE_PK} ?>" >
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
<?php } ?>
