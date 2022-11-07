<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/22/2016
 * Time: 3:53 AM
 */
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_department_<?= $department->{$department::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_department_<?= $department->{$department::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('human_resources/departments/department_form'); ?>
    </div>
    <button <?= $number_of_employees > 0 ? 'disabled' : '' ?> department_id="<?= $department->{$department::DB_TABLE_PK} ?>" class="btn btn-xs btn-danger delete_department"><i class="fa fa-trash"></i> Delete</button>
</span>
