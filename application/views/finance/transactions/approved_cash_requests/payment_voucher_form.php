<?php
	$currency_id = $currency->{$currency::DB_TABLE_PK};
	switch ($request_type) {
		case 'requisition':
			$requested_for = $requisition->requested_for();
			if ($requested_for == 'project') {
			$junction_id = $requisition->project_requisition()->project_id;
			} else {
				$junction_id = $requisition->cost_center_requisition()->cost_center_id;
			}
			$reference = '';
			break;
		case 'payment_request_invoice':
			$reference = $invoice->reference;
			$requested_for = '';
			$junction_id = '';
			break;
		case 'sub_contract_payment_requisition':
			$reference = $approved_item->sub_contract_payment_requisition_item()->certificate()->certificate_number;
			$requested_for = '';
			$junction_id = '';
			break;
	}
?>
<div style="width: 80%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Payment Voucher</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-4">
                        <label for="payment_date" class="control-label">Payment Date</label>
                        <input type="text" class="form-control datepicker" required name="payment_date" value="<?= date('Y-m-d') ?>">
                        <input type="hidden" name="requisition_approval_id" value="<?= $requisition_approval_id ?>">
                        <input type="hidden" name="amount_to_be_paid" value="<?= $amount_to_be_paid ?>">
                        <input type="hidden" name="request_type" value="<?= $request_type ?>">
                        <input type="hidden" name="currency_symbol" value="<?= $currency->symbol ?>">
						<input type="hidden" name="junction_type" value="<?= $requested_for ?>">
						<input type="hidden" name="junction_id" value="<?= $junction_id ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reference" class="control-label">Reference</label>
                        <input type="text" class="form-control" name="reference" value="<?= $reference ?>" <?= isset($reference) ? 'readonly' : '' ?>>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reference" class="control-label">Currency</label>
                        <?= form_dropdown('currency_id', [$currency->{$currency::DB_TABLE_PK} => $currency->name_and_symbol()],$currency_id,' class="form-control" ') ?>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="credit_account_id" class="control-label">Credit Account</label>
                        <?= form_dropdown('credit_account_id',$credit_account_options,'',' class="form-control searchable" ') ?>
                        <input type="hidden" name="junction_type" value="<?= $requested_for ?>">
                        <input type="hidden" name="junction_id" value="<?= $junction_id ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cheque_number" class="control-label">Cheque Number</label>
                        <input type="text" class="form-control" placeholder="Optional" name="cheque_number" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="payee" class="control-label">Payee</label>
                        <input type="text" class="form-control" required name="payee" value="<?= isset($stakeholder) ? $stakeholder->stakeholder_name : '' ?>" >
                    </div>

                    <?php if($currency_id != 1){ ?>
                        <div class="form-group col-md-4">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control number_format" required name="exchange_rate" value="<?= currency_exchange_rate($currency_id) ?>">
                        </div>
                        <?php } else {
                            ?>
                            <input type="hidden" name="exchange_rate" class="number_format" value="1.00">
                        <?php
                        }
                    ?>

                </div>

				<div class="col-xs-12">
					<table  width="100%" class="table table-striped" style="table-layout: fixed">
					<thead>
						<tr>
							<th style="width: 3%">S.No</th>
							<th style="width: 25%">Debit Account</th>
							<th style="width: 34%">Description</th>
							<th>Quantity</th>
							<th style="width: 3%">Unit</th>
							<th nowrap="true" style="width: 15%; text-align: right">Rate</th>
							<th nowrap="true" style="width: 12%">Amount</th>
						</tr>
						<tr>
							<td colspan="6" style="text-align: right"></td>
							<td></td>
						</tr>
					</thead>
					<tbody>
			<?php
			   $sn = 0;
			   $total_amount=0;
			   if($request_type == 'requisition') {
				   $material_items = $requisition_approval->material_items('cash');
				   foreach ($material_items as $item) {
					   $sn++;
					   $material = $item->requisition_material_item()->material_item();
					   $unit_symbol = $material->unit()->symbol;
					   $quantity_paid = $item->paid_quantity('material');
					   $material_name = htmlentities($material->item_name);
					   $quantity_to_pay = $item->approved_quantity - $quantity_paid;
					   $total_amount += $amount = $quantity_to_pay* $item->approved_rate;
					   if ($quantity_to_pay > 0) {
						   ?>
						   <tr>
							   <td><?= $sn ?></td>
							   <td><?= form_dropdown('debit_account_id', $expense_pv_debit_account_options, '', ' class="form-control "') ?></td>
							   <td><?= $material_name ?>
								   <input type="hidden" name="description"
										  value="<?= $material_name . ' (' . $item->approved_quantity . ' ' . $unit_symbol . ')' ?>">
								   <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
								   <input type="hidden" name="item_type" value="material">
							   </td>
							   <td style="text-align: right;">
								   <input type="text" name="quantity" class="form-control"
										  value="<?= $quantity_to_pay ?>">
							   </td>
							   <td><?= $unit_symbol ?></td>
							   <td nowrap="nowrap"">
								   <div class="input-group">
									   <span
										   class="input-group-addon currency_display"> <?= $item->currency()->symbol ?></span>
									   <input style="text-align: right" type="text" name="rate"
											  class="form-control money"
											  value="<?= $item->approved_rate ?>">
								   </div>
							   </td>
							   <td>
								   <input style="text-align: right" name="amount" readonly class="form-control money"
										  value="<?= $amount ?>">
							   </td>
						   </tr>
						   <?php
					   }
				   }

				   $asset_items = $requisition_approval->asset_items('cash');
				   foreach ($asset_items as $item) {
					   $sn++;
					   $asset_item = $item->requisition_asset_item()->asset_item();
					   $quantity_paid = $item->paid_quantity('asset');
					   $quantity_to_pay = $item->approved_quantity - $quantity_paid;
					   $total_amount += $amount = $quantity_to_pay * $item->approved_rate;
					   if ($quantity_to_pay > 0) {
						   ?>
						   <tr>
							   <td><?= $sn ?></td>
							   <td><?= form_dropdown('debit_account_id', $expense_pv_debit_account_options, '', ' class="form-control "') ?></td>
							   <td><?= $asset_item->asset_name ?>
								   <input type="hidden" name="description"
										  value="<?= $asset_item->asset_name . ' (' . $item->approved_quantity . ' Nos)' ?>">
								   <input type="hidden" name="item_id"
										  value="<?= $asset_item->{$asset_item::DB_TABLE_PK} ?>">
								   <input type="hidden" name="item_type" value="asset">
							   </td>
							   <td style="text-align: right;">
								   <input type="text" name="quantity" class="form-control"
										  value="<?= $quantity_to_pay ?>">
							   </td>
							   <td></td>
							   <td nowrap="nowrap">
								   <div class="input-group">
									   <span class="input-group-addon currency_display"> <?= $currency->symbol ?></span>
									   <input style="text-align: right" type="text" name="rate"
											  class="form-control money"
											  value="<?= $item->approved_rate ?>">
								   </div>
							   </td>
							   <td>
								   <input style="text-align: right" name="amount" readonly class="form-control money"
										  value="<?= $amount ?>">
							   </td>
						   </tr>
						   <?php
					   }
				   }

				   $service_items = $requisition_approval->service_items('cash');
				   foreach ($service_items as $item) {
					   $sn++;
					   $requisition_item = $item->requisition_service_item();
					   $description = htmlentities($requisition_item->description);
					   $unit_symbol = $requisition_item->measurement_unit()->symbol;
					   $quantity_paid = $item->paid_quantity('service');
					   $quantity_to_pay = $item->approved_quantity - $quantity_paid;
					   $total_amount += $amount = $quantity_to_pay * $item->approved_rate;
					   if ($quantity_to_pay > 0) {
						   ?>
						   <tr>
							   <td><?= $sn ?></td>
							   <td><?= form_dropdown('debit_account_id', $expense_pv_debit_account_options, '', ' class="form-control "') ?></td>
							   <td><?= $description ?>
								   <input type="hidden" name="description"
										  value="<?= $description . ' (' . $item->approved_quantity . ' ' . $unit_symbol . ')' ?>">
								   <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
								   <input type="hidden" name="item_type" value="service">
							   </td>
							   <td style="text-align: right;">
								   <input type="text" name="quantity" class="form-control"
										  value="<?= $quantity_to_pay ?>">
							   </td>
							   <td>
								   <?= $unit_symbol ?>
							   </td>
							   <td nowrap="nowrap">
								   <div class="input-group">
									   <span class="input-group-addon currency_display"> <?= $currency->symbol ?></span>
									   <input style="text-align: right" type="text" name="rate"
											  class="form-control money"
											  value="<?= $item->approved_rate ?>">
								   </div>
							   </td>
							   <td>
								   <input style="text-align: right" name="amount" readonly class="form-control money"
										  value="<?= $amount ?>">
							   </td>
						   </tr>
						   <?php
					   }
				   }

				   $cash_items = $requisition_approval->cash_items();
				   foreach ($cash_items as $item) {
					   $sn++;
					   $requisition_item = $item->requisition_cash_item();
					   $description = htmlentities($requisition_item->description);
					   $unit_symbol = $requisition_item->measurement_unit()->symbol;
					   $quantity_paid = $item->paid_quantity('cash');
					   $quantity_to_pay = $item->approved_quantity - $quantity_paid;
					   $total_amount += $amount = $quantity_to_pay * $item->approved_rate;
					   if ($quantity_to_pay > 0) {
						   ?>
						   <tr>
							   <td><?= $sn ?></td>
							   <td><?= form_dropdown('debit_account_id', $expense_pv_debit_account_options, '', ' class="form-control "') ?></td>
							   <td><?= $description ?>
								   <input type="hidden" name="description"
										  value="<?= $description . ' (' . $item->approved_quantity . ' ' . $unit_symbol . ')' ?>">
								   <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
								   <input type="hidden" name="item_type" value="cash">
							   </td>
							   <td style="text-align: right;">
								   <input type="text" name="quantity" class="form-control"
										  value="<?= $quantity_to_pay ?>">
							   </td>
							   <td>
								   <?= $unit_symbol ?>
							   </td>
							   <td nowrap="nowrap">
								   <div class="input-group">
									   <span class="input-group-addon currency_display"> <?= $currency->symbol ?></span>
									   <input style="text-align: right" type="text" name="rate"
											  class="form-control money"
											  value="<?= $item->approved_rate ?>">
								   </div>
							   </td>
							   <td>
								   <input style="text-align: right" name="amount" readonly class="form-control money"
										  value="<?= $amount ?>">
							   </td>
						   </tr>
						   <?php
					   }
				   }
			   } else if($request_type == 'payment_request_invoice') {
				   if ($amount_to_be_paid) {
				   		$total_amount += $amount_to_be_paid;
				   		$stakeholder = $invoice->stakeholder();
				   		$sn++;
					   ?>
					   <tr>
						   <td><?= $sn ?></td>
						   <td>
							   <span><?= $invoice->stakeholder()->stakeholder_name.' - Account Payable' ?></span>
							   <input type="hidden" name="debit_account_id" value="<?= 'stakeholder_'.$stakeholder->{$stakeholder::DB_TABLE_PK} ?>">
						   </td>
						   <td><?= $approved_item->purchase_order_payment_request_invoice_item()->description ?>
							   <input type="hidden" name="invoice_id" value="<?= $invoice->{$invoice::DB_TABLE_PK} ?>">
							   <input type="hidden" name="item_id" value="<?= $approved_item->{$approved_item::DB_TABLE_PK} ?>">
							   <input type="hidden" name="item_type" value="invoice">
						   </td>
						   <td style="text-align: center;">
							   <?= 1 ?>
							   <input type="hidden" name="quantity" value="1">
						   </td>
						   <td>Item</td>
						   <td nowrap="nowrap">
							   <span class="pull-left"> <?= $currency->symbol ?></span>
							   <span class="pull-right"> <?= number_format($amount_to_be_paid,2) ?></span>
							   <input type="hidden" name="rate" class="money" value="<?= $amount_to_be_paid ?>">
						   </td>
						   <td>
							   <input style="text-align: right" name="amount" readonly class="form-control money" value="<?= $amount_to_be_paid ?>">
						   </td>
					   </tr>
					   <?php
				   }
			   } else if($request_type == 'sub_contract_payment_requisition') {
				   if ($amount_to_be_paid) {
					   $total_amount += $amount_to_be_paid;
					   $sn++;
					   ?>
					   <tr>
						   <td><?= $sn ?></td>
						   <td>
							   <span><?= $stakeholder->stakeholder_name.' - Account Payable' ?></span>
							   <input type="hidden" name="debit_account_id" value="<?= 'stakeholder_'.$stakeholder->{$stakeholder::DB_TABLE_PK} ?>">
						   </td>
						   <td><?= $approved_item->sub_contract_payment_requisition_item()->certificate()->sub_contract()->contract_name.' - '.$approved_item->sub_contract_payment_requisition_item()->certificate()->certificate_number ?>
							   <input type="hidden" name="item_id" value="<?= $approved_item->{$approved_item::DB_TABLE_PK} ?>">
							   <input type="hidden" name="item_type" value="certificate">
						   </td>
						   <td style="text-align: center;">
							   <?= 1 ?>
							   <input type="hidden" name="quantity" value="1">
						   </td>
						   <td>Item</td>
						   <td nowrap="nowrap">
							   <span class="pull-left"> <?= $currency->symbol ?></span>
							   <span class="pull-right"> <?= number_format($amount_to_be_paid,2) ?></span>
							   <input type="hidden" name="rate" class="money" value="<?= $amount_to_be_paid ?>">
						   </td>
						   <td>
							   <input style="text-align: right" name="amount" readonly class="form-control money" value="<?= $amount_to_be_paid ?>">
						   </td>
					   </tr>
					   <?php
				   }
			   }
			?>
				</tbody>
					<tfoot>
						<tr>
							<td colspan="6" style="text-align: right">TOTAL</td>
							<td style="text-align: right" class="total_amount_display"><?= number_format($total_amount) ?></td>
						</tr>
						<?php
						$grand_total = $total_amount;
						if($request_type == 'requisition' || $request_type == 'sub_contract_payment_requisition') {
							if ((!is_null($requisition_approval->vat_inclusive) && $requisition_approval->vat_inclusive == 1) || (!is_null($requisition_approval->vat_inclusive) && $requisition_approval->vat_inclusive == "VAT COMPONENT")) {
								if ($requisition_approval->vat_inclusive == 'VAT PRICED') {
									$total_amount_vat_exclusive = $total_amount / 1.18;
									$vat_amount = $total_amount - $total_amount_vat_exclusive;
								} else {
									$vat_amount = $total_amount * 0.18;
								}
								?>
								<tr>
									<td colspan="6" style="text-align: right">VAT</td>
									<td style="text-align: right" class="vat_amount"><?= number_format($vat_amount) ?></td>
									<input type="hidden" name="vat_amount" value="<?= $vat_amount ?>">
									<input type="hidden" name="vat_percentage" value="<?= $requisition_approval->vat_percentage ?>">
								</tr>
								<?php
								$grand_total += $vat_amount;
								?>
							<?php }
						}
						if($request_type == 'payment_request_invoice') { ?>
							<tr style="display: none">
								<td colspan="6" style="text-align: right">DISCOUNT(Based on Invoice payment terms)</td>
								<td style="text-align: right" class="discount"><span class="display_discount_amount"></span></td>
								<input type="hidden" name="discount_amount" value="">
							</tr>
						<?php }
						if($request_type != 'payment_request_invoice') {
							if ((!is_null($requisition_approval->vat_inclusive) && $requisition_approval->vat_inclusive == 1) || (!is_null($requisition_approval->vat_inclusive) && $requisition_approval->vat_inclusive == "VAT COMPONENT")) {
								?>
								<tr style="background-color: #dfdfdf">
									<td colspan="6" style="text-align: right">GRAND TOTAL</td>
									<td style="text-align: right" class="grand_total">
										<span
											class="pull-right display_grand_total"><?= number_format($grand_total, 2) ?></span>
									</td>
								</tr>
							<?php }
						}
						?>
						<tr>
							<td colspan="7" style="text-align: right">&nbsp;</td>
						</tr>
							<tr>
								<td rowspan="2" colspan="4">
									<div class="form-group col-xs-12">
										<label for="remarks" class="control-label">Remarks</label>
										<textarea name="remarks" class="form-control"></textarea>
									</div>
								</td>
								<?php
								if($request_type == 'payment_request_invoice' || $request_type == 'sub_contract_payment_requisition') {
								?>
								<td colspan="2" style="text-align: right">AMOUNT PAID</td>
								<td style="text-align: right">
									<input type="text" style="text-align: right" class="form-control money" name="total_paid_amount" value="">
								</td>
								<?php } ?>
							</tr>
						<?php
						if($request_type == 'payment_request_invoice' || $request_type == 'sub_contract_payment_requisition') {
						?>
							<tr>
								<td colspan="2" style="text-align: right">WITHHOLDING TAX</td>
								<td style="text-align: right">
									<input type="text" style="text-align: right" class="form-control money" name="wht_amount" value="">
								</td>
							</tr>
							<tr style="background-color: #dfdfdf">
								<td colspan="6" style="text-align: right">TOTAL PAID</td>
								<input type="hidden" name="amount_paid" value="">
								<td style="text-align: right" class="grand_total"><span class="display_total_paid_amount" style="padding-left: 10%"></span></td>
							</tr>
						<?php } else { ?>
							<input type="hidden" name="total_paid_amount" value="<?= $grand_total ?>">
						<?php } ?>
					</tfoot>
					</table>
				</div>
				<div class="form-group col-xs-12">
					<div class="form-group form-inline col-md-6">
						<?php
						if($request_type != 'requisition'){
						?>
						<label for="deduct_witholding_tax" class="control-label text-center">Deduct Withholding Tax</label>
						<div class="input-group col-md-5">
							<input style="text-align: right" type="text" name="wht_percentage" class="form-control" value="">
							<span class="input-group-addon ">%</span>
						</div>
						<?php } ?>
					</div>
					<div class="form-group col-md-6">
						<div class="pull-right">
							<?php if(!$payment_voucher){ ?>
								<button type="button" class="btn btn-sm btn-danger revoke_approved_payment"><i class="fa fa-ban"></i> Revoke</button>
							<?php } ?>
							<button type="button" class="btn btn-sm btn-default save_payment_voucher"><i class="fa fa-check-circle"></i> Submit</button>
						</div>
					</div>
				</div>
			</div>
        </div>
        </form>
    </div>
</div>
