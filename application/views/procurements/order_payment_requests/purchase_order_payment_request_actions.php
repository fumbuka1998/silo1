<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/24/2018
 * Time: 10:44 AM
 */

$payment_request_id = $payment_request->{$payment_request::DB_TABLE_PK};
$employee_id = $this->session->userdata('employee_id');
$string_for_email = false;
$print = true;
?>

<span>
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
             Preview
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a class="btn btn-default btn-xs" target="_blank"
                   href="<?= base_url('procurements/preview_purchase_order_payment_request/'.$payment_request_id) ?>" role="button">
                    <i class="fa fa-clipboard"></i>Request
                </a>
            </li>
            <?php
                if(in_array($payment_request_id,$approved_payment_requests)) {
                    ?>
                    <li>
                        <a class="btn btn-default btn-xs" target="_blank"
                           href="<?= base_url('procurements/preview_approved_purchase_order_payments/' . $payment_request_approval_id) ?>"
                           role="button">
                            <i class="fa fa-check-circle"></i>Approval
                        </a>
                    </li>
            <?php
                }

                if($payment_request->status != 'REJECTED'){
            ?>
            <li>
                <a data-toggle="modal" data-target="#order_payment_request_attachments_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>" href="#">
                    <i class="fa fa-paperclip"></i> Attachments
                    <?= ''  ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>

    <?php
    if($payment_request->status == 'PENDING') {
        $can_edit = $payment_request->requester_id == $this->session->userdata('employee_id') && !$last_approval;
        if($last_approval) {
            $can_approve = $payment_request->status != 'APPROVED' && $current_approval_level && in_array($employee_id, $current_approval_level->can_approve_positions()) && (is_null($last_approval->forward_to) || $last_approval->forward_to == $employee_id);
        } else {
            $can_approve = $payment_request->status != 'APPROVED' && ((is_null($payment_request->forward_to) && $current_approval_level && in_array($employee_id, $current_approval_level->can_approve_positions())) || ($payment_request->forward_to == $employee_id));
        }
        if ($can_approve || $can_edit) {
            ?>
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                             Actions
                        </button>
                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                <ul class="dropdown-menu" role="menu">
                    <?php
                    if ($can_approve) {
                        if (!in_array($payment_request_id, $approved_payment_requests)) {
                            ?>
                            <li>
                                    <a style="color: white" href="#" data-toggle="modal"
                                       data-target="#approve_purchase_order_payment_request_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>"
                                       class="btn btn-success btn-xs">
                                        <i class="fa fa-check-circle"></i> Approve
                                    </a>
                                </li>
                        <?php }
                    }
                    if ($can_edit) {
                        ?>
                        <li>
                            <a data-toggle="modal"
                               data-target="#edit_payment_request_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>"
                               class="btn btn-default btn-xs">
                                <i class="fa fa-edit"></i>Edit
                            </a>
                        </li>
                        <li>
                            <a style="color: white" class="btn btn-danger btn-xs delete_payment_request"
                               payment_request_id="<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>">
                                <i class="fa fa-trash"></i>Delete
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <?php
        }

        if ($can_edit) {
            ?>

            <div id="edit_payment_request_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>"
                 class="modal fade order_payment_request_form" role="dialog">
            <?php
            $data['payment_request'] = $payment_request;
            $this->load->view('procurements/order_payment_requests/order_payment_request_form', $data);
            ?>
        </div>

        <?php }

        if ($can_approve) { ?>
            <div id="approve_purchase_order_payment_request_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>"
                 class="modal fade order_payment_request_approval_form" role="dialog">
            <?php
                $data['payment_request'] = $payment_request;
                $this->load->view('procurements/order_payment_requests/order_payment_request_approval_form', $data);
                ?>
            </div>
        <?php } ?>
    <?php
    }
    ?>

    <div id="order_payment_request_attachments_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>"
     class="modal fade purchase_order_payment_request_attachements_modal" tabindex="-1" role="dialog">
        <?php
        $this->load->view('procurements/order_payment_requests/purchase_order_payment_request_attachements_modal');
        ?>
    </div>
</span>

