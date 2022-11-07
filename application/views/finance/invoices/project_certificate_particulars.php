<div class="box collapsed-box">
	<div class="box-header with-border bg-aqua-gradient">
		<h5 class="box-title collapse-title pull-left"  data-widget="collapse"><?= $certificate->certificate_number ?></h5>
		<div class="box-tools col-md-8 pull-right" style="text-align: right">
			<strong>AMOUNT :</strong><span style="width: 30%" class="pull-right"><?= number_format($certificate->certified_amount,2) ?></span>
		</div>
	</div><!-- /.box-header -->
	<div class="box-body">
		<div class="row">
			<div class="col-xs-12">
				<table cellspacing="0" cellpadding="6px" style="font-size: 14px; table-layout: fixed" width="100%">
					<thead>
					<tr style="background: silver">
						<td style="text-align: right; width: 20%;">
							<b>Certificate Date : </b>
							<input type="hidden" name="item_type" value="">
							<input type="hidden" name="debt_nature" value="certificate">
							<input type="hidden" name="debt_nature_id" value="<?= $certificate->{$certificate::DB_TABLE_PK} ?>">
							<input type="hidden" name="debted_item_id" value="<?= $certificate->{$certificate::DB_TABLE_PK} ?>">
						</td>
						<td style="text-align: left; width: 40%;">
							<span style="padding-left: 4%" class="pull-left"><?= set_date($certificate->certificate_date) ?></span>
							<input type="hidden" name="unit_id" value="<?= '' ?>">
							<input type="hidden" name="quantity" value="<?= 1 ?>">
							<input type="hidden" name="rate" value="<?= $certificate->certified_amount ?>">
						</td>
						<td style="text-align: right; width: 20%;">
						</td>
						<td style="text-align: left; width: 20%;">
						</td>
					</tr>
					<tr>
						<td style="text-align: right; width: 20%;">
							<b>Project : </b>
						</td>
						<td style="text-align: left; width: 40%; justify-content: center">
							<span style="padding-left: 4%" class="pull-left"><?= $certificate->project()->project_name ?></span>
						</td>
						<td style="text-align: right; width: 20%;">
							<b>Certificate No : </b>
						</td>
						<td style="text-align: left; width: 20%;">
							<span style="padding-left: 4%" class="pull-left"><?= $certificate->certificate_number ?></span>
						</td>
					</tr>
					<tr>
						<td style="text-align: right; width: 20%;">
							<b>Created By : </b>
						</td>
						<td style="text-align: left; width: 20%;">
							<span style="padding-left: 4%" class="pull-left"><?= $certificate->employee()->full_name() ?></span>
						</td>
						<td style="text-align: right; width: 20%;">
							<b>Certified Amount : </b>
						</td>
						<td style="text-align: left; width: 40%;">
							<span style="padding-left: 4%; background: aquamarine" class="pull-left"><?= number_format($certificate->certified_amount,2) ?></span>
						</td>
					</tr>
					</thead>
				</table>
				<strong>Comments</strong><br/>
				<span style="font-size: 12px; justify-content: center"><?= $certificate->comments != '' ? $certificate->comments : 'N/A' ?></span>
			</div>
		</div>
	</div><!-- /.box-body -->
</div>

