<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 23/05/2019
 * Time: 11:05
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center"><?= $project->project_name ?> UNRECEIVED GOODS</h2>
<br/>
<table style="font-size: 12px" width="100%">
	<tr>
		<td style="  vertical-align: top">
			<strong>As Of: </strong><?= set_date($as_of) ?>
		</td>
	</tr>
</table>
<br/>
<table width="100%" border="1" cellspacing="0" style="font-size: 11px">
	<thead>
	<tr>
		<th>Order No.</th><th>Order Value</th><th>Received Value</th><th>Paid Amount</th><th>Unreceived Value</th><th>Balance(Base Currency)</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$running_balance = 0;
	foreach($project_orders as $index=>$project_order){
		$running_balance += $unreceived_goods[$project->project_name][$project_order]['balance_in_base_currency'];
		?>
		<tr>
			<td style="text-align: left"><?= 'P.O/'.add_leading_zeros($unreceived_goods[$project->project_name][$project_order]['order_id']) ?></td>
			<td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['order_value'],2) ?></td>
			<td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['received_value'],2) ?></td>
			<td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['paid_amount'],2) ?></td>
			<td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['unreceived_value'],2) ?></td>
			<td style="text-align: right"><?= $native_currency->symbol.' '.number_format($running_balance,2) ?></td>
		</tr>
		<?php
	}
	?>
	<tr style="background-color: #91e8e1;">
		<td colspan="5" style="text-align: left"><strong>TOTAL IN BASE CURRENCY</strong></td>
		<td style="text-align: right;"><strong><?= $native_currency->symbol.' '.number_format($unreceived_goods[$project->project_name]['summation_base_currency'],2) ?></strong></td>
	</tr>
	</tbody>
</table>
