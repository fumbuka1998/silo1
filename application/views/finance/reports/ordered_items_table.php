<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/13/2018
 * Time: 4:05 PM
 */

?>

<table  <?php if($print_pdf){ ?> style="font-size: 10px" width = "100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th>S/N</th><th>Receive Date</th><th style="width: 40%">Description</th><th>Order NO.</th><th>Cost Center</th><th>Ordered Quantity</th><th>Received Quantity</th><th>Rate</th><th style="width: 20%">Amount</th><th style="width: 20%">Requested From</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    $overall_amount = 0;
    foreach ($table_items as $item){
        $sn++;
        $overall_amount += $item['amount'];
        $purchase_order = $item['purchase_order'];
        ?>

        <tr>
            <td><?= $sn ?></td>
            <td><?= custom_standard_date(($item['receive_date'])) ?></td>
            <td style="width: 40%"><?= $item['description'] ?></td>
            <td><?= $print_pdf ? $purchase_order->order_number() : anchor(base_url('procurements/preview_purchase_order/' . $purchase_order->{$purchase_order::DB_TABLE_PK}),$purchase_order->order_number(), '" target="_blank"' )?></td>
            <td><?= $item['cost_center'] ?></td>
            <td><?= $item['ordered_quantity'].' '.$item['measurement_unit'] ?></td>
            <td><?= $item['received_quantity'].' '.$item['measurement_unit'] ?></td>
            <td nowrap style="text-align: right"><?= 'TSH '. number_format(($item['receiving_price']),2) ?></td>
            <td nowrap style="text-align: right; width: 20%;"><?= 'TSH '. number_format(($item['amount']),2) ?></td>
            <td style="width: 20%"><?= $item['ordered_from'] ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td></td>
        <td colspan="7" style="text-align: left"><strong>TOTAL</strong></td>
        <td style="text-align: right; width: 20%;"><strong><?= 'TSH '.number_format($overall_amount,2) ?></strong></td>
        <td ></td>
    </tr>
    </tbody>
</table>
