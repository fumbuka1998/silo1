<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/8/2016
 * Time: 5:56 PM
 */

$this->load->view('includes/letterhead');
$has_project = $requisition->project_requisition();

if ($has_project) {
    $project = $has_project->project();
}
$full_access = !$has_project || ($has_project && $project->manager_access()) || check_permission('Administrative Actions');
$approved = $requisition->status == 'APPROVED';
$last_approval = $requisition->last_approval();
$last_approval_id = $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : false;
$total_amount = 0;
?>
<h2 style="text-align: center"><?= $approved ? 'APPROVED REQUISITION' : 'REQUISITION SHEET' ?></h2>
<br />

<table style="font-size: 11px" width="100%">
    <tr>
        <td style=" width:20%; vertical-align: top">
            <strong>Department: </strong><br />
            <?= $requisition->department() ?>
        </td>
        <td style=" width:20%; vertical-align: top">
            <strong>Requisition No: </strong><br /><?= $requisition->requisition_number() ?>
        </td>
        <td style=" width:20%;  vertical-align: top">
            <strong>Required
                Date: </strong><br /><?= $requisition->required_date != null ? custom_standard_date($requisition->required_date) : 'N/A' ?>
        </td>
        <td style=" width:40%;  vertical-align: top">
            <strong>Requested
                For: </strong><br /><?= $has_project ? $project->project_name : $requisition->cost_center_name() ?>
        </td>
    </tr>
