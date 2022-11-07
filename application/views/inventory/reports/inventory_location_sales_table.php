<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/15/2019
 * Time: 12:19 PM
 */
if(!empty($inventory_location_sales)){
?>

<table
    <?php if($print){
        ?> width="100%" border="1" cellspacing="0"
        style="font-size: 10px"
        <?php
    } else {
        ?>
        class="table table-bordered table-hover table-striped"
        <?php
    } ?>>
    <thead>
    <tr>
        <th>Date</th><th>Item</th><th>UOM</th><th>Reference</th><th>Quantity</th><th>Purchasing Price</th><th>Selling Price</th><th>Unit Profit</th><th>Amount</th><th>Profit Amount</th>
    </tr>
    </thead>
    <tbody>

    <?php
    $row = '';
    $total_purchase_amount = $total_selling_amount = $total_profit = 0;
    foreach($inventory_location_sales as $inventory_sale) {
        $selling_amount = $inventory_sale->quantity * $inventory_sale->selling_price;
        $profit_amount = $inventory_sale->quantity*($inventory_sale->selling_price - $inventory_sale->purchasing_price);
        $total_profit += $profit_amount * $inventory_sale->exchange_rate;
        $total_selling_amount += $selling_amount;
        ?>
        <tr>
            <td><?= custom_standard_date($inventory_sale->sale_date) ?></td>
            <td><?= $inventory_sale->item_name ?></td>
            <td><?= $inventory_sale->unit_symbol ?></td>
            <?php if($print){ ?>
                <td><?= 'SALE/'.add_leading_zeros($inventory_sale->stock_sale_id) ?></td>
            <?php } else { ?>
                <td><?= anchor(base_url('inventory/preview_stock_sale/stock_sales_sheet/'.$inventory_sale->stock_sale_id),'SALE/'.add_leading_zeros($inventory_sale->stock_sale_id),'target="_blank" ') ?></td>
            <?php } ?>
            <td><?= $inventory_sale->quantity ?></td>
            <td style="text-align: right"><?= $inventory_sale->currency_symbol.'  '.number_format($inventory_sale->purchasing_price,2) ?></td>
            <td style="text-align: right"><?= $inventory_sale->currency_symbol.'  '.number_format($inventory_sale->selling_price,2) ?></td>
            <td style="text-align: right"><?= $inventory_sale->currency_symbol.'  '.number_format($inventory_sale->selling_price - $inventory_sale->purchasing_price,2) ?></td>
            <td style="text-align: right"><?= $inventory_sale->currency_symbol.'  '.number_format($selling_amount,2) ?></td>
            <td style="text-align: right"><?= $inventory_sale->currency_symbol.'  '.number_format($profit_amount,2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr style="font-weight: bold">
        <td colspan="8" style="text-align: left">TOTAL IN BASE CURRENCY</td>
        <td style="text-align: right"><?= 'TSH  '.number_format($total_selling_amount,2) ?></td>
        <td style="text-align: right"><?= 'TSH  '.number_format($total_profit,2) ?></td>
    </tr>
    </tfoot>
</table>
<?php } else {
    ?>
    <div style="text-align: center; height: 50px; border-radius: 8px;" class="info alert-info col-xs-12">
        No conducted sale(s) currently
    </div>
    <?php
} ?>