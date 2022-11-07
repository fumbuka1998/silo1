<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/8/2017
 * Time: 11:02 AM
 */

$this->load->view('includes/letterhead');
$has_project = $requisition->project_requisition();
if ($has_project) {
	$project = $has_project->project();
}
$full_access = !$has_project || ($has_project && $project->manager_access()) || check_permission('Administrative Actions');
$approved = $requisition->status == 'APPROVED';

$currency = $requisition->currency();
?>
<h2 style="text-align: center">REQUISITION APPROVAL CHAIN</h2>
<br />
<table style="font-size: 11px" width="100%">
	<tr>
		<td style=" width:20%; vertical-align: top">
			<strong>Requisition No: </strong><br /><?= $requisition->requisition_number() ?>
		</td>
		<td style=" width:20%;  vertical-align: top">
			<strong>Required Date: </strong><br /><?= $requisition->required_date != null ? custom_standard_date($requisition->required_date) : 'N/A' ?>
		</td>
		<td style=" width:60%;  vertical-align: top">
			<strong>Requested For: </strong><br /><?= $has_project ? $project->project_name : $requisition->cost_center_name() ?>
		</td>
	</tr>
</table>
<br />

<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
	<thead>
		<tr>
			<th>Item Description</th>
			<th>Part No.</th>
			<th>Unit</th>
			<th>Requested Quantity</th>
			<th>Requested Rate</th>
			<th>Requested Amount</th>
			<th>Source</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$material_items = $requisition->material_items();
		$total_amount = 0;
		foreach ($material_items as $item) {
			$material = $item->material_item();
			$total_amount += $amount = $item->requested_quantity * $item->requested_rate;
		?>
			<tr>
				<td><?= $material->item_name ?></td>
				<td><?= $material->part_number ?></td>
				<td><?= $material->unit()->symbol ?></td>
				<td style="text-align: right"><?= $item->requested_quantity ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->requested_rate, 2) ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' .  number_format(($amount), 2) ?></td>
				<td><?= $item->requested_source() ?></td>
			</tr>
		<?php
		}

		$asset_items = $requisition->asset_items();

		foreach ($asset_items as $item) {
			$asset_item = $item->asset_item();
			$total_amount += $amount = $item->requested_quantity * $item->requested_rate;
		?>
			<tr>
				<td><?= $asset_item->asset_name ?></td>
				<td></td>
				<td>No.</td>
				<td style="text-align: right"><?= $item->requested_quantity ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->requested_rate, 2) ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' .  number_format(($amount), 2) ?></td>
				<td><?= $item->requested_source() ?></td>
			</tr>
		<?php
		}

		$service_items = $requisition->service_items();

		foreach ($service_items as $item) {
			$total_amount += $amount = $item->requested_quantity * $item->requested_rate;
		?>
			<tr>
				<td><?= $item->description ?></td>
				<td></td>
				<td><?= $item->measurement_unit()->symbol ?></td>
				<td style="text-align: right"><?= $item->requested_quantity ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->requested_rate, 2) ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' .  number_format(($amount), 2) ?></td>
				<td><?= $item->requested_source() ?></td>
			</tr>
		<?php
		}

		$cash_items = $requisition->cash_items();
		foreach ($cash_items as $item) {
			$total_amount += $amount = $item->requested_quantity * $item->requested_rate;
		?>
			<tr>
				<td><?= $item->description ?></td>
				<td></td>
				<td><?= $item->measurement_unit()->symbol ?></td>
				<td style="text-align: right"><?= $item->requested_quantity ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->requested_rate, 2) ?></td>
				<td style="text-align: right"><?= $currency->symbol . ' ' .  number_format(($amount), 2) ?></td>
				<td>CASH</td>
			</tr>
		<?php
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<th style="text-align: right" colspan="5">Total</th>
			<th style="text-align: right"><?= number_format($total_amount, 2) ?></th>
			<th></th>
		</tr>
		<tr>
			<th style="text-align: right" colspan="5">Freight</th>
			<th style="text-align: right"><?= number_format($requisition->freight, 2) ?></th>
			<th></th>
		</tr>
		<tr>
			<th style="text-align: right" colspan="5">Inspection and Other Charges</th>
			<th style="text-align: right"><?= number_format($requisition->inspection_and_other_charges, 2) ?></th>
			<th></th>
		</tr>
		<?php
		$total_amount = $total_amount + $requisition->inspection_and_other_charges + $requisition->freight;
		if (!is_null($requisition->vat_inclusive)) { ?>
			<tr>
				<th style="text-align: right" colspan="5">VAT</th>
				<th style="text-align: right"><?= number_format($total_amount * 0.18, 2) ?></th>
				<th></th>
			</tr>
		<?php }

		$grand_total = !is_null($requisition->vat_inclusive) ? $total_amount * 1.18 : $total_amount;
		?>
		<tr>
			<th style="text-align: right" colspan="5">Grand Total</th>
			<th style="text-align: right"><?= number_format($grand_total, 2) ?></th>
			<th></th>
		</tr>
	</tfoot>
</table><br />
<?php
$approvals = $requisition->requisition_approvals();

