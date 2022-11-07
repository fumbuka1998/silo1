<div class="box collapsed-box">
	<div class="box-header with-border bg-aqua-gradient">
		<h5 class="box-title collapse-title pull-left"  data-widget="collapse"><?= $service->maintenance_services_no() ?></h5>
		<div class="box-tools col-md-8 pull-right" style="text-align: right">
			<strong>AMOUNT :</strong><span style="width: 30%" class="pull-right"><?= number_format($service->maintenance_cost(),2) ?></span>
		</div>
	</div><!-- /.box-header -->
	<div class="box-body">
		<div class="row">
			<div class="col-xs-12">
				<table style="font-size: 10px" width="100%" border="1" cellpadding="3" cellspacing="0">
					<thead>
					<tr style="background: #cdcdcd; color: #ed1c24; ">
						<th>S/N</th><th>Description</th><th>UOM</th><th style="text-align: right">Quantity</th><th style="text-align: right">Rate</th><th style="text-align: right">Amount</th>
					</tr>
					<?php
					$total_amount = $sn = 0;
					$service_items = $service->maintenance_service_items();
					$currency = $service->currency();
					foreach ($service_items as $item){
						$sn++;
						$measurement_unit = $unit->measurement_unit_details($item->measurement_unit_id);
						?>
						<tr>
							<td><?= $sn ?></td>
							<td style="text-align: left">
								<?= $item->description ?>
								<input type="hidden" name="item_type" value="">
								<input type="hidden" name="debt_nature" value="maintenance_service">
								<input type="hidden" name="debt_nature_id" value="<?= $service->{$service::DB_TABLE_PK} ?>">
								<input type="hidden" name="debted_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
							</td>
							<td style="width: 2%">
								<?= $measurement_unit->symbol ?>
								<input type="hidden" name="unit_id" value="<?= $measurement_unit->{$measurement_unit::DB_TABLE_PK} ?>">
							</td>
							<td style="text-align: right; width: 5%;">
								<?=  $item->quantity ?>
								<input type="hidden" name="quantity" value="<?= $item->quantity ?>">
							</td>
							<td style="text-align: right; width: 15%;">
								<?= $currency->symbol .' &nbsp; '. number_format($item->rate, 2) ?>
								<input type="hidden" name="rate" value="<?= $item->rate ?>">
							</td>
							<td style="text-align: right; width: 15%;"><?= $currency->symbol .' &nbsp; '. number_format(($item->quantity * $item->rate), 2) ?></td>
						</tr>

						<?php
						$total_amount += $item->quantity * $item->rate;
					}
					?>
					<tr>
						<td colspan="5"><strong>TOTAL</strong></td>
						<td style="text-align: right; width: 15%; background: aquamarine"><strong><?= $currency->symbol .' &nbsp; '. number_format($total_amount, 2)  ?></strong></td>
					</tr>
					</thead>
				</table>
				<br/>
				<table style="font-size: 12px" width="100%">
					<thead>
					<tr>
						<td style="width: 25%">
							<strong>Issued By: </strong><br/><?= $service->crested_by()->full_name() ?>
						</td>
						<td style="width: 25%">
							<strong>Issue Date: </strong><br/><?= custom_standard_date($service->service_date) ?>
						</td>
						<td  style="vertical-align: top">
							<strong>Remarks: </strong><br/><?= $service->remarks ?>
						</td>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</div><!-- /.box-body -->
</div>
