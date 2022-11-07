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
        <th>S/N</th><th>Retired Date</th><th style="width: 40%">Description</th><th>Requisition NO.</th><th>Cost Center</th><th>Approved Quantity</th><th>Received Quantity</th><th>Rate</th><th style="width: 20%">Amount</th><th style="width: 20%">Retired Personnel</th><th>Examiner</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    $overall_amount = 0;
    foreach ($table_items as $item){
        $sn++;
        $overall_amount += $item['amount'];
        ?>

        <tr>
            <td><?= $sn ?></td>
            <td><?= custom_standard_date(($item['examination_date'])) ?></td>
            <td style="width: 40%"><?= $item['description'] ?></td>
            <td><?= $print_pdf ? $item['requisition_number'] : !is_null($item['requisition_approval_id']) ? anchor(base_url('requisition/preview_approved_cash_requisition/' . $item['requisition_approval_id']),$item['requisition_number'], '" target="_blank"' ) : $item['requisition_number'] ?></td>
            <td><?= $item['cost_center'] ?></td>
            <td><?= $item['approved_quantity'].' '.$item['measurement_unit_symbol'] ?></td>
            <td><?= $item['retired_quantity'].' '.$item['measurement_unit_symbol'] ?></td>
            <td nowrap style="text-align: right"><?= 'TSH '. number_format(($item['retired_rate']),2) ?></td>
            <td nowrap style="text-align: right; width: 20%;"><?= 'TSH '. number_format(($item['amount']),2) ?></td>
            <td style="width: 20%"><?= $item['retirer'] ?></td>
            <td style="width: 20%"><?= $item['examiner'] ?></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td></td>
        <td colspan="7" style="text-align: left"><strong>TOTAL</strong></td>
        <td style="text-align: right; width: 20%;"><strong><?= 'TSH '.number_format($overall_amount,2) ?></strong></td>
        <td colspan="2"></td>
    </tr>
    </tbody>
</table>
