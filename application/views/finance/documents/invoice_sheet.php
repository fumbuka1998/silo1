<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/28/2018
 * Time: 2:18 PM
 */

$this->load->view('includes/letterhead');
switch ($invoice_type){
	case 'sales':
		$stakeholder = 'Client';
		$client = $invoice->invoice_to();
		$title = 'Sales Invoice';
		$corresponding_to = 'Sale/Certificate/Service';
		$desc_or_note = 'Notes';
		break;
	case 'purchases':
		$stakeholder = 'Vendor';
		$client = $invoice->stakeholder();
		$title = 'Purchase Invoice';
		$corresponding_to = 'Order';
		$desc_or_note = 'Descriptions';
		break;
}
?>
    <h2 style="text-align: center"><?= $title ?></h2>
	<table width="50%" cellspacing="0.5">
		<tr>
			<td style="text-align: left; font-size: small">
				<span class="pull-left"><strong>INVOICE NO: </strong></span>
			</td>
			<td style="text-align: right; font-size: small">
				<span class="pull-right"><?= $invoice->invoice_no ?></span>
			</td>
		</tr>
		<tr>
			<td style="text-align: left; font-size: small">
				<span class="pull-left"><strong>REFERENCE: </strong></span>
			</td>
			<td style="text-align: right; font-size: small">
				<span class="pull-right"><?= $invoice->reference ?></span>
			</td>
		</tr>
		<tr>
			<td style="text-align: left; font-size: small">
				<span class="pull-left"><strong>DATE: </strong></span>
			</td>
			<td style="text-align: right; font-size: small">
				<span class="pull-right"><?= custom_standard_date($invoice->invoice_date) ?></span>
			</td>
		</tr>
		<tr>
			<td style="text-align: left; font-size: small">
				<span class="pull-left"><strong>DUE DATE: </strong></span>
			</td>
			<td style="text-align: right; font-size: small">
				<span class="pull-right"><?= custom_standard_date($invoice->due_date) ?></span>
			</td>
		</tr>
		<tr>
			<td style="text-align: left; font-size: small">
				<span class="pull-left"><strong>CURRENCY: </strong></span>
			</td>
			<td style="text-align: right; font-size: small">
				<span class="pull-right"><?= $invoice->currency()->name_and_symbol() ?></span>
			</td>
		</tr>
	</table>
	<br/>
    <table style="font-size: 14px" width="100%">
		<?php if($invoice_type == 'sales'){ ?>
			<tr>
				<td style="text-align: left; width: 50%;">FROM</td><td style="text-align: left; width: 50%;">TO</td>
			</tr>
		<?php } else { ?>
		<tr>
			<td style="text-align: left; width: 50%;">TO</td><td style="text-align: left; width: 50%;">FROM</td>
		</tr>
		<?php } ?>
        <tr>
			<td style="text-align: left; width: 50%;">
				<strong><?= $company_details->company_name ?></strong><br/>
				<span><strong>TIN: </strong><?= $company_details->tin ?></span><br/>
				<span><strong>VRN: </strong><?= $company_details->vrn ?></span><br/>
				<?php
				$company_address = explode(',', $company_details->address);
				foreach ($company_address as $detail){
					?>
					<span><?= $detail ?></span><br/>
					<?php
				}
				?>
				<span><?= $company_details->mobile ?></span><br/>
				<span><?= $company_details->fax ?></span><br/>
				<span><?= $company_details->email ?></span><br/>
				<span><?= $company_details->website ?></span>
			</td>
            <td style="text-align: left; width: 50%;">
                <span><?= $client->stakeholder_name ?></span><br/>
                <?php
                $client_address = explode(PHP_EOL, $client->address);
                foreach ($client_address as $item){
                    ?>
                    <span><?= $item ?></span><br/>
                    <?php
                }
                ?>
                <span><?= $client->phone ?></span><br/>
                <span><?= $client->alternative_phone ?></span><br/>
                <span><?= $client->email ?></span><br/>
            </td>
        </tr>
    </table>
    <br/>
    <table  style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th style="width: 10px">S/N</th><th style="width: 350px">Description</th><th>UOM</th><th>Quantity</th><th>Rate</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>

       <?php
       $invoice_amount = $sn = 0;
       if($invoice_type == 'sales') {
		   $invoice_items = $invoice->invoice_items();
		   foreach ($invoice_items as $item) {
			   $sn++;
			   $invoice_amount += $amount = ($item->quantity * $item->rate);
			   ?>

			   <tr>
				   <td><?= $sn ?></td>
				   <td><?= $item->description ?></td>
				   <td><?= $item->measurement_unit()->symbol ?></td>
				   <td style="text-align: right"><?= $item->quantity ?></td>
				   <td style="text-align: right"><?= number_format($item->rate, 2) ?></td>
				   <td style="text-align: right"><?= number_format($amount, 2) ?></td>
			   </tr>
			   <?php
		   }
	   } else {
       		$sn++;
       		$invoice_amount = $invoice->amount;
       		?>
		   <tr>
			   <td><?= $sn ?></td>
			   <td><?= $invoice->description ?></td>
			   <td><?= 'Item' ?></td>
			   <td style="text-align: right"><?= 1 ?></td>
			   <td style="text-align: right"><?= number_format($invoice->amount, 2) ?></td>
			   <td style="text-align: right"><?= number_format($invoice->amount, 2) ?></td>
		   </tr>
		   <?php
	   }

         if($invoice->vat_inclusive == 1) {
             $vat_amount = (0.01 * $invoice->vat_percentage * $invoice_amount);
         } else {
             $vat_amount = 0;
         }
         $grand_actual = ($vat_amount + $invoice_amount);
       ?>

       <tr>
           <td colspan="5" style="text-align: right"><strong>TOTAL</strong></td>
           <td style="text-align: right"><strong><?= number_format($invoice_amount, 2)  ?></strong></td>
       </tr>
       <tr>
           <td colspan="5" style="text-align: right"><strong>VAT</strong></td>
           <td style="text-align: right"><strong><?= number_format($vat_amount, 2)  ?></strong></td>
       </tr>
       <tr>
           <td colspan="5" style="text-align: right"><strong>GRAND TOTAL</strong></td>
           <td style="text-align: right"><strong><?= number_format($grand_actual, 2)  ?></strong></td>
       </tr>
       <tr>
           <td colspan="6"><strong>Amount In Words: </strong>&nbsp;<?= numbers_to_words($grand_actual).' '.$invoice->currency()->currency_name ?><br/></td>
       </tr>
    </tbody>
    </table>
    <br/>
    <table style="font-size: 12px"  width="100%" >
        <?php if($invoice == 'sales'){ ?>
			<tr>
				<td colspan="2" style="text-align: left">
					<strong>Bank Details:</strong><br/>
					<?= nl2br($invoice->bank_details) ?>
				</td>
			</tr>
			<br/>
			<br/>
        <?php } ?>
        <tr>
            <td colspan="2">
                <strong>Payment Terms:</strong><br/>
                <?= nl2br($invoice->payment_terms) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr/></td>
        </tr>
        <br/>
		<?php if($invoice == 'sales'){ ?>
        <tr>
            <td  colspan="2" style="vertical-align: top; text-align: center">
                <strong>Issued By: </strong><br/><br/>
                <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span><br/>
                <?= $invoice->created_by()->full_name() ?>
            </td>
        </tr>
		<?php } ?>
    </table>
