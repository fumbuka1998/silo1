<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 26/10/2018
 * Time: 08:23
 */
?>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="col-xs-12 table-responsive">
				<table stakeholder_id="<?= $stakeholder->{$stakeholder::DB_TABLE_PK} ?>" class="table table-bordered table-hover stakeholder_invoices_list">
					<thead>
					<tr>
						<th>Invoice Date</th><th>Invoice Correspondence No.</th><th>Reference</th><th style="width: 200px !important;">Total Amount</th><th style="width: 200px !important;">Outstanding Balance</th><th style="width: 10%"></th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>
