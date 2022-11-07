<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 4/16/2017
 * Time: 9:38 AM
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_miscellaneous_budget_<?= $item->{$item::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_miscellaneous_budget_<?= $item->{$item::DB_TABLE_PK} ?>" class="modal fade miscellaneous_budget_form" role="dialog">
        <?php $this->load->view('budgets/miscellaneous/miscellaneous_budget_form'); ?>
    </div>
    <button item_id="<?= $item->{$item::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs budget_item_delete">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
