<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/22/2016
 * Time: 11:36 AM
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_job_position_<?= $position->{$position::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_job_position_<?= $position->{$position::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1"
         role="dialog">
        <?php $this->load->view('human_resources/settings/job_position_form'); ?>
    </div>
    <button <?= $number_of_employees > 0 ? 'disabled' : '' ?> position_id="<?= $position->{$position::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_job_position">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
