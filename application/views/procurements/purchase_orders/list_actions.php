<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/13/2016
 * Time: 12:05 PM
 */
if (check_privilege('Procurement Actions')) {
?>
    <div style="width: 100% !important; overflow-x: visible">
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                View
            </button>
            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a target="_blank" href="<?= base_url('procurements/preview_purchase_order/' . $order->{$order::DB_TABLE_PK}) ?>" class="btn btn-xs btn-default">
                        <i class="fa fa-eye"></i> Preview
                    </a>
                </li>
                <li>
                    <a target="_blank" href="<?= base_url('procurements/view_purchase_order_summary/' . $order->{$order::DB_TABLE_PK}) ?>" class="btn btn-xs btn-default">
                        Order Summary
                    </a>
                </li>
                <li>
                    <a target="_blank" href="<?= base_url('procurements/preview_goods_received_report/' . $order->{$order::DB_TABLE_PK}) ?>" class="btn btn-xs btn-default">
                        Check &amp; Balance
                    </a>
                </li>
                <li>
                    <a target="_blank" href="<?= base_url('procurements/preview_goods_received_report/' . $order->{$order::DB_TABLE_PK}) ?>/true" class="btn btn-xs btn-default">
                        Unreceived
                    </a>
                </li>
                <li>
                    <a href="#" data-toggle="modal" data-target="#preview_invoices_<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                        Invoices
                    </a>
                </li>
                <li>
                    <a type="button" title="Order Attachments" class="btn btn-default btn-xs" data-toggle="modal" data-target="#attachment_<?= $order->order_id ?>">
                        Attachments
                    </a>
                </li>
            </ul>
        </div>
        <?php
        $can_close = $order->handler_id == $this->session->userdata('employee_id') && $order->status != 'CLOSED';
        $can_edit = ($order->employee_id == $this->session->userdata('employee_id') && ($order->status != 'PARTIAL RECEIVED' && $order->status != 'RECEIVED' && $order->status != 'CANCELLED' && $order->status != 'CLOSED') || check_permission('Administrative Actions'));
        $can_receive = ($order->status == 'PENDING' || $order->status == 'PARTIAL RECEIVED') && check_privilege('Orders Approval') && $order->status != 'CANCELLED' && $order->handler_id == $this->session->userdata('employee_id') && $order->receivable();
        if ($can_edit || $can_receive || $can_close) {
        if ($can_edit) { ?>

            <div id="cancel_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
                <?php $this->load->view('procurements/purchase_orders/purchase_order_cancellation_form'); ?>
            </div>

            <div id="edit_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="modal fade purchase_order_form" role="dialog">
                <?php $this->load->view('procurements/purchase_orders/purchase_order_form'); ?>
            </div>
        <?php }
        if ($can_receive) { ?>
            <div id="receiver_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="modal fade purchase_order_receive_form" role="dialog">
                <?php $this->load->view('procurements/purchase_orders/receive_purchase_order_form') ?>
            </div>

        <?php
        }
        if ($can_close) {
        ?>
            <div id="close_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
                <?php $this->load->view('procurements/purchase_orders/purchase_order_closing_form'); ?>
            </div>
        <?php
        }
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
                <?php if ($can_receive) { ?>
                    <li>
                        <a style="color: white" href="#" data-toggle="modal" data-target="#receiver_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-success btn-xs">
                            <i class="fa fa-check-circle"></i> Receive
                        </a>
                    </li>
                <?php }
                if ($can_edit) { ?>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#edit_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#cancel_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs">
                            <i class="fa fa-window-close"></i> Cancel
                        </a>
                    </li>
                    <?php if(1<0) { ?>
                        <li>
                            <a style="color: white" href="#" class="btn btn-danger btn-xs delete_purchase_order"
                               order_id="<?= $order->{$order::DB_TABLE_PK} ?>">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </li>
                        <?php
                    }
                }
                if ($can_close) { ?>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#close_purchase_order_<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-default btn-xs" order_id="<?= $order->{$order::DB_TABLE_PK} ?>">
                            <i class="fa fa-archive"></i> Close
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php
        }
        ?>
        <div id="attachment_<?= $order->order_id ?>" class="modal fade procurement_attachment_modal" role="dialog">
            <?php $this->load->view('attachments/purchase_order_related/index', ['reffering_to' => 'ORDER']) ?>
        </div>
        <div id="preview_invoices_<?= $order->{$order::DB_TABLE_PK} ?>" class="modal fade purchase_order_invoices" role="dialog">
            <?php $this->load->view('procurements/purchase_orders/purchase_order_invoices'); ?>
        </div>
    </div>
<?php } ?>