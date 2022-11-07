<?php
$company_details = get_company_details();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>RQ/<?= add_leading_zeros($requisition->{$requisition::DB_TABLE_PK}) ?> APPROVAL CHAIN</title>
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
if($has_project){
    $project = $has_project->project();
}
$full_access = !$has_project || ($has_project && $project->manager_access()) || check_permission('Administrative Actions');
$approved = $requisition->status == 'APPROVED';

$currency = $requisition->currency();

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
            <h2 style="text-align: left; color: #8E3518">REQUISITION APPROVAL CHAIN</h2>
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
                    <th>Item Description</th>
                    <th>Part No.</th>
                    <th>Unit</th>
                    <th>Requested Quantity</th>
                    <th>Requested Rate</th>
                    <th>Requested Amount</th>
                    <th>Source</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $material_items = $requisition->material_items();
                $total_amount = 0;
                foreach($material_items as $item){
                    $material = $item->material_item();
                    $total_amount += $amount = $item->requested_quantity*$item->requested_rate;
                    ?>
                    <tr>
                        <td><?= $material->item_name ?></td>
                        <td><?= $material->part_number ?></td>
                        <td><?= $material->unit()->symbol ?></td>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '. number_format($item->requested_rate,2) ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '.  number_format(($amount),2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    </tr>
                    <?php
                }

                $asset_items = $requisition->asset_items();

                foreach ($asset_items as $item){
                    $asset_item = $item->asset_item();
                    $total_amount += $amount = $item->requested_quantity*$item->requested_rate;
                    ?>
                    <tr>
                        <td><?= $asset_item->asset_name ?></td>
                        <td></td>
                        <td>No.</td>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '. number_format($item->requested_rate,2) ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '.  number_format(($amount),2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    </tr>
                    <?php
                }

                $service_items = $requisition->service_items();

                foreach ($service_items as $item){
                    $total_amount += $amount = $item->requested_quantity*$item->requested_rate;
                    ?>
                    <tr>
                        <td><?= $item->description ?></td>
                        <td></td>
                        <td>No.</td>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '. number_format($item->requested_rate,2) ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '.  number_format(($amount),2) ?></td>
                        <td><?= $item->requested_source() ?></td>
                    </tr>
                    <?php
                }

                $cash_items = $requisition->cash_items();
                foreach ($cash_items as $item){
                    $total_amount += $amount = $item->requested_quantity*$item->requested_rate;
                    ?>
                    <tr>
                        <td><?= $item->description ?></td>
                        <td><?= $item->measurement_unit()->symbol ?></td>
                        <td></td>
                        <td style="text-align: right"><?= $item->requested_quantity ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '. number_format($item->requested_rate,2) ?></td>
                        <td style="text-align: right"><?= $currency->symbol.' '.  number_format(($amount),2) ?></td>
                        <td>N/A</td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th style="text-align: right"  colspan="5">Total</th><th style="text-align: right"><?= number_format($total_amount, 2) ?></th><th></th>
                </tr>
                <tr>
                    <th style="text-align: right"  colspan="5">Freight</th><th style="text-align: right"><?= number_format($requisition->freight, 2) ?></th><th></th>
                </tr>
                <tr>
                    <th style="text-align: right"  colspan="5">Inspection and Other Charges</th><th style="text-align: right"><?= number_format($requisition->inspection_and_other_charges, 2) ?></th><th></th>
                </tr>
                <?php
                $total_amount = $total_amount+$requisition->inspection_and_other_charges+$requisition->freight;
                if (!is_null($requisition->vat_inclusive)) { ?>
                    <tr>
                        <th style="text-align: right" colspan="5">VAT</th>
                        <th style="text-align: right"><?= number_format($total_amount * 0.18, 2) ?></th>
                        <th></th>
                    </tr>
                <?php }

                $grand_total = !is_null($requisition->vat_inclusive) ? $total_amount * 1.18 : $total_amount;
                ?>
                <tr>
                    <th style="text-align: right"  colspan="5">Grand Total</th><th style="text-align: right"><?= number_format($grand_total, 2) ?></th><th></th>
                </tr>
                </tfoot>
            </table><br/><br/>
            <?php
            $approvals = $requisition->requisition_approvals();

            foreach ($approvals as $approval){
            $total_amount = 0;
            ?>
            <br/>
            <table class="table-below-table">
                <tr>
                    <td style=" width:50%">
                        <strong><?= ucwords(strtolower($approval->approval_chain_level()->label)) ?> By: </strong><?= $approval->created_by()->full_name() ?>
                    </td>
                    <td style=" width:50%">
                        <strong>Action Date </strong><?= standard_datetime($approval->created_at,true) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Comments: </strong><?= $approval->approving_comments != '' ? $approval->approving_comments : 'N/A' ?>
                    </td>
                </tr>
            </table>
            <br/>
            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Part No.</th>
                    <th>Unit</th>
                    <th>Approved Information</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $material_items = $requisition->material_items();
                foreach($material_items as $item){
                    $material = $item->material_item();
                    ?>
                    <tr>
                        <td><?= $material->item_name ?></td>
                        <td><?= $material->part_number ?></td>
                        <td><?= $material->unit()->symbol ?></td>
                        <?php
                        $sources = $approval->material_items('all',$item->{$item::DB_TABLE_PK});
                        ?>
                        <td style="width: 60%">
                            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th>Source</th><th>Quantity</th><th>Price</th><th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($sources as $source){
                                    $source_name = $source->source_name();
                                    $total_amount += $amount = $source->approved_quantity*$source->approved_rate;
                                    ?>
                                    <tr>
                                        <td><?= $source_name ?></td>
                                        <td style="text-align: right"><?= $source->approved_quantity ?></td>
                                        <td style="text-align: right"><?= number_format($source->approved_rate,3) ?></td>
                                        <td style="text-align: right"><?= number_format($amount,2) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
                }

                $asset_items = $requisition->asset_items();
                foreach($asset_items as $item){
                    $asset_item = $item->asset_item();
                    ?>
                    <tr>
                        <td><?= $asset_item->asset_name ?></td>
                        <th></th>
                        <td>No.</td>
                        <?php
                        $sources = $approval->asset_items('all',$item->{$item::DB_TABLE_PK});
                        ?>
                        <td style="width: 60%">
                            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th>Source</th><th>Quantity</th><th>Price</th><th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($sources as $source){
                                    $source_name = $source->source_name();
                                    $total_amount += $amount = $source->approved_quantity*$source->approved_rate;
                                    ?>
                                    <tr>
                                        <td><?= $source_name ?></td>
                                        <td style="text-align: right"><?= $source->approved_quantity ?></td>
                                        <td style="text-align: right"><?= number_format($source->approved_rate,3) ?></td>
                                        <td style="text-align: right"><?= number_format($amount,2) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
                }

                $service_items = $requisition->service_items();
                foreach($service_items as $item){
                    ?>
                    <tr>
                        <td><?= $item->description ?></td>
                        <th></th>
                        <td>No.</td>
                        <?php
                        $sources = $approval->service_items('all',$item->{$item::DB_TABLE_PK});
                        ?>
                        <td style="width: 60%">
                            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th>Source</th><th>Quantity</th><th>Price</th><th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($sources as $source){
                                    $source_name = $source->source_name();
                                    $total_amount += $amount = $source->approved_quantity*$source->approved_rate;
                                    ?>
                                    <tr>
                                        <td><?= $source_name ?></td>
                                        <td style="text-align: right"><?= $source->approved_quantity ?></td>
                                        <td style="text-align: right"><?= number_format($source->approved_rate,3) ?></td>
                                        <td style="text-align: right"><?= number_format($amount,2) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
                }

                $cash_items = $requisition->cash_items();
                foreach ($cash_items as $item){
                    ?>
                    <tr>
                        <td><?= $item->description ?></td>
                        <td></td>
                        <td><?= $item->measurement_unit()->symbol ?></td>
                        <?php
                        $sources = $approval->cash_items($item->{$item::DB_TABLE_PK});
                        ?>
                        <td style="width: 60%">
                            <table  cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th>Source</th><th>Quantity</th><th>Price</th><th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($sources as $source){
                                    $source_name = $source->account()->account_name;
                                    $total_amount += $amount = $source->approved_quantity*$source->approved_rate;
                                    ?>
                                    <tr>
                                        <td><?= $source_name ?></td>
                                        <td style="text-align: right"><?= $source->approved_quantity ?></td>
                                        <td style="text-align: right"><?= number_format($source->approved_rate,3) ?></td>
                                        <td style="text-align: right"><?= number_format($amount,2) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php
                }

                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th style="text-align: right"  colspan="3">Total</th><th style="text-align: right"><?= number_format($total_amount, 2) ?></th>
                </tr>
                <tr>
                    <th style="text-align: right"  colspan="3">Freight</th><th style="text-align: right"><?= number_format($approval->freight, 2) ?></th>
                </tr>
                <tr>
                    <th style="text-align: right"  colspan="3">Inpection and Other Charges</th><th style="text-align: right"><?= number_format($approval->inspection_and_other_charges, 2) ?></th>
                </tr>
                <?php
                $total_amount = $total_amount+$approval->freight+$approval->inspection_and_other_charges;
                if(!is_null($approval->vat_inclusive)) { ?>
                    <tr>
                        <th style="text-align: right" colspan="3">VAT </th><th style="text-align: right"><?= $currency->symbol.'  '. number_format($total_amount*0.18,2) ?></th>
                    </tr>
                <?php }
                $grand_total = !is_null($approval->vat_inclusive) ? $total_amount*1.18 : $total_amount;
                ?>
                <tr>
                    <th style="text-align: right" colspan="3">Grand Total</th><th style="text-align: right"><?= number_format($grand_total, 2) ?></th>
                </tr>
                </tfoot>
            </table><br/>

            <?php
            }
            ?>
        </td>
    </tr>
</table>
</body>
</html>

