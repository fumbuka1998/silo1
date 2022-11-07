<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/23/2018
 * Time: 6:56 AM
 */

$edit = isset($payment_request);
?>

<div class="modal-dialog modal-lg">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Order Payment Request</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="request_date" class="control-label">Request Date</label>
                            <input type="text" class="form-control datepicker" required name="request_date" value="<?= $edit ? $payment_request->request_date : date('Y-m-d') ?>">
                            <input type="hidden" name="purchase_order_payment_request_id" value="<?= $edit ? $payment_request->{$payment_request::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="order" class="control-label">Order</label>
                            <?= form_dropdown('order_id',$order_dropdown_options, $edit ? $payment_request->purchase_order_id : '', 'class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="currency" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id',$currency_dropdown_options, $edit ? $payment_request->currency_id : '', 'class="form-control searchable"') ?>
                        </div>
                    </div>
                    <div class="col-xs-12 order_requests_container">
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Description</th><th style="width: 30%">Reference</th><th style="width: 20%">Amount</th><th>Claimed By</th><th></th>
                                </tr>
                                <tr style="display: none" class="invoice_row_template">
                                    <td>
                                        <input type="text" class="form-control" required name="description" value="">
                                        <input type="hidden" name="item_type" value="invoice">
                                    </td>
                                    <td>
                                        <?= form_dropdown('invoice_id', $edit ? $invoice_options : [], '', 'class="form-control"') ?>
                                    </td>
                                    <td>
                                        <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="">
                                        <input type="hidden" name="amount_buffer" value="">
                                    </td>
                                    <td>
                                        <input type="text" name="vendor_name" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                                <tr style="display: none" class="cash_row_template">
                                    <td>
                                        <input type="text" class="form-control" required name="description" value="">
                                        <input type="hidden" name="item_type" value="cash">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" required name="reference" value="">
                                    </td>
                                    <td>
                                        <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="">
                                        <input type="hidden" name="amount_buffer" value="">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="claimed_by">
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!$edit){
                                ?>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" required name="description" value="">
                                        <input type="hidden" name="item_type" value="invoice">
                                    </td>
                                    <td>
                                        <?= form_dropdown('invoice_id', [], '', 'class="form-control searchable"') ?>
                                    </td>
                                    <td>
                                        <input style="text-align: right" type="text" class="form-control number_format" required name="amount" previous_value="0">
                                        <input type="hidden" name="amount_buffer" value="">
                                    </td>
                                    <td>
                                        <input type="text" name="vendor_name" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            <?php }else {
                                $invoice_items = $payment_request->invoice_items();
                                foreach ($invoice_items as $invoice_item){
                                    $invoice = $invoice_item->invoice();
                                    ?>
                                    <tr>
                                        <td style="width: 20%">
                                            <input type="text" class="form-control" required name="description" value="<?= $invoice_item->description ?>">
                                            <input type="hidden" name="item_type" value="invoice">
                                        </td>
                                        <td style="width: 20%">
                                            <?= form_dropdown('invoice_id',[$invoice_item->invoice_id=>$invoice->reference],'', 'class="form-control searchable"') ?>
                                        </td>
                                        <td>
                                            <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="<?= $invoice_item->requested_amount ?>">
                                            <input type="hidden" name="amount_buffer" value="">
                                        </td>
                                        <td>
                                            <input type="text" readonly class="form-control" value="<?= $invoice->stakeholder()->stakeholder_name ?>" >
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                <?php
                                }
                                $cash_items = $payment_request->cash_items();
                                foreach ($cash_items as $cash_item){
                                    ?>
                                    <tr>
                                        <td style="width: 20%">
                                            <input type="text" class="form-control" required name="description" value="<?= $cash_item->description ?>">
                                            <input type="hidden" name="item_type" value="cash">
                                        </td>
                                        <td style="width: 20%">
                                            <input type="text" class="form-control" required name="reference" value="<?= $cash_item->reference ?>">
                                        </td>
                                        <td>
                                            <input style="text-align: right" type="text" class="form-control number_format" required name="amount" value="<?= $cash_item->requested_amount ?>">
                                            <input type="hidden" name="amount_buffer" value="">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="claimed_by" value="<?= $cash_item->claimed_by ?>">
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                <?php }
                            }?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2">TOTAL</th>
                                <th style="text-align: right" class="total_amount_display"></th>
                                <th colspan="2"></th>
                            </tr>
                            <tr>
                                <th style="text-align: right">Forward For Special Approval</th>
                                <td>
                                    <?= form_dropdown('forward_to', $first_approver_options, $edit ? $payment_request->forward_to : '', ' class="form-control searchable " ') ?>
                                </td>
                                <th colspan="3">
                                    <span class="pull-right">
                                        <button type="button" class="btn btn-xs btn-default invoice_row_adder">
                                        <i class="fa fa-plus"></i> Invoice
                                        </button>
                                        <!--<button type="button" class="btn btn-xs btn-default cash_row_adder">
                                            <i class="fa fa-plus"></i> Others
                                        </button>-->
                                    </span>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xs-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea style="width: 100%; height: 30%" type="text" class="form-control" required name="remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default submit_order_payment_request">Submit</button>
            </div>
        </div>
    </form>
</div>
