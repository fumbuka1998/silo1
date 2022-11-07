<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/13/2016
 * Time: 2:42 PM
 */

    $this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PURCHASE ORDER</h2>
<table style="font-size: 11px"  width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <td style="width: 70%">
            <?php
                $requisition = $order->requisition();
                $currency = $order->currency();
                ?>
                <h2>No. <?= $order->order_number() ?></h2>
                <?php
                echo $order->cost_center_name().'<br/>';
                echo (trim($order->reference) != '' ? '<b>RE: </b>'.$order->reference.'<br/>' : '');
                echo ($requisition != '' ? '<b>Req. No: </b>'.$requisition->requisition_number().'<br/>' : '');
                ?>
                <b>Date: </b><?= custom_standard_date($order->issue_date) ?><br/>
                <b>Currency: </b><?= $currency->currency_name ?>
                <?php
            ?>
            <br/>
            <br/>
            <b>M/s:</b><br/>
            <?= $vendor->stakeholder_name ?><br/>
            <?= nl2br($vendor->address) ?>
        </td>
        <td nowrap="nowrap" style="vertical-align: top">
            <?php $company_details = get_company_details(); ?>
            <?= $company_details->company_name; ?><br/>
            Email: <?= $company_details->email; ?><br/>
            Telephone: <?= $company_details->telephone; ?><br/>
            Mobile: <?= $company_details->mobile; ?><br/>
            Fax: <?= $company_details->fax; ?><br/>
            VRN: <?= $company_details->vrn; ?><br/>
            TIN: <?= $company_details->tin; ?><br/>
        </td>
    </tr>
</table>
<br/>

