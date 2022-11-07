<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/22/2016
 * Time: 7:57 PM
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_material_item_category_<?= $category->{$category::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_material_item_category_<?= $category->{$category::DB_TABLE_PK} ?>" class="modal fade"
         role="dialog">
        <?php $this->load->view('inventory/settings/material_item_category_form'); ?>
    </div>
    <button category_id="<?= $category->{$category::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_material_item_category">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
