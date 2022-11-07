<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/26/2018
 * Time: 12:05 AM
 */

?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Approved Sub Contract Payment</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="payment_date" class="control-label">Payment Date</label>
                            <input type="text" class="form-control datepicker" required name="payment_date" value="<?= date('Y-m-d') ?>" >
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="invoice_id" class="control-label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="<?= $sub_contract_requisition_approved_item->sub_contract_payment_requisition_item()->certificate()->certificate_number ?>" readonly>
                            <input type="hidden" name="approved_sub_contract_requisition_item_id" value="<?= $sub_contract_requisition_approved_item->{$sub_contract_requisition_approved_item::DB_TABLE_PK} ?>">
                            <input type="hidden" name="sub_contract_requisition_approval_id" value="<?= $sub_contract_requisition_approval->{$sub_contract_requisition_approval::DB_TABLE_PK} ?>">
                            <input type="hidden" name="sub_contract_requisition_id" value="<?= $sub_contract_requisition->{$sub_contract_requisition::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="cheque_number" class="control-label">Cheque Number</label>
                            <input type="text" class="form-control" placeholder="Optional" name="cheque_number" value="" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="payee" class="control-label">Payee</label>
                            <input type="text" class="form-control" name="payee" value="<?= $contractor->contractor_name ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="currency" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id',$currency_options, $sub_contract_requisition_approval->currency_id, ' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control" <?= $sub_contract_requisition_approval->currency_id == 1 ? 'readonly' : '' ?>  name="exchange_rate" value="<?= $currency->rate_to_native() ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="credit_account" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id', $credit_account_options, '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="debit_account" class="control-label">Debit Account</label>
                            <?= form_dropdown('debit_account_id',$contractor_account_options, '', 'class="form-control searchable" ') ?>
                        </div >
                        <div class="form-group col-md-4">
                            <?php
                                $certificate_amount = $sub_contract_requisition_approval->vat_inclusive == 1 ? $sub_contract_requisition_approved_item->approved_amount*1.18 : $sub_contract_requisition_approved_item->approved_amount;
                            ?>
                            <label for="amount" class="control-label">Certificate Amount</label>
                            <input type="text" class="form-control number_format" name="amount" value="<?=  $certificate_amount ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="withholding_tax" class="control-label">Withholding Tax(%)</label>
                            <input type="text" class="form-control"  name="withholding_tax" value="" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount_withheld" class="control-label">Amount Withheld</label>
                            <input type="text" class="form-control"  name="amount_withheld" value="" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount_retained" class="control-label">Paid Amount</label>
                            <input type="text" class="form-control number_format"  name="amount_payable" value="" >
                        </div>
                        <div class="form-group col-md-12">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea class="form-control" name="remarks" ></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger cancel_sub_contract_payment">Revoke</button>
                <button type="button" class="btn btn-default btn-sm save_sub_contract_payment">Submit</button>
            </div>
        </form>
    </div>
</div>

