<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/11/2018
 * Time: 2:20 AM
 */

$this->load->view('includes/letterhead');
$project = $stock_sale->project();
$currency = $stock_sale->currency();
?>

<h2 style="text-align: center">STOCK SALES</h2>
<table style="font-size: 12px" width="100%">
    <tr>
        <td>
            <b>Sale No : </b><?= $stock_sale->sale_number() ?>
        </td>
        <td>
            <b>Project : </b><?= substr($project ? $project->project_name : 'N/A',0,50) ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>Location : </b><?= $stock_sale->location()->location_name ?>
        </td>
        <td>
            <b>Client : </b><?= $stock_sale->stakeholder()->stakeholder_name ?>
        </td>
    </tr>
</table>

<br/><br/>
<table style="font-size: 12px" width="100%" cellspacing="0" border="1">
    <thead>
    <tr>
        <th>Source</th>
        <th>Item</th>
        <th>UOM</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
        $material_items = $stock_sale->material_items();
        $total_amount = $sn = 0;

        foreach ($material_items as $item){
            $sn++;
            $total_amount += $amount = $item->quantity*$item->price;
            ?>
            <tr>
                <td><?= $item->source_sub_location()->sub_location_name ?></td>
                <td><?= $item->material_item()->item_name ?></td>
                <td><?= $item->material_item()->unit()->symbol ?></td>
                <td style="text-align: center"><?= $item->quantity ?></td>
                <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($item->price) ?></td>
                <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($amount) ?></td>
            </tr>
    <?php
        }
        $asset_items = $stock_sale->asset_items();
        foreach ($asset_items as $item){
            $sn++;
            $total_amount += $item->price;
            ?>
            <tr>
                <td><?= $item->source_sub_location()->sub_location_name ?></td>
                <td><?= $item->asset()->asset_code() ?></td>
                <td>No.</td>
                <td style="text-align: center">1</td>
                <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($item->price) ?></td>
                <td style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($item->price) ?></td>
            </tr>
    <?php
        }

    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">TOTAL</th><th style="text-align: right"><?= $currency->symbol .' &nbsp; '. number_format($total_amount) ?></th>
        </tr>
    </tfoot>
</table>
<br/>
<strong>Comments</strong><br/>
<span style="font-size: 12px"><?= $stock_sale->comments != '' ? $stock_sale->comments : 'N/A' ?></span>
<br/><br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="2">
            <strong>Issued Date: </strong><?= custom_standard_date($stock_sale->sale_date) ?>
        </td>
        <td style="width: 50%">
            <strong>Issued By: </strong><?= $stock_sale->employee()->full_name() ?>
        </td>
    </tr>
</table>

