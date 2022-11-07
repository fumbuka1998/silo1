<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/12/2018
 * Time: 2:30 PM
 */

    $payment_request_approval_id = $payment_request_approval->{$payment_request_approval::DB_TABLE_PK};
    $credit_account_options = isset($account) ? [$account->{$account::DB_TABLE_PK} => $account->account_name] : $credit_account_options;
    $debit_account_options = account_dropdown_options(['DIRECT EXPENSES','INDIRECT EXPENSES']);
    $payment_request = $payment_request_approval->purchase_order_payment_request();
    $currency = $payment_request->currency();

?>
<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Approved Cash Payment Voucher</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-2">
                            <label for="credit_account_id" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id',$credit_account_options,'',' class="form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="payment_date" class="control-label">Payment Date</label>
                            <input type="text" class="form-control datepicker" required name="payment_date" value="<?= date('Y-m-d') ?>">
                            <input type="hidden" name="payment_request_approval_id" value="<?= $payment_request_approval_id ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="payee" class="control-label">Payee</label>
                            <input type="text" class="form-control" required name="payee" value="">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="reference" class="control-label">Reference</label>
                            <input type="text" class="form-control" required name="reference" value="">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="reference" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', [$currency->{$currency::DB_TABLE_PK} => $currency->name_and_symbol()],$payment_request->currency_id,' class="form-control" ') ?>
                        </div>

                        <?php if($payment_request->currency_id != 1){ ?>
                            <div class="form-group col-md-3">
                                <label for="exchange_rate" class="control-label">Exchange Rate</label>
                                <input type="text" class="form-control number_format" required name="exchange_rate" value="<?= currency_exchange_rate($payment_request_approval->purchase_order_payment_request()->currency_id) ?>">
                            </div>
                            <?php } else {
                                ?>
                                <input type="hidden" name="exchange_rate" class="number_format" value="1">
                            <?php
                            } ?>
                    </div>

             <div class="col-xs-12">
                <table  width="100%" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Debit Account</th>
                            <th>Reference</th>
                            <th nowrap="true">Amount</th>
                        </tr>
                    </thead>
                    <tbody>

            <?php
            $total_amount=0;
            $cash_items = $payment_request_approval->approved_cash_items();

            foreach ($cash_items as $item){
                $description = htmlentities($item->description);
                $reference = $item->reference;

                $approved_cash_item = $item->approved_item($payment_request_approval_id);
                $total_amount += $amount = $approved_cash_item->approved_amount;
                ?>
                    <tr>
                        <td>
                            <input type="text" class="form-control" required name="description" value="<?= $description ?>">
                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                            <input type="hidden" name="item_type" value="cash">
                        </td>
                        <td>
                            <?= form_dropdown('debit_account_id', $debit_account_options, '', 'class="form-control searchable"') ?>
                        </td>
                        <td style="text-align: center;width: 10%">
                            <span class="form-control-static"><?= $reference ?></span>
                        </td>
                        <td nowrap="nowrap">
                            <div class="input-group">
                                <input style="text-align: right" name="amount" class="form-control money" value="<?= $amount ?>">
                                <span class="input-group-addon currency_display"> <?= $currency->symbol ?></span>
                            </div>
                        </td>
                    </tr>
                <?php
                    }
                ?>
                    </tbody>

                    <tfoot>
                            <tr>
                                <td></td>
                                <th colspan="2">TOTAL</th>
                                <th style="text-align: right" class="total_amount_display"></th>
                            </tr>
                    </tfoot>
                    </table>
                </div>

                <div class="form-group col-xs-12">
                    <label for="remarks" class="control-label">Remarks</label>
                    <textarea name="remarks" class="form-control"></textarea>
                </div>
            </div>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default submit_approved_requested_payment">Submit</button>
            </div>
        </form>
    </div>
</div>