<?php if($print){
    $this->load->view('includes/mpdf_css');
    $this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PURCHASE ORDER REPORT</h2>
<br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width:50%">
            <strong>Project: </strong><?= $project->project_name ?>
        </td>
        <td style=" width:25%">
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td style=" width:25%">
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php }?>

<table <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover">
    <thead>
		 <tr>
		    <th>Order Date</th><th>Order No.</th><th>Vendor</th><th>Value</th><th>Exchange Rate</th><th>Value In Tshs </th><th>Received Value in Tshs</th>
		</tr>
    </thead>
    <tbody>

		<?php

		       $total_amount_in_base_currency = 0;
		       $total_received_value = 0;

		        foreach ($purchase_orders as $purchase_order) {
		            $currency = $purchase_order->currency();
		            $total_amount_in_base_currency += $order_value = $purchase_order->total_order_in_base_currency();
		            $total_received_value += $received_value = $purchase_order->material_received_value_with_duties();
                ?>

		        <tr>
		            <td><?php if($purchase_order->status == "CANCELLED") { ?><strike><?= custom_standard_date($purchase_order->issue_date) ?></strike><?php } else { ?><?= custom_standard_date($purchase_order->issue_date) ?><?php } ?></td>
                    <td><?php if($purchase_order->status == "CANCELLED") { ?><strike><?= $print ? $purchase_order->order_number() : anchor(base_url('procurements/preview_purchase_order/'.$purchase_order->{$purchase_order::DB_TABLE_PK}),$purchase_order->order_number(),' target="_blank"') ?></strike><?php } else { ?><?= $print ? $purchase_order->order_number() : anchor(base_url('procurements/preview_purchase_order/'.$purchase_order->{$purchase_order::DB_TABLE_PK}),$purchase_order->order_number(),' target="_blank"') ?><?php } ?></td>
		            <td><?php if($purchase_order->status == "CANCELLED") { ?><strike><?= $purchase_order->vendor()->vendor_name ?></strike><?php } else { ?><?= $purchase_order->vendor()->vendor_name ?><?php } ?></td>
		            <td style="text-align: right"><?php if($purchase_order->status == "CANCELLED") { ?><strike><?= $currency->symbol.' '. number_format($purchase_order->total_order_value(),2) ?></strike><?php } else { ?><?= $currency->symbol.' '. number_format($purchase_order->total_order_value(),2) ?><?php } ?></td>
                    <td style="text-align: right"><?php if($purchase_order->status == "CANCELLED") { ?><strike><?= $currency->rate_to_native($purchase_order->issue_date) ?></strike><?php } else { ?><?= $currency->rate_to_native($purchase_order->issue_date) ?><?php } ?></td>
		            <td style="text-align: right"><?php if($purchase_order->status == "CANCELLED") { ?><strike><?= number_format($order_value,2) ?></strike><?php } else { ?><?= number_format($order_value,2) ?><?php } ?></td>
		            <td style="text-align: right"><?php if($purchase_order->status == "CANCELLED") { ?><strike><?= number_format($received_value,2) ?></strike><?php } else { ?><?= number_format($received_value,2) ?><?php } ?></td>
                </tr>

		      <?php  } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">TOTAL</th>
            <th style="text-align: right"><?= number_format($total_amount_in_base_currency,2) ?></th>
            <th style="text-align: right"><?= number_format($total_received_value,2) ?></th>
        </tr>
    </tfoot>

</table>