</table>
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
            <th>Source</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sn = 0;

        $vat_factor = $last_approval ? $last_approval->vat_percentage/100 : $requisition->vat_percentage/100;


        $material_items = $requisition->material_items();
        foreach ($material_items as $item) {

            if (!$last_approval || ($last_approval_id && $item->approved_item($last_approval_id))) {

                $sn++;
                $material = $item->material_item();
        ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= $material->name_with_part_number() ?></td>
                    <td><?= $material->unit()->symbol ?></td>
                    <?php
                    if ($last_approval) {
                        $approved_item = $item->approved_item($last_approval_id);
                        $quantity = $approved_item->approved_quantity;
                        $rate = $approved_item->approved_rate;
                        $vat_exclusive_rate = $last_approval->vat_inclusive == 'VAT PRICED' ? $rate / (1+$vat_factor) : $rate;
                        $total_amount += $amount = $quantity * $vat_exclusive_rate;
                    ?>
                        <td style="text-align: right"><?= $quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                        <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td><?= $item->requested_source() ?></td>

                    <?php
                    } else {
                        $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / (1+$vat_factor) : $item->requested_rate;
                        $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;
                    ?>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                        <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    <?php
                    }
                    ?>
                </tr>
            <?php
            }
        }

        $asset_items = $requisition->asset_items();
        foreach ($asset_items as $item) {
            if (!$approved || ($last_approval_id && $item->approved_item($last_approval_id))) {
                $sn++;
                $asset_item = $item->asset_item();
            ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= $asset_item->asset_name ?></td>
                    <td>No.</td>
                    <?php
                    if ($approved) {

                        $approved_item = $item->approved_item($last_approval_id);
                        $quantity = $approved_item->approved_quantity;
                        $rate = $approved_item->approved_rate;
                        $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $rate / 1.18 : $rate;
                        $total_amount += $amount = $quantity * $vat_exclusive_rate;

                    ?>
                        <td style="text-align: right"><?= $quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                        <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    <?php
                    } else {
                        $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / 1.18 : $item->requested_rate;
                        $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;
                    ?>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($item->requested_rate, 2) ?></td>
                        <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    <?php
                    }
                    ?>
                </tr>
            <?php
            }
        }

        $service_items = $requisition->service_items();
        foreach ($service_items as $item) {
            if (!$approved || ($last_approval_id && $item->approved_item($last_approval_id))) {
                $sn++;
            ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= $item->description ?></td>
                    <td><?= $item->measurement_unit()->symbol ?></td>
                    <?php
                    if ($approved) {

                        $approved_item = $item->approved_item($last_approval_id);
                        $quantity = $approved_item->approved_quantity;
                        $rate = $approved_item->approved_rate;
                        $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? ($rate / (1+$vat_factor)) : $rate;
                        $total_amount += $amount = $quantity * $vat_exclusive_rate;

                    ?>
                        <td style="text-align: right"><?= $quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                        <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    <?php
                    } else {
                        $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / (1+$vat_factor) : $item->requested_rate;
                        $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;

                    ?>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($item->requested_rate, 2) ?></td>
                        <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    <?php
                    }
                    ?>
                </tr>
            <?php
            }
        }

        $cash_items = $requisition->cash_items();
        foreach ($cash_items as $item) {
            if (!$approved || ($last_approval_id && $item->approved_item($last_approval_id))) {
                $sn++;
            ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= $item->description ?></td>
                    <td><?= $item->measurement_unit()->symbol ?></td>
                    <?php
                    if ($approved) {

                        $approved_item = $item->approved_item($last_approval_id);
                        $quantity = $approved_item->approved_quantity;
                        $rate = $approved_item->approved_rate;
                        $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $rate / (1+$vat_factor) : $rate;
                        $total_amount += $amount = $quantity * $vat_exclusive_rate;

                    ?>
                        <td style="text-align: right"><?= $quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td>CASH</td>
                    <?php
                    } else {
                        $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / (1 + $vat_factor) : $item->requested_rate;
                        $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;
                    ?>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($item->requested_rate, 2) ?></td>
                        <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                        <td>CASH</td>
                    <?php
                    }
                    ?>
                </tr>
        <?php
            }
        }
        ?>

    </tbody>
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="5">Total</th>
            <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($total_amount, 2) ?></th>
            <th></th>
        </tr>

        <?php
        if ($last_approval) {
            if ($last_approval->freight > 0 || $last_approval->inspection_and_other_charges > 0 || !is_null($last_approval->vat_inclusive)) {
                if ($last_approval->freight > 0) {
                    $vat_exclusive_freight = $last_approval->vat_inclusive == 'VAT PRICED' ? $last_approval->freight / (1+$vat_factor) : $last_approval->freight;
                    $total_amount = $total_amount + $vat_exclusive_freight;
        ?>

                    <tr>
                        <th style="text-align: right" colspan="5">Freight</th>
                        <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_freight, 2) ?></th>
                        <th></th>
                    </tr>

                <?php }

                if ($last_approval->inspection_and_other_charges > 0) {
                    $vat_exclusive_inspection_and_other_charges = $last_approval->vat_inclusive == 'VAT PRICED' ? ($last_approval->inspection_and_other_charges / (1+$vat_factor)) : $last_approval->inspection_and_other_charges;
                    $total_amount = $total_amount + $vat_exclusive_inspection_and_other_charges;

                ?>
                    <tr>
                        <th style="text-align: right" colspan="5">
                            Inspection and Other Charges<br/>
                            <?php if($last_approval->vat_inclusive == "VAT COMPONENT"){ ?>
                                <i style="color: #0d6aad">*Does not affect VAT*</i>
                            <?php } ?>
                        </th>
                        <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_inspection_and_other_charges, 2) ?></th>
                        <th></th>
                    </tr>

                <?php
                }

                if (!is_null($last_approval->vat_inclusive)) {
                    $vat = $last_approval->vat_inclusive == "VAT PRICED" ? $total_amount*$vat_factor : ($total_amount-$last_approval->inspection_and_other_charges)*$vat_factor
                    ?>
                    <tr>
                        <th style="text-align: right" colspan="5">VAT</th>
                        <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat, 2) ?></th>
                        <th></th>
                    </tr>
                <?php }



                $grand_total = !is_null($last_approval->vat_inclusive) ? $total_amount + $vat : $total_amount;

                ?>

                <tr>
                    <th style="text-align: right" colspan="5">Grand Total</th>
                    <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($grand_total, 2) ?></th>
                    <th></th>
                </tr>
                <?php }
        } else {
            if ($requisition->freight > 0 || $requisition->inspection_and_other_charges > 0 || !is_null($requisition->vat_inclusive)) {
                $vat_factor = $requisition->vat_percentage/100;
                if ($requisition->freight > 0) {
                    $vat_exclusive_freight = $requisition->vat_inclusive == 'VAT PRICED' ? $requisition->freight / (1+$vat_factor) : $requisition->freight;
                    $total_amount = $total_amount + $vat_exclusive_freight;
                ?>

                    <tr>
                        <th style="text-align: right" colspan="5">Freight</th>
                        <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_freight, 2) ?></th>
                        <th></th>
                    </tr>

                <?php }


                if ($requisition->inspection_and_other_charges > 0) {
                    $vat_exclusive_inspection_and_other_chargest = $requisition->vat_inclusive == 'VAT PRICED' ? $requisition->inspection_and_other_chargest / (1+$vat_factor) : $requisition->inspection_and_other_charges;
                    $total_amount = $total_amount + $vat_exclusive_inspection_and_other_chargest;

                ?>
                    <tr>
                        <th style="text-align: right" colspan="5">
                            Inspection and Other Charges<br/>
                            <?php if($requisition->vat_inclusive == "VAT COMPONENT"){ ?>
                            <i style="color: #0d6aad">*Does not affect VAT*</i>
                            <?php } ?>

                        </th>
                        <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_inspection_and_other_chargest, 2) ?></th>
                        <th></th>
                    </tr>

                <?php
                }

                if (!is_null($requisition->vat_inclusive)) {
                    $vat = $requisition->vat_inclusive == "VAT PRICED" ? $total_amount*$vat_factor : ($total_amount-$requisition->inspection_and_other_charges)*$vat_factor
                    ?>
                    <tr>
                        <th style="text-align: right" colspan="5">VAT</th>
                        <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat, 2) ?></th>
                        <th></th>
                    </tr>
                <?php }

                $grand_total = !is_null($requisition->vat_inclusive) ? $total_amount + $vat : $total_amount;

                ?>

                <tr>
                    <th style="text-align: right" colspan="5">Grand Total</th>
                    <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($grand_total, 2) ?></th>
                    <th></th>
                </tr>
        <?php }
        } ?>
    </tfoot>
