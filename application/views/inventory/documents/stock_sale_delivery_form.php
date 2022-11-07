<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/14/2018
 * Time: 3:33 AM
 */

$this->load->view('includes/letterhead');
$currency = $stock_sale->currency();
$client = $stock_sale->stakeholder();
?>
<table cellspacing="0" cellpadding="6px" style="font-size: 14px" width="100%">
    <tr>
        <th colspan="2"><h2>DELIVERY NOTE</h2><hr/></th>
    </tr>
    <tr>
        <td>
            <b>No : </b><?= $stock_sale->sale_number() ?>
        </td>
        <td>
            <b>Date : </b><?= custom_standard_date($stock_sale->sale_date) ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>M/s. : </b><br/>
            <?= $client->stakeholder_name ?><br/>
            <?= nl2br($client->address) ?>
        </td>
        <td>
            <b>Reference : </b><?= $stock_sale->reference ?><br/>
        </td>
    </tr>
    <?php
    $project_name = $stock_sale->project()->project_name != '' ? $stock_sale->project()->project_name : "UNASSIGNED";
    ?>
    <tr>
        <td>
            <b>Project : </b><?= $project_name ?>
        </td>
        <td>
        </td>
    </tr>
</table>
<br/>
<table cellpadding="2px" style="font-size: 12px" width="100%" border="1" cellspacing="0">
    <thead>
    <tr>
        <th>SN</th><th>Particulars</th><th>Unit</th><th>Quantity</th><th>Remarks</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    $items = $stock_sale->material_items();
    foreach($items as $item){
        $sn++;
        $sale = $item->material_item();
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $sale->item_name ?></td>
            <td><?= $sale->unit()->symbol ?></td>
            <td style="text-align: center"><?= $item->quantity ?></td>
            <td><?= $item->remarks ?></td>
        </tr>
        <?php
    }

    $distinct_items = $stock_sale->distinct_asset_items();
    foreach($distinct_items as $item){
        $stock_sale_asset_items = $item->stock_sale_asset_items($stock_sale->{$stock_sale::DB_TABLE_PK});
        $quantity = 0;
        foreach ($stock_sale_asset_items as $stock_sale_asset_item){
            $quantity++;
        }

        $sn++;
        ?>
        <tr>

            <td><?= $sn ?></td>
            <td><?= $item->asset_name ?></td>
            <td>PCS</td>
            <td style="text-align: center"><?= $quantity ?></td>
            <td></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<table style="margin-top: 40%" border="1" cellspacing="0" cellpadding="6px" width="100%">
    <tr>
        <td style="vertical-align: top">
            Vehicle Number: <br/>
        </td>
        <td style="vertical-align: top">
            Driver Name: <br/>
        </td>
    </tr>
    <tr>
        <td style="width: 50%; vertical-align: top">
            Received above goods in good order and condition.<br/><br/>
            Received By<br/>
            (Full Name):
        </td>
        <td style="width: 50%; vertical-align: top">
            Company Official Rubber Stamp
        </td>
    </tr>
    <tr>
        <td colspan="2">Comments<br/><br/><br/><br/><?= $stock_sale->comments ?></td>
    </tr>
    <tr>
        <td colspan="2">
            <b>Issued By:</b> <?= $stock_sale->employee()->full_name() ?>,</br>
        </td>
    </tr>
</table>

