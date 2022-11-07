
<?php
$employee_bank_id = $employee_bank->{$employee_bank::DB_TABLE_PK};
//print_r([$employee_contract]);


?>
<span class="pull-right">

    <button data-toggle="modal" data-target="#edit_employee_bank_<?= $employee_bank_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i>&nbsp; Edit
    </button>
    <div id="edit_employee_bank_<?= $employee_bank_id ?>" class="modal fade" role="dialog">
        <?php  $this->load->view('employees/employee_banks/employee_bank_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_employee_bank" employee_bank_id="<?= $employee_bank_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
