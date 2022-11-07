<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/28/2018
 * Time: 2:18 PM
 */

$this->load->view('includes/letterhead');
$client = $outgoing_invoice->invoice_to();
?>
    <h2 style="text-align: center">TAX INVOICE</h2>
    <table style="font-size: 14px" width="100%">
        <tr>
            <td style="text-align: left; width: 50%;">
                <span><?= $client->client_name ?></span><br/>
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
            <td style="text-align: left; width: 50%;">
                <strong><?= $company_details->company_name ?></strong><br/>
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
        </tr>
    </table>
    <br/>
    <table width="100%">
        <tr>
            <td style="text-align: left; font-size: small">
                <strong>Invoice Date: </strong><?= custom_standard_date($outgoing_invoice->invoice_date) ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; font-size: small">
                <strong>Invoice No: </strong><?= $outgoing_invoice->invoice_no ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; font-size: small">
                <strong>Reference: </strong><?= $outgoing_invoice->reference ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; font-size: small">
                <strong>Currency: </strong><?= $outgoing_invoice->currency()->name_and_symbol() ?>
            </td>
        </tr>
    </table>
    <table  style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th style="width: 10px">S/N</th><th style="width: 350px">Description</th><th>UOM</th><th>Quantity</th><th>Rate</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>

       <?php
       $invoice_amount = $sn = 0;
       $invoice_items = $outgoing_invoice->outgoing_invoice_items();
       foreach ($invoice_items as $item){
             $sn++;
             $invoice_amount += $amount = ($item->quantity * $item->rate);
             ?>

             <tr>
                 <td><?= $sn ?></td>
                 <td><?= $item->description ?></td>
                 <td><?= $item->measurement_unit()->symbol ?></td>
                 <td style="text-align: right"><?= $item->quantity ?></td>
                 <td style="text-align: right"><?= number_format($item->rate, 2) ?></td>
                 <td style="text-align: right"><?= number_format($amount, 2)  ?></td>
             </tr>
             <?php
         }

         if($outgoing_invoice->vat_inclusive == 1) {
             $vat_amount = ($outgoing_invoice->vat_percentage / 100 * $invoice_amount);
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
           <td colspan="6"><strong>Amount In Words: </strong>&nbsp;<?= $outgoing_invoice->currency()->currency_name.' '.numbers_to_words($invoice_amount) ?><br/></td>
       </tr>
    </tbody>
    </table>
    <br/>
    <table style="font-size: 12px"  width="100%" >
        <tr>
            <td colspan="2" style="text-align: left">
                <strong>Bank Details:</strong><br/>
                <?= nl2br($outgoing_invoice->bank_details) ?>
            </td>
        </tr>
        <br/>
        <br/>
        <tr>
            <td colspan="2">
                <strong>Payment Terms:</strong><br/>
                <?= nl2br($outgoing_invoice->payment_terms) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr/></td>
        </tr>
        <br/>
        <tr>
            <td  colspan="2" style="vertical-align: top; text-align: center">
                <strong>Issued By: </strong><br/><br/>
                <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span><br/>
                <?= $outgoing_invoice->created_by()->full_name() ?>
            </td>
        </tr>
    </table>