<?php
$company_details = get_company_details();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>P.O-P.R/<?= add_leading_zeros($payment_request->{$payment_request::DB_TABLE_PK}) ?></title>
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
$request_date = DateTime::createFromFormat('Y-m-d', $payment_request->request_date);
$formated_request_date = $request_date->format('l, F d, Y');
?>
<table cellspacing="0.5" class="table-main" style="table-layout: fixed">
    <tr>
        <td style="width: 40%">
            <img style="width: 100px; padding: 2% 8%" src="<?= base_url('images/company_logo.png') ?>">
        </td>
        <td style="width: 60%">
            <h2 style="text-align: left; color: #8E3518">P.O PAYMENT REQUEST</h2>
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
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $payment_request->request_number() ?></td>
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
                <tr>
                    <td style="color: #31314E; height: 10px"><?= ucwords(nl2br($company_details->mobile)) ?></td>
                </tr>
            </table>
        </td>
        <td style="width: 50%">
            <table cellspacing="0.5" class="table-to-table">
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Request No. :</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $payment_request->request_number() ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">P.O No. :</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= 'P.O/'.add_leading_zeros($payment_request->purchase_order_id) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Requested For :</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $payment_request->cost_center_name() ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Currency :</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $payment_request->currency()->name_and_symbol() ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr style="background-color: #8E3518;">
        <td colspan="2"
            style="padding: 8px; width: 100%; text-align: center; color: white; font-size: 14px"><?= $payment_request->cost_center_name() ?></td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100%">
            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Description</th>
                    <th>Claimed By</th>
                    <th>Reference</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $invoice_items = $payment_request->invoice_items();
                $total_amount = $sn = 0;

                foreach ($invoice_items as $invoice_item){
                    $sn++;
                    $total_amount += $invoice_item->requested_amount;

                    $invoice = $invoice_item->invoice();
                    ?>
                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $invoice_item->description ?></td>
                        <td><?= $invoice_item->invoice()->stakeholder()->stakeholder_name ?></td>
                        <td><?= $invoice->reference ?></td>
                        <td style="text-align: right"><?= $invoice_item->purchase_order_payment_request()->currency()->symbol.' '.number_format($invoice_item->requested_amount,2) ?></td>
                    </tr>
                    <?php
                }
                $cash_items = $payment_request->cash_items();
                foreach ($cash_items as $cash_item){
                    $sn++;
                    $total_amount += $cash_item->requested_amount;
                    ?>
                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $cash_item->description ?></td>
                        <td><?= $cash_item->claimed_by ?></td>
                        <td><?= $cash_item->reference ?></td>
                        <td style="text-align: right"><?= $payment_request->currency()->symbol.' '.number_format($cash_item->requested_amount,2) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4">TOTAL</th><th style="text-align: right"><?= $payment_request->currency()->symbol.'&nbsp;'. number_format($total_amount,2) ?></th>
                </tr>
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

                    <td style="width: 25%">
                        <strong>Requested By: </strong><br/><?= $payment_request->employee()->full_name() ?>
                    </td>
                    <td>
                        <strong>Request Date: </strong><br/><?= custom_standard_date($payment_request->request_date) ?>
                    </td>
                    <td  style=" width:25%; vertical-align: top">
                        <strong>Comments: </strong><br/><?= nl2br($payment_request->remarks) ?>
                    </td>
                </tr>

                <?php

                foreach($chain_levels as $chain_level){ ?>
                    <tr>
                        <td colspan="3"><hr/></td>
                    </tr>
                    <tr>
                        <td style=" width:25%; vertical-align: top">
                            <strong><?= $chain_level->label ?> By: </strong><br/>
                            <?php
                            $has_approval = isset($payment_request_approvals[$chain_level->{$chain_level::DB_TABLE_PK}]);
                            $approval = $has_approval ?  $payment_request_approvals[$chain_level->{$chain_level::DB_TABLE_PK}] : null;
                            if($has_approval){
                                echo '<i>'.$approval->employee()->full_name().'</i>';
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
                            <?php if($has_approval){
                                echo custom_standard_date($approval->approval_date);
                            } else { ?>
                                <br/>
                                <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                            <?php } ?>
                        </td>
                        <td style=" width:25%; vertical-align: top">
                            <strong>Comments: </strong><br/>
                            <?php if($has_approval){
                                echo nl2br($approval->comments);
                            } else { ?>
                                <br/>
                                <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

