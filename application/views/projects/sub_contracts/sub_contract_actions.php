<?php
$sub_contract_id = $sub_contract->{$sub_contract::DB_TABLE_PK};

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#view_project_sub_contract_<?= $sub_contract_id ?>" class="btn btn-default btn-xs">
        <i class="fa fa-eye"></i> Details
    </button>
    <div id="view_project_sub_contract_<?= $sub_contract_id ?>" sub_contract_id="<?= $sub_contract_id ?>" class="modal fade sub_contract_profile" role="dialog">
        <?php $this->load->view('projects/sub_contracts/sub_contract_profile');   ?>
    </div>

    <button data-toggle="modal" data-target="#edit_project_sub_contract_<?= $sub_contract_id ?>" class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>

    <div id="edit_project_sub_contract_<?= $sub_contract_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('projects/sub_contracts/sub_contract_form'); ?>
    </div>

    <button sub_contract_id="<?= $sub_contract_id ?>" class="btn btn-danger btn-xs delete_project_sub_contract">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>