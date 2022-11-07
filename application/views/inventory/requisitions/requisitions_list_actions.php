<span class="pull-right">
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
            <a  target="_blank" href="<?= base_url('inventory/preview_requisition/'.$requisition->{$requisition::DB_TABLE_PK}) ?>">
                <i class="fa fa-clipboard"></i> Sheet
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
        <?php $this->load->view('inventory/requisitions/requisition_attachments_modal'); ?>
    </div>
    <?php

        $can_edit = ($requisition->status == 'PENDING' || $requisition->status == 'INITIATED') && ($requisition->requester_id == $this->session->userdata('employee_id') ||
                ($requisition->status == 'INITIATED' && $requisition->initiator_id == $this->session->userdata('employee_id')) || (isset($project) && $project->manager_access()));
    $has_actions = $requisition->status != 'APPROVED' && $requisition->status != 'DECLINED';
        if($has_actions) {
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
                    if ($requisition->status == 'PENDING' && check_permission('Requisitions Approval')) {
                        ?>
                        <li>
                            <button data-toggle="modal"
                                    data-target="#approve_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                                    class="btn btn-block btn-success btn-xs">
                                <i class="fa fa-check-circle"></i> Approve
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

            <div id="edit_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                 class="modal fade requisition_form" role="dialog">
                <?php $this->load->view('inventory/requisitions/requisition_form'); ?>
            </div>

            <?php if($requisition->status == 'PENDING' && check_permission('Requisitions Approval')) { ?>
                <div id="approve_requisition_<?= $requisition->{$requisition::DB_TABLE_PK} ?>"
                     class="modal fade requisition_approval_form" role="dialog">
                    <?php $this->load->view('inventory/requisitions/requisition_approval_form'); ?>
                </div>
                <?php
            }
        }
    ?>
</span>