<table style="font-size: 10px" width="100%" border="1" cellpadding="3" cellspacing="0">
    <thead>
        <tr style="background: #cdcdcd; color: #ed1c24; ">
            <th>S/No</th><th>Material/Item</th><th  style="width: 13%;">Part No.</th><th>Unit</th><th>Quantity</th><th>Price</th><th>Amount</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total_amount = 0;
        $material_items = $order->material_items();
        $sn = 0;
        foreach($material_items as $item){
            $vat_percentage_value = $order->vat_percentage;
            $vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price * 100)/(100 + $vat_percentage_value) : $item->price;
            $total_amount += $amount = $item->quantity*$vat_exclusive_price;
            $sn++;
            $material = $item->material_item();


            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $material->item_name ?></td>
                <td><?= $material->part_number ?></td>
                <td><?= $material->unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->quantity ?></td>
                <td style="text-align: right"><?= $currency->symbol.' '. number_format($vat_exclusive_price,2) ?></td>
                <td style="text-align: right"><?= $currency->symbol.' '. number_format($amount,2) ?></td>
            </tr>
            <?php
        }

        $asset_items = $order->asset_items();
        foreach($asset_items as $item){
            $vat_percentage_value = $order->vat_percentage;
            $vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price * 100)/(100 + $vat_percentage_value) : $item->price;
            $total_amount += $amount = $item->quantity*$vat_exclusive_price;
            $sn++;
            $asset = $item->asset_item();
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $asset->asset_name ?></td>
                <td><?= $asset->part_number ?></td>
                <td>NO.</td>
                <td style="text-align: right"><?= $item->quantity ?></td>
                <td style="text-align: right"><?= $currency->symbol.' '. number_format($vat_exclusive_price,2) ?></td>
                <td style="text-align: right"><?= $currency->symbol.' '. number_format($amount,2) ?></td>
            </tr>
            <?php
        }

        $service_items = $order->service_items();
        foreach ($service_items as $item){
            $vat_percentage_value = $order->vat_percentage;
            $vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price * 100)/(100 + $vat_percentage_value) : $item->price;
            $total_amount += $amount = $item->quantity*$vat_exclusive_price;
            $sn++;
            ?>
            <tr>
                <td><?= $sn ?></td><td><?= $item->description ?></td>
                <td></td>
                <td><?= $item->measurement_unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->quantity ?></td>
                <td style="text-align: right"><?= $currency->symbol.' '. number_format($vat_exclusive_price,2) ?></td>
                <td style="text-align: right"><?= $currency->symbol.' '. number_format($amount,2) ?></td>
            </tr>
            <?php
        }


        $total_items_amount = $total_amount;


    ?>
    </tbody>
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="6">Total</th>
            <th style="text-align: right"><?= $currency->symbol.' '. number_format($total_amount,2) ?></th>
        </tr>
        <?php
        if($order->freight > 0 || $order->inspection_and_other_charges > 0 || !is_null($order->vat_inclusive)){
            $vat_factor = $order->vat_percentage/100;
            if($order->freight > 0){
                $vat_percentage_value = $order->vat_percentage;
                $freight_vat_exclusive = $order->vat_inclusive == 'VAT PRICED' ? $order->freight/(1 + $vat_factor) : $order->freight;
                $total_amount = $total_amount + $freight_vat_exclusive;
                ?>
            <tr>
                <th style="text-align: right"  colspan="6">Freight</th>
                <th style="text-align: right"><?=$currency->symbol.' '.  number_format($freight_vat_exclusive,2) ?></th>
            </tr>
            <?php }
            if($order->inspection_and_other_charges > 0){
                $vat_percentage_value = $order->vat_percentage;
                $inspection_and_other_charges_vat_exclusive = $order->vat_inclusive == 'VAT PRICED' ? $order->inspection_and_other_charges/(1 + $vat_factor) : $order->inspection_and_other_charges;
                $total_amount = $total_amount + $inspection_and_other_charges_vat_exclusive;
                ?>
            <tr>
                <th style="text-align: right"  colspan="6">Inspection and Other Charges<br/>
                    <?php if($order->vat_inclusive == "VAT COMPONENT"){ ?>
                        <i style="color: #0d6aad">*Does not affect VAT*</i>
                    <?php } ?>
                </th>
                <th style="text-align: right"><?=$currency->symbol.' '.  number_format($inspection_and_other_charges_vat_exclusive,2) ?></th>
            </tr>
            <?php }

                $grand_total = $total_amount;
                if(!is_null($order->vat_inclusive)){
                    $vat_percentage_value = $order->vat_percentage;
                    $vat = $order->vat_inclusive == "VAT COMPONENT" ? ($total_items_amount+$order->freight)*$vat_factor : $total_amount* $vat_factor;
                    $grand_total = $order->vat_inclusive == "VAT COMPONENT" ? (($grand_total+$order->freight-$order->inspection_and_other_charges)*(1+$vat_factor))+$order->inspection_and_other_charges : $grand_total*(1 + $vat_factor);
                    $grand_total = $total_amount+$vat;
                    ?>
                    <tr>
                        <th style="text-align: right" colspan="6">VAT</th>
                        <th style="text-align: right"><?= $currency->symbol.' '. number_format($vat,2) ?></th>
                    </tr>
            <?php }  ?>
            <tr>
                <th style="text-align: right"  colspan="6">Grand Total <?= $order->vat_inclusive ? '(VAT Inclusive)' : ''  ?>  </th>
                <th style="text-align: right"><?= $currency->symbol.' '. number_format($grand_total,2) ?></th>
            </tr>
        <?php } ?>
    </tfoot>
</table>

<br/><br/>
<div style="font-size: 12px">
<strong>Terms &amp; Conditions</strong><br/><?= $order->comments != '' ? nl2br($order->comments) : 'N/A' ?>
<br/><br/>
<strong>Delivery Date: </strong><?= $order->delivery_date != '' ? custom_standard_date($order->delivery_date) : 'N/A' ?>
<br/><br/>
</div>
<table style="font-size: 12px" width="100%">
    <tr>
        <td width="60%">&nbsp;</td>
        <th>Prepared By: </th>
        <td>
            <?= $order->employee()->full_name() ?>
        </td>
    </tr>
    <tr>
        <?php if($order->status == 'PENDING'){ ?>
        <td></td>
        <th>Approval Signature:</th>
        <td><br/>

            <strong> </strong>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
        </td>
        <?php } else {
            ?>
            <td></td>
            <th>Order Status</th>
            <td><?= $order->status ?></td>
        <?php
        } ?>
    </tr>
</table>
