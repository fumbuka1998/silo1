<?php $contract_id = $employee_contract->{$employee_contract::DB_TABLE_PK}; ?>

<span class="pull-right">

    <button class="btn btn-default btn-xs view_employee_contract" employee_contract_id="<?= $contract_id ?>">
        <i class="fa fa-eye"></i> More
    </button>

   <?php  

   if($employee_contract_status=='active_contract' || $employee_contract_status=='incomplete_contract'){?>

     <div id="edit_more_employee_contract_<?= $contract_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('employees/contract_form'); ?>
     </div>
    <button data-toggle="modal" data-target="#edit_employee_contract_<?= $contract_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_employee_contract_<?= $contract_id ?>" class="modal fade contract_form" role="dialog">
        <?php $this->load->view('employees/contract_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_employee_contract" contract_selected_id="<?= $contract_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>

   <?php }  ?>


   <?php if($employee_contract_status=='closed_contract'){?>

    <button class="btn btn-success btn-xs activate_employee_contract" activate_contract_id="<?= $contract_id ?>">
        <i class="fa fa-check"></i> Activate
    </button>
   <?php  }?>

   <?php if($employee_contract_status=='active_contract'){?>
    <button data-toggle="modal" data-target="#close_employee_contract_<?= $contract_id ?>"
            class="btn btn-warning btn-xs">
        <i class="fa fa-close"></i> Close
    </button>

    <div id="close_employee_contract_<?= $contract_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('employees/close_contract_form'); ?>
    </div>


   <?php }?>
</span>
