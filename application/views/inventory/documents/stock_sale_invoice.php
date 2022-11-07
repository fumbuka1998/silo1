<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/14/2018
 * Time: 3:33 AM
 */

$this->load->view('includes/letterhead');
$currency = $stock_sale->currency();
$client = $stock_sale->client();
$company_details = get_company_details();
?>
<form class="stock_sale_preview" stock_sale_id="<?= $stock_sale->{$stock_sale::DB_TABLE_PK} ?>">
<table cellspacing="0" cellpadding="6px" style="font-size: 14px" width="100%">
    <tr>
        <th colspan="2"><h2>TAX INVOICE</h2><hr/></th>
    </tr>
    <tr>
        <td rowspan="2">
            <b>Billed To. : </b><br/>
            <?= $client->client_name ?><br/>
            <?= nl2br($client->address) ?><br/>
        </td>
        <td style="text-align: right">
            <b>Date : </b><?= custom_standard_date($invoice->invoice_date) ?>
        </td>
    </tr>
    <tr>
        <td style="text-align: right">
            <b>Invoice No : </b><?= $invoice->invoice_number() ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align: right">
            <b>Reference : </b><?= $stock_sale->reference ?>
        <br/>
        <br/>
            <b>TIN : </b><?= $company_details->tin ?>
        <br/>
            <b>VRN : </b><?= $company_details->vrn ?>
        </td>
    </tr>
</table>
<br/>
<table cellpadding="2px" style="font-size: 12px" width="100%" border="1" cellspacing="0">
    <thead>
    <tr>
        <th>SN</th><th>Particulars</th><th>Unit</th><th>Quantity</th><th>Price</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_amount = $sn = 0;
    $items = $stock_sale->material_items();
    foreach($items as $item){
        $sn++;
        $total_amount += $amount = $item->quantity*$item->price;
        $sale = $item->material_item();
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $sale->item_name ?></td>
            <td><?= $sale->unit()->symbol ?></td>
            <td style="text-align: center"><?= $item->quantity ?></td>
            <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($item->price,2) ?></td>
            <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format(($item->quantity*$item->price),2) ?></td>
        </tr>
        <?php
    }

    $distinct_items = $stock_sale->distinct_asset_items();
    foreach($distinct_items as $item){
        $stock_sale_asset_items = $item->stock_sale_asset_items($stock_sale->{$stock_sale::DB_TABLE_PK});
        $quantity = $total_price = 0;
        foreach ($stock_sale_asset_items as $stock_sale_asset_item){
            $quantity++;
            $total_price += $stock_sale_asset_item->price;

        }

        $price = $total_price/$quantity;

        $sn++;
        $total_amount += $total_price;
        ?>
        <tr>

            <td><?= $sn ?></td>
            <td><?= $item->asset_name ?></td>
            <td>PCS</td>
            <td style="text-align: center"><?= $quantity ?></td>
            <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($price,2) ?></td>
            <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($total_price,2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="5" style="text-align: right">Sub Total(exc VAT)<th style="text-align: right"><?=  $currency->symbol .' &nbsp; '.  number_format($total_amount,2) ?></th></th>
        </tr>
    <tr>
        <?php
            $vat = $total_amount*$invoice->vat_percentage/100;
        ?>
        <th colspan="5" style="text-align: right">VAT @<?= $invoice->vat_percentage ?>%<th style="text-align: right"><?= $currency->symbol .' &nbsp; '.  number_format($vat,2) ?></th></th>
        </tr>
    <tr>
        <th colspan="5" style="text-align: right">Gross Due<th style="text-align: right"><?= $currency->symbol .' &nbsp; '.  number_format($total_amount+$vat,2) ?></th></th>
    </tr>
    </tfoot>
</table>
<br/>
<strong>Bank Details</strong><br/>
<span style="font-size: 12px"><?= $invoice->remarks != '' ? nl2br($invoice->remarks) : 'N/A' ?></span>
<br/><br/>
</form>

