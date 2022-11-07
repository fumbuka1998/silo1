<?php
$company_details = get_company_details();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PO/<?= add_leading_zeros($order->order_id) ?></title>
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
$issue_date = DateTime::createFromFormat('Y-m-d', $order->issue_date);
$formated_issue_date = $issue_date->format('l, F d, Y');
$delivery_date = DateTime::createFromFormat('Y-m-d', $order->delivery_date);
$formated_delivery_date = $delivery_date->format('l, F d, Y');
?>
<table cellspacing="0.5" class="table-main" style="table-layout: fixed">
    <tr>
        <td style="width: 40%">
            <img style="width: 100px; padding: 2% 8%" src="<?= base_url('images/company_logo.png') ?>">
        </td>
        <td style="width: 60%">
            <h2 style="text-align: left; color: #8E3518">PURCHASE ORDER</h2>
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
                    <td style="color: white">Invoice Date:</td>
                </tr>
                <tr style="background-color: #8E3518;">
                    <td style="width: 5%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $order->order_number() ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 30%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $formated_issue_date ?></td>
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
                    <td style="color: #000080; padding-left: 15px; height: 10px"><strong>FROM</strong></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px">Account Department</td>
                </tr>
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
                    <td style="color: #8E3518; padding-left: 15px; height: 10px"><strong>TO</strong></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px">Supplier Name</td>
                </tr>
                <tr>
                    <td style="color: #417AC0; height: 10px"><?= strtoupper($order->Stakeholder()->stakeholder_name) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 50px"><?= ucwords(nl2br($order->Stakeholder()->address)) ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr style="background-color: #8E3518;">
        <td colspan="2"
            style="padding: 8px; width: 100%; text-align: center; color: white; font-size: 14px"><?= $order->cost_center_name() ?></td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100%">
            <?php
            $requisition = $order->requisition();
            $currency = $order->currency();
            ?>
            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                <thead>
                <tr>
                    <th style="width: 3%">S/No</th>
                    <th style="width: 34%">Material/Item</th>
                    <th style="width: 13%;">Part No.</th>
                    <th style="width: 5%">Unit</th>
                    <th style="width: 15%">Quantity</th>
                    <th style="width: 15%">Price</th>
                    <th style="width: 15%">Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total_amount = 0;
                $material_items = $order->material_items();
                $sn = 0;
                foreach ($material_items as $item) {
                    $vat_percentage_value = $order->vat_percentage;
                    $vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price * 100) / (100 + $vat_percentage_value) : $item->price;
                    $total_amount += $amount = $item->quantity * $vat_exclusive_price;
                    $sn++;
                    $material = $item->material_item();

                    ?>
                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $material->item_name ?></td>
                        <td><?= $material->part_number ?></td>
                        <td><?= $material->unit()->symbol ?></td>
                        <td style="text-align: right"><?= $item->quantity ?></td>
                        <td style="text-align: right"><?= $currency->symbol . ' ' . $vat_exclusive_price ?></td>
                        <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
                    </tr>
                    <?php
                }

                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td style="text-align: right" colspan="6">Total</td>
                    <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($total_amount, 2) ?></td>
                </tr>
                <?php
                if ($order->freight > 0 || $order->inspection_and_other_charges > 0 || !is_null($order->vat_inclusive)) {
                    if ($order->freight > 0) {
                        $vat_percentage_value = $order->vat_percentage;
                        $freight_vat_exclusive = $order->vat_inclusive == 'VAT PRICED' ? ($order->freight * 100) / (100 + $vat_percentage_value) : $order->freight;
                        $total_amount = $total_amount + $freight_vat_exclusive;
                        ?>
                        <tr>
                            <td style="text-align: right" colspan="6">Freight</td>
                            <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($freight_vat_exclusive, 2) ?></td>
                        </tr>
                    <?php }
                    if ($order->inspection_and_other_charges > 0) {
                        $vat_percentage_value = $order->vat_percentage;
                        $inspection_and_other_charges_vat_exclusive = $order->vat_inclusive == 'VAT PRICED' ? ($order->inspection_and_other_charges * 100) / (100 + $vat_percentage_value) : $order->inspection_and_other_charges;
                        $total_amount = $total_amount + $inspection_and_other_charges_vat_exclusive;
                        ?>
                        <tr>
                            <td style="text-align: right" colspan="6">Inspection and Other Charges</td>
                            <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($inspection_and_other_charges_vat_exclusive, 2) ?></td>
                        </tr>
                    <?php }

                    if (!is_null($order->vat_inclusive)) {
                        $vat_percentage_value = $order->vat_percentage;
                        $total_amount = $total_amount * ((100 + $vat_percentage_value) / 100);
                        ?>
                        <tr>
                            <td style="text-align: right" colspan="6">VAT</td>
                            <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($total_amount * ($vat_percentage_value / 100), 2) ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td style="text-align: right" colspan="6">Grand
                            Total <?= $order->vat_inclusive ? '(VAT Inclusive)' : '' ?>  </td>
                        <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($total_amount, 2) ?></td>
                    </tr>
                <?php } ?>
                <tr style="background-color: white;">
                    <td colspan="6" style="color: #393894; text-align: right;">Total Payable in TZS</td>
                    <td style="background-color: #548235; color: white"><?= $currency->symbol . ' ' . number_format($total_amount, 2) ?></td>
                </tr>
                <tr>
                    <td colspan="7" style="color: #393894; text-align: left;">AMOUNT IN
                        WORD: <?= numbers_to_words($total_amount) ?> ONLY
                    </td>
                </tr>
                <tr style="background-color: #AEAAAA;">
                    <td colspan="7" style="color: #393894; text-align: left;">DATE REQUIRED</td>
                </tr>
                <tr>
                    <td style="width: 8%; background-color: #A9D08E"></td>
                    <td colspan="4"
                        style="background-color: white; color: #0570C0; text-align: center; vertical-align: center"><?= $formated_delivery_date ?></td>
                    <td colspan="2" style="background-color: white"></td>
                </tr>
                <tr>
                    <td colspan="4"
                        style="width: 8%; background-color: #AEAAAA; color: #160290; vertical-align: top; text-align: left">
                        APPROVED BY
                    </td>
                    <td colspan="3" style="background-color: white"><?= $order->employee()->full_name() ?></td>
                </tr>
                <tr>
                    <td colspan="2"
                        style="width: 8%; background-color: #AEAAAA; color: #160290; font-size: 10px; vertical-align: bottom">
                        &nbsp;
                    </td>
                    <td colspan="2"
                        style="background-color: white; color: #828993; text-align: center; vertical-align: bottom; font-size: 10px;">&nbsp;</td>
                    <td colspan="3"
                        style="background-color: white; color: #828993; vertical-align: bottom; text-align: left; font-size: 10px;">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td colspan="2"
                        style="width: 8%; background-color: #AEAAAA; color: #160290; font-size: 10px; vertical-align: bottom; text-align: right">
                        Date:
                    </td>
                    <td colspan="2"
                        style="background-color: white; color: #828993; text-align: center; vertical-align: bottom; font-size: 10px;"><?= set_date($order->issue_date) ?></td>
                    <td colspan="3"
                        style="background-color: white; color: #828993; vertical-align: bottom; text-align: left; font-size: 10px;">
                        Authorized Signature
                    </td>
                </tr>
                </tfoot>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