</table><br />
<table style=" font-size: 12px" width="100%">
    <tr>
        <td colspan="3">
            <hr />
        </td>
    </tr>
    <tr>
        <td style=" width:25%; vertical-align: top">
            <strong>Requested By: </strong>
            <?php if ($requisition->status == 'PENDING') { ?>
                <br /><br />
                <span style="text-decoration: underline">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span>
            <?php } ?>
            <br /><?= $requisition->requester()->full_name() ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Request
                Date: </strong><br /><?= $requisition->request_date != null ? custom_standard_date($requisition->request_date) : '' ?>
        </td>
        <td style=" vertical-align: top">
            <strong>Requesting Comments</strong><br /><?= nl2br($requisition->requesting_comments) ?>
        </td>
    </tr>
    <?php
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
            <?php
        } else {
            //Yohana said we remove this no stronger reason thou
            if ($chain_level->status == 'NOMORE') {
            ?>
                <tr>
                    <td colspan="3">
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td style=" width:25%; vertical-align: top">
                        <strong><?= $chain_level->label ?> By: </strong><br />
                        <br />
                        <span style="text-decoration: underline">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                        <br /><?= $chain_level->level_name ?>
                    </td>
                    <td style=" width:25%; vertical-align: top">
                        <strong>Date: </strong><br />
                        <br />
                        <span style="text-decoration: underline">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                    </td>
                    <td style=" width:50%; vertical-align: top">
                        <strong>Comments</strong><br />
                        <br />
                        <span style="text-decoration: underline">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                    </td>
                </tr>
    <?php   }
        }
    }
    ?>
</table>