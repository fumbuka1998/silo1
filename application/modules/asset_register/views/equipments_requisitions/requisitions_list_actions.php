<span>
<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/8/2016
 * Time: 4:07 PM
 */
?>

<div class="btn-group">
    <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown"><i class="fa fa-eye"></i> Preview</button>
    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <a  target="_blank" href="<?= base_url('requisitions/preview_requisition/'.$requisition->{$requisition::DB_TABLE_PK}) ?>">
                <i class="fa fa-clipboard"></i> Sheet
            </a>
        </li>
        <li>
            <a  target="_blank" href="<?= base_url('requisitions/preview_requisition_approved_chains/'.$requisition->{$requisition::DB_TABLE_PK}) ?>">
                <i class="fa fa-chain"></i> Approval Chain
            </a>
        </li>
        <li>
            <a data-toggle="modal" data-target="#requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>" href="#">
                <i class="fa fa-paperclip"></i> Attachments
            </a>
        </li>
    </ul>
</div>
    <div id="requisition_attachments_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
         class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('requisitions/requisition_attachments_modal'); ?>
    </div>
    <?php

        if($requisition->status == 'PENDING') {
            $data['last_approval'] = $requisition->last_approval();
            $can_edit = $requisition->requester_id == $this->session->userdata('employee_id') && !$data['last_approval'] ;
            if($data['last_approval']){
                $data['current_approval_level'] = $data['last_approval']->approval_chain_level()->next_level();
            } else {
                $data['current_approval_level'] = $requisition->current_approval_level();
            }
            $can_approve = $data['current_approval_level'] && $data['current_approval_level']->job_position_id == $this->session->userdata('job_position_id');
            if($can_edit || $can_approve) {
                ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"> Actions
                    </button>
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <?php
                        if ($can_edit) {
                            ?>
                            <li>
                                <a class="btn btn-block btn-xs btn-default" data-toggle="modal"
                                   data-target="#edit_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </li>
                            <li>
                                <button type="button" requisition_id="<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                                        class="btn btn-block btn-xs btn-danger delete_requisition"><i
                                        class="fa fa-trash"></i> Delete
                                </button>
                            </li>
                            <?php
                        }
                        if ($can_approve) {
                            ?>
                            <li>
                                <button data-toggle="modal"
                                        data-target="#approve_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                                        class="btn btn-block btn-success btn-xs">
                                    <i class="fa fa-check-square-o"></i> Act
                                </button>
                            </li>
                            <li>
                                <button requisition_id="<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                                        class="btn btn-block btn-xs btn-danger decline_requisition"><i
                                        class="fa fa-close"></i> Decline
                                </button>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }

            if($can_edit) {
                ?>

                <div id="edit_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                     class="modal fade requisition_form" role="dialog">
                    <?php $this->load->view('equipments_requisitions/requisition_form'); ?>
                </div>

                <?php
            }
                if($can_approve) {
            ?>
                <div id="approve_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                     class="modal fade requisition_approval_form" role="dialog">
                    <?php $this->load->view('requisitions/requisition_approval_form',$data); ?>
                </div>
                <?php
            }
        }
    ?>
</span>
