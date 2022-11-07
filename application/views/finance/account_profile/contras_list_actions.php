<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/22/2016
 * Time: 3:06 PM
 */

if($contra->employee_id == $this->session->userdata('employee_id') || check_permission('Administrative Actions')) {
    ?>
    <span class="pull-right">
    <button data-toggle="modal" data-target="#edit_contra_<?= $contra->{$contra::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_contra_<?= $contra->{$contra::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('finance/account_profile/contra_form'); ?>
    </div>
    <button class="btn btn-danger btn-xs delete_contra" contra_id="<?= $contra->{$contra::DB_TABLE_PK} ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
    <?php
}