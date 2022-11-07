<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/23/2016
 * Time: 11:29 AM
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_measurement_unit_<?= $unit->{$unit::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_measurement_unit_<?= $unit->{$unit::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('inventory/settings/measurement_unit_form'); ?>
    </div>
    <button unit_id="<?= $unit->{$unit::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_measurement_unit">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
