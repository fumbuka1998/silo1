<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/31/2018
 * Time: 6:37 AM
 */

$last_approval_id =  $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : 0;
?>

<div class="modal-dialog modal-lg">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Order Payment Request Approval</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#order_payment_request_approval_tab_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>" data-toggle="tab">Request</a></li>
                                <li><a id="payment_request_attachments_activator" href="#order_payment_request_attachments_tab_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>"  data-toggle="tab">Attachments</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="active tab-pane" id="order_payment_request_approval_tab_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group col-md-4">
                                                <label for="approval_date" class="control-label">Approval Date</label>
                                                <input type="text" class="form-control datepicker" required name="approval_date" value="<?= date('Y-m-d') ?>">
                                                <input type="hidden" name="approval_chain_level_id" value="<?= $current_approval_level->{$current_approval_level::DB_TABLE_PK} ?>">
                                                <input type="hidden" name="purchase_order_payment_request_id" value="<?= $payment_request->{$payment_request::DB_TABLE_PK}?>">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Description</th><th>Reference</th><th>Amount</th><th>Claimed By</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php

                                                $invoice_items = $payment_request->invoice_items();
                                                foreach ($invoice_items as $invoice_item){
                                                    $invoice = $invoice_item->invoice();
                                                    ?>
                                                    <tr>
                                                    <td style="width: 20%">
                                                        <span class="form-control-static"><?= wordwrap($invoice_item->description,60,'<br/>') ?></span>
                                                        <input type="hidden"
                                                               name="purchase_order_payment_request_invoice_item_id"
                                                               value="<?= $invoice_item->{$invoice_item::DB_TABLE_PK} ?>">
                                                        <input type="hidden" name="item_type" value="invoice">
                                                    </td>
                                                    <td style="width: 20%">
                                                        <span class="form-control-static"><?= stringfy_dropdown_options([$invoice_item->invoice_id => $invoice->reference]) ?></span>
                                                    </td>
                                                    <?php
                                                    if($last_approval) {
                                                        $approved_item = $invoice_item->approved_item($last_approval_id);
                                                        $amount = $approved_item->approved_amount;
                                                        $claimed_by = $approved_item->claimed_by;
                                                        ?>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><?= $invoice_item->purchase_order_payment_request()->currency()->symbol ?></span>
                                                                <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="<?= round($amount,2) ?>">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <textarea type="text" rows="1" class="form-control" required name="claimed_by"><?= $invoice_item->invoice_id = $invoice->stakeholder()->stakeholder_name ?></textarea>
                                                        </td>
                                                        </tr>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><?= $invoice_item->purchase_order_payment_request()->currency()->symbol ?></span>
                                                                <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="<?= round($invoice_item->requested_amount,2) ?>">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" readonly value="<?= $invoice->stakeholder()->stakeholder_name ?>">
                                                        </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }

                                                $cash_items = $payment_request->cash_items();
                                                foreach ($cash_items as $cash_item) {
                                                    ?>
                                                    <tr>
                                                    <td style="width: 20%">
                                                        <span class="form-control-static"><?= wordwrap($cash_item->description, 60,'<br/>') ?></span>
                                                        <input type="hidden" name="purchase_order_payment_request_cash_item_id" value="<?= $cash_item->{$cash_item::DB_TABLE_PK} ?>">
                                                        <input type="hidden" name="item_type" value="cash">
                                                    </td>
                                                    <td style="width: 20%">
                                                        <span class="form-control-static"><?= $cash_item->reference ?></span>
                                                    </td>
                                                    <?php
                                                    if($last_approval) {
                                                        $approved_item = $cash_item->approved_item($last_approval_id);
                                                        $amount = $approved_item->approved_amount;
                                                        $claimed_by = $approved_item->claimed_by;
                                                        ?>
                                                        <td>
                                                            <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="<?= round($amount,2) ?>">
                                                        </td>
                                                        <td>
                                                            <textarea type="text" rows="1" class="form-control" required name="claimed_by"><?= $claimed_by ?></textarea>
                                                        </td>
                                                        </tr>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <td>
                                                            <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="<?= round($cash_item->requested_amount,2) ?>">
                                                        </td>
                                                        <td>
                                                            <textarea type="text" rows="1" class="form-control" required name="claimed_by"><?= $cash_item->claimed_by ?></textarea>
                                                        </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th colspan="2">TOTAL</th>
                                                    <th>
                                                        <span class="form-control-static pull-right total_amount_display"></span>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <?php
                                                $current_level = $payment_request->current_approval_level();
                                                $levels = $current_level->next_level();
                                                $next_level = !empty($levels) ? $levels : false;
                                                if($next_level){
                                                    ?>
                                                    <tr>
                                                        <th style="text-align: right" colspan="3">
                                                            Forward For Special Approval
                                                        </th>
                                                        <td>
                                                            <?= form_dropdown('forward_to', $to_foward_approval_level, '', ' class="form-control searchable" ') ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tfoot>
                                            </table>
                                            <div>
                                                <label for="comments">Comments</label>
                                                <textarea name="comments" class="form-control" style="width: 100%; height: 50%"><?= $last_approval ? $last_approval->comments : $payment_request->remarks ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="order_payment_request_attachments_tab_<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>">
                                    <div class="row">
                                        <?php if($payment_request->requester_id == $this->session->userdata('employee_id') || check_permission('Administrative Actions')){ ?>
                                            <form class="purchase_order_payment_request_attachment_form">
                                                <div class="form-group col-md-4">
                                                    <input type="file" name="file" class="form-control">
                                                    <input type="hidden" name="purchase_order_payment_request_id" value="<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>">
                                                </div>
                                                <div class="form-group col-md-7">
                                                    <input type="text" name="caption" class="form-control col-md-6" placeholder="Caption">
                                                </div>
                                                <button type="button" class="btn btn-primary btn-sm purchase_order_payment_request_attach">
                                                    <i class="fa fa-upload"></i> Upload
                                                </button>
                                            </form>
                                            <hr/>
                                        <?php } ?>
                                        <div class="payment_request_attachments_container table-responsive col-xs-12" purchase_order_payment_request_id="<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>">
                                            <?php $this->load->view('procurements/order_payment_requests/purchase_order_payment_request_attachments_table',['payment_request'=>$payment_request]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger reject_order_payment_request_approval">Reject</button>
                <button type="button" class="btn btn-sm btn-default submit_order_payment_request_approval">Submit</button>
            </div>
        </div>
    </form>
</div>

