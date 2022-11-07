<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 04/09/2018
 * Time: 09:25
 */

if(!empty($orders)){
    ?>
    <table <?php if($print){
?>
        width="100%" border="1" cellspacing="0" style="font-size: 11px"
            <?php
    } else { ?> class="table table-bordered table-hover" <?php } ?>>
        <thead>
            <tr>
                <th>Order Date</th><th>Order No.</th><th>Project</th><th>P.O Value</th>
                <th>Unreceived Value</th><th>Amount Paid</th><th>Amount Due</th>
                <th>Balance Due</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $balance_due = 0;
            foreach ($orders as $order){
                $currency = $order->currency();
                if($order->status == "CLOSED" || $order->status == 'CANCELLED') {
                    $balance_due += $amount_due = 0;
                } else {
                    $balance_due += $amount_due = $order->amount_due();
                }
                ?>
                <tr>
                    <td><?= custom_standard_date($order->issue_date) ?></td>
                    <?php if($print){ ?>
                        <td><?= $order->order_number() ?></td>
                    <?php } else { ?>
                        <td><?= anchor(base_url('procurements/preview_purchase_order/'.$order->{$order::DB_TABLE_PK}),$order->order_number(),'target="_blank" ') ?></td>
                    <?php } ?>
                    <td><?php if($order->status == "CANCELLED") { ?><strike><?= $order->cost_center_name() ?></strike><?php } else { ?><?=  $order->cost_center_name() ?><?php } ?></td>
                    <td style="text-align: right"><?php if($order->status == "CANCELLED") { ?><strike><?= $currency->symbol.' '. number_format($order->cif(),2) ?></strike><?php } else { ?><?=  $currency->symbol.' '. number_format($order->cif(),2) ?><?php } ?></td>
                    <td style="text-align: right"><?php if($order->status == "CANCELLED") { ?><strike><?= $currency->symbol.' '. number_format($order->unreceived_amount(),2) ?></strike><?php } else { ?><?=  $currency->symbol.' '. number_format($order->unreceived_amount(),2) ?><?php } ?></td>
                    <td style="text-align: right"><?php if($order->status == "CANCELLED") { ?><strike><?= $currency->symbol.' '. number_format($order->amount_paid(),2) ?></strike><?php } else { ?><?=  $currency->symbol.' '. number_format($order->amount_paid(),2) ?><?php } ?></td>
                    <td style="text-align: right"><?php if($order->status == "CANCELLED") { ?><strike><?= $currency->symbol.' '. number_format($amount_due,2) ?></strike><?php } else { ?><?=  $currency->symbol.' '. number_format($amount_due,2) ?><?php } ?></td>
                    <td style="text-align: right"><?= $currency->symbol.' '. number_format($balance_due,2) ?></td>
                </tr>
                <?php
            }
        ?>
        </tbody>
    </table>
    <?php

} else {
    ?>
    <div class="alert alert-info">No Orders found</div>
<?php
}