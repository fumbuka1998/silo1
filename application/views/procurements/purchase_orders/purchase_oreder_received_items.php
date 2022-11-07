<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/13/2016
 * Time: 2:42 PM
 */

    $vendor = $order->vendor();
    $location = $order->location();
    $project = $order->project();
    $this->load->view('includes/mpdf_css');
    $this->load->view('includes/letterhead');
?>
<br><br>
<table style="font-size: 11px"  width="100%" border="1" cellpadding="4" cellspacing="0">
    <tr>
        <td>
            <b>FROM:</b><br/><br/>
            <?= $vendor->vendor_name ?><br/>
            <?= nl2br($vendor->address) ?>
        </td>
        <td style="vertical-align: top">
            <b>SHIP TO:</b><br/><br/>
            <?php
            $company_details = get_company_details();
            echo $company_details['address'];
            ?>
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

<table style="font-size: 10px" width="100%" border="1" cellspacing="0">
    <thead>
        <tr style="background: #cdcdcd; color: #ed1c24; ">
            <th rowspan="2">SN</th><th rowspan="2" width="20%">Material/Item</th><th rowspan="2">Unit</th>
            <th colspan="3">Received</th>
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
            $total_amount += $amount = $item->quantity*$item->price;
            $sn++;
            $material = $item->material_item();
            $unreceived_quantity = $item->unreceived_quantity();
            $received_quantity =($item->quantity)-($unreceived_quantity);

            $total_received += $received_amount = $received_quantity*$item->price;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $material->item_name ?></td>
                <td><?= $material->unit()->symbol ?></td>
               
                <td style="text-align: right"><?= $received_quantity ?></td>
                <td style="text-align: right"><?= number_format($item->price,2) ?></td>
                <td style="text-align: right"><?= number_format($received_amount,2) ?></td>

            </tr>
            <?php
        }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">Total</th>
            <th></th>
            <th></th>
            <th style="text-align: right"><?= number_format($total_received,2) ?></th>
           
        </tr>
    </tfoot>
</table>

