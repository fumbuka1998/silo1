<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/28/2018
 * Time: 2:18 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">SERVICE(S)</h2>
<br/>

<table style="font-size: 14px"  width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <td style="width: 70%">
            <table width="100%" style="font-size: 18px;">
                <tr>
                    <td style="text-align: left; font-weight: bold">
                        Service No : <?= $service->maintenance_services_no() ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; font-size: small">
                        <strong>Service Date : </strong><?= custom_standard_date($service->service_date) ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; font-size: small">
                        <strong>Location : </strong><?= strtoupper($service->location) ?>
                    </td>
                </tr>
            </table>
        </td>
        <td nowrap="nowrap" style="vertical-align: top">
            <b>M/s:</b><br/>
            <?= $service->client()->stakeholder_name ?><br/>
            <?= $service->client()->phone ?><br/>
            <?= $service->client()->alternative_phone ?><br/>
            <?= nl2br($service->client()->address) ?>
        </td>
    </tr>
</table>
<br/>
<br/>
<table  style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th>S/N</th><th>Description</th><th>UOM</th><th>Quantity</th><th>Rate</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>

    <?php
    $total_amount = $sn = 0;
     foreach ($service_items as $item){
         $sn++;
         $measurement_unit = $unit->measurement_unit_details($item->measurement_unit_id);
         ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $item->description ?></td>
            <td><?= $measurement_unit->symbol ?></td>
            <td style="text-align: right"><?=  $item->quantity ?></td>
            <td style="text-align: right"><?= number_format($item->rate, 2) ?></td>
            <td style="text-align: right"><?= number_format(($item->quantity * $item->rate), 2) ?></td>
        </tr>

        <?php
         $total_amount += $item->quantity * $item->rate;
    }
    ?>


    <tr>
        <td colspan="5"><strong>TOTAL</strong></td>
        <td style="text-align: right"><strong><?= number_format($total_amount, 2)  ?></strong></td>
    </tr>

    </tbody>
    </table>

<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style="width: 25%">
            <strong>Issued By: </strong><br/><?= $service->crested_by()->full_name() ?>
        </td>
        <td style="width: 25%">
            <strong>Issue Date: </strong><br/><?= custom_standard_date($service->service_date) ?>
        </td>
        <td  style="vertical-align: top">
            <strong>Remarks: </strong><br/><?= $service->remarks ?>
        </td>
    </tr>
</table>
<hr/>