foreach ($approvals as $approval) {
	$total_amount = 0;
?>
	<pagebreak>
		<br />
		<table width="100%">
			<tr>
				<td style=" width:50%">
					<strong><?= ucwords(strtolower($approval->approval_chain_level()->label)) ?> By: </strong><?= $approval->created_by()->full_name() ?>
				</td>
				<td style=" width:50%">
					<strong>Action Date </strong><?= standard_datetime($approval->created_at, true) ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<strong>Comments: </strong><?= $approval->approving_comments != '' ? $approval->approving_comments : 'N/A' ?>
				</td>
			</tr>
		</table>
		<br />
		<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
			<thead>
				<tr>
					<th>Item Description</th>
					<th>Part No.</th>
					<th>Unit</th>
					<th>Approved Information</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$material_items = $requisition->material_items();
				foreach ($material_items as $item) {
					$material = $item->material_item();
				?>
					<tr>
						<td><?= $material->item_name ?></td>
						<td><?= $material->part_number ?></td>
						<td><?= $material->unit()->symbol ?></td>
						<?php
						$sources = $approval->material_items('all', $item->{$item::DB_TABLE_PK});
						?>
						<td style="width: 60%">
							<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
								<thead>
									<tr>
										<th>Source</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($sources as $source) {
										$source_name = $source->source_name();
										$total_amount += $amount = $source->approved_quantity * $source->approved_rate;
									?>
										<tr>
											<td><?= $source_name ?></td>
											<td style="text-align: right"><?= $source->approved_quantity ?></td>
											<td style="text-align: right"><?= number_format($source->approved_rate, 3) ?></td>
											<td style="text-align: right"><?= number_format($amount, 2) ?></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php
				}

				$asset_items = $requisition->asset_items();
				foreach ($asset_items as $item) {
					$asset_item = $item->asset_item();
				?>
					<tr>
						<td><?= $asset_item->asset_name ?></td>
						<th></th>
						<td>No.</td>
						<?php
						$sources = $approval->asset_items('all', $item->{$item::DB_TABLE_PK});
						?>
						<td style="width: 60%">
							<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
								<thead>
									<tr>
										<th>Source</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($sources as $source) {
										$source_name = $source->source_name();
										$total_amount += $amount = $source->approved_quantity * $source->approved_rate;
									?>
										<tr>
											<td><?= $source_name ?></td>
											<td style="text-align: right"><?= $source->approved_quantity ?></td>
											<td style="text-align: right"><?= number_format($source->approved_rate, 3) ?></td>
											<td style="text-align: right"><?= number_format($amount, 2) ?></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php
				}

				$service_items = $requisition->service_items();
				foreach ($service_items as $item) {
				?>
					<tr>
						<td><?= $item->description ?></td>
						<th></th>
						<td><?= $item->measurement_unit()->symbol ?></td>
						<?php
						$sources = $approval->service_items('all', $item->{$item::DB_TABLE_PK});
						?>
						<td style="width: 60%">
							<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
								<thead>
									<tr>
										<th>Source</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($sources as $source) {
										$source_name = $source->source_name();
										$total_amount += $amount = $source->approved_quantity * $source->approved_rate;
									?>
										<tr>
											<td><?= $source_name ?></td>
											<td style="text-align: right"><?= $source->approved_quantity ?></td>
											<td style="text-align: right"><?= number_format($source->approved_rate, 3) ?></td>
											<td style="text-align: right"><?= number_format($amount, 2) ?></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php
				}

				$cash_items = $requisition->cash_items();
				foreach ($cash_items as $item) {
				?>
					<tr>
						<td><?= $item->description ?></td>
						<td></td>
						<td><?= $item->measurement_unit()->symbol ?></td>
						<?php
						$sources = $approval->cash_items($item->{$item::DB_TABLE_PK});
						?>
						<td style="width: 60%">
							<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
								<thead>
									<tr>
										<th>Source</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($sources as $source) {
										// $source_name = !empty($source->account()) ? $source->account()->account_name : 'CASH';
										$total_amount += $amount = $source->approved_quantity * $source->approved_rate;
									?>
										<tr>
											<td>CASH</td>
											<td style="text-align: right"><?= $source->approved_quantity ?></td>
											<td style="text-align: right"><?= number_format($source->approved_rate, 3) ?></td>
											<td style="text-align: right"><?= number_format($amount, 2) ?></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php
				}

				?>
			</tbody>
			<tfoot>
				<tr>
					<th style="text-align: right" colspan="3">Total</th>
					<th style="text-align: right"><?= number_format($total_amount, 2) ?></th>
				</tr>
				<tr>
					<th style="text-align: right" colspan="3">Freight</th>
					<th style="text-align: right"><?= number_format($approval->freight, 2) ?></th>
				</tr>
				<tr>
					<th style="text-align: right" colspan="3">Inpection and Other Charges</th>
					<th style="text-align: right"><?= number_format($approval->inspection_and_other_charges, 2) ?></th>
				</tr>
				<?php
				$total_amount = $total_amount + $approval->freight + $approval->inspection_and_other_charges;
				if (!is_null($approval->vat_inclusive)) { ?>
					<tr>
						<th style="text-align: right" colspan="3">VAT </th>
						<th style="text-align: right"><?= $currency->symbol . '  ' . number_format($total_amount * 0.18, 2) ?></th>
					</tr>
				<?php }
				$grand_total = !is_null($approval->vat_inclusive) ? $total_amount * 1.18 : $total_amount;
				?>
				<tr>
					<th style="text-align: right" colspan="3">Grand Total</th>
					<th style="text-align: right"><?= number_format($grand_total, 2) ?></th>
				</tr>
			</tfoot>
		</table><br />

	<?php
}
	?>