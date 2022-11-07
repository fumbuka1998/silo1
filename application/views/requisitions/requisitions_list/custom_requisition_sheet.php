<?php
$company_details = get_company_details();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>RQ/<?= add_leading_zeros($requisition->{$requisition::DB_TABLE_PK}) ?></title>
    <style>
        @page {
            sheet-size: A4;
            margin: 1pt 4pt 3pt 4pt;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        body {
            margin: 0 auto;
            color: #000000;
            background: #FFFFFF;
            font-family: 'Open Sans', sans-serif;;
            font-size: 12px;
        }

        body p {
            padding: 5px;
            text-indent: 8px;
            color: #000000;
            font-family: 'Open Sans', sans-serif;;
            font-size: 11px;
        }

        .address strong {
            font-size: 12px;
            color: #bc1f27;
        }

        header {
            padding: 4px 0;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #000000;
            font-size: 1.5em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 2px 0;
        }

        h4 {
            color: #000000;
            font-size: 12px;
            line-height: 1.0em;
            font-weight: normal;
            text-align: center;
            margin-bottom: 10px;
        }

        .table-main {
            width: 100%;
            border: 3px solid #000000;
            border-collapse: collapse;
            margin: 8%;
        }

        .table-header-table {
            width: 100%;
            font-size: 20px;
            border-top: 3px solid #000000;
            border-bottom: 3px solid #000000;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .table-from-table, .table-to-table {
            width: 100%;
            font-size: 14px;
            border: 2px solid #6C6C9D;
            overflow: hidden;
            border-radius: 15px;
            -moz-border-radius: 15px;
            -webkit-border-radius: 15px;
            border-collapse: separate !important;
            margin-top: 3px;
        }

        .table-below-table {
            width: 100%;
            font-size: 14px;
            border: 2px solid white;
            overflow: hidden;
            border-radius: 15px;
            -moz-border-radius: 15px;
            -webkit-border-radius: 15px;
            border-collapse: separate !important;
            margin-top: 3px;
        }

        .table-main-item-table {
            width: 100%;
            border: 2px solid #000000;
            border-collapse: collapse;
        }

        .table-from-table td:first-child, .table-from-table th:first-child {
            border-left: none;
        }

        .table-from-table th:first-child {
            -moz-border-radius: 15px 0 0 0;
            -webkit-border-radius: 15px 0 0 0;
            border-radius: 15px 0 0 0;
        }

        .table-from-table th:last-child {
            -moz-border-radius: 0 15px 0 0;
            -webkit-border-radius: 0 15px 0 0;
            border-radius: 0 15px 0 0;
        }

        .table-from-table th:only-child {
            -moz-border-radius: 15px 15px 0 0;
            -webkit-border-radius: 15px 15px 0 0;
            border-radius: 15px 15px 0 0;
        }

        .table-from-table tr:last-child td:first-child {
            -moz-border-radius: 0 0 0 15px;
            -webkit-border-radius: 0 0 0 15px;
            border-radius: 0 0 0 15px;
        }

        .table-from-table tr:last-child td:last-child {
            -moz-border-radius: 0 0 15px 0;
            -webkit-border-radius: 0 0 15px 0;
            border-radius: 0 0 15px 0;
        }

        .table-main td, .table-main th {
            border: none;
            padding-left: 0;
            padding-right: 0;
        }

        .table-header-table td, .table-header-table th {
            font-size: 14px;
            padding: 0px;
        }

        .table-main-item-table td, .table-main-item-table th {
            border: 1px solid #000000;
            font-size: 14px;
            padding: 3px;
        }

        .table-from-table td, .table-from-table th {
            font-size: 14px;
            padding: 3px;
        }

        .table-below-table td, .table-below-table th {
            font-size: 14px;
            padding: 3px;
        }

        .table-to-table td, .table-to-table th {
            font-size: 14px;
            padding: 3px;
        }

        .table-main-item-table tfoot tr {
            border: none;
            background: #D9D9D9;
        }

        table tfoot tr:first-child td {
            border-top: 1px solid #000000;
        }
    </style>
</head>
<body>
<br/>
<?php
$has_project = $requisition->project_requisition();

if ($has_project) {
    $project = $has_project->project();
}
$full_access = !$has_project || ($has_project && $project->manager_access()) || check_permission('Administrative Actions');
$approved = $requisition->status == 'APPROVED';
$last_approval = $requisition->last_approval();
$last_approval_id = $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : false;
$total_amount = 0;


$request_date = DateTime::createFromFormat('Y-m-d', $requisition->request_date);
$formated_request_date = $request_date->format('l, F d, Y');
$required_date = DateTime::createFromFormat('Y-m-d', $requisition->required_date);
$formated_required_date = $required_date->format('l, F d, Y');
?>
<table cellspacing="0.5" class="table-main" style="table-layout: fixed">
    <tr>
        <td style="width: 40%">
            <img style="width: 100px; padding: 2% 8%" src="<?= base_url('images/company_logo.png') ?>">
        </td>
        <td style="width: 60%">
            <h2 style="text-align: left; color: #8E3518"><?= $approved ? 'APPROVED REQUISITION' : 'REQUISITION SHEET' ?></h2>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100%">
            <table cellspacing="0.5" class="table-header-table">
                <tr style="background-color: #AEAAAA">
                    <td colspan="4" style="padding: 3px"></td>
                </tr>
                <tr style="background-color: #8E3518;">
                    <td style="width: 8%">&nbsp;</td>
                    <td style="color: white">Request Number:</td>
                    <td style="width: 32%">&nbsp;</td>
                    <td style="color: white">Request Date:</td>
                </tr>
                <tr style="background-color: #8E3518;">
                    <td style="width: 5%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $requisition->requisition_number() ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 30%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $formated_request_date ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="background-color: #AEAAAA">
                    <td colspan="4" style="padding: 3px"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="width: 50%">
            <table cellspacing="0.5" class="table-from-table" style="height: 200px">
                <tr>
                    <td style="color: #8E3518; height: 10px"><?= strtoupper($company_details->company_name) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 50px"><?= ucwords(nl2br($company_details->address)) ?></td>
                </tr>
            </table>
        </td>
        <td style="width: 50%">
            <table cellspacing="0.5" class="table-to-table">
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Department:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= strtoupper( $requisition->department()) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Requisition No:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $requisition->requisition_number() ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Required Date:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $requisition->required_date != null ? custom_standard_date($requisition->required_date) : 'N/A' ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Requested For:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $has_project ? $project->project_name : $requisition->cost_center_name() ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr style="background-color: #8E3518;">
        <td colspan="2"
            style="padding: 8px; width: 100%; text-align: center; color: white; font-size: 14px"><?= $has_project ? $project->project_name : $requisition->cost_center_name() ?></td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100%">
            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
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
                $material_items = $requisition->material_items();
                foreach ($material_items as $item) {

                    if (!$approved || ($last_approval_id && $item->approved_item($last_approval_id))) {

                        $sn++;
                        $material = $item->material_item();
                        ?>
                        <tr>
                            <td><?= $sn ?></td>
                            <td><?= $material->name_with_part_number() ?></td>
                            <td><?= $material->unit()->symbol ?></td>
                            <?php
                            if ($approved) {
                                $approved_item = $item->approved_item($last_approval_id);
                                $quantity = $approved_item->approved_quantity;
                                $rate = $approved_item->approved_rate;
                                $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $rate / 1.18 : $rate;
                                $total_amount += $amount = $quantity * $vat_exclusive_rate;
                                ?>
                                <td style="text-align: right"><?= $quantity ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                                <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                                <td><?= $item->requested_source() ?></td>

                                <?php
                            } else {
                                $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / 1.18 : $item->requested_rate;
                                $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;
                                ?>
                                <td style="text-align: right"><?= $item->requested_quantity ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
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
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                                <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                                <td><?= $item->requested_source() ?></td>
                                <?php
                            } else {
                                $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / 1.18 : $item->requested_rate;
                                $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;
                                ?>
                                <td style="text-align: right"><?= $item->requested_quantity ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($item->requested_rate, 2) ?></td>
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
                                $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $rate / 1.18 : $rate;
                                $total_amount += $amount = $quantity * $vat_exclusive_rate;

                                ?>
                                <td style="text-align: right"><?= $quantity ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                                <td style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                                <td><?= $item->requested_source() ?></td>
                                <?php
                            } else {
                                $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / 1.18 : $item->requested_rate;
                                $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;

                                ?>
                                <td style="text-align: right"><?= $item->requested_quantity ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($item->requested_rate, 2) ?></td>
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
                                $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $rate / 1.18 : $rate;
                                $total_amount += $amount = $quantity * $vat_exclusive_rate;

                                ?>
                                <td style="text-align: right"><?= $quantity ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($vat_exclusive_rate, 2) ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                                <td>CASH</td>
                                <?php
                            } else {
                                $vat_exclusive_rate = $requisition->vat_inclusive == 'VAT PRICED' ? $item->requested_rate / 1.18 : $item->requested_rate;
                                $total_amount += $amount = $item->requested_quantity * $vat_exclusive_rate;
                                ?>
                                <td style="text-align: right"><?= $item->requested_quantity ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($item->requested_rate, 2) ?></td>
                                <td nowrap="nowrap"
                                    style="text-align: right"><?= $item->currency_symbol() . ' ' . number_format($amount, 2) ?></td>
                                <td>N/A</td>
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
                            $vat_exclusive_freight = $last_approval->vat_inclusive == 'VAT PRICED' ? $last_approval->freight / 1.18 : $last_approval->freight;
                            $total_amount = $total_amount + $vat_exclusive_freight;
                            ?>

                            <tr>
                                <th style="text-align: right" colspan="5">Freight</th>
                                <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_freight, 2) ?></th>
                                <th></th>
                            </tr>

                        <?php }
                        if ($last_approval->inspection_and_other_charges > 0) {
                            $vat_exclusive_inspection_and_other_chargest = $last_approval->vat_inclusive == 'VAT PRICED' ? $last_approval->inspection_and_other_chargest / 1.18 : $last_approval->inspection_and_other_charges;
                            $total_amount = $total_amount + $vat_exclusive_inspection_and_other_chargest;

                            ?>
                            <tr>
                                <th style="text-align: right" colspan="5">Inspection and Other Charges</th>
                                <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_inspection_and_other_chargest, 2) ?></th>
                                <th></th>
                            </tr>

                            <?php
                        }

                        if (!is_null($last_approval->vat_inclusive)) { ?>
                            <tr>
                                <th style="text-align: right" colspan="5">VAT</th>
                                <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($total_amount * 0.18, 2) ?></th>
                                <th></th>
                            </tr>
                        <?php }

                        $grand_total = !is_null($last_approval->vat_inclusive) ? $total_amount * 1.18 : $total_amount;
                        ?>

                        <tr>
                            <th style="text-align: right" colspan="5">Grand Total</th>
                            <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($grand_total, 2) ?></th>
                            <th></th>
                        </tr>
                    <?php }
                } else {
                    if ($requisition->freight > 0 || $requisition->inspection_and_other_charges > 0 || !is_null($requisition->vat_inclusive)) {
                        if ($requisition->freight > 0) {
                            $vat_exclusive_freight = $requisition->vat_inclusive == 'VAT PRICED' ? $requisition->freight / 1.18 : $requisition->freight;
                            $total_amount = $total_amount + $vat_exclusive_freight;
                            ?>

                            <tr>
                                <th style="text-align: right" colspan="5">Freight</th>
                                <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_freight, 2) ?></th>
                                <th></th>
                            </tr>

                        <?php }
                        if ($requisition->inspection_and_other_charges > 0) {
                            $vat_exclusive_inspection_and_other_chargest = $requisition->vat_inclusive == 'VAT PRICED' ? $requisition->inspection_and_other_chargest / 1.18 : $requisition->inspection_and_other_charges;
                            $total_amount = $total_amount + $vat_exclusive_inspection_and_other_chargest;

                            ?>
                            <tr>
                                <th style="text-align: right" colspan="5">Inspection and Other Charges</th>
                                <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($vat_exclusive_inspection_and_other_chargest, 2) ?></th>
                                <th></th>
                            </tr>

                            <?php
                        }

                        if (!is_null($requisition->vat_inclusive)) { ?>
                            <tr>
                                <th style="text-align: right" colspan="5">VAT</th>
                                <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($total_amount * 0.18, 2) ?></th>
                                <th></th>
                            </tr>
                        <?php }

                        $grand_total = !is_null($requisition->vat_inclusive) ? $total_amount * 1.18 : $total_amount;
                        ?>

                        <tr>
                            <th style="text-align: right" colspan="5">Grand Total</th>
                            <th style="text-align: right"><?= $item->currency_symbol() . '  ' . number_format($grand_total, 2) ?></th>
                            <th></th>
                        </tr>
                    <?php }
                } ?>
                </tfoot>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <table style=" font-size: 12px"  class="table-below-table" >
                <tr>
                    <td colspan="3">
                        <hr/>
                    </td>
                </tr>
                <tr>
                    <td style=" width:25%; vertical-align: top">
                        <strong>Requested By: </strong>
                        <?php if ($requisition->status == 'PENDING') { ?>
                            <br/><br/>
                            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                        <?php } ?>
                        <br/><?= $requisition->requester()->full_name() ?>
                    </td>
                    <td style=" width:25%; vertical-align: top">
                        <strong>Request
                            Date: </strong><br/><?= $requisition->request_date != null ? custom_standard_date($requisition->request_date) : '' ?>
                    </td>
                    <td style=" vertical-align: top">
                        <strong>Requesting Comments</strong><br/><?= nl2br($requisition->requesting_comments) ?>
                    </td>
                </tr>
                <?php
                $special_level_approval = $requisition->special_level_approval();
                if ($special_level_approval) {
                    ?>
                    <tr>
                        <td colspan="3">
                            <hr/>
                        </td>
                    </tr>
                    <tr>
                        <td style=" width:25%; vertical-align: top">
                            <strong><?= $requisition->foward_to()->label ?> By: </strong><br/>
                            <?php
                            $has_approval = isset($requisition_approvals[$requisition->foward_to]);
                            $approval = $has_approval ? $requisition_approvals[$requisition->foward_to] : null;
                            if ($has_approval) {
                                echo '<i>' . $approval->created_by()->full_name() . '</i>';
                            } else { ?>
                                <br/>
                                <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                            <?php } ?>
                            <br/><?= $requisition->foward_to()->level_name ?>
                        </td>
                        <td style=" width:25%; vertical-align: top">
                            <strong>Date: </strong><br/>
                            <?php if ($has_approval) {
                                echo custom_standard_date($approval->approved_date);
                            } else { ?>
                                <br/>
                                <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                            <?php } ?>
                        </td>
                        <td style=" width:50%; vertical-align: top">
                            <strong>Comments</strong><br/>
                            <?php if ($has_approval) {
                                echo nl2br($approval->approving_comments);
                            } else { ?>
                                <br/>
                                <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                } else {
                    foreach ($chain_levels as $chain_level) {
                        $has_approval = isset($requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}]);
                        $approval = $has_approval ? $requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}] : null;
                        if ($chain_level->status == 'active' && $approval) {
                            ?>
                            <tr>
                                <td colspan="3">
                                    <hr/>
                                </td>
                            </tr>
                            <tr>
                                <td style=" width:25%; vertical-align: top">
                                    <strong><?= $chain_level->label ?> By: </strong><br/>
                                    <?php
                                    $has_approval = isset($requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}]);
                                    $approval = $has_approval ? $requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}] : null;
                                    if ($has_approval) {
                                        echo '<i>' . $approval->created_by()->full_name() . '</i>';
                                    } else { ?>
                                        <br/>
                                        <span style="text-decoration: underline">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </span>
                                    <?php } ?>
                                    <br/><?= $chain_level->level_name ?>
                                </td>
                                <td style=" width:25%; vertical-align: top">
                                    <strong>Date: </strong><br/>
                                    <?php if ($has_approval) {
                                        echo custom_standard_date($approval->approved_date);
                                    } else { ?>
                                        <br/>
                                        <span style="text-decoration: underline">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </span>
                                    <?php } ?>
                                </td>
                                <td style=" width:50%; vertical-align: top">
                                    <strong>Comments</strong><br/>
                                    <?php if ($has_approval) {
                                        echo nl2br($approval->approving_comments);
                                    } else { ?>
                                        <br/>
                                        <span style="text-decoration: underline">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        } else {
                            ?>
                            <tr>
                                <td colspan="3">
                                    <hr/>
                                </td>
                            </tr>
                            <tr>
                                <td style=" width:25%; vertical-align: top">
                                    <strong><?= $chain_level->label ?> By: </strong><br/>
                                    <br/>
                                    <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                                    <br/><?= $chain_level->level_name ?>
                                </td>
                                <td style=" width:25%; vertical-align: top">
                                    <strong>Date: </strong><br/>
                                    <br/>
                                    <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                                </td>
                                <td style=" width:50%; vertical-align: top">
                                    <strong>Comments</strong><br/>
                                    <br/>
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
                            <?php
                        }
                    }
                } ?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

