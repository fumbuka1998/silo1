<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 4/24/2017
 * Time: 5:54 PM
 */

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_casual_labour_type_<?= $type->{$type::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_casual_labour_type_<?= $type->{$type::DB_TABLE_PK} ?>" class="modal fade"
         role="dialog">
        <?php $this->load->view('human_resources/settings/casual_labour_type_form'); ?>
    </div>
    <button type_id="<?= $type->{$type::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_casual_labour_type">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
