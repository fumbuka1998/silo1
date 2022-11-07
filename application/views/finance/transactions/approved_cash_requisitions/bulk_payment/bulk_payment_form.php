<?php

?>
<div style="width: 90%" class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Bulk Payment</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <form>
                                        <div class="col-xs-12">
                                            <div class="form-group col-md-3">
                                                <label for="vendor_id" class="control-label">Supplier</label>
                                                <?= form_dropdown('vendor_id', $creditors_options,'',' class="form-control searchable" ') ?>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="credit_account_id" class="control-label">Credit Account</label>
                                                <?= form_dropdown('credit_account_id', $credit_account_options,'',' class="form-control searchable" ') ?>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="debit_account_id" class="control-label">Debit Account</label>
                                                <?= form_dropdown('debit_account_id', $credit_account_options,'',' class="form-control searchable" ') ?>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="payee" class="control-label">Payee</label>
                                                <input type="text" class="form-control" name="payee" value="">
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group col-md-3">
                                                <label for="payment_date" class="control-label">Payment Date</label>
                                                <input type="text" class="form-control datepicker" required name="payment_date" value="<?= date('Y-m-d') ?>">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="reference" class="control-label">Reference</label>
                                                <input type="text" class="form-control" name="reference" value="">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="reference" class="control-label">Currency</label>
                                                <?= form_dropdown('currency_id', $currency_options,'',' class="form-control  searchable" ') ?>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="cheque_number" class="control-label">Cheque Number</label>
                                                <input type="text" class="form-control" placeholder="Optional" name="cheque_number" value="">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-xs-12 table_container"  style="max-height: 320px; overflow-y: scroll">

                                </div>
                                <div class="col-xs-12">
                                    <form class="form-inline">
                                        <div class="col-md-12">
                                            <div class="form-group pull-right" style="padding-left: 40px; padding-right: 88px;">
                                                <label for="selected_items_total_amount">Amount(Selected Items):&nbsp;&nbsp;</label>
                                                <input type="text" class="form-control number_format" name="selected_items_total_amount" value="<?= 0 ?>" readonly>
                                            </div>
                                            <div class="form-group pull-right" style="padding-left: 40px;">
                                                <label for="total_typed_amount">Amount:&nbsp;&nbsp;</label>
                                                <input type="text" class="form-control number_format" name="total_typed_amount" value="">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-xs-12 withholding_tax_div">
                                    <div class="form-group col-md-4">
                                        <label for="withholding_tax" class="control-label">Withholding Tax(%)</label>
                                        <input type="text" class="form-control"  name="withholding_tax" value="">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="amount_withheld" class="control-label">Amount Withheld</label>
                                        <input type="text" class="form-control"  name="amount_withheld" value="" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="amount_retained" class="control-label">Amount Payable</label>
                                        <input type="text" class="form-control"  name="amount_payable" value="" readonly>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="remarks" class="control-label">Remarks</label>
                                        <textarea class="form-control" name="remarks" ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_bulk_payment">Submit</button>
        </div>
    </div>
</div>
