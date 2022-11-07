<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/14/2018
 * Time: 7:15 PM
 */
?>

<span class="pull-right">
    <a href="<?= base_url('tenders/tender_profile/'.$tender->{$tender::DB_TABLE_PK})?>" class="btn btn-xs btn-default" role="button">
        <i class="fa fa-eye"></i> Preview
    </a>
    <?php if($tender->created_by == $this->session->userdata('employee_id') || check_permission('Administrative Actions')){ ?>
    <button class="btn btn-danger btn-xs delete_tender" tender_id="<?= $tender->{$tender::DB_TABLE_PK} ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
    <?php } ?>
</span>
