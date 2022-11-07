<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/23/2018
 * Time: 10:36 AM
 */

?>
<span>
<div class="btn-group">
    <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
        Actions
    </button>
    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a class="btn btn-block btn-xs" data-toggle="modal" style="text-align: left"
               data-target="#requisition_transaction_documents_<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                <i class="fa fa-bookmark"></i> Documents
            </a>
        </li>
        <li>
            <a  target="_blank" href="<?= base_url('requisitions/preview_sub_contract_requisition_approved_chains/'.$requisition->{$requisition::DB_TABLE_PK}) ?>">
                <i class="fa fa-chain"></i> Approval Chain
            </a>
        </li>
        <li>
            <a data-toggle="modal" data-target="#sub_contract_requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>" href="#">
                <i class="fa fa-paperclip"></i> Attachments
            </a>
        </li>
        <?php
        if($requisition->status == 'PENDING') {
            $data['last_approval'] = $requisition->last_approval();
            $can_edit = ($requisition->requester_id == $this->session->userdata('employee_id') && !$data['last_approval']);

            $data['current_approval_level'] = $requisition->current_approval_level();

            if ($can_edit) {
                ?>
                <li>
                    <a class="btn btn-block btn-xs" data-toggle="modal"
                       data-target="#edit_sub_contract_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
                <li>
                    <a style="color: white" sub_contract_requisition_id="<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                            class="btn  btn-block btn-xs btn-danger delete_sub_contract_requisition"><i
                            class="fa fa-trash"></i> Delete
                    </a>
                </li>
                <?php
            }
            if ($can_approve) {
                ?>
                <li>
                    <button class="btn btn-block btn-success btn-xs" data-toggle="modal"
                            data-target="#approve_sub_contract_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                            class="btn btn-block btn-success btn-xs">
                        <i class="fa fa-check-square-o"></i> Act
                    </button>
                </li>
                <?php
            }
        }
        ?>
    </ul>
</div>
    <div id="sub_contract_requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
         class="modal fade sub_contract_payment_requisition_attachments_modal" tabindex="-1" role="dialog">
        <?php $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_attachments_modal'); ?>
    </div>
    <?php
        if($requisition->status == 'PENDING') {
            if($can_edit) {
                ?>

                <div id="edit_sub_contract_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                     class="modal fade sub_contract_payment_requisition_form" role="dialog">
                    <?php $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_form'); ?>
                </div>

                <?php
            }
                if($can_approve) {
            ?>
                <div id="approve_sub_contract_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                     class="modal fade sub_contract_payment_requisition_approval_form" role="dialog">
                    <?php $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_approval_form',$data); ?>
                </div>
                <?php
            }
        }
    ?>
    <div id="requisition_transaction_documents_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
         class="modal fade " role="dialog">
        <?php $this->load->view('requisitions/requisitions_list/approved_requisition_transactions_document_modal'); ?>
    </div>
</span>
