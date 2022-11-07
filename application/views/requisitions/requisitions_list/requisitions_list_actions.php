<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/8/2016
 * Time: 4:07 PM
 */

if(check_privilege('Requisition Actions')){
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
        <?php if(check_privilege('Approval Chain')){ ?>
        <li>
            <a  target="_blank" href="<?= base_url('requisitions/preview_requisition_approved_chains/'.$requisition->{$requisition::DB_TABLE_PK}) ?>">
                <i class="fa fa-chain"></i> Approval Chain
            </a>
        </li>
        <?php } ?>
        <li>
            <a data-toggle="modal" data-target="#requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>" href="#">
                <i class="fa fa-paperclip"></i> Attachments
            </a>
        </li>

        <?php

        if($requisition->status == 'PENDING' || $requisition->status == 'INCOMPLETE') {
            $data['last_approval'] = $requisition->last_approval();
            $can_edit = ($requisition->requester_id == $this->session->userdata('employee_id') && !$data['last_approval']);

            $data['current_approval_level'] = $requisition->current_approval_level();

            if ($can_edit) {
                ?>
                <li>
                    <a class="btn btn-block btn-xs" data-toggle="modal"
                       data-target="#edit_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
                <li>
                    <a style="color: white" requisition_id="<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                            class="btn  btn-block btn-xs btn-danger delete_requisition"><i
                            class="fa fa-trash"></i> Delete
                    </a>
                </li>
                <?php
            }

            $special_level_approval = $requisition->special_level_approval();
            if($can_override_prev && !$forwarded_to_employee && !$special_level_approval){
                $can_approve = $requisition->status != 'INCOMPLETE' && $requisition->status != 'APPROVED';
            } else if($forwarded_to_employee && !$special_level_approval){
                $can_approve = $requisition->status != 'INCOMPLETE' && $data['current_approval_level'] && $this->session->userdata('employee_id') == $requisition->forwarded_to_employee_approval(true);
            } else if($special_level_approval){
                $can_approve = $requisition->status != 'INCOMPLETE' && in_array($this->session->userdata('employee_id'),$employees_with_special_approval);
            } else {
                $can_approve = $requisition->status != 'INCOMPLETE' && $data['current_approval_level'] && in_array($this->session->userdata('employee_id'),$data['current_approval_level']->can_approve_positions());
            }

            if ($can_approve) {
                ?>
                <li>
                    <button class="btn btn-block btn-success btn-xs" data-toggle="modal"
                            data-target="#approve_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
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
    <div id="requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
         class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('requisitions/requisitions_list/requisition_attachments_modal'); ?>
    </div>
    <?php
        if($requisition->status == 'PENDING' || $requisition->status == 'INCOMPLETE') {
            if($can_edit) {
                ?>

                <div id="edit_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                     class="modal fade requisition_form" role="dialog">
                    <?php $this->load->view('requisitions/requisitions_list/requisition_form'); ?>
                </div>

                <?php
            }
                if($can_approve) {
            ?>
                <div id="approve_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                     class="modal fade requisition_approval_form" role="dialog">
                    <?php $this->load->view('requisitions/requisitions_list/requisition_approval_form',$data); ?>
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
<?php } ?>
