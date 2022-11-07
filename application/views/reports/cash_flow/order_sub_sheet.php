<?php
$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center"><?= $project_name ?> ORDERS</h2>
    <br/>
    <table style="font-size: 12px" width="100%">
        <tr>
            <td style="  vertical-align: top">
                <strong>From: </strong><?= custom_standard_date($from) ?>
            </td>
            <td style="  vertical-align: top">
                <strong>To: </strong><?= custom_standard_date($to) ?>
            </td>
        </tr>
    </table>
    <br/>
<table width="100%" border="1" cellspacing="0" style="font-size: 11px">
    <thead>
    <tr>
        <th rowspan="2">Order Number</th><th rowspan="2">Supplier</th><th colspan="2">Order Value</th><th colspan="2">Other Charges</th><th colspan="2">Paid Amount</th><th colspan="2">Balance</th>
    </tr>
    <tr>
        <th>Order Currency</th><th><?= $currency_symbol ?></th>
        <th>Charges Currency</th><th><?= $currency_symbol ?></th>
        <th>Order Currency</th><th><?= $currency_symbol ?></th>
        <th>Order Currency</th><th><?= $currency_symbol ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_order_value = $total_paid_amount = $total_other_charges = $total_balance = 0;
    foreach ($orders_with_balance as $order_item){
        $total_order_value += $order_item['value_in_current_currency'];
        $total_other_charges += $order_item['other_charges_in_current_currency'];
        $total_paid_amount += $order_item['paid_amount_in_current_currency'];
        $total_balance += $order_item['balance_in_current_currency'];
        ?>
        <tr>
            <td style="text-align: left"><?= $order_item['order_number'] ?></td>
            <td style="text-align: left"><?= $order_item['vendor_name'] ?></td>
            <td style="text-align: right"><?= $order_item['order_currency_symbol'] . ' ' . number_format($order_item['order_value']) ?></td>
            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['value_in_current_currency']) ?></td>
            <td style="text-align: right"><?= $order_item['other_charges_currency_symbol'] . ' ' . number_format($order_item['order_other_charges']) ?></td>
            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['other_charges_in_current_currency']) ?></td>
            <td style="text-align: right"><?= $order_item['order_currency_symbol'] . ' ' . number_format($order_item['paid_amount']) ?></td>
            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['paid_amount_in_current_currency']) ?></td>
            <td style="text-align: right"><?= $order_item['order_currency_symbol'] . ' ' . number_format($order_item['balance']) ?></td>
            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['balance_in_current_currency']) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2">TOTAL</th>
        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_order_value) ?></th>
        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_other_charges)?></th>
        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_paid_amount) ?></th>
        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_balance) ?></th>
    </tr>
    </tfoot>
</table>