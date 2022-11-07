<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 10/5/2018
 * Time: 11:13 AM
 */

    $edit = isset($contra)
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Contra Form</h4>
        </div>
        <div class="modal-body">
            <form>
                <div class='row'>
                    <div class="col-xs-12">

                        <div class="form-group col-md-3">
                            <label for="contra_date" class="control-label">Contra Date</label>
                            <input type="text" class="form-control datepicker" required name="contra_date" value="<?= $edit ? $contra->contra_date : date('Y-m-d') ?>">
                            <input type="hidden" name="contra_id" value="<?= $edit ? $contra->{$contra::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="credit_account_id" value="<?= $edit ? $contra->credit_account_id : $imprest_voucher->debit_account_id ?>">
                            <input type="hidden" name="imprest_voucher_id" value="<?= $edit ? $contra->imprest_voucher_contra()->imprest_voucher_id : $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="reference" class="control-label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="<?= $edit ? $contra->reference : $imprest_voucher->imprest_voucher_number() ?>" readonly>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="currency_id" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $edit ? [$contra->currency_id => $currency->currency_name] : $currency_options, $edit ? $contra->currency_id : '',' class="form-control searchable"') ?>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control" name="exchange_rate" value="<?= $edit ? $contra->exchange_rate : '' ?>" readonly>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="debit_account_id" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id',  $account_options, $edit ? $contra->credit_account_id : $imprest_voucher->debit_account_id, ' class="form-control searchable"') ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="debit_account_id" class="control-label">Debit Account</label>
                            <?= form_dropdown('debit_account_id',  $account_options, $edit ? $contra->imprest_contra_debit_account() : '', ' class="form-control searchable"') ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount</label>
                            <input type="text" name="amount" class="form-control number_format" value="<?= $edit ? $contra->imprest_contra_debit_account(false,true) : '' ?>">
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea name="remarks" class="form-control"><?= $edit ? $contra->remarks : '' ?></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default save_imprest_contra">Submit</button>
    </div>
    </div>
</div>