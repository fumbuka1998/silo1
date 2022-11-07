<?php
if (isset($type)) {
	$invoice_type = $type;
}
$edit = isset($invoice);
switch ($invoice_type) {
	case 'sales':
		$stakeholder = 'Client';
		$title = 'Tax Invoice';
		$corresponding_to = 'Sale/Certificate/Service';
		$desc_or_note = 'Notes';
		break;
	case 'purchases':
		$stakeholder = 'Vendor';
		$title = 'Purchase Invoice';
		$corresponding_to = 'Order';
		$desc_or_note = 'Descriptions';
		break;
}

$payment_terms_options = [
	'due_on_receipt' => 'Due On Receipt',
	'net_ten' => 'Net 10',
	'net_twenty' => 'Net 20',
	'net_thirty' => 'Net 30',
	'set_manually' => 'Set Manually'
]

?>
<div class="modal-dialog modal-lg" style="width: 80%">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= $title ?></h4>
		</div>
		<form>
			<div class="modal-body" style="overflow:auto;">
				<div class="form-group col-xs-6">

					<?php if ($invoice_type == 'purchases') { ?>
						<div class="form-group col-md-12" style="margin-bottom: 5px;">
							<input type="checkbox" name="is_for_other_charges">
							&nbsp;&nbsp;
							<label for="is_for_other_charges" class="control-label text-center">For Other Charges&nbsp;&nbsp;</label>
						</div>
					<?php } ?>
					<div class="form-group col-md-12">
						<label for="client_id" class="control-label"><?= $stakeholder ?></label>
						<?= form_dropdown('stakeholder_id', $stakeholder_options, $edit ? $invoice->invoice_details()['stakeholder_id'] : '', ' class="form-control searchable"'); ?>
						<input type="hidden" name="invoice_type" value="<?= $invoice_type ?>">
						<input type="hidden" name="invoice_id" value="<?= $edit ? $invoice->id : '' ?>">
					</div>
					<?php if ($invoice_type == 'sales') { ?>
						<div class="form-group col-md-12">
							<label for="billing_adddress" class="control-label">Billing Address</label>
							<textarea id="billing_adddress_text_area" readonly class="form-control" name="billing_address" rows="3"><?= $edit ? $invoice->invoice_details()['billing_address'] : '' ?></textarea>
						</div>
					<?php } ?>
					<div class="form-group col-md-6">
						<label for="invoice_no" class="control-label">Invoice No</label>
						<input type="text" class="form-control" name="invoice_no" value="<?= $edit ? $invoice->invoice_no : next_invoice_no() ?>" disabled>
					</div>
					<div class="form-group col-md-6">
						<label for="currency_id" class="control-label">Currency</label>
						<?= form_dropdown('currency_id', $currency_options, $edit ? $invoice->currency_id : '', ' class="form-control searchable"'); ?>
					</div>
				</div>
				<div class="form-group col-md-6">
					<div class="form-group col-md-6">
						<label for="due_date" class="control-label">Invoice Date</label>
						<input type="text" class="form-control datepicker" name="invoice_date" value="<?= $edit ? $invoice->invoice_date : date('Y-m-d') ?>">
					</div>
					<div class="form-group col-md-6">
						<label for="reference" class="control-label">Reference</label>
						<input type="text" class="form-control" name="reference" value="<?= $edit ? $invoice->reference : '' ?>">
					</div>
					<div class="form-group col-md-6">
						<label for="payment_terms" class="control-label">Payment Terms</label>
						<?= form_dropdown('payment_term', $payment_terms_options, $edit ? $invoice->payment_terms : '', ' class="form-control searchable"'); ?>
					</div>
					<div class="form-group col-md-6">
						<label for="invoice_date" class="control-label">Due Date</label>
						<input type="text" class="form-control datepicker" name="due_date" value="<?= $edit ? $invoice->due_date : '' ?>">
					</div>
					<div class="form-group col-md-12">
						<label for="desc_or_note" class="control-label"><?= $desc_or_note ?></label>
						<textarea class="form-control" placeholder="Enter a message for your <?= $stakeholder ?>" name="desc_or_note" rows="2"><?= $edit ? $invoice->invoice_details()['desc_or_note'] : '' ?></textarea>
					</div>
				</div>
				<div class="col-xs-12 table-responsive">
					<table class="table table-striped" style="table-layout: fixed">
						<thead>
							<tr style="background-color: #dfdfdf">
								<td style="width: 1%"><i class="fa fa-close"></i></td>
								<td style="width: 30%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><span id="other_charge_for"></span><?= $corresponding_to ?></strong> <span style="padding-left: 40%" class="centered"><strong>Particulars</strong></span></td>
							</tr>
							<tr class="row_template" style="display: none">
								<td>
									<button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash-o"></i></button>
								</td>
								<td>
									<div class="row">
										<div class="col-xs-12">
											<div class="active  tab-pane form-group" id="corresponding_item">
												<div class=" col-md-3">
													<?= form_dropdown('debt_id', [], '', ' class="form-control"'); ?>
													<input type="hidden" name="amount" value="">
												</div>
												<div style="text-align: center" class=" col-md-9 display_item_particulars">
													Please select <?= $corresponding_to ?> to <?php if ($invoice_type == 'sales') { ?>to invoice<?php } else { ?> to record invoice <?php } ?>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</thead>
						<tbody>
							<?php
							if (!$edit) {
							?>
								<tr>
									<td>
										<button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash-o"></i></button>
									</td>
									<td>
										<div class="row">
											<div class="col-xs-12">
												<div class="active  tab-pane form-group" id="corresponding_item">
													<div class=" col-md-3">
														<?= form_dropdown('debt_id', [], '', ' class="form-control"'); ?>
														<input type="hidden" name="amount" value="">
													</div>
													<div style="text-align: center" class=" col-md-9 display_item_particulars">
														Please select <?= $corresponding_to ?> to <?php if ($invoice_type == 'sales') { ?>to invoice<?php } else { ?> to record invoice <?php } ?>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<?php } else {
								$invoice_items = $invoice->invoice_edit_details();
								$edit_total_amount = 0;
								foreach ($invoice_items as $invoice_item) {
									$edit_total_amount += $invoice_item->item_amount;
								?>
									<tr>
										<td>
											<button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-trash-o"></i></button>
										</td>
										<td>
											<div class="row">
												<div class="col-xs-12">
													<div class="active  tab-pane form-group" id="corresponding_item">
														<div class=" col-md-3">
															<?= form_dropdown('debt_id', $invoice_item->item_options, $invoice_item->item_id, ' class="form-control"'); ?>
															<input type="hidden" name="amount" value="<?= $invoice_item->item_amount ?>">
														</div>
														<div style="text-align: center" class=" col-md-9 display_item_particulars">
															<?php
															echo $invoice_item->item_particulars ?>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
							<?php }
							} ?>
						</tbody>
						<tfoot>
							<tr style="display: none;">
								<td colspan="2">
									<input type="hidden" name="total_amount" value="">
									<input type="hidden" name="vat_amount" value="">
									<input type="hidden" name="grand_total_amount" value="">
								</td>
							</tr>
							<?php if ($invoice_type == 'sales') { ?>
								<tr>
									<td></td>
									<td style="text-align: right">
										<strong>TOTAL :</strong><span style="width: 17%; padding-right: 3%;" class="total_amount_display pull-right"></span>
									</td>
								</tr>
								<tr>
									<td></td>
									<td style="text-align: right">
										<strong>VAT :</strong><span style="width: 17%; padding-right: 3%;" class="vat_amount_display pull-right">0</span>
									</td>
								</tr>
								<tr style="background-color: #dfdfdf">
									<td></td>
									<td style="text-align: right">
										<strong>GRAND TOTAL :</strong><span style="width: 17%; padding-right: 3%;" class="grand_total_display pull-right"></span>
									</td>
								</tr>
							<?php } else { ?>
								<tr>
									<td></td>
									<td style="text-align: right">
										<strong>INVOICE AMOUNT :</strong>
										<div style="padding-left: 2%; padding-right: 1%" class="form-group pull-right">
											<input style="width: 100%; text-align: right" type="text" class="form-control money" name="invoice_amount" value="<?= $edit ? number_format($invoice_item->item_amount) : 0 ?>">
										</div>
									</td>
								</tr>
							<?php } ?>
							<tr>
								<td colspan="2">
									<button type="button" class="btn btn-xs btn-default row_adder pull-right"><i class="fa fa-plus"></i> Item </button>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="form-group col-xs-12">
					<div class="form-group col-md-2">
						<input type="checkbox" name="vat_inclusive" <?= $edit && $invoice->vat_inclusive == 1 ? 'checked' : '' ?>>
						&nbsp;&nbsp;
						<label for="vat_inclusive" class="control-label text-center">Include VAT</label>
					</div>

					<div class="form-group col-md-2" <?= $edit ? ($invoice->vat_inclusive == 0 ? 'style="display: none;"' : '') : 'style="display: none;"' ?>>
						<?php $vat_options = array(0 => 'VAT@0%', 15 => 'VAT@15%', 18 => 'VAT@18%') ?>
						<?= form_dropdown('vat_percentage', $vat_options, $edit ? $invoice->vat_percentage : '', ' class="form-control searchable"') ?>
					</div>
				</div>

				<?php if ($invoice_type == 'sales') { ?>
					<div class=" form-group col-xs-4">
						<label for="account_id" class="control-label">Accounts</label>
						<?= form_dropdown('account_id', $accounts, '', " class='searchable' ") ?>
					</div>
					<div class="form-group col-xs-8">
						<label for="bank_details" class="control-label">Bank Details</label>
						<textarea id="bank_details_text_area" readonly class="form-control" name="bank_details" rows="7"><?= $edit ? $invoice->bank_details : '' ?></textarea>
					</div>
				<?php } ?>
			</div>
		</form>

		<div class="modal-footer">
			<button type="button" class="btn btn-default btn-sm submit_invoice">
				<i class="fa fa-save"></i> Submit
			</button>
		</div>
	</div>
</div>