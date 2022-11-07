<?php
    $edit = isset($payment_voucher);
?>
<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Expense Payment Voucher Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-4">
                        <label for="payment_date" class="control-label">Payment Date</label>
                        <input type="text" class="form-control datepicker" required name="payment_date" value="<?= $edit ? $payment_voucher->payment_date : '' ?>">
                        <input type="hidden" name="credit_account_id" value="<?= $account->{$account::DB_TABLE_PK} ?>">
                        <input type="hidden" name="payment_voucher_id" value="<?= $edit ? $payment_voucher->{$payment_voucher::DB_TABLE_PK} : '' ?>">
                        <input type="hidden" name="cash_requisition_id" value="<?= isset($requisition_id) ? $requisition_id : '' ?>">
                        <input type="hidden" name="payment_voucher_type" value="<?= $account_is_site_petty_cash ? 'DIRECT EXPENSE' : 'INDIRECT EXPENSE' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="payee" class="control-label">Payee</label>
                        <input type="text" class="form-control" required name="payee" value="<?= $edit ? $payment_voucher->payee : '' ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reference" class="control-label">Reference</label>
                        <input type="text" class="form-control" name="reference" value="<?= $edit ? $payment_voucher->reference : '' ?>">
                    </div>
                    <div class="col-xs-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="27%">Debit Account</th><th>Cost Center Type</th><th width="27%">Cost Center</th><th>Amount</th><th>Description</th><th></th>
                                </tr>
                                <tr style="display: none" class="row_template">
                                    <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,'',' class="form-control"') ?></td>
                                    <td><?= form_dropdown('cost_center_type',$expense_pv_cost_center_type_options,'',' class="form-control"') ?></td>
                                    <td><?= form_dropdown('cost_center_id',$expense_pv_cost_center_options,'',' class="form-control"') ?></td>
                                    <td><input class="form-control number_format" type="text" name="amount"></td>
                                    <td><textarea rows="1" class="form-control" name="description"></textarea></td>
                                    <td>
                                        <span class="pull-right">
                                            <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </span>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($edit) {
                                $pv_items = $payment_voucher->payment_voucher_items();
                                foreach ($pv_items as $pv_item) {
                                    $debit_account = $pv_item->debit_account();
                                    ?>
                                    <tr>
                                        <td>
                                            <?= form_dropdown('debit_account_id', [$pv_item->debit_account_id => $debit_account->account_name], $pv_item->debit_account_id, ' class="form-control"') ?>
                                        </td>
                                        <td><?= form_dropdown('cost_center_type', $expense_pv_cost_center_type_options, '', ' class="form-control"') ?></td>
                                        <td><?= form_dropdown('cost_center_id', $expense_pv_cost_center_options, '', ' class="form-control"') ?></td>
                                        <td><input class="form-control number_format" type="text" name="amount"
                                                   value="<?= number_format($pv_item->amount) ?>"></td>
                                        <td><textarea rows="1" class="form-control"
                                                      name="description"><?= $pv_item->description ?></textarea></td>
                                        <td>
                                            <span class="pull-right">
                                                <button type="button" class="btn btn-xs btn-danger row_remover"><i
                                                        class="fa fa-close"></i></button>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else if(isset($requisition_id)){
                                $requisition_items = $material_items;
                                foreach ($requisition_items as $item){
                                    ?>
                                    <tr>
                                        <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,'',' class="form-control searchable"') ?></td>
                                        <td><?= form_dropdown('cost_center_type',$expense_pv_cost_center_type_options,'',' class="form-control"') ?></td>
                                        <td><?= form_dropdown('cost_center_id',$expense_pv_cost_center_options,'',' class="form-control searchable"') ?></td>
                                        <td><input class="form-control number_format" type="text" name="amount" value="<?= $item->approved_rate*$item->approved_quantity ?>"></td>
                                        <td><textarea readonly rows="1" class="form-control" name="description"><?= $item->description ?></textarea></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                }
                            } else { ?>
                                    <tr>
                                        <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,'',' class="form-control searchable"') ?></td>
                                        <td><?= form_dropdown('cost_center_type',$expense_pv_cost_center_type_options,'',' class="form-control"') ?></td>
                                        <td><?= form_dropdown('cost_center_id',$expense_pv_cost_center_options,'',' class="form-control searchable"') ?></td>
                                        <td><input class="form-control number_format" type="text" name="amount"></td>
                                        <td><textarea rows="1" class="form-control" name="description"></textarea></td>
                                        <td></td>
                                    </tr>
                                <?php }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Total</th><th></th>
                                    <th>
                                        <?php if(!isset($requisition_id)){ ?>
                                        <button type="button" class="btn btn-xs btn-default row_adder"><i class="fa fa-plus"></i> Add Row</button>
                                        <?php } ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea name="remarks" class="form-control"><?= $edit ? $payment_voucher->remarks : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_expense_payment_voucher">Submit</button>
        </div>
        </form>
    </div>
</div>