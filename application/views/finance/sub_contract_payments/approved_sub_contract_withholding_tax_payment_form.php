<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/26/2018
 * Time: 12:05 AM
 */

$tra_account = $withholding_tax->tra_account();
?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Withholding Tax Payment</h4>
            </div>
            <div class="modal-body" >
                <div class='row'>
                    <div class="col-xs-12">

                        <div class="form-group col-md-6">
                            <label for="payment_date" class="control-label">Payment Date</label>
                            <input type="text" class="form-control datepicker" required name="payment_date" value="<?= date('Y-m-d') ?>" >
                        </div>

                        <div class="form-group col-md-6">
                            <label for="credit_account" class="control-label">Credit Account</label>
                            <input type="text" class="form-control"  name="credit_account_id" value="<?= $withholding_tax->debit_account()->account_name ?>" readonly >
                            <input type="hidden" name="withholding_tax_id" value="<?= $withholding_tax->{$withholding_tax::DB_TABLE_PK} ?>">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="amount" class="control-label">Amount(TSH)</label>
                            <input style="text-align: right" type="text" class="form-control number_format" name="amount" value="<?=  $withholding_tax->withheld_amount ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" type="button" class="btn btn-default btn-sm save_sub_contract_withholding_tax_payment">Submit</button>
            </div>
        </form>
    </div>
</div>
