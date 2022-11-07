<div class="box collapsed-box">
	<div class="box-header with-border bg-aqua-gradient">
		<h5 class="box-title collapse-title pull-left"  data-widget="collapse"><?= $order->order_number() ?></h5>
		<div class="box-tools col-md-8 pull-right" style="text-align: right">
			<strong>AMOUNT :</strong><span style="width: 30%" class="pull-right"><?= number_format($order->cif(),2) ?></span>
		</div>
	</div><!-- /.box-header -->
	<div class="box-body">
		<div class="row">
			<div class="col-xs-12">
				<table style="font-size: 10px" width="100%" border="1" cellpadding="3" cellspacing="0">
					<thead>
					<tr style="background: #cdcdcd; color: #ed1c24; ">
						<th>S/No</th><th>Material/Item</th><th  style="width: 13%;">Part No.</th><th>Unit</th><th style="text-align: right">Quantity</th><th style="text-align: right">Price</th><th style="text-align: right">Amount</th>
					</tr>
					<?php
					$total_amount = 0;
					$material_items = $order->material_items();
					$currency = $order->currency();
					$sn = 0;
					foreach($material_items as $item){
						$vat_percentage_value = $order->vat_percentage;
						$vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price * 100)/(100 + $vat_percentage_value) : $item->price;
						$total_amount += $amount = $item->quantity*$vat_exclusive_price;
						$sn++;
						$material = $item->material_item();
						?>
						<tr>
							<td><?= $sn ?></td>
							<td style="text-align: left"><?= $material->item_name ?></td>
							<td><?= $material->part_number ?></td>
							<td><?= $material->unit()->symbol ?></td>
							<td style="text-align: right"><?= $item->quantity ?></td>
							<td style="text-align: right"><?= $currency->symbol.' '. $vat_exclusive_price ?></td>
							<td style="text-align: right"><?= $currency->symbol.' '. number_format($amount,2) ?></td>
						</tr>
						<?php
					}

					$asset_items = $order->asset_items();
					foreach($asset_items as $item){
						$vat_percentage_value = $order->vat_percentage;
						$vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price * 100)/(100 + $vat_percentage_value) : $item->price;
						$total_amount += $amount = $item->quantity*$vat_exclusive_price;
						$sn++;
						$asset = $item->asset_item();
						?>
						<tr>
							<td><?= $sn ?></td>
							<td style="text-align: left"><?= $asset->asset_name ?></td>
							<td><?= $asset->part_number ?></td>
							<td>NO.</td>
							<td style="text-align: right"><?= $item->quantity ?></td>
							<td style="text-align: right"><?= $currency->symbol.' '. $vat_exclusive_price ?></td>
							<td style="text-align: right"><?= $currency->symbol.' '. number_format($amount,2) ?></td>
						</tr>
						<?php
					}

					$service_items = $order->service_items();
					foreach ($service_items as $item){
						$vat_percentage_value = $order->vat_percentage;
						$vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price * 100)/(100 + $vat_percentage_value) : $item->price;
						$total_amount += $amount = $item->quantity*$vat_exclusive_price;
						$sn++;
						?>
						<tr>
							<td><?= $sn ?></td>
							<td style="text-align: left"><?= $item->description ?></td>
							<td></td>
							<td><?= $item->measurement_unit()->symbol ?></td>
							<td style="text-align: right"><?= $item->quantity ?></td>
							<td style="text-align: right"><?= $currency->symbol.' '. $vat_exclusive_price ?></td>
							<td style="text-align: right"><?= $currency->symbol.' '. number_format($amount,2) ?></td>
						</tr>
						<?php
					}
					?>
					<tr>
						<th style="text-align: right" colspan="6">Total</th>
						<input type="hidden" name="item_type" value="">
						<input type="hidden" name="debt_nature" value="order_invoice">
						<input type="hidden" name="debt_nature_id" value="<?= $order->{$order::DB_TABLE_PK} ?>">
						<input type="hidden" name="debted_item_id" value="<?= $order->{$order::DB_TABLE_PK} ?>">
						<input type="hidden" name="unit_id" value="<?= '' ?>">
						<input type="hidden" name="quantity" value="<?= 1 ?>">
						<input type="hidden" name="rate" value="<?= $total_amount ?>">
						<th style="text-align: right"><?= $currency->symbol.' '. number_format($total_amount,2) ?></th>
					</tr>
					<?php
					if($order->freight > 0 || $order->inspection_and_other_charges > 0 || !is_null($order->vat_inclusive)){
						if($order->freight > 0){
							$vat_percentage_value = $order->vat_percentage;
							$freight_vat_exclusive = $order->vat_inclusive == 'VAT PRICED' ? ($order->freight * 100)/(100 + $vat_percentage_value) : $order->freight;
							$total_amount = $total_amount + $freight_vat_exclusive;
							?>
							<tr>
								<th style="text-align: right"  colspan="6">Freight</th>
								<th style="text-align: right"><?=$currency->symbol.' '.  number_format($freight_vat_exclusive,2) ?></th>
							</tr>
						<?php }
						if($order->inspection_and_other_charges > 0){
							$vat_percentage_value = $order->vat_percentage;
							$inspection_and_other_charges_vat_exclusive = $order->vat_inclusive == 'VAT PRICED' ? ($order->inspection_and_other_charges * 100)/(100 + $vat_percentage_value) : $order->inspection_and_other_charges;
							$total_amount = $total_amount + $inspection_and_other_charges_vat_exclusive;
							?>
							<tr>
								<th style="text-align: right"  colspan="6">Inspection and Other Charges</th>
								<th style="text-align: right"><?=$currency->symbol.' '.  number_format($inspection_and_other_charges_vat_exclusive,2) ?></th>
							</tr>
						<?php }

						$grand_total = $total_amount;
						if(!is_null($order->vat_inclusive)){
							$vat_percentage_value = $order->vat_percentage;
							$grand_total = $grand_total*((100 + $vat_percentage_value)/100);
							?>
							<tr>
								<th style="text-align: right" colspan="6">VAT</th>
								<th style="text-align: right"><?= $currency->symbol.' '. number_format($total_amount* ($vat_percentage_value/100),2) ?></th>
							</tr>
						<?php }  ?>
						<tr>
							<th style="text-align: right"  colspan="6">Grand Total <?= $order->vat_inclusive ? '(VAT Inclusive)' : ''  ?>  </th>
							<th style="text-align: right"><?= $currency->symbol.' '. number_format($grand_total,2) ?></th>
						</tr>
					<?php } ?>
					</thead>
				</table>
				<div style="font-size: 12px">
					<strong>Terms &amp; Conditions</strong><br/><?= $order->comments != '' ? nl2br($order->comments) : 'N/A' ?>
					<br/>
				</div>
				<table style="font-size: 12px" width="100%">
					<thead>
					<tr>
						<td width="60%">&nbsp;</td>
						<th>Delivery Date: </th>
						<td>
							<?= $order->delivery_date != '' ? custom_standard_date($order->delivery_date) : 'N/A' ?>
						</td>
					</tr>
					<tr>
						<td width="60%">&nbsp;</td>
						<th>Prepared By: </th>
						<td>
							<?= $order->employee()->full_name() ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<th>Order Status</th>
						<td><?= $order->status ?></td>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</div><!-- /.box-body -->
</div>
