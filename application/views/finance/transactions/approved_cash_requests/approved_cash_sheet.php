<?php

$this->load->view('includes/letterhead');
$requisition = $requisition_approval->requisition();
?>
<h2 style="text-align: center">APPROVED PAYMENT</h2>
<span style="font-weight: bold; font-size: 12px"><?= $cost_center_name ?></span>
<br />
<strong>Req. No.</strong><?= $requisition->requisition_number() ?>
<br />
<br />
<br />

<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
    <tr>
        <th>S.No</th>
        <th>Item Description</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th nowrap="true">Rate</th>
        <th nowrap="true">Amount</th>

    </tr>
    </thead>
    <tbody>
    <?php
    $currency = $requisition->currency();

    $sn = 0;
    $total_amount = 0;
    $material_items = $requisition_approval->material_items('cash');

    foreach ($material_items as $item) {
        $sn++;
        $material = $item->material_item();
        $total_amount += $amount = $item->approved_quantity * $item->approved_rate;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $material->item_name ?></td>
            <td><?= $material->unit()->symbol ?></td>
            <td style="text-align: right"><?= $item->approved_quantity ?></td>
            <td nowrap="nowrap" style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->approved_rate, 2) ?></td>
            <td style="text-align: right"><?= $currency->symbol . ' ' .  number_format($amount, 2) ?></td>
        </tr>
        <?php
    }

    $asset_items = $requisition_approval->asset_items('cash');

    foreach ($asset_items as $item) {
        $sn++;
        $requisition_asset_item = $item->requisition_asset_item();
        $asset_item = $requisition_asset_item->asset_item();
        $total_amount += $amount = $item->approved_quantity * $item->approved_rate;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $asset_item->asset_name ?></td>
            <td>Nos</td>
            <td style="text-align: right"><?= $item->approved_quantity ?></td>
            <td nowrap="nowrap" style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->approved_rate, 2) ?></td>
            <td style="text-align: right"><?= $currency->symbol . ' ' .  number_format($amount, 2) ?></td>
        </tr>
        <?php
    }

    $cash_items = $requisition_approval->cash_items(null, $account_id);

    foreach ($cash_items as $item) {
        $sn++;
        $total_amount += $amount = $item->approved_quantity * $item->approved_rate;
        $requisition_cash_item = $item->requisition_cash_item();
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $requisition_cash_item->description ?></td>
            <td><?= $requisition_cash_item->measurement_unit()->symbol ?></td>
            <td style="text-align: right"><?= $item->approved_quantity ?></td>
            <td nowrap="nowrap" style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->approved_rate, 2) ?></td>
            <td nowrap="nowrap" style="text-align: right"><?= $currency->symbol . ' ' .  number_format($amount, 2) ?></td>
        </tr>
        <?php
    }

    $service_items = $requisition_approval->service_items('cash');

    foreach ($service_items as $item) {
        $sn++;
        $total_amount += $amount = $item->approved_quantity * $item->approved_rate;
        $requisition_service_item = $item->requisition_service_item();
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $requisition_service_item->description ?></td>
            <td><?= $requisition_service_item->measurement_unit()->symbol ?></td>
            <td style="text-align: right"><?= $item->approved_quantity ?></td>
            <td nowrap="nowrap" style="text-align: right"><?= $currency->symbol . ' ' . number_format($item->approved_rate, 2) ?></td>
            <td nowrap="nowrap" style="text-align: right"><?= $currency->symbol . ' ' .  number_format($amount, 2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th style="text-align: right" colspan="5">Total</th>
        <th style="text-align: right"><?= $currency->symbol . '  ' .  number_format($total_amount, 2) ?></th>
    </tr>

    <?php
    if ($requisition_approval->freight > 0 || $requisition_approval->inspection_and_other_charges > 0 || !is_null($requisition_approval->vat_inclusive)) {
        if ($requisition_approval->freight > 0) {
            $vat_exclusive_freight = $requisition_approval->vat_inclusive == 'VAT PRICED' ? $requisition_approval->freight / 1.18 : $requisition_approval->freight;
            $total_amount = $total_amount + $vat_exclusive_freight;
            ?>

            <tr>
                <th style="text-align: right" colspan="5">Freight </th>
                <th style="text-align: right"><?= $currency->symbol . '  ' . number_format($vat_exclusive_freight, 2) ?></th>
            </tr>

        <?php }
        if ($requisition_approval->inspection_and_other_charges > 0) {
            $vat_exclusive_inspection_and_other_chargest = $requisition_approval->vat_inclusive == 'VAT PRICED' ? $requisition_approval->inspection_and_other_chargest / 1.18 : $requisition->inspection_and_other_charges;
            $total_amount = $total_amount + $vat_exclusive_inspection_and_other_chargest;

            ?>
            <tr>
                <th style="text-align: right" colspan="5">Inspection and Other Charges </th>
                <th style="text-align: right"><?= $currency->symbol . '  ' . number_format($vat_exclusive_inspection_and_other_chargest, 2) ?></th>
            </tr>

            <?php
        }

        if (!is_null($requisition_approval->vat_inclusive)) { ?>
            <tr>
                <th style="text-align: right" colspan="5">VAT </th>
                <th style="text-align: right"><?= $currency->symbol . '  ' . number_format($total_amount * 0.18, 2) ?></th>
            </tr>
        <?php }

        $grand_total = !is_null($requisition_approval->vat_inclusive) ? $total_amount * 1.18 : $total_amount;
        ?>

        <tr>
            <th style="text-align: right" colspan="5">Grand Total</th>
            <th style="text-align: right"><?= $currency->symbol . '  ' . number_format($grand_total, 2) ?></th>
        </tr>
    <?php } ?>
    </tfoot>
</table><br />
<strong>Amount In Words: </strong><?= numbers_to_words($total_amount) ?>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3">
            <hr />
        </td>
    </tr>
    <tr>
        <td style=" width: 30% vertical-align: top">
            <strong>Requested By: </strong><br /><?= $requisition->requester()->full_name() ?>
        </td>
        <td style=" width: 30% vertical-align: top">
            <strong>Request Date: </strong><br /><?= custom_standard_date($requisition->request_date) ?>
        </td>
        <td style=" vertical-align: top">
            <strong>Requesting Comments: </strong><br /><?= nl2br($requisition->requesting_comments) ?>
        </td>
    </tr>
    <?php
    if ($requisition->foward_to) {
        ?>
        <tr>
            <td colspan="3">
                <hr />
            </td>
        </tr>
        <tr>
            <?php
            $has_approval = isset($requisition_approvals[$requisition->foward_to]);
            $approval = $has_approval ? $requisition_approvals[$requisition->foward_to] : null;
            if ($has_approval) {
                $forward_to = $requisition->foward_to();
                ?>

                <td style=" width:25%; vertical-align: top">
                    <strong><?= $forward_to->label ?> By: </strong><br />
                    <i><?php $approval->created_by()->full_name() ?></i>
                    <br /><?= $requisition->foward_to()->level_name ?>
                </td>
                <td style=" width:25%; vertical-align: top">
                    <strong>Date: </strong><br />
                    <?= custom_standard_date($approval->approved_date) ?>
                </td>
                <td style=" width:50%; vertical-align: top">
                    <strong>Comments</strong><br />
                    <?= nl2br($approval->approving_comments) ?>
                </td>
            <?php } else {
                $finalizer = $requisition->finalizer();
                $chain_level = $requisition_approval->approval_chain_level();

                ?>
                <td style=" width:25%; vertical-align: top">
                    <strong><?= $chain_level->label ?> By: </strong><br />
                    <br />
                    <span><?= $finalizer->full_name() ?></span>
                    <br /><?= $requisition->foward_to()->position()->position_name ?>
                </td>
                <td style=" width:25%; vertical-align: top">
                    <strong>Date: </strong><br />
                    <br />
                    <span><?= custom_standard_date($requisition_approval->approved_date) ?></span>
                </td>
                <td style=" width:50%; vertical-align: top">
                    <strong>Comments</strong><br />
                    <br />
                    <span><?= $requisition_approval->approving_comments ?></span>
                </td>
            <?php } ?>
        </tr>
    <?php } else {
        foreach ($chain_levels as $chain_level) {
            $has_approval = isset($requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}]);
            $approval = $has_approval ? $requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}] : null; ?>

            <?php
            if ($has_approval) { ?>
                <tr>
                    <td colspan="3">
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td style=" width:25%; vertical-align: top">
                        <strong><?= $chain_level->label ?> By: </strong><br />
                        <i><?= $approval->created_by()->full_name() ?></i>
                        <br /><?= $chain_level->level_name ?>
                    </td>
                    <td style=" width:25%; vertical-align: top">
                        <strong>Date: </strong><br />
                        <?= custom_standard_date($approval->approved_date) ?>
                    </td>
                    <td style=" width:50%; vertical-align: top">
                        <strong>Comments</strong><br />
                        <?= nl2br($approval->approving_comments) ?>
                    </td>
                </tr>
            <?php }
        }
    } ?>
</table>