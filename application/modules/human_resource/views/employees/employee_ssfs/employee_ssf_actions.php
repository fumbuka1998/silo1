
<?php
$employee_ssf_id = $employee_ssf->{$employee_ssf::DB_TABLE_PK};
//print_r([$employee_ssf_id]);


?>
<span class="pull-right">

    <button data-toggle="modal" data-target="#edit_employee_ssf_<?= $employee_ssf_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_employee_ssf_<?= $employee_ssf_id ?>" class="modal fade" role="dialog">
        <?php  $this->load->view('employees/employee_ssfs/employee_ssf_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_employee_ssf" employee_ssf_id="<?= $employee_ssf_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
