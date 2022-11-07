<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/13/2018
 * Time: 4:05 PM
 */

?>

<table  <?php if($print){ ?> style="font-size: 10px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th>S/N</th><th>Required Date</th><th style="width: 40%">Description</th><th>RQ NO.</th><th>Cost Center</th><th>Approved Quantity</th><th>Approved Rate</th><th style="width: 20%">Amount</th><th>Source Type</th><th style="width: 20%">Requested From</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    $overall_amount = 0;
    foreach ($table_items as $item){
        $sn++;
        $date = $item['required_date'] != '' ? $item['required_date'] : $item['request_date'];
        $exchange_rate = $currency->rate_to_native($date);
        $overall_amount += ($item['amount']) * $exchange_rate;
        $requisition = $item['requisition'];
        ?>

        <tr>
            <td><?= $sn ?></td>
            <td><?= custom_standard_date(($item['required_date'])) ?></td>
            <td style="width: 40%"><?= $item['description'] ?></td>
            <td><?= $print ? $requisition->requisition_number() : anchor(base_url("requisitions/preview_requisition/".$requisition->{$requisition::DB_TABLE_PK}),$requisition->requisition_number(),'target="_blank"')?></td>
            <td><?= $item['cost_center'] ?></td>
            <td><?= $item['approved_quantity'] ?></td>
            <td nowrap style="text-align: right"><?= $item['currency'].' '. number_format(($item['approved_rate']),2) ?></td>
            <td nowrap style="text-align: right; width: 20%;"><?= $item['currency'].' '. number_format(($item['amount']),2) ?></td>
            <td><?= $item['source_type'] ?></td>
            <td style="width: 20%"><?= $item['requested_from'] ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td></td>
        <td colspan="6" style="text-align: left"><strong>TOTAL</strong></td>
        <td style="text-align: right; width: 20%;"><strong><?= 'TSH '.number_format($overall_amount,2) ?></strong></td>
        <td colspan="2"></td>
    </tr>
    </tbody>
</table>
