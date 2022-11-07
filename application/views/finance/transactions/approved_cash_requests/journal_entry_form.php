<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/26/2019
 * Time: 10:09 AM
 */

$stakeholder = $invoice->stakeholder();
$requisition_approval_id = $requisition_approval->{$requisition_approval::DB_TABLE_PK};
switch ($request_type) {
	case 'requisition':
		$item_type = 'requisition';
		break;
	case 'payment_request_invoice':
		$item_type = 'invoice';
		break;
	case 'sub_contract_payment_requisition':
		$item_type = 'sub_contract';
		break;
}
?>

<div class="modal-dialog" style="width: 70%">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Journal Entry</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
							<div class="form-group col-md-5">
								<label for="currency_id" class="control-label">Credit</label>
								<?= form_dropdown('credit_account_id', $account_options , 1 , 'class="form-control searchable"') ?>
							</div>
                            <div class="form-group col-md-2">
                                <label for="transaction_date" class="control-label">Transaction Date</label>
                                <input type="text" class="form-control datepicker" name="transaction_date" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="reference" class="control-label">Reference</label>
                                <input type="text" readonly class="form-control" name="reference" value="<?= $request_number ?>">
                                <input type="hidden" name="transaction_type" value="JOURNAL">
								<input type="hidden" name="item_type" value="<?= $item_type ?>">
                                <input type="hidden" name="requisition_approval_id" value="<?= $requisition_approval_id ?>">
                                <input type="hidden" name="invoice_id" value="<?= $invoice->{$invoice::DB_TABLE_PK}?>">
                                <input type="hidden" name="approved_item_id" value="<?= $approved_item->{$approved_item::DB_TABLE_PK}?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="currency_id" class="control-label">Currency</label>
                                <?= form_dropdown('currency_id', $currency_options , $requisition_approval->purchase_order_payment_request()->currency_id , 'class="form-control searchable"') ?>
                            </div>
                        </div>
                        <div class="form-group col-md-12 account_adder_container" >
                            <div class="form-group col-md-5">
                                <label for="stakeholder_id" class="control-label">Debit</label><br/>
								<span class="pull-left" style="text-align: left"><?= $stakeholder->stakeholder_name.' - Account Payable' ?></span>
								<input type="hidden" name="stakeholder_id" value="<?= 'stakeholder_'.$stakeholder->{$stakeholder::DB_TABLE_PK} ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="number_format form-control" name="amount" value="<?= number_format($amount_to_be_paid,0) ?>">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="narration" class="control-label">Narration</label>
                                <input type="text" class="form-control" name="narration" value="">
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea class="form-control" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_journal_entry2"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>
