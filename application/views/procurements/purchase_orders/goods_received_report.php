<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/13/2016
 * Time: 2:42 PM
 */

 
    $vendor = $order->stakeholder();
    $location = $order->location();
    $project = $order->project();
    $this->load->view('includes/mpdf_css');
    $this->load->view('includes/letterhead');
?>
<br><br>

     <h2 style="text-align: center;">
         <?= !$unreceived_only ? 'RECEIVED ITEMS REPORT' : 'PENDING ITEMS REPORT' ?>
     </h2>

<table style="font-size: 11px"  width="100%" border="1" cellpadding="4" cellspacing="0">
    <tr>
        <td>
            <b>VENDOR:</b><br/><br/>
            <?= $vendor->stakeholder_name ?><br/>
            <?= nl2br($vendor->address) ?>
        </td>
        <td nowrap="nowrap" style="vertical-align: top">
            <b>P.O. No. & DATE:</b><br/><br/>
            <h2><?= $order->order_number() ?></h2>
            Date: <?= custom_standard_date($order->issue_date) ?><br/>
            Currency: <?= $order->currency()->currency_name ?>
        </td>
    </tr>
</table>
<br/>

<p>
    <?php
        $project = $order->project();
        echo $project ? $project->project_name : '';
    ?>
</p>

<table style="font-size: 10px" width="100%" border="1" cellspacing="0">
    <thead>
        <tr style="background: #cdcdcd; color: #ed1c24; ">
            <th rowspan="2">SN</th>
            <th rowspan="2" width="20%">Material/Item</th>
            <th rowspan="2">Part No.</th>
            <th rowspan="2">Unit</th>
            <th colspan="3">Ordered</th><th colspan="3">Received</th><th colspan="3">Balance</th>
            </tr>
             <tr style="background: #cdcdcd; color: #ed1c24; ">
             <th>Quantity</th><th>Price</th><th>Amount</th>
             <th>Quantity</th><th>Price</th><th>Amount</th>
             <th>Quantity</th><th>Price</th><th>Amount</th>
            </tr>
    </thead>
    <tbody>
    <?php
        $total_amount = 0;
        $material_items = $order->material_items();
        $sn = 0;
        $total_received = 0;
        foreach($material_items as $item){
            $unreceived_quantity = $item->unreceived_quantity();
            if(!$unreceived_only || $unreceived_quantity > 0) {
                $price = ($order->vat_inclusive == "VAT COMPONENT" ? $item->price*(1+(0.01*$order->vat_percentage)) : $item->price);
                $total_amount += $amount = $item->quantity*$price;
                $material = $item->material_item();
                $received_quantity = ($item->quantity) - ($unreceived_quantity);
                $sn++;
                $total_received += $received_amount = $received_quantity * $price;
                ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= $material->item_name ?></td>
                    <td><?= $material->part_number ?></td>
                    <td><?= $material->unit()->symbol ?></td>
                    <td style="text-align: right"><?= round($item->quantity,2) ?></td>
                    <td style="text-align: right"><?= number_format($price, 2) ?></td>
                    <td style="text-align: right"><?= number_format($amount, 2) ?></td>

                    <td style="text-align: right"><?= $received_quantity ?></td>
                    <td style="text-align: right"><?= number_format($price, 2) ?></td>
                    <td style="text-align: right"><?= number_format($received_amount, 2) ?></td>

                    <td style="text-align: right"><?= $unreceived_quantity ?></td>
                    <td style="text-align: right"><?= number_format($price, 2) ?></td>
                    <td style="text-align: right"><?= number_format($unreceived_quantity * $price, 2) ?></td>
                </tr>
                <?php
            }
        }

    $asset_items = $order->asset_items();
    foreach($asset_items as $item){
        $unreceived_quantity = $item->unreceived_quantity();
        if(!$unreceived_only || $unreceived_quantity > 0) {
            $price = ($order->vat_inclusive == "VAT COMPONENT" ? $item->price*(1+(0.01*$order->vat_percentage)) : $item->price);
            $total_amount += $amount = $item->quantity * $price;
            $asset = $item->asset_item();
            $received_quantity = ($item->quantity) - ($unreceived_quantity);
            $sn++;
            $total_received += $received_amount = $received_quantity * $price;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $asset->asset_name ?></td>
                <td></td>
                <td>No.</td>
                <td style="text-align: right"><?= $item->quantity ?></td>
                <td style="text-align: right"><?= number_format($price, 2) ?></td>
                <td style="text-align: right"><?= number_format($amount, 2) ?></td>

                <td style="text-align: right"><?= $received_quantity ?></td>
                <td style="text-align: right"><?= number_format($price, 2) ?></td>
                <td style="text-align: right"><?= number_format($received_quantity*$price, 2) ?></td>

                <td style="text-align: right"><?= $unreceived_quantity ?></td>
                <td style="text-align: right"><?= number_format($price, 2) ?></td>
                <td style="text-align: right"><?= number_format(($unreceived_quantity*$price), 2) ?></td>
            </tr>
            <?php
        }
    }

    $service_items = $order->service_items();
    foreach($service_items as $item){
        $unreceived_quantity = $item->unreceived_quantity();
        if(!$unreceived_only || $unreceived_quantity > 0) {
            $price = ($order->vat_inclusive == "VAT COMPONENT" ? $item->price*(1+(0.01*$order->vat_percentage)) : $item->price);
            $total_amount += $amount = $item->quantity * $price;
            $received_quantity = ($item->quantity) - ($unreceived_quantity);
            $sn++;
            $total_received += $received_amount = $received_quantity * $price;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= wordwrap($item->description,35,'<br/>') ?></td>
                <td></td>
                <td>No.</td>
                <td style="text-align: right"><?= $item->quantity ?></td>
                <td style="text-align: right"><?= number_format($price, 2) ?></td>
                <td style="text-align: right"><?= number_format($amount, 2) ?></td>

                <td style="text-align: right"><?= $received_quantity ?></td>
                <td style="text-align: right"><?= number_format($price, 2) ?></td>
                <td style="text-align: right"><?= number_format($received_quantity*$price, 2) ?></td>

                <td style="text-align: right"><?= $unreceived_quantity ?></td>
                <td style="text-align: right"><?= number_format($price, 2) ?></td>
                <td style="text-align: right"><?= number_format(($unreceived_quantity*$price), 2) ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total</th>
            <th style="text-align: right"><?= number_format($total_amount,2) ?></th>
            <th colspan="2"></th>
            <th style="text-align: right"><?= number_format($total_received,2) ?></th>
            <th colspan="2"></th>
            <th style="text-align: right"><?= number_format($total_amount-$total_received,2) ?></th>
        </tr>
    </tfoot>
</table>

