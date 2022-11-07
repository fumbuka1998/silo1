<?php
/**
 * Created by PhpStorm.
 * User: miralearn
 * Date: 03/12/2018
 * Time: 11:30
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_material_stock_<?= $item->{$item::DB_TABLE_PK} ?>"
            class="btn btn-xs btn-default">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_material_stock_<?= $item->{$item::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
        <?php $this->load->view('inventory/material/edit_material_stock_form'); ?>
    </div>
</span>
