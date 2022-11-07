<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/16/2018
 * Time: 6:42 PM
 */
$edit = isset($payment);
$cost_center_options = isset($cost_center_options) ? $cost_center_options : [];
?>

<div style="width: 80%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Expense Payment</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="payment_date control_label">Payment Date</label>
                            <input type="text" class="form-control datepicker" name="payment_date" value="<?= $edit ? $payment->payment_date : date('Y-m-d') ?>" >
                            <input type="hidden" name="payment_voucher_id" value="<?= $edit ? $payment->{$payment::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="credit_account control_label">Credit Account</label>
                            <?= form_dropdown('credit_account_id', $credit_account_options,$edit ? $payment->credit_account_id : '', ' class="form-control searchable"') ?>

                        </div>
                        <div class="form-group col-md-4">
                            <label for="payee control_label">Payee</label>
                            <input type="text" class="form-control" name="payee" value="<?= $edit ? $payment->payee : '' ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label for="reference control_label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="<?= $edit ? $payment->reference  : '' ?>" >
                        </div>
                        <div class="form-group col-md-4">
                            <label for="currecy control_label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options,$edit ? $payment->currency_id : 1, ' class="form-control searchable" id="payment_currency_option"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exchange_rate control_label">Exchange Rate</label>
                            <input type="text" <?= $edit ? '' : 'readonly' ?> class="form-control display_exchange_rate" name="exchange_rate" value="<?= $edit ? $payment->exchange_rate : '1' ?>">
                        </div>

                        <div class="col-xs-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Description</th><th>Cost Center Type</th><th>Cost Center</th><th>Amount</th><th>Debit Account</th><th></th>
                                </tr>

                                <!-- Payment row template-->
                                <tr style="display: none" class="payment_row_template">
                                    <td class="col-md-3">
                                        <textarea class="form-control" rows="1" name="description"></textarea>
                                    </td>
                                    <td>
                                        <?= form_dropdown('cost_center_type',$cost_center_type_options,'',' class="form-control" ') ?>
                                    </td>
                                    <td class="col-md-2">
                                        <?= form_dropdown('cost_center_id',$cost_center_options,'',' class="form-control" ') ?>
                                    </td>
                                    <td class="col-md-2">
                                        <input type="text" class="form-control number_format"  required name="amount" value="">
                                    </td>
                                    <td class="col-md-3">
                                        <?= form_dropdown('debit_account_id',$expense_debit_account_options,  '', ' class="form-control"') ?>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger payment_row_remover">
                                            <i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                </thead>
                                <tbody>
                                <?php if(!$edit){ ?>
                                    <tr>
                                        <td class="col-md-3">
                                            <textarea class="form-control" rows="1" name="description"></textarea>
                                        </td>
                                        <td class="col-md-2">
                                            <?= form_dropdown('cost_center_type',$cost_center_type_options,'',' class="form-control searchable" ') ?>
                                        </td>
                                        <td class="col-md-2">
                                            <?= form_dropdown('cost_center_id',$cost_center_options,'',' class="form-control searchable" ') ?>
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" class="form-control number_format" required name="amount" value="">
                                        </td>
                                        <td class="col-md-3">
                                            <?= form_dropdown('debit_account_id',$expense_debit_account_options,  '', ' class="form-control searchable"') ?>
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger payment_row_remover">
                                                <i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                <?php }else{
                                        $payment_items = $payment->payment_voucher_items();
                                        foreach($payment_items as $item){
                                            $cost_center_type = $item->cost_center_type();
                                            $debit_account = $item->debit_account();
                                            $junction = $item->cost_center_junction();
                                            if($cost_center_type == 'department'){
                                                $cost_center_id = $junction->department_id;
                                                $cost_center_options = $department_dropdown_options;
                                            } else if($cost_center_type == 'project'){
                                                $cost_center_id = $junction->project_id;
                                                $cost_center_options = $project_dropdown_options;
                                            } else if($cost_center_type == 'cost_center'){
                                                $cost_center_id = $junction->cost_center_id;
                                                $cost_center_options = $cost_center_dropdown_options;
                                            }  else if($cost_center_type == 'task'){
                                                $cost_center_id = $junction->task_id;
                                                $cost_center_options = $junction->task()->project()->cost_center_options();
                                            } else {
                                                $cost_center_id = '';
                                            }
                                    ?>
                                    <tr class="payment_edit_row">
                                        <td class="col-md-3">
                                            <textarea class="form-control" rows="1" name="description"><?= $item->description ?></textarea>
                                        </td>
                                        <td class="col-md-2">
                                            <?= form_dropdown('cost_center_type',[$cost_center_type => ucfirst(str_replace('_',' ',$cost_center_type))],$cost_center_type,' class="form-control searchable" ') ?>
                                        </td>
                                        <td class="col-md-2">
                                            <?= form_dropdown('cost_center_id', $cost_center_options,$cost_center_id,' class=" form-control searchable"') ?>
                                        </td>
                                        <td class="col-md-2">
                                            <input type="text" class="form-control number_format" required name="amount" value="<?= $item->amount ?>">
                                        </td>
                                        <td class="col-md-3">
                                            <?= form_dropdown('debit_account_id',$expense_debit_account_options,  $item->debit_account_id, ' class="form-control searchable"') ?>
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger payment_row_remover">
                                                <i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button type="button" class="btn btn-default btn-xs payment_row_adder pull-right">Add Row</button>
                                        <span class="pull-right">&nbsp;</span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="remarks" class="control-label">Remarks</label>
                                <textarea type="text" class="form-control" name="remarks" ><?= $edit ? $payment->remarks : ''?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-md pull-right save_expense_payment">Submit</button>
            </div>
        </form>
    </div>
</div>
