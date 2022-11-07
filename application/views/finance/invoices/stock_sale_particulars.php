<div class="box collapsed-box">
	<div class="box-header with-border bg-aqua-gradient">
		<h5 class="box-title collapse-title pull-left"  data-widget="collapse"><?= $sale->sale_number() ?></h5>
		<div class="box-tools col-md-8 pull-right" style="text-align: right">
			<strong>AMOUNT :</strong><span style="width: 30%" class="pull-right"><?= number_format($sale->sale_amount()) ?></span>
		</div>
	</div><!-- /.box-header -->
	<div class="box-body">
		<div class="row">
			<div class="col-xs-12">
				<table cellspacing="0" cellpadding="6px" style="font-size: 14px" width="100%">
					<?php
					$project_name = $sale->project()->project_name != '' ? $sale->project()->project_name : "UNASSIGNED";
					?>
					<thead>
					<tr>
						<td style="text-align: left">
							<b>Project : </b><?= $project_name ?>
						</td>
						<td style="text-align: left">
							<b>Project : </b><?= $project_name ?>
						</td>
					</tr>
					</thead>
				</table>
				<table style="font-size: 10px" width="100%" border="1" cellpadding="3" cellspacing="0">
					<thead>
					<tr style="background: #cdcdcd; color: #ed1c24; ">
						<th style="text-align: left">Source</th>
						<th>Item</th>
						<th>UOM</th>
						<th style="text-align: right">Quantity</th>
						<th style="text-align: right">Price</th>
						<th style="text-align: right">Amount</th>
					</tr>
						<?php
						$material_items = $sale->material_items();
						$total_amount = $sn = 0;

						foreach ($material_items as $item){
							$sn++;
							$total_amount += $amount = $item->quantity*$item->price;
							?>
							<tr>
								<td style="text-align: left">
									<?= $item->source_sub_location()->sub_location_name ?>
									<input type="hidden" name="item_type" value="material">
									<input type="hidden" name="debt_nature" value="stock_sale">
									<input type="hidden" name="debt_nature_id" value="<?= $sale->{$sale::DB_TABLE_PK}?>">
								</td>
								<td style="text-align: left">
									<?= $item->material_item()->item_name ?>
									<input type="hidden" name="debted_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
								</td>
								<td style="width: 2%">
									<?= $item->material_item()->unit()->symbol ?>
									<input type="hidden" name="unit_id" value="<?= $item->material_item()->unit_id ?>">
								</td>
								<td style="text-align: center; width: 5%;">
									<?= $item->quantity ?>
									<input type="hidden" name="quantity" value="<?= $item->quantity ?>">
								</td>
								<td style="text-align: right; width: 15%;">
									<?= $currency->symbol .' &nbsp; '. number_format($item->price) ?>
									<input type="hidden" name="rate" value="<?= $item->price ?>">
								</td>
								<td style="text-align: right; width: 15%"><?= $currency->symbol .' &nbsp; '. number_format($amount) ?></td>
							</tr>
							<?php
						}
						$asset_items = $sale->asset_items();
						foreach ($asset_items as $item){
							$sn++;
							$total_amount += $item->price;
							?>
							<tr>
								<td style="text-align: left">
									<?= $item->source_sub_location()->sub_location_name ?>
									<input type="hidden" name="item_type" value="asset">
									<input type="hidden" name="debt_nature" value="stock_sale">
									<input type="hidden" name="debt_nature_id" value="<?= $sale->{$sale::DB_TABLE_PK}?>">
								</td>
								<td style="text-align: left">
									<?= $item->asset()->asset_code() ?>
									<input type="hidden" name="debted_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
									<input type="hidden" name="quantity" value="<?= 1 ?>">
									<input type="hidden" name="unit_id" value="<?= '' ?>">
								</td>
								<td style="width: 2%;">No.</td>
								<td style="text-align: center; width: 5%;">1</td>
								<td style="text-align: right; width: 15%;">
									<?= $currency->symbol .' &nbsp; '. number_format($item->price) ?>
									<input type="hidden" name="rate" value="<?= $item->price ?>">
								</td>
								<td style="text-align: right; width: 15%;"><?= $currency->symbol .' &nbsp; '. number_format($item->price) ?></td>
							</tr>
							<?php
						}
						?>
						<tr>
							<th colspan="5">TOTAL</th><th style="text-align: right; width: 15%; background: aquamarine;"><?= $currency->symbol .' &nbsp; '. number_format($total_amount) ?></th>
						</tr>
					</thead>
				</table>
				<strong>Comments</strong><br/>
				<span style="font-size: 12px"><?= $sale->comments != '' ? $sale->comments : 'N/A' ?></span>
				<table style="font-size: 12px" width="100%">
					<thead>
					<tr>
						<td colspan="2">
							<strong>Issued Date: </strong><?= custom_standard_date($sale->sale_date) ?>
						</td>
						<td style="width: 50%">
							<strong>Issued By: </strong><?= $sale->employee()->full_name() ?>
						</td>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</div><!-- /.box-body -->
</div>